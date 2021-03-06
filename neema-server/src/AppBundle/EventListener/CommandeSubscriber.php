<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 22/05/2016
 * Time: 06:52
 */

namespace AppBundle\EventListener;


use AppBundle\Entity\Livraison;
use AppBundle\Entity\Notification;
use AppBundle\Event\CommandeEnregistreEvent;
use AppBundle\NeemaEvents;
use AppBundle\Service\CommandeManager;
use AppBundle\Service\LivraisonServiceInterface;
use AppBundle\Service\LivreurServiceInterface;
use AppBundle\Service\RabbitMQService;
use AppBundle\Util\Util;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommandeSubscriber implements EventSubscriberInterface
{
    use Util;
    private $em;
    private $commandeManager;
    private $rabbitMQService;
    private $livreurService;
    private $livraisonService;


    public function __construct(EntityManager $em,CommandeManager $commandeManager,RabbitMQService $rabbitMQService,
                                LivreurServiceInterface $livreurService,LivraisonServiceInterface $livraisonService){
        $this->em = $em;
        $this->commandeManager = $commandeManager;
        $this->rabbitMQService = $rabbitMQService;
        $this->livreurService = $livreurService;
        $this->livraisonService = $livraisonService;
    }

    /**
     * @param CommandeEnregistreEvent $commandeEnregistreEvent
     * Crée une livraison en fonction de la commande passée dans $commandeEnregistreEvent
     * Selectiionne un livreur free
     *
     */
    public function onCommandeEnregistre(CommandeEnregistreEvent $commandeEnregistreEvent){

        $commande = $commandeEnregistreEvent->getCommande();

        $message = array('event'=>NeemaEvents::COMMANDE_ENREGISTRE,
            'commande'=>$commande->getId(),
            'user' => array(
                'telephone'=>$commande->getUser()->getTelephone()
            ),
            'restaurant' => array(
                'id'=>$commande->getRestaurant()->getId(),
                'nom'=>$commande->getRestaurant()->getNom()
            ),
                'dateMessage'=> new \DateTime()
        );
        $this->rabbitMQService->publish(json_encode($message),'commande.enregistre');

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
        $telephone = $commande->getTelephone();

        if($commande->getALivrer()){
            $contentMessage = 'La commande que vous avez passé au restaurant '.$commande->getRestaurant()->getNom().' est prête.
Adresse Livraison: '. $commande->getLieuLivraison()->getNom().'.
Merci pour votre confiance';
        }else{
            $contentMessage = 'La commande que vous avez passé au restaurant '.$commande->getRestaurant()->getNom().' est prête.
Merci pour votre confiance';
        }

        $notification = new Notification();
        $notification->setTitle('Commande prête');
        $notification->setType('commande');
        $notification->setIdType($commande->getId());
        $notification->setMessage($contentMessage);
        $notification->setUser($commande->getUser());

        $this->em->persist($notification);

        // notification par sms

        $message = array(
            'telephone' => $this->addCountryCodeInPhoneNumber($telephone),
            'content'=>$notification->getMessage(),
            'commande'=>$commande->getId(),
            'dateMessage'=> new \DateTime(),
        );

        $this->rabbitMQService->publish(json_encode($message),'notification.sms');

        //notification par push

        foreach($deviceTokens as $deviceToken){
            $message = array('token'=>$deviceToken->getToken(),
                'content'=>$contentMessage,
                'dateMessage'=> new \DateTime(),
            );
            $this->rabbitMQService->publish(json_encode($message),'notification.'.$deviceToken->getOs());

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