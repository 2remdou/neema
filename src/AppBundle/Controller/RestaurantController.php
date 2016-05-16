<?php
/**
*	mdoutoure 01/05/2016	
*/

namespace AppBundle\Controller;

use AppBundle\Entity\ImageRestaurant;
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
	 * @Security("has_role('ROLE_ADMIN')")
     */
	public function postRestaurantAction(Request $request,ParamFetcher $paramFetcher){
        
		$operation = $this->get('app.operation');
		$restaurant = new Restaurant();
		return $operation->post($request,$restaurant);
	}

	/**
	 * Ajouter une image à un restaurant
	 *
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Ajouter une image à un restaurant",
	 *   statusCodes = {
	 *     201 = "Created",
	 *   }
	 * )
	 * @RequestParam(name="restaurant",nullable=false, description="id du restaurant")
	 * @Route("api/restaurants/image",name="post_image_restaurant", options={"expose"=true})
	 * @Method({"POST"})
	 * @Security("has_role('ROLE_RESTAURANT')")
	 */
	public function postRestaurantImageAction(Request $request,ParamFetcher $paramFetcher){
		$em = $this->getDoctrine()->getManager();
        $restaurant = $em->getRepository('AppBundle:Restaurant')->find($paramFetcher->get('restaurant'));
        if(!$restaurant){
            return MessageResponse::message('Erreur lors de l\'enregistrement de l\'image','danger',404);
        }

        try{
            $file = $request->files->get('file');
            $image = new ImageRestaurant();
            $image->setImageFile($file);
            $image->setRestaurant($restaurant);

            $em->persist($image);
            $em->flush();

            return MessageResponse::message('Enregistrement effectué avec succès','success',200);

        }catch (Exception $e){
            $em->remove($restaurant);
            $em->flush();
        }
	}

	/**
	 * Supprimer une image à un restaurant
	 *
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Supprimer une image à un restaurant",
	 *   statusCodes = {
	 *     201 = "Created",
	 *   }
	 * )
	 * @Route("api/restaurants/image/{id}",name="delete_image_restaurant", options={"expose"=true})
	 * @Method({"DELETE"})
	 * @Security("has_role('ROLE_RESTAURANT')")
	 */
	public function deleteRestaurantImageAction($id){

        $operation = $this->get('app.operation');
        return $operation->delete('AppBundle:ImageRestaurant',$id);
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
	 * @Security("has_role('ROLE_RESTAURANT')")
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
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 */
	public function deleteRestaurantAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Restaurant',$id);

	}
}