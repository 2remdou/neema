<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 22/05/2016
 * Time: 06:52
 */

namespace AppBundle\EventListener;


use AppBundle\Exception\ApiException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    private $kernel;

    public function __construct(Kernel $kernel){
        $this->kernel = $kernel;
    }
    public function onKernelRequest(GetResponseEvent $event){
        $request = $event->getRequest();
        $version = $request->headers->get('version');
        if(!$version && $this->kernel->getEnvironment()==="prod"){
            throw new ApiException('Veuillez mettre à jour neema, pour beneficier des nouvelles fonctionalités. Merci !',400,'info');
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
        );
    }

}