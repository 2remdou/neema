<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 21/10/2016
 */

namespace AppBundle\Service;




use Doctrine\ORM\EntityManager;

class RestaurantService implements RestaurantServiceInterface
{

    private $em;

    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    private function getRepository(){
        return $this->em->getRepository('AppBundle:Restaurant');
    }

    /**
     * @param int $page
     * @return array Restaurant
     *
     */
    public function getList($page=1){
        $restaurants = $this->getRepository()->findAll($page);

        return $restaurants;
    }


}