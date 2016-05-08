<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 02/05/2016
 * Time: 09:08
 */

namespace AppBundle\Controller;


use AppBundle\MessageResponse\MessageResponse;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher,
	FOS\RestBundle\Controller\Annotations\RequestParam,
	FOS\RestBundle\View\View,
	FOS\RestBundle\Controller\Annotations as Rest;
;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class TokenController extends FOSRestController
{
    /**
     * Generer un token
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Generer un token",
     *   statusCodes = {
     *     200 = "Created",
     *     400 = "Bad credentiel",
     *   }
     * )
     * @RequestParam(name="username",nullable=false, description="username")
     * @RequestParam(name="password",nullable=false, description="password")
     * @Route("api/users/token",name="get_token", options={"expose"=true})
     * @Method({"POST"})
     */

    public function newTokenAction(ParamFetcher $paramFetcher){
        $user = $this->getDoctrine()->getRepository('AppBundle:User')
                     ->findOneBy(['username'=>$paramFetcher->get('username')]);

        if(!$user){
            return MessageResponse::message('Nom utilisateur ou mot de passe incorrect','danger',400);
        }

        $isValid = $this->get('security.password_encoder')
                        ->isPasswordValid($user,$paramFetcher->get('password'));

        if(!$isValid){
            return MessageResponse::message('Nom utilisateur ou mot de passe incorrect','danger',400);
        }

        $token = $this->get('lexik_jwt_authentication.encoder')
                      ->encode(['username' => $user->getUsername()]);

        return $this->view(array('token'=>$token,'user'=>$user),200);



    }

}