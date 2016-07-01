<?php
/**
*	mdoutoure 01/05/2016	
*/

namespace AppBundle\Controller;

use AppBundle\Annotation\RestaurantIsAllow;
use AppBundle\Entity\DetailCommande;
use AppBundle\Event\CommandeEnregistreEvent;
use AppBundle\NeemaEvents;
use AppBundle\Service\DurationCommande;
use AppBundle\Util\FillAttributes;
use FOS\RestBundle\Controller\FOSRestController,
	FOS\RestBundle\Request\ParamFetcher,
	FOS\RestBundle\Controller\Annotations\RequestParam,
	FOS\RestBundle\View\View,
	FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\MessageResponse\MessageResponse;
use JMS\SerializerBundle\JMSSerializerBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @RequestParam(name="fraisTransport",nullable=false, description="les frais de port de la commande")
     * @RequestParam(name="durationLivraison",nullable=false, description="le temps de livraison de la commande en secondes")
     * @RequestParam(name="distance",nullable=false, description="la distance entre le client et le restaurant en metre")
     * @RequestParam(name="restaurant",nullable=false, description="Le restaurant de la commande")
     * @Route("api/commandes",name="post_commande", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
	public function postCommandeAction(Request $request,ParamFetcher $paramFetcher){

        $details = $paramFetcher->get('detailCommandes');
        $operation = $this->get('app.operation');

        if(!$details){
            return MessageResponse::message('Une commande ne peut exister sans plat','danger',400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();

        $restaurant = $em->getRepository('AppBundle:Restaurant')->findOneBy(array('id'=>$paramFetcher->get('restaurant')));

        if(!$restaurant){
            return MessageResponse::message('Le restaurant de la commande est inconnu','danger',400);
        }
        $commande = new Commande();
        $etatCommande = $em->getRepository('AppBundle:EtatCommande')->findOneBy(array('code'=>'CN1'));
        try{

            $commande->setTelephone($paramFetcher->get('telephone'));
            $commande->setLatitude($paramFetcher->get('latitude'));
            $commande->setLongitude($paramFetcher->get('longitude'));
            $commande->setFraisTransport($paramFetcher->get('fraisTransport'));
            $commande->setDurationLivraison($paramFetcher->get('durationLivraison'));
            $commande->setDistance($paramFetcher->get('distance'));
            $commande->setUser($this->getUser());
            $commande->setRestaurant($restaurant);
            $commande->setEtatCommande($etatCommande);

            $duration = new DurationCommande($commande);

            dump($commande);

            $validator = $this->get('validator');

            if($messages = MessageResponse::messageAfterValidation($validator->validate($commande))){
                return MessageResponse::message($messages,'danger',400);
            }

            $em->persist($commande);
//            $em->flush();

            $nbrePlat = 1;
            $restaurantPlat = null;
            foreach($details as $detail){
                $plat = $operation->get('AppBundle:Plat',$detail['plat']);
                if($nbrePlat===1){
                    $restaurantPlat = $plat->getRestaurant();
                    if($restaurantPlat !== $restaurant){
                        return MessageResponse::message('Une commande ne peut avoir des plats de differents restaurants','danger',400);
                    }
                }else{
                    if($restaurantPlat !== $plat->getRestaurant()){
                        return MessageResponse::message('Une commande ne peut avoir des plats de differents restaurants','danger',400);
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

                $duration->addDuration($plat->getDureePreparation());

                if($messages = MessageResponse::messageAfterValidation($validator->validate($detailCommande))){
                    return MessageResponse::message($messages,'danger',400);
                }

                $em->persist($detailCommande);
//                $em->flush();

                $nbrePlat++;

            }

            $duration->addDuration($this->getParameter('majorationTimeLivraison'));

            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(NeemaEvents::COMMANDE_ENREGISTRE,new CommandeEnregistreEvent($commande));

            $em->flush();
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
		$commandes =  $operation->all('AppBundle:Commande');
        return $commandes;
	}
	/**
     * @Route("api/test/commandes/{id}",name="get_test_commandes", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getTestCommandesAction($id){
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('AppBundle:Commande')->findOneBy(array('id'=>$id));
        if(!$commande)
            return MessageResponse::message('Commande introuvable','danger',404);
        $dateActuelle = new \DateTime();
        $tempsEcoule = $dateActuelle->format('U')-$commande->getDateCommande()->format('U');
        dump($tempsEcoule);

        return $em->getRepository('AppBundle:Commande')->getDureeRestant($id,$tempsEcoule);

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

            $commandes = $em->getRepository('AppBundle:Commande')->findByTypeDelivered(false);
            return $commandes;
        }

        $user = $this->getUser();
        $userRestaurant = $em->getRepository('AppBundle:UserRestaurant')->findOneBy(array('user'=>$user->getId()));
        if(!$userRestaurant){
            return MessageResponse::message('Cet utilisateur n\'est lié à aucun restaurant','danger',400);
        }
        $commandes = $em->getRepository('AppBundle:Commande')->findByTypeDelivered(false,$userRestaurant->getRestaurant()->getId());
        return $commandes;
	}

	/**
     * Lister les commandes passées par utilisateur connecté
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les commandes passées par utilisateur connecté",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/commandes/userConnected",name="get_commandes_by_user", options={"expose"=true})
     * @Method({"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

	public function getCommandesByUserConnectedAction(){
        $em = $this->getDoctrine()->getManager();


        $user = $this->getUser();

        $commandes = $em->getRepository('AppBundle:Commande')->findByUser($user->getId());
        return $commandes;
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

	public function getCommandeAction($id,Request $request){

        $em = $this->getDoctrine()->getManager();
        $commande=$em->getRepository('AppBundle:Commande')->findById($id);
//        $commande=$em->getRepository('AppBundle:Commande')->findOneBy(array("id"=>$id));
        if(!$commande){
            return MessageResponse::message('Commande introuvable','danger',400);
        }
//        dump($c);
        return $commande;
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
     * @RequestParam(name="fraisTransport",nullable=false, description="les frais de port de la commande")
     * @RequestParam(name="durationLivraison",nullable=false, description="le temps de livraison de la commande")
     * @RequestParam(name="distance",nullable=false, description="la distance entre le client et le restaurant")
     * @Route("api/commandes/{id}",name="put_commande", options={"expose"=true})
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function putCommandeAction($id,Request $request,ParamFetcher $paramFetcher){
		$operation = $this->get('app.operation');
		return $operation->put($request,'AppBundle:Commande',$id);
	}	
	/**
     * Marquer la fin de la preparation d'un plat dans une commande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Marquer la fin de la preparation d'un plat dans une commande",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/commandes/details/{id}/finish",name="put_close_detail_commande", options={"expose"=true})
     * @ParamConverter("detailCommande", class="AppBundle:DetailCommande")
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_RESTAURANT')")
	 */
	public function putCloseDetailCommandeAction(DetailCommande $detailCommande){
		$userRestaurant = $this->getUser()->getUserRestaurant();
        $platRestaurant = $detailCommande->getPlat()->getRestaurant();
        if($userRestaurant->getRestaurant() !== $platRestaurant){
            return MessageResponse::message('Vous n\'êtes pas autorisé à modifier cette commande','danger',400);
        }

        $em = $this->getDoctrine()->getManager();

        $detailCommande->setFinished(true);
        $detailCommande->setDateFinished(new \DateTime());

        $em->flush();

        return MessageResponse::message('Merci, ce plat a été marqué terminé','success',200);
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