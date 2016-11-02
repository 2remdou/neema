<?php
/**
*	mdoutoure 01/05/2016	
*/

namespace AppBundle\Controller;

use AppBundle\Annotation\RestaurantIsAllow;
use AppBundle\Entity\DetailCommande;
use AppBundle\Entity\Restaurant;
use AppBundle\Event\CommandeEnregistreEvent;
use AppBundle\Event\DetailCommandeEvent;
use AppBundle\Event\LivraisonEvent;
use AppBundle\Event\LivreurEvent;
use AppBundle\Exception\ApiException;
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
use Symfony\Component\Validator\Constraints\DateTime;


class CommandeController extends FOSRestController
{
    /**
     * @Route("api/test",name="commande_test", options={"expose"=true})
     * @Method({"GET"})
     */
    public function testCommande(){
        $utilService = $this->get('app.util.service');
        $utilService->attach(new Commande(),'restaurant',array('id'=>'071e0764-1504-11e6-b945-e0397cc46092'));
        return array();
    }


    private function getCommandeManager(){
        return $this->get('app.commande.manager');
    }
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
     * @RequestParam(name="restaurant",nullable=false, description="Le restaurant de la commande")
     * @RequestParam(name="aLivrer",nullable=false, description="La commande est à emporter ou pas")
     * @RequestParam(name="lieuLivraison",nullable=true, description="L'adresse de livraison")
     * @Route("api/commandes",name="post_commande", options={"expose"=true})
     * @Method({"POST"})
     * @Security("has_role('ROLE_CLIENT')")
     */
	public function postCommandeAction(Request $request,ParamFetcher $paramFetcher){

        $em = $this->getDoctrine()->getManager();
        $commandeManager = $this->getCommandeManager();

        $this->getUser();

        $validator = $this->get('validator');


        $em->getConnection()->beginTransaction();

        try{
            $commande = new Commande();

            $commande = $commandeManager->create($commande,$paramFetcher);

            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(NeemaEvents::COMMANDE_ENREGISTRE,new CommandeEnregistreEvent($commande));

            if($messages = MessageResponse::messageAfterValidation ($validator->validate($commande))){
                throw new ApiException($messages);
            }

            if($commande->getALivrer()){
                $livraison = $this->get('app.livraison.service')->create($commande);
                $em->persist($livraison);
            }

            $em->persist($commande);
            $em->flush();
            $em->getConnection()->commit();

        }catch (Exception $e){
            $em->getConnection()->rollBack();
            throw $e;
        }

        return MessageResponse::message('La commande a été enrégistré avec succes','success',201,array('id'=>$commande->getId(),'codeCommande'=>$commande->getCodeCommande()));


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

	public function getCommandesByRestaurantAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $commandeManager = $this->get('app.commande.manager');

        $user = $this->getUser();
        $userRestaurant = $em->getRepository('AppBundle:UserRestaurant')->findOneBy(array('user'=>$user->getId()));
        if(!$userRestaurant){
            throw new ApiException('Cet utilisateur n\'est lié à aucun restaurant',404,'danger');
        }


        $page = $request->query->getInt('page', 1);
        $page = $page<=0?1:$page;

        $commandes = $commandeManager->getNotFinishedByRestaurant($page);

        if(!$commandes){
            throw new ApiException('Aucune commande pour le moment',404,'info');
        }
        return $commandes;
	}
	/**
     * Rafraichir les commandes d'un restaurant en fonction d'un interval de temps
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Rafraichir les commandes d'un restaurant en fonction d'un interval de temps",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @RequestParam(name="from",nullable=false, description="la date de debut")
     * @Route("api/commandes/refresh-restaurant",name="get_refresh_commandes_by_restaurant", options={"expose"=true})
     * @Method({"POST"})
     * @Security("has_role('ROLE_RESTAURANT')")
     */

	public function getRefreshRestaurantCommandesFrom(Request $request){
        $em = $this->getDoctrine()->getManager();

        $from = $request->request->get('from');
        if(!$from){
            return MessageResponse::message('Le parametre from est obligatoire','danger',400);
        }
        $from = new \DateTime($from,new \DateTimeZone("UTC"));
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $operation = $this->get('app.operation');

            $commandes = $em->getRepository('AppBundle:Commande')->refreshMenu(null,$from,new \DateTime());
            return $commandes;
        }

        $user = $this->getUser();
        $userRestaurant = $em->getRepository('AppBundle:UserRestaurant')->findOneBy(array('user'=>$user->getId()));
        if(!$userRestaurant){
            return MessageResponse::message('Cet utilisateur n\'est lié à aucun restaurant','danger',400);
        }
        $commandes = $em->getRepository('AppBundle:Commande')->refreshByRestaurantMenu($userRestaurant->getRestaurant()->getId(),$from,new \DateTime());

