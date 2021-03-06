<?php

namespace AppBundle\Repository;

/**
 * DetailCommandeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DetailCommandeRepository extends \Doctrine\ORM\EntityRepository
{
    private function minQueryBuilder(){
        $queryBuilder = $this->createQueryBuilder('d');
        return $queryBuilder;
    }
    private function mainQueryBuilder(){

        $queryBuilder = $this->minQueryBuilder()
            ->addSelect(['PARTIAL c.{id}','p','ip'])
            ->leftJoin('d.commande','c')
            ->leftJoin('d.plat','p')
            ->leftJoin('p.imagePlat','ip')
        ;

        return $queryBuilder;
    }

    public function findByCommandes(array $commandes){

        $commandes = $this->mainQueryBuilder()
//            ->leftJoin('c.user','u')
            ->where('c.id IN (:commandes)')
            ->setParameter(':commandes',$commandes)
            ->getQuery()
            ->getArrayResult();
        return $commandes;
    }

}
