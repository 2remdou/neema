<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 29/06/2016
 * Time: 08:34
 */

namespace AppBundle\EventListener;


use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\EventListener\SecurityListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;



class RestaurantIsAllowListener implements EventSubscriberInterface
{
    private $em;
    private $tokenStorage;
    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage = null)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        if (!$configuration = $request->attributes->get('_restaurant_is_allow')) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        dump($token->getUser());
/*
        $userRestaurant = $this->tokenStorage->getToken()->getUser()->getUserRestaurant();

        if (!$userRestaurant) {
            throw new AccessDeniedException("Votre restaurant ne peut modifier ce plat");
        }*/

    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::CONTROLLER => 'onKernelController');
    }






}