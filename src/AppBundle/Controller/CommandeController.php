<?php
/**
*	mdoutoure 01/05/2016	
*/

namespace AppBundle\Controller;

use AppBundle\Entity\DetailCommande;
use AppBundle\Event\CommandeEnregistreEvent;
use AppBundle\NeemaEvents;
use AppBundle\Util\FillAttributes;
use FOS\RestBundle\Controller\FOSRestController,
	FOS\RestBundle\Request\ParamFetcher,
	FOS\RestBundle\Controller\Annotations\RequestParam,
	FOS\RestBundle\View\View,
	FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\MessageResponse\MessageResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request,
	Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\Commande;


class CommandeController extends FOSRestController
{
	/**
     * Ajouter une commande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter une commande",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="dateCommande",nullable=false, description="la date de la commande")
     * @RequestParam(name="telephone",nullable=false, description="le numero de telephone du client")
     * @RequestParam(name="detailCommandes", array=true,nullable=false, description="les details de la commande")
     * @RequestParam(name="longitude",nullable=false, description="la longitude de la commande")
     * @RequestParam(name="latitude",nullable=false, description="la latitude de la commande")
     * @Route("api/commandes",name="post_commande", options={"expose"=true})
     * @Method({"POST"})
	 */
	public function postCommandeAction(Request $request,ParamFetcher $paramFetcher){

        $details = $paramFetcher->get('detailCommandes');
        $operation = $this->get('app.operation');

        if(!$details){
            return MessageResponse::message('Une commande ne peut exister sans plat','danger',400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();

        $commande = new Commande();
        try{

            $commande->setTelephone($paramFetcher->get('telephone'));
            $commande->setLatitude($paramFetcher->get('latitude'));
            $commande->setLongitude($paramFetcher->get('longitude'));

            $validator = $this->get('validator');

            if($messages = MessageResponse::messageAfterValidation($validator->validate($commande))){
                return MessageResponse::message($messages,'danger',400);
            }

            $em->persist($commande);
            $em->flush();

            $nbrePlat = 1;
            $restaurant = null;
            foreach($details as $detail){
                $plat = $operation->get('AppBundle:Plat',$detail['plat']);
                if($nbrePlat===1){
                    $restaurant = $plat->getRestaurant();
                }else{
                    if($restaurant !== $plat->getRestaurant()){
                        return MessageResponse::message('La même commande ne peut avoir des plats de differents restaurants','danger',400);
                    }
                }
                //plat introuvable
                if($plat instanceof View){
                    return $plat;
                }
                $detailCommande = new DetailCommande();

                $detailCommande->setCommande($commande);
                $detailCommande->setPlat($plat);
                $detailCommande->setQuantite($detail['quantite']);

                if($messages = MessageResponse::messageAfterValidation($validator->validate($detailCommande))){
                    return MessageResponse::message($messages,'danger',400);
                }

                $em->persist($detailCommande);
                $em->flush();

                $nbrePlat++;

            }
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(NeemaEvents::COMMANDE_ENREGISTRE,new CommandeEnregistreEvent($commande));

            $em->getConnection()->commit();

        }catch (Exception $e){
            $em->getConnection()->rollBack();
            throw $e;
        }

        return MessageResponse::message('La commande a été enrégistré avec succes','success',201,array('commande'=>$commande));


	}

	/**
     * Lister les commandes
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les commandes",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/commandes",name="get_commandes", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getCommandesAction(){
		$operation = $this->get('app.operation');
		return $operation->all('AppBundle:Commande');
	}
	/**
     * Lister les commandes d'un restaurant
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les commandes d'un restaurant",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/commandes/restaurantConnected",name="get_commandes_by_restaurant", options={"expose"=true})
     * @Method({"GET"})
     * @Security("has_role('ROLE_RESTAURANT')")
     */

	public function getCommandesByRestaurantAction(){
        $em = $this->getDoctrine()->getManager();

        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $operation = $this->get('app.operation');
            return array('plats'=>$operation->all('AppBundle:Commande'));
        }

        $user = $this->getUser();

        $commandes = $em->getRepository('AppBundle:Commande')->findByRestaurant($user->getUserRestaurant()->getRestaurant()->getId());
        return array('commandes'=> $commandes);
	}
	/**
     * Lister les details commandes
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les details commandes",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/commandes/details",name="get_detail_commandes", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getDetailCommandesAction(){
		$operation = $this->get('app.operation');
		return $operation->all('AppBundle:DetailCommande');
	}

	/**
     * retourner une commande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner une commande",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/commandes/{id}",name="get_commande", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getCommandeAction($id){
		$operation = $this->get('app.operation');
		return $operation->get('AppBundle:Commande',$id);
	}

	/**
     * retourner un detail
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner un detail",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/commandes/details/{id}",name="get_detail_commande", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getDetailCommandeAction($id){
		$operation = $this->get('app.operation');
		return $operation->get('AppBundle:DetalCommande',$id);
	}

	/**
     * Modifier une commande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier une commande",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="dateCommande",nullable=false, description="la date de la commande")
     * @RequestParam(name="telephone",nullable=false, description="le numero de telephone du client")
     * @RequestParam(name="longitude",nullable=false, description="la longitude de la commande")
     * @RequestParam(name="latitude",nullable=false, description="la latitude de la commande")
     * @Route("api/commandes/{id}",name="put_commande", options={"expose"=true})
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function putCommandeAction($id,Request $request,ParamFetcher $paramFetcher){
		$operation = $this->get('app.operation');
		return $operation->put($request,'AppBundle:Commande',$id);
	}	
	/**
     * Modifier un detail commande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier un detail commande",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="quantite",nullable=false, description="la quantite du plat")
     * @RequestParam(name="prix",nullable=false, description="le prix du plat")
     * @RequestParam(name="commande",nullable=false, description="Id de la commande")
     * @Route("api/commandes/details/{id}",name="put_detail_commande", options={"expose"=true})
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function putDetailCommandeAction($id,Request $request,ParamFetcher $paramFetcher){
		$operation = $this->get('app.operation');
		return $operation->put($request,'AppBundle:DetailCommande',$id);
	}

	/**
     * Supprimer une commande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer une commande",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/commandes/{id}",name="delete_commande", options={"expose"=true})
     * @Method({"DELETE"})
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 */
	public function deleteCommandeAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Commande',$id);

	}
	/**
     * Supprimer un detail d'une commande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer un detail d'une commande",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/commandes/details/{id}",name="delete_detail_commande", options={"expose"=true})
     * @Method({"DELETE"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function deleteDetailCommandeAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:DetailCommande',$id);

	}
}