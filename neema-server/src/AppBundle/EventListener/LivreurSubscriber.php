<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 22/05/2016
 * Time: 06:52
 */

namespace AppBundle\EventListener;


use AppBundle\Entity\Livraison;
use AppBundle\Event\CommandeEnregistreEvent;
use AppBundle\Event\LivreurEvent;
use AppBundle\NeemaEvents;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LivreurSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    public function onLivreurIsFree(LivreurEvent $livreurEvent){
        $livreur = $livreurEvent->getLivreur();

        $livreur->setIsFree(true);

        //Affecter au livreur qui vient de se liberer une commande sans livreur
        $commande = $this->em->getRepository('AppBundle:Commande')->findCommandeWithoutLivreur();
        if($commande){
            $commande->getLivraison()->setLivreur($livreur);
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
            NeemaEvents::LIVREUR_IS_FREE => 'onLivreurIsFree',
        );
    }
}