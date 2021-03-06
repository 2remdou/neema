<?php

namespace AppBundle\Repository;

/**
 * LivreurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LivreurRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function mainQueryBuilder(){
        $queryBuilder = $this->createQueryBuilder('l')
            ->addSelect(['PARTIAL u.{id,nom,prenom,telephone}'])
            ->leftJoin('l.user','u')
        ;

        return $queryBuilder;
    }

    /**
     * @return array
     */
    public function findAll(){
        return $this->mainQueryBuilder()
            ->getQuery()
            ->getArrayResult();
    }

    public function findFree(){

        return  $this->mainQueryBuilder()
            ->where('l.isFree=true')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
