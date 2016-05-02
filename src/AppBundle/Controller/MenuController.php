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
use AppBundle\Entity\Menu;


class MenuController extends FOSRestController
{
	/**
     * Ajouter un menu
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un menu",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="prix",nullable=false, description="prix du plat")
	 * @RequestParam(name="plat",nullable=false, description="id du plat")
	 * @RequestParam(name="restaurant",nullable=false, description="id du restaurant")
	 * @Route("api/menus",name="post_menu", options={"expose"=true})
     * @Method({"POST"})
     */
	public function postMenuAction(Request $request,ParamFetcher $paramFetcher){
        
		$operation = $this->get('app.operation');
		$menu = new Menu();
		return $operation->post($request,$menu);
	}

	/**
     * Lister les menus
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les menus",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/menus",name="get_menus", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getMenusAction(){
		$operation = $this->get('app.operation');
		return $operation->all('AppBundle:Menu');
	}

	/**
     * retourner un menu
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner un menu",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/menus/{id}",name="get_menu", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getMenuAction($id){
		$operation = $this->get('app.operation');
		return $operation->get('AppBundle:Menu',$id);
	}

	/**
     * Modifier un menu
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier un menu",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="prix",nullable=false, description="prix du plat")
	 * @RequestParam(name="plat",nullable=false, description="id du plat")
	 * @RequestParam(name="restaurant",nullable=false, description="id du restaurant")
	 * @Route("api/menus/{id}",name="put_menu", options={"expose"=true})
     * @Method({"PUT"})
     */
	public function putMenuAction($id,Request $request,ParamFetcher $paramFetcher){
		$operation = $this->get('app.operation');
		return $operation->put($request,'AppBundle:Menu',$id);
	}	

	/**
     * Supprimer un menu
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer un menu",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/menus/{id}",name="delete_menu", options={"expose"=true})
     * @Method({"DELETE"})
     */
	public function deleteMenuAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Menu',$id);

	}
}