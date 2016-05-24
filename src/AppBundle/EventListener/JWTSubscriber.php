<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 11/05/2016
 * Time: 19:23
 */

namespace AppBundle\EventListener;



use JMS\Serializer\Serializer;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTSubscriber implements EventSubscriberInterface
{
    private $serializer;

    public function __construct(Serializer $serializer){
        $this->serializer = $serializer;
    }

    public function onJWTCreated(JWTCreatedEvent $event){

        if (!($request = $event->getRequest())) {
            return;
        }
        $user = $event->getUser();
        $payload       = $event->getData();
        $payload['id'] = $user->getId();
        $payload['nom'] = $user->getNom();
        $payload['prenom'] = $user->getPrenom();
        $payload['roles'] = $user->getRoles();
        if($user->getUserRestaurant()){
            $payload['restaurant'] = array(
                                        'id'=>$user->getUserRestaurant()->getRestaurant()->getId(),
                                        'nom' => $user->getUserRestaurant()->getRestaurant()->getNom()
                                         ) ;
        }

        $event->setData($payload);
    }


    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event){
        dump($event);

        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['data'] = array(
            'roles' => $user->getRoles(),
        );

        $event->setData($data);

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
            Events::JWT_CREATED=>'onJWTCreated',
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccessResponse'
        );
    }
}