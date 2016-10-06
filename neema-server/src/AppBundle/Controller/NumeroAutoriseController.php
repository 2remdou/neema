<?php
/**
*	mdoutoure 28/09/2016	
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
use AppBundle\Entity\NumeroAutorise;


class NumeroAutoriseController extends FOSRestController
{

    /**
     * Ajouter un numero autorise à passer une commande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un numero autorise à passer une commande",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="numero",nullable=false, description="le numero à autoriser")
     * @Route("api/numero-autorises",name="post_numeroAutorise", options={"expose"=true})
     * @Method({"POST"})
     * @Security("has_role('ROLE_RESTAURANT')")
     */
    public function postNumeroAutoriseAction(ParamFetcher $paramFetcher){
        $numeroAutorise = new NumeroAutorise();
        $numeroAutorise->setNumero($paramFetcher->get('numero'));

        $em = $this->getDoctrine()->getManager();
        $validator = $this->get('validator');

        if($messages = MessageResponse::messageAfterValidation($validator->validate($numeroAutorise))){
            return MessageResponse::message($messages,'danger',400);
        }


        $em->persist($numeroAutorise);
        $em->flush();

        return MessageResponse::message('Le numero '.$numeroAutorise->getNumero().' est desormais
        autorisé à passer une commande','success',201);

    }

    /**
     * Liste des numeros autorisée
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Liste des numeros autorisée",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/numero-autorises",name="get_numeroAutorise", options={"expose"=true})
     * @Method({"GET"})
     */

    public function getNumeroAutorisesAction(){
        $operation = $this->get('app.operation');
        return $operation->all('AppBundle:NumeroAutorise');
    }



}