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
     */
	public function postPlatAction(Request $request,ParamFetcher $paramFetcher){
        $operation = $this->get('app.operation');
        $plat = new Plat();
        return $operation->post($request,$plat);
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
     */
    public function postPlatImageAction(Request $request,ParamFetcher $paramFetcher){
        $em = $this->getDoctrine()->getManager();

        $plat = $em->getRepository('AppBundle:Plat')->find($paramFetcher->get('plat'));
        if(!$plat){
            return MessageResponse::message('Erreur lors de l\'enregistrement de l\'image','danger',404);
        }

        $file = $request->files->get('file');
        $image = new ImagePlat();
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
     */

	public function getPlatsAction(){
		$operation = $this->get('app.operation');
		return $operation->all('AppBundle:Plat');
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
     */
	public function deletePlatAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Plat',$id);

	}
}