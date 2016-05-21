<?php
/**
*	mdoutoure 01/05/2016	
*/

namespace AppBundle\Controller;

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
use AppBundle\Entity\Livraison;


class LivraisonController extends FOSRestController
{
	/**
     * Ajouter une livraison
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter une livraison",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="commande",nullable=false, description="Id de la commande de la livraison")
     * @RequestParam(name="livreur",nullable=false, description="Id du livreur")
     * @Route("api/livraisons",name="post_livraison", options={"expose"=true})
     * @Method({"POST"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function postLivraisonAction(Request $request,ParamFetcher $paramFetcher){
        
		$operation = $this->get('app.operation');
		$livraison = new Livraison();
		return $operation->post($request,$livraison);
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
     * @Method({"GET"})
     */

	public function getLivraisonAction($id){
		$operation = $this->get('app.operation');
		return $operation->get('AppBundle:Livraison',$id);
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