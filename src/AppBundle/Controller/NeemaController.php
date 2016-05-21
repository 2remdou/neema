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


class NeemaController extends FOSRestController
{

	/**
     * @Route("/",name="homepage", options={"expose"=false})
     * @Method({"GET"})
     */

	public function indexAction(){
        return $this->render('base.html.twig');
	}

    /**
     * Passer une commande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Passer une commande",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="dateCommande",nullable=false, description="la date de la commande")
     * @RequestParam(name="telephone",nullable=false, description="le numero de telephone du client")
     * @RequestParam(name="longitude",nullable=false, description="la longitude de la livraison")
     * @RequestParam(name="latitude",nullable=false, description="la latitude de la livraison")
     * @RequestParam(name="detailCommandes", array=true,nullable=false, description="les details de la commande")
     * @Route("api/commandes",name="post_commande", options={"expose"=true})
     * @Method({"POST"})
     */
    public function passerCommandeAction(Request $request,ParamFetcher $paramFetcher){

        $details = $paramFetcher->get('detailCommandes');
        $operation = $this->get('app.operation');

        if(!$details){
            return MessageResponse::message('Une commande ne peut exister sans plat','danger',400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();

        $commande = new Commande();
        try{

            $commande->setTelephone($paramFetcher->get('telephone'));

            $validator = $this->get('validator');

            if($messages = MessageResponse::messageAfterValidation($validator->validate($commande))){
                return MessageResponse::message($messages,'danger',400);
            }

            $em->persist($commande);
            $em->flush();

            foreach($details as $detail){
                $plat = $operation->get('AppBundle:Plat',$detail['plat']);

                //plat introuvable
                if($plat instanceof View){
                    return $plat;
                }
                $detailCommande = new DetailCommande();

                $detailCommande->setCommande($commande);
                $detailCommande->setPlat($plat);
                $detailCommande->setQuantite($detail['quantite']);

                if($messages = MessageResponse::messageAfterValidation($validator->validate($detailCommande))){
                    return MessageResponse::message($messages,'danger',400);
                }

                $em->persist($detailCommande);
                $em->flush();

            }
            $em->getConnection()->commit();

        }catch (Exception $e){
            $em->getConnection()->rollBack();
            throw $e;
        }

        return MessageResponse::message('La commande a été enrégistré avec succes','success',201,array('commande'=>$commande));


    }


}