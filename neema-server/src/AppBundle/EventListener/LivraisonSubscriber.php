<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 22/05/2016
 * Time: 06:52
 */

namespace AppBundle\EventListener;


use AppBundle\Entity\Livraison;
use AppBundle\Event\LivraisonEvent;
use AppBundle\NeemaEvents;
use AppBundle\Service\CommandeManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LivraisonSubscriber implements EventSubscriberInterface
{
    private $em;
    private $commandeManager;


    public function __construct(EntityManager $em,CommandeManager $commandeManager){
        $this->em = $em;
        $this->commandeManager = $commandeManager;

    }

    public function onLivraisonIsFinished(LivraisonEvent $livraisonEvent){
        $livraison = $livraisonEvent->getLivraison();

        $commande = $livraison->getCommande();
        $this->commandeManager->calculDurationExact($commande);
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
            NeemaEvents::LIVRAISON_IS_FINISHED => 'onLivraisonIsFinished',
        );
    }
}