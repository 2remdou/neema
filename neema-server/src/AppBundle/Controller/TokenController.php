<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 02/05/2016
 * Time: 09:08
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\MessageResponse\MessageResponse;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher,
	FOS\RestBundle\Controller\Annotations\RequestParam,
	FOS\RestBundle\View\View,
	FOS\RestBundle\Controller\Annotations as Rest;
;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class TokenController extends FOSRestController
{
    /**
     * Se connecter et Generer un token avec le role client
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Se connecter et Generer un token avec le role client",
     *   statusCodes = {
     *     200 = "Created",
     *     400 = "Bad credentiel",
     *   }
     * )
     * @RequestParam(name="username",nullable=false, description="username")
     * @RequestParam(name="password",nullable=false, description="password")
     * @Route("api/users/login-client",name="login_client", options={"expose"=true})
     * @Method({"POST"})
     */

    public function loginClientAction(Request $request, ParamFetcher $paramFetcher){
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

        if(!$user->hasRole('ROLE_CLIENT')){
            return MessageResponse::message("Vous n'êtes autorisé à acceder à cette application",'danger',400);
        }

        $jwt = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

        $response = new JsonResponse();
        $event    = new AuthenticationSuccessEvent(['token' => $jwt], $user, $request, $response);

        $dispatcher = $this->get('event_dispatcher');

        $dispatcher->dispatch(Events::AUTHENTICATION_SUCCESS, $event);
        $response->setData($event->getData());

        return $response;


    }
    /**
     * Se connecter et Generer un token avec le role restaurant
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Se connecter et Generer un token avec le role restaurant",
     *   statusCodes = {
     *     200 = "Created",
     *     400 = "Bad credentiel",
     *   }
     * )
     * @RequestParam(name="username",nullable=false, description="username")
     * @RequestParam(name="password",nullable=false, description="password")
     * @Route("api/users/login-restaurant",name="login_restaurant", options={"expose"=true})
     * @Method({"POST"})
     */

    public function loginRestaurantAction(Request $request, ParamFetcher $paramFetcher){
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

        if(!$user->hasRole('ROLE_RESTAURANT')){
            return MessageResponse::message("Vous n'êtes autorisé à acceder à cette application",'danger',400);
        }

        $jwt = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

        $response = new JsonResponse();
        $event    = new AuthenticationSuccessEvent(['token' => $jwt], $user, $request, $response);

        $dispatcher = $this->get('event_dispatcher');

        $dispatcher->dispatch(Events::AUTHENTICATION_SUCCESS, $event);
        $response->setData($event->getData());

        return $response;


    }

}