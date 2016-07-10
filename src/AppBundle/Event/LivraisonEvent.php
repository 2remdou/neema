<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 22/05/2016
 * Time: 06:46
 */

namespace AppBundle\Event;


use AppBundle\Entity\Livraison;
use Symfony\Component\EventDispatcher\Event;

class LivraisonEvent extends Event
{
    private $livraison;

    public function __construct(Livraison $livraison){
        $this->livraison = $livraison;
    }

    public function getLivraison(){
        return $this->livraison;
    }
}