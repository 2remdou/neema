<?php
/**
*	mdoutoure 01/05/2016	
*/

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Exception\ApiException;
use AppBundle\Util\FillAttributes;
use FOS\RestBundle\Controller\FOSRestController,
	FOS\RestBundle\Request\ParamFetcher,
	FOS\RestBundle\Controller\Annotations\RequestParam,
	FOS\RestBundle\View\View,
	FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\MessageResponse\MessageResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request,
	Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\Livraison;


class LivraisonController extends FOSRestController
{
	/**
     * Pour marquer qu'une commande a été livré
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Pour marquer qu'une commande a été livré",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @Route("api/livraisons/commandes/{id}/finished",name="post_livraison", options={"expose"=true})
     * @ParamConverter("commande", class="AppBundle:Commande")
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_LIVREUR')")
	 */
	public function putLivraisonFinishedAction(Commande $commande){

        $livraisonService = $this->get('app.livraison.service');

        $livraison = $livraisonService->getLivraionByCommande($commande);

        if(!$livraison){
            throw new ApiException('Erreur lors de la recuperation de la livraison',400,'danger');
        }

        $livraisonService->livrer($livraison);

        $this->getDoctrine()->getManager()->flush();

        throw new ApiException('La commande a été marqué comme livré',201,'success');

    }

	/**
     * Lister les livraisons
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les livraisons",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/livraisons",name="get_livraisons", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getLivraisonsAction(){
		$operation = $this->get('app.operation');
		return $operation->all('AppBundle:Livraison');
	}

	/**
     * La livraison encours du user connecté
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "La livraison encours du user connecté",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/livraisons/current",name="get_livraisons_current", options={"expose"=true})
     * @Method({"GET"})
     * @Security("has_role('ROLE_LIVREUR')")
     */

	public function getLivrisonEncoursByLivreurConnected(){
        $livreurService = $this->get('app.livreur.service');
        $livraisonService = $this->get('app.livraison.service');

        $livreur = $livreurService->getLivreurByUser($this->getUser());
        if(!$livreur){
            return MessageResponse::message('Livreur inconnu','danger',400);
        }

        $livrason = $livraisonService->getLivraionEncours($livreur);
        if(!$livrason){
            throw new ApiException('Aucune livraison active',404,'info');
        }

        return $livrason;
	}

	/**
     * L'historique des livraison effectuées par un livreur
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "L'historique des livraison effectuées par un livreuré",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/livraisons/historique/livreur-connected",name="get_historique_livreur", options={"expose"=true})
     * @Method({"GET"})
     * @Security("has_role('ROLE_LIVREUR')")
     */

	public function getHistoriqueByLivreurConnected(){
        $livreurService = $this->get('app.livreur.service');
        $livraisonService = $this->get('app.livraison.service');

        $livreur = $livreurService->getLivreurByUser($this->getUser());

        if(!$livreur){
            return MessageResponse::message('Livreur inconnu','danger',400);
        }

        $livraisons = $livraisonService->getHistorique($livreur);

        if(!$livraisons){
//            return MessageResponse::message('Aucune livraison effectuée','info',400);
            throw new ApiException('Aucune livraison effectuée',204);
        }

        return $livraisons;
	}

	/**
     * retourner une livraison
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner une livraison",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/livraisons/{id}",name="get_livraison", options={"expose"=true})
     * @ParamConverter("livraison", class="AppBundle:Livraison")
     * @Method({"GET"})
     */

	public function getLivraisonAction($id){
        $em = $this->getDoctrine()->getManager();
        $livraison = $em->getRepository('AppBundle:Livraison')->findById($id);
        if(!$livraison){
            throw new ApiException("Livraison introuvable");
        }
        return $livraison;
	}

	/**
     * Modifier une livraison
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier une livraison",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="commande",nullable=false, description="Id de la commande de la livraison")
     * @RequestParam(name="livreur",nullable=false, description="Id du livreur")
     * @Route("api/livraisons/{id}",name="put_livraison", options={"expose"=true})
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function putLivraisonAction($id,Request $request,ParamFetcher $paramFetcher){
		$operation = $this->get('app.operation');
		return $operation->put($request,'AppBundle:Livraison',$id);
	}	

	/**
     * Supprimer une livraison
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer une livraison",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/livraisons/{id}",name="delete_livraison", options={"expose"=true})
     * @Method({"DELETE"})
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 */
	public function deleteLivraisonAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Livraison',$id);

	}
}