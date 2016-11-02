<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 21/10/2016
 */

namespace AppBundle\Service;




use AppBundle\Entity\Restaurant;
use Doctrine\ORM\EntityManager;

class PlatService implements PlatServiceInterface
{

    private $em;

    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    private function getRepository(){
        return $this->em->getRepository('AppBundle:Plat');
    }

    /**
     * @param int $page
     * @return array Plat $plats
     *
     */
    public function getPlatOnMenu($page=1){
        $plats = $this->getRepository()->findOnMenu($page);

        return $plats;
    }

    public function getPlatByRestaurant(Restaurant $restaurant,$page=1){

        $plats = $this->getRepository()->findOnMenuByRestaurant($restaurant->getId());

        return $plats;
    }


}