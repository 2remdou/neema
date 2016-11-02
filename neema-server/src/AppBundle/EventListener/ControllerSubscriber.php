<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 22/05/2016
 * Time: 06:52
 */

namespace AppBundle\EventListener;


use AppBundle\Exception\ApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;


class ControllerSubscriber implements EventSubscriberInterface
{
    private $kernel;
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage){
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelController(FilterControllerEvent $event){

        $request = $event->getRequest();
        $version = $request->headers->get('version');

        if(!$version && $event->getController()[1]!=='indexAction'){
            throw new ApiException('Veuillez mettre à jour neema, pour beneficier des nouvelles fonctionalités. Merci !',400,'info');
        }


        if(!$this->tokenStorage->getToken()) return;
        $user=$this->tokenStorage->getToken()->getUser();
        if($user instanceof UserInterface){
            if($user->getIsReseted() && $event->getController()[1]!=='changePasswordAction'){
                throw new ApiException('Vous devez changer votre mot de passe, après réinitialisation',400,'info');
            }
            if(!$user->getEnabled() && $event->getController()[1]!=='enabledAction' && $event->getController()[1]!=='sendBackActivationCodeAction'){
                throw new ApiException('Tapez le code réçu par sms, pour activer votre compte',400,'info');
            }
        }
    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }

}