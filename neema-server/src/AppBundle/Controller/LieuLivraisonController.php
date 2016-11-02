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
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request,
	Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\LieuLivraison;


class LieuLivraisonController extends FOSRestController
{

    /**
     * Ajouter un lieu de livraison
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un lieu de livraison",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="le nom du lieu de la livraison")
     * @RequestParam(name="latitude",nullable=true, description="la latitude du lieu de la livraison")
     * @RequestParam(name="longitude",nullable=true, description="la longitude du lieu de la livraison")
     * @RequestParam(name="quartier",nullable=false, description="le quartier du lieu de la livraison")
     * @RequestParam(name="description",nullable=true, description="une description du restaurant")
     * @Route("api/lieu-livraisons",name="post_lieu_livraisons", options={"expose"=true})
     * @Method({"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function postLieuLivraisonAction(Request $request,ParamFetcher $paramFetcher){
        $lieuLivraison = new LieuLivraison();
        $lieuLivraison->setNom($paramFetcher->get('nom'));
        if($paramFetcher->get('latitude')) $lieuLivraison->setLatitude($paramFetcher->get('latitude'));
        if($paramFetcher->get('longitude')) $lieuLivraison->setLongitude($paramFetcher->get('longitude'));
        if($paramFetcher->get('description')) $lieuLivraison->setDescription($paramFetcher->get('description'));

        $em = $this->getDoctrine()->getManager();

        $quartier = $em->getRepository('AppBundle:Quartier')->findOneBy(array('id'=>$paramFetcher->get('quartier')));

        if(!$quartier){
            return MessageResponse::message('Le quartier est introuvable','danger',400);
        }
        $lieuLivraison->setQuartier($quartier);

        $validator = $this->get('validator');

        if($messages = MessageResponse::messageAfterValidation($validator->validate($lieuLivraison))){
            return MessageResponse::message($messages,'danger',400);
        }

        $em->persist($lieuLivraison);
        $em->flush();

        return MessageResponse::message('Le lieu a été enrégistré avec succes','success',201);

    }

    /**
     * Modifier un lieu de livraison
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier un lieu de livraison",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="le nom du lieu de la livraison")
     * @RequestParam(name="latitude",nullable=true, description="la latitude du lieu de la livraison")
     * @RequestParam(name="longitude",nullable=true, description="la longitude du lieu de la livraison")
     * @RequestParam(name="quartier",nullable=false, description="le quartier du lieu de la livraison")
     * @RequestParam(name="description",nullable=true, description="une description du restaurant")
     * @Route("api/lieu-livraisons/{id}",name="put_lieu_livraisons", options={"expose"=true})
     * @ParamConverter("lieuLivraison", class="AppBundle:LieuLivraison")
     * @Method({"PUT"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function putLieuLivraisonAction(LieuLivraison $lieuLivraison,ParamFetcher $paramFetcher){

        $lieuLivraison->setNom($paramFetcher->get('nom'));
        if($paramFetcher->get('latitude')) $lieuLivraison->setLatitude($paramFetcher->get('latitude'));
        if($paramFetcher->get('longitude')) $lieuLivraison->setLongitude($paramFetcher->get('longitude'));
        if($paramFetcher->get('description')) $lieuLivraison->setDescription($paramFetcher->get('description'));

        $em = $this->getDoctrine()->getManager();

        if($lieuLivraison->getQuartier()->getId() !== $paramFetcher->get('quartier')){
            $quartier = $em->getRepository('AppBundle:Quartier')->findOneBy(array('id'=>$paramFetcher->get('quartier')));

            if(!$quartier){
                return MessageResponse::message('Le quartier est introuvable','danger',400);
            }
            $lieuLivraison->setQuartier($quartier);
        }
        $validator = $this->get('validator');

        if($messages = MessageResponse::messageAfterValidation($validator->validate($lieuLivraison))){
            return MessageResponse::message($messages,'danger',400);
        }


        $em->flush();

        return MessageResponse::message('Le lieu a été modifié avec succes','success',201);

    }
    /**
     * Supprimer un lieu de livraison
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer un lieu de livraison",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @Route("api/lieu-livraisons/{id}",name="delete_lieu_livraisons", options={"expose"=true})
     * @ParamConverter("lieuLivraison", class="AppBundle:LieuLivraison")
     * @Method({"DELETE"})
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteLieuLivraisonAction(LieuLivraison $lieuLivraison){

        $em = $this->getDoctrine()->getManager();

        $em->remove($lieuLivraison);

        $em->flush();

        return MessageResponse::message('Suppression effectuée','success',200);

    }


    /**
     * retourner un lieuLivraison
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner un lieuLivraison",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/lieu-livraisons/{id}",name="get_lieu_livraison", options={"expose"=true})
     * @ParamConverter("lieuLivraison", class="AppBundle:LieuLivraison")
     * @Method({"GET"})
     */

	public function getLieuLivraisonAction(LieuLivraison $lieuLivraison){
        return $lieuLivraison;
	}

	/**
     * retourner un lieuLivraison d'un user
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner un lieuLivraison d'un user",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/lieu-livraisons",name="get_lieu_livraison", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getLieuLivraisonsAction(){
        $em = $this->getDoctrine()->getManager();

        $lieuLivraisons = $em->getRepository('AppBundle:LieuLivraison')->findAll();
        if(count($lieuLivraisons) === 0){
            return MessageResponse::message('Aucun lieu de livraison pour le moment','info',400);
        }

        return $lieuLivraisons;
	}

}