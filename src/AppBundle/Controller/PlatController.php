<?php
/**
*	mdoutoure 01/05/2016	
*/

namespace AppBundle\Controller;

use AppBundle\Entity\ImagePlat;
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
use AppBundle\Entity\Plat;

/**
 * Class PlatController
 * @package AppBundle\Controller
 */
class PlatController extends FOSRestController
{
	/**
     * Ajouter un plat
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un plat",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="nom du plat")
     * @RequestParam(name="description",nullable=false, description="description du plat")
     * @RequestParam(name="prix",nullable=false, description="prix du plat")
     * @RequestParam(name="restaurant",nullable=false, description="id du restaurant")
     * @Route("api/plats",name="post_plat", options={"expose"=true})
     * @Method({"POST"})
	 * @Security("has_role('ROLE_RESTAURANT')")
     */
	public function postPlatAction(Request $request,ParamFetcher $paramFetcher){
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            return MessageResponse::message('Un administrateur ne peut creer de plat','danger',400);
        }

        $em = $this->getDoctrine()->getManager();
        $operation = $this->get('app.operation');
        $plat = new Plat();
        $plat = $operation->fill($request->request,$plat);
        if($plat instanceof View){
            return $plat;
        }
        $userRestaurant = $em->getRepository('AppBundle:UserRestaurant')->findOneBy(array('user'=>$this->getUser()->getId()));
        if(!$userRestaurant){
            return MessageResponse::message('Cet utilisateur n\'est lié à aucun restaurant','danger',400);
        }
        $restaurant = $userRestaurant->getRestaurant();
        $plat->setRestaurant($restaurant);
        $validator = $this->get('validator');

        if($messages = MessageResponse::messageAfterValidation($validator->validate($plat))){
            return MessageResponse::message($messages,'danger',400);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($plat);
        $em->flush();

        return MessageResponse::message('Enregistrement effectué','success',201, array('plat' =>$plat));


    }

    /**
     * Ajouter une image à un plat
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter une image à un plat",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="plat",nullable=false, description="id du plat")
     * @Route("api/plats/image",name="post_image_plat", options={"expose"=true})
     * @Method({"POST"})
	 * @Security("has_role('ROLE_RESTAURANT')")
	 */
    public function postPlatImageAction(Request $request,ParamFetcher $paramFetcher){
        $em = $this->getDoctrine()->getManager();

        $plat = $em->getRepository('AppBundle:Plat')->find($paramFetcher->get('plat'));
        if(!$plat){
            return MessageResponse::message('Erreur lors de l\'enregistrement de l\'image','danger',404);
        }

        if($plat->getImage()){ //s'il existe une autre image, je le supprime
            $em->remove($plat->getImage());
            $em->flush();
        }

        $file = $request->files->get('file');
        $image = new ImagePlat();
        $image->setWebPath($this->getParameter('urlimages').'/plats');
        $image->setImageFile($file);
        $image->setPlat($plat);

        $em->persist($image);
        $em->flush();

        return MessageResponse::message('Enregistrement effectué avec succès','success',200);
    }


    /**
     * Lister les plats
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les plats",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/plats",name="get_plats", options={"expose"=true})
     * @Method({"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

	public function getPlatsAction(){
		$operation = $this->get('app.operation');
        return array('plats'=>$operation->all('AppBundle:Plat'));
	}
    /**
     * Lister les plats sur des menus
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les plats sur des menus",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/plats/onMenu",name="get_plats_onmenu", options={"expose"=true})
     * @Method({"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

	public function getPlatsOnMenuAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $plats = $em->getRepository('AppBundle:Plat')->findOnMenu();

        $paginator  = $this->get('knp_paginator');
        $platsPaginate = $paginator->paginate($plats,$request->query->getInt('page', 1),10);
        return $platsPaginate->getItems();
	}

    /**
     * Lister les plats par restaurant en fonction du user connecté
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les plats par restaurant en fonction du user connecté",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/plats/restaurant/userConnected",name="get_plats_restaurant_user", options={"expose"=true})
     * @Method({"GET"})
     * @Security("has_role('ROLE_RESTAURANT')")
     */

    public function getPlatsByRestaurantByUserAction(){
        $em = $this->getDoctrine()->getManager();

        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $operation = $this->get('app.operation');
            return $operation->all('AppBundle:Plat');
        }
        $userRestaurant = $em->getRepository('AppBundle:UserRestaurant')->findOneBy(array('user'=>$this->getUser()->getId()));
        if(!$userRestaurant){
            return MessageResponse::message('Cet utilisateur n\'est lié à aucun restaurant','danger',400);
        }
        $restaurant = $userRestaurant->getRestaurant();
        if(!$restaurant){
            return MessageResponse::message('Restaurant introuvable','info',400);
        }
        $operation = $this->get('app.operation');
        return $em->getRepository('AppBundle:Plat')->findByRestaurant($restaurant->getId());
    }


    /**
     * Lister les plats par restaurant
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les plats par restaurant",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/plats/restaurant/{restaurant}",name="get_plats_restaurant", options={"expose"=true})
     * @Method({"GET"})
     */

    public function getPlatsByRestaurantAction($restaurant){
        $em = $this->getDoctrine()->getManager();
        $restaurant = $em->getRepository('AppBundle:Restaurant')->findOneBy(array('id'=>$restaurant));
        if(!$restaurant){
            return MessageResponse::message('Restaurant introuvable','info',400);
        }
        return $em->getRepository('AppBundle:Plat')->findByRestaurant($restaurant->getId());
    }


    /**
     * retourner un plat
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner un plat",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/plats/{id}",name="get_plat", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getPlatAction($id){
		$operation = $this->get('app.operation');
		return $operation->get('AppBundle:Plat',$id);
	}

	/**
     * Modifier un plat
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier un plat",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="nom du plat")
     * @Route("api/plats/{id}",name="put_plat", options={"expose"=true})
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_RESTAURANT')")
	 */
	public function putPlatAction($id,Request $request,ParamFetcher $paramFetcher){
		$operation = $this->get('app.operation');
		return $operation->put($request,'AppBundle:Plat',$id);
	}	

	/**
     * Supprimer un plat
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer un plat",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/plats/{id}",name="delete_plat", options={"expose"=true})
     * @Method({"DELETE"})
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 */
	public function deletePlatAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Plat',$id);

	}

    /**
     * Met a jour le menu d'un restaurant en activant ou desactivant des plats
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Met a jour le menu d'un restaurant en activant ou desactivant des plats",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="plats", array=true,nullable=false, description="les plats a mettre à jour")
     * @Route("api/updateMenu",name="update_menu", options={"expose"=true})
     * @Method({"PUT"})
     * @Security("has_role('ROLE_RESTAURANT')")
     */

    public function updateMenu(ParamFetcher $paramFetcher){

        $em = $this->getDoctrine()->getManager();
        $plats = $paramFetcher->get('plats');
        if($plats){
            $fail = array();
            $success = array();
            foreach($plats as $p){
                $plat = $em->getRepository('AppBundle:Plat')->findOneBy(array('id'=>$p['id']));
                if($plat){
                    $plat->setOnMenu($p['onMenu']);
                    $success[]=$p['id'];
                }else{
                    $fail[]=$p['id'];
                }
            }
            $em->flush();

            return MessageResponse::message('Le menu a été mis à jour','success',200,array('fail'=>$fail,'success'=>$success));
        }{
            return MessageResponse::message('Veuillez fournir des plats à mettre à jour','danger',400);
        }
    }
}