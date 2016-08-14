<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 22/05/2016
 * Time: 06:46
 */

namespace AppBundle\Event;


use AppBundle\Entity\Commande;
use Symfony\Component\EventDispatcher\Event;

class CommandeEnregistreEvent extends Event
{
    private $commande;

    public function __construct(Commande $commande){
        $this->commande = $commande;
    }

    public function getCommande(){
        return $this->commande;
    }
}