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
use AppBundle\Entity\Livreur;


class LivreurController extends FOSRestController
{
	/**
     * Ajouter un livreur
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un livreur",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="code",nullable=false, description="le code du livreur")
     * @RequestParam(name="nom",nullable=false, description="le libelle du livreur")
     * @RequestParam(name="prenom",nullable=false, description="le prenom du livreur")
     * @Route("api/livreurs",name="post_livreur", options={"expose"=true})
     * @Method({"POST"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function postLivreurAction(Request $request,ParamFetcher $paramFetcher){
        
		$operation = $this->get('app.operation');
		$livreur = new Livreur();
		return $operation->post($request,$livreur);
	}

	/**
     * Lister les livreurs
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les livreurs",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/livreurs",name="get_livreurs", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getLivreursAction(){
		$operation = $this->get('app.operation');
		return $operation->all('AppBundle:Livreur');
	}

	/**
     * retourner un livreur
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner un livreur",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/livreurs/{id}",name="get_livreur", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getLivreurAction($id){
		$operation = $this->get('app.operation');
		return $operation->get('AppBundle:Livreur',$id);
	}

	/**
     * Modifier un livreur
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier un livreur",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="code",nullable=false, description="le code du livreur")
     * @RequestParam(name="nom",nullable=false, description="le libelle du livreur")
     * @RequestParam(name="prenom",nullable=false, description="le prenom du livreur")
     * @Route("api/livreurs/{id}",name="put_livreur", options={"expose"=true})
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function putLivreurAction($id,Request $request,ParamFetcher $paramFetcher){
		$operation = $this->get('app.operation');
		return $operation->put($request,'AppBundle:Livreur',$id);
	}	

	/**
     * Supprimer un livreur
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer un livreur",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/livreurs/{id}",name="delete_livreur", options={"expose"=true})
     * @Method({"DELETE"})
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 */
	public function deleteLivreurAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Livreur',$id);

	}
}