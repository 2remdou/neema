<?php

namespace AppBundle\Repository;

/**
 * RestaurantRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RestaurantRepository extends \Doctrine\ORM\EntityRepository
{
    private function getMainQuery(){
        $dql  = "SELECT r,q,c from AppBundle:Restaurant r
                  JOIN r.quartier q
                  JOIN q.commune c";


        $query = $this->getEntityManager()
            ->createQuery($dql);

        return $query;
    }

    private function mainQueryBuilder(){

        $queryBuilder = $this->createQueryBuilder('r')
            ->addSelect(['q','c','ir'])
            ->leftJoin('r.quartier','q')
            ->leftJoin('q.commune','c')
            ->leftJoin('r.imageRestaurants','ir');

        return $queryBuilder;
    }

    public function findAll(){

        $query = $this->mainQueryBuilder()
                ->getQuery();
        return $query->getArrayResult();
    }

    public function findById($id){

        $restaurant = $this->mainQueryBuilder()
            ->where('r.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getArrayResult();
        return count($restaurant)===1?$restaurant[0]:null;
    }


}
