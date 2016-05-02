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
use AppBundle\Entity\Quartier;


class QuartierController extends FOSRestController
{
	/**
     * Ajouter un quartier
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un quartier",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="libelle de la quartier")
	 * @RequestParam(name="commune",nullable=false, description="id de la commune")
	 * @Route("api/quartiers",name="post_quartier", options={"expose"=true})
     * @Method({"POST"})
     */
	public function postQuartierAction(Request $request,ParamFetcher $paramFetcher){
        
		$operation = $this->get('app.operation');
		$quartier = new Quartier();
		return $operation->post($request,$quartier);
	}

	/**
     * Lister les quartiers
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les quartiers",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/quartiers",name="get_quartiers", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getQuartiersAction(){
		$operation = $this->get('app.operation');
		return $operation->all('AppBundle:Quartier');
	}

	/**
     * retourner une quartier
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner une quartier",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/quartiers/{id}",name="get_quartier", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getQuartierAction($id){
		$operation = $this->get('app.operation');
		return $operation->get('AppBundle:Quartier',$id);
	}

	/**
     * Modifier une quartier
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier une quartier",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="libelle de la quartier")
	 * @RequestParam(name="commune",nullable=false, description="id de la commune")
	 * @Route("api/quartiers/{id}",name="put_quartier", options={"expose"=true})
     * @Method({"PUT"})
     */
	public function putQuartierAction($id,Request $request,ParamFetcher $paramFetcher){
		$operation = $this->get('app.operation');
		return $operation->put($request,'AppBundle:Quartier',$id);
	}	

	/**
     * Supprimer une quartier
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer une quartier",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/quartiers/{id}",name="delete_quartier", options={"expose"=true})
     * @Method({"DELETE"})
     */
	public function deleteQuartierAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Quartier',$id);

	}
}