<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 10/05/2016
 * Time: 09:36
 */

namespace AppBundle\EventListener;


use AppBundle\Exception\ChangePasswordException;
use JMS\Serializer\Serializer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiExceptionSubscriber implements EventSubscriberInterface
{

    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage){
        $this->tokenStorage = $tokenStorage;
    }
    public function onKernelException(GetResponseForExceptionEvent $event){

        $e = $event->getException();

        if($e instanceof ChangePasswordException){
            $response = new JsonResponse($e->getMessage(),$e->getCode());
            dump($response);
            $event->setResponse($response);
        }

        if($e instanceof AccessDeniedHttpException){
            $response = new JsonResponse('Vous n\'êtes pas autorisé à effectuer cette opération',403);
            $event->setResponse($response);
        }


    }

    public function onKernelRequest(GetResponseEvent $event){

    }

    public function onKernelController(FilterControllerEvent $event){

        if(!$this->tokenStorage->getToken()) return;
        $user=$this->tokenStorage->getToken()->getUser();
        if($user instanceof UserInterface){
            if($user->getIsReseted() && $event->getController()[1]!=='changePasswordAction'){
                throw new ChangePasswordException('Vous devez changer votre mot de passe, après réinitialisation');
            }
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION=> 'onKernelException',
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::CONTROLLER => 'onKernelController'
        );
    }
}