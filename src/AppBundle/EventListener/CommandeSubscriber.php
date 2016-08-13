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
use AppBundle\NeemaEvents;
use AppBundle\Service\CommandeManager;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommandeSubscriber implements EventSubscriberInterface
{
    private $em;
    private $commandeManager;
    private $producer;

    public function __construct(EntityManager $em,CommandeManager $commandeManager,Producer $producer){
        $this->em = $em;
        $this->commandeManager = $commandeManager;
        $this->producer = $producer;
    }

    /**
     * @param CommandeEnregistreEvent $commandeEnregistreEvent
     * Crée une livraison en fonction de la commande passée dans $commandeEnregistreEvent
     * Selectiionne un livreur free
     *
     */
    public function onCommandeEnregistre(CommandeEnregistreEvent $commandeEnregistreEvent){
/*        $livraison = new Livraison();
        $livraison->setCommande($commandeEnregistreEvent->getCommande());

        $livreur = $this->em->getRepository('AppBundle:Livreur')->findFree();
        if($livreur){
            $livraison->setLivreur($livreur);
            $livreur->setIsFree(false);
        }

        $this->em->persist($livraison);
        $this->em->flush();*/
        null;
    }

    /**
     * Envoyer un message à rabbit
     * @param CommandeEnregistreEvent $commandeEnregistreEvent
     *
     * Declenché dans
     *  - CommandeController:putCloseDetailCommandeAction
     */
    public function onCommandePrete(CommandeEnregistreEvent $commandeEnregistreEvent){
        $commande = $commandeEnregistreEvent->getCommande();
        $deviceTokens = $commande->getUser()->getDeviceTokens();
        $this->producer->setContentType('application/json');

        foreach($deviceTokens as $deviceToken){
            $message = array('token'=>$deviceToken->getToken(),
                'message'=>$commande->getRestaurant()->getNom().' : '.'Votre commande est prête','commande'=>$commande->getId());
            $this->producer->publish(json_encode($message),$deviceToken->getOs());
        }
    }

    public function onCommandeLivre(CommandeEnregistreEvent $commandeEnregistreEvent){
        $commande = $commandeEnregistreEvent->getCommande();

        $etatCommande = $this->em->getRepository('AppBundle:EtatCommande')->findOneBy(array('code'=>'CL2'));

        $commande->setDelivered(true);
        $commande->setEtatCommande($etatCommande);

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
            NeemaEvents::COMMANDE_ENREGISTRE => 'onCommandeEnregistre',
            NeemaEvents::COMMANDE_LIVREE => 'onCommandeLivre',
            NeemaEvents::COMMANDE_PRETE => 'onCommandePrete',
        );
    }
}