        return $commandes;
	}

    /**
     * L'historique des commandes livrées à un client
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "L'historique des commandes livrées à un client",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/commandes/historique/client-connected",name="get_historique_client", options={"expose"=true})
     * @Method({"GET"})
     * @Security("has_role('ROLE_CLIENT')")
     */

    public function getHistoriqueByClientConnected(Request $request){

        $client = $this->getUser();

        $page = $request->query->getInt('page', 1);
        $page = $page<=0?1:$page;

        $from = $request->query->get('from');
        $to = new \DateTime();

        if($from){
            $d = new \DateTime();
            $from = $d->setTimestamp($from);
        }

        $commandeManager = $this->get('app.commande.manager');

        $commandes = $commandeManager->getHistoriqueByClient($client,$from,$to,$page);

        if(!$commandes && $page===1){
            throw new ApiException('Aucune commande livrée pour le moment',404,'info');
        }

        if(!$commandes){
            $paginator = array('currentPage'=>$page,'nextPage'=>$page);
        }else{
            $paginator = array('currentPage'=>$page,'nextPage'=>$page+1);
        }

        return array('commandes'=>$commandes,'paginator'=>$paginator);
    }


    /**
     * Rafraichir les commandes d'un client en fonction d'un interval de temps
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Rafraichir les commandes d'un client en fonction d'un interval de temps",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @RequestParam(name="from",nullable=false, description="la date de debut")
     * @Route("api/commandes/refresh-client",name="get_refresh_commandes_client", options={"expose"=true})
     * @Method({"POST"})
     * @Security("has_role('ROLE_CLIENT')")
     */

	public function getRefreshCommandesClientFrom(Request $request){
        $em = $this->getDoctrine()->getManager();

        $from = $request->request->get('from');
        if(!$from){
            return MessageResponse::message('Le parametre from est obligatoire','danger',400);
        }
        $from = new \DateTime($from,new \DateTimeZone("UTC"));

        $user = $this->getUser();

        $commandes = $em->getRepository('AppBundle:Commande')->refreshByClientMenu($user->getId(),$from,new \DateTime());
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

	public function getCommandesByUserConnectedAction(Request $request){
        $em = $this->getDoctrine()->getManager();


        $user = $this->getUser();

        $page = $request->query->getInt('page', 1);
        $page = $page<=0?1:$page;

        $commandes = $em->getRepository('AppBundle:Commande')->findByUser($user->getId(),$page,10);
        if(!$commandes){
            throw new ApiException('Aucune commande en attente',404,'info');
        }


        return array('commandes'=>$commandes,'currentPage'=>$page);
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
     * @ParamConverter("commande", class="AppBundle:Commande")
     * @Method({"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

	public function getCommandeAction($id){
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('AppBundle:Commande')->findById($id);
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
        $commandeManager = $this->get('app.commande.manager');
        $commandeManager->closeDetailCommande($detailCommande);

        $em->flush();


        return MessageResponse::message('Merci, ce plat a été marqué terminé','success',200);
    }

	/**
     * Donner la commande à un livreur
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Donner la commande à un livreur",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/commandes/{id}/give-to-livreur",name="give_to_livreur", options={"expose"=true})
     * @Method({"PUT"})
     * @ParamConverter("commande", class="AppBundle:Commande")
     * @Security("has_role('ROLE_RESTAURANT')")
	 */
	public function putGiveCommandeToLivreurAction(Commande $commande){
        $livraisonService = $this->get('app.livraison.service');

        $livraison = $commande->getLivraison();

        $livraisonService->startLivraison($livraison);

        $this->getDoctrine()->getManager()->flush();


        return MessageResponse::message('Merci, la commande a été donné au livreur','success',200);
    }


	/**
     * Marquer une commande comme livrée
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Marquer une commande comme livrée",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/commandes/{id}/delivered",name="put_commande_delivered", options={"expose"=true})
     * @RequestParam(name="code",nullable=false, description="le code secret du client")
     * @ParamConverter("commande", class="AppBundle:Commande")
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_RESTAURANT')")
	 */

	public function putDeliveredCommandeAction(Commande $commande,ParamFetcher $paramFetcher){

        if($commande->getCodeCommande()!=$paramFetcher->get('code')){
            return MessageResponse::message('Ce code n\'est pas valide','danger',400);
        }
        $em = $this->getDoctrine()->getManager();

        $dispatcher = $this->get('event_dispatcher');
        $commandeManager = $this->get('app.commande.manager');

        $commandeManager->closeCommande($commande);

        $em->flush();

        return MessageResponse::message('Merci, la commande est marquée comme livrée','success',200);
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