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
use AppBundle\Entity\Restaurant;


class RestaurantController extends FOSRestController
{
	/**
     * Ajouter un restaurant
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un restaurant",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="nom du restaurant")
	 * @RequestParam(name="quartier",nullable=false, description="id du quartier")
	 * @Route("api/restaurants",name="post_restaurant", options={"expose"=true})
     * @Method({"POST"})
     */
	public function postRestaurantAction(Request $request,ParamFetcher $paramFetcher){
        
		$operation = $this->get('app.operation');
		$restaurant = new Restaurant();
		return $operation->post($request,$restaurant);
	}

	/**
     * Lister les restaurants
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les restaurants",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/restaurants",name="get_restaurants", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getRestaurantsAction(){
		$operation = $this->get('app.operation');
		return $operation->all('AppBundle:Restaurant');
	}

	/**
     * retourner une restaurant
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner une restaurant",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/restaurants/{id}",name="get_restaurant", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getRestaurantAction($id){
		$operation = $this->get('app.operation');
		return $operation->get('AppBundle:Restaurant',$id);
	}

	/**
     * Modifier une restaurant
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier une restaurant",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="nom de la restaurant")
	 * @RequestParam(name="quartier",nullable=false, description="id de quartier")
	 * @Route("api/restaurants/{id}",name="put_restaurant", options={"expose"=true})
     * @Method({"PUT"})
     */
	public function putRestaurantAction($id,Request $request,ParamFetcher $paramFetcher){
		$operation = $this->get('app.operation');
		return $operation->put($request,'AppBundle:Restaurant',$id);
	}	

	/**
     * Supprimer une restaurant
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer une restaurant",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/restaurants/{id}",name="delete_restaurant", options={"expose"=true})
     * @Method({"DELETE"})
     */
	public function deleteRestaurantAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Restaurant',$id);

	}
}