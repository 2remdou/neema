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
use AppBundle\Entity\Commune;


class CommuneController extends FOSRestController
{
	/**
     * Ajouter une commune
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter une commune",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="libelle de la commune")
     * @Route("api/communes",name="post_commune", options={"expose"=true})
     * @Method({"POST"})
     */
	public function postCommuneAction(Request $request,ParamFetcher $paramFetcher){
        
		$operation = $this->get('app.operation');
		$commune = new Commune();
		return $operation->post($request,$commune);
	}

	/**
     * Lister les communes
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les communes",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/communes",name="get_communes", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getCommunesAction(){
		$operation = $this->get('app.operation');
		return $operation->all('AppBundle:Commune');
	}

	/**
     * retourner une commune
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner une commune",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/communes/{id}",name="get_commune", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getCommuneAction($id){
		$operation = $this->get('app.operation');
		return $operation->get('AppBundle:Commune',$id);
	}

	/**
     * Modifier une commune
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier une commune",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="libelle de la commune")
     * @Route("api/communes/{id}",name="put_commune", options={"expose"=true})
     * @Method({"PUT"})
     */
	public function putCommuneAction($id,Request $request,ParamFetcher $paramFetcher){
		$operation = $this->get('app.operation');
		return $operation->put($request,'AppBundle:Commune',$id);
	}	

	/**
     * Supprimer une commune
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer une commune",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/communes/{id}",name="delete_commune", options={"expose"=true})
     * @Method({"DELETE"})
     */
	public function deleteCommuneAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Commune',$id);

	}
}