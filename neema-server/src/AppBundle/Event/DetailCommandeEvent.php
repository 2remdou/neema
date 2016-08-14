<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 22/05/2016
 * Time: 06:46
 */

namespace AppBundle\Event;


use AppBundle\Entity\DetailCommande;
use Symfony\Component\EventDispatcher\Event;

class DetailCommandeEvent extends Event
{
    private $detailCommande;

    public function __construct(DetailCommande $detailCommande){
        $this->detailCommande = $detailCommande;
    }

    public function getDetailCommande(){
        return $this->detailCommande;
    }
}