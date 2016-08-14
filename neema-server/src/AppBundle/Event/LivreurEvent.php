<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 22/05/2016
 * Time: 06:46
 */

namespace AppBundle\Event;


use AppBundle\Entity\Livreur;
use Symfony\Component\EventDispatcher\Event;

class LivreurEvent extends Event
{
    private $livreur;

    public function __construct(Livreur $livreur){
        $this->livreur = $livreur;
    }

    public function getLivreur(){
        return $this->livreur;
    }
}