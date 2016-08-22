<?php

namespace AppBundle\Repository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * CommandeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommandeRepository extends \Doctrine\ORM\EntityRepository
{
    use UtilForRepository;

    private function minQueryBuilder(){
        $queryBuilder = $this->createQueryBuilder('c');
        return $queryBuilder;
    }
    private function mainQueryBuilder(){

        $queryBuilder = $this->minQueryBuilder()
            ->addSelect(['r','ir','d','p','ip','e','PARTIAL u.{id,username,nom,prenom}'])
            ->leftJoin('c.restaurant','r')
            ->leftJoin('r.imageRestaurants','ir')
            ->leftJoin('c.detailCommandes','d')
            ->leftJoin('d.plat','p')
            ->leftJoin('p.imagePlat','ip')
            ->leftJoin('c.etatCommande','e')
            ->leftJoin('c.user','u')
        ;

        return $queryBuilder;
    }
    public function findAll(){

       return $this->minQueryBuilder()
           ->getQuery()
           ->getArrayResult();
    }

    public function getDureeRestant($idCommande,$tempsEcoule,$majorationTimeLivraison=0){

//        $dql = "SELECT sum(CASE WHEN p.dureePreparation<0 THEN 0 ELSE p.dureePreparation)+c.durationLivraison+600
        $dql = "SELECT sum(CASE WHEN p.dureePreparation-:tempsEcoule<=0 THEN 0 ELSE p.dureePreparation-:tempsEcoule END)
                FROM AppBundle:Commande c
                LEFT JOIN c.detailCommandes dc
                JOIN dc.plat p
                WHERE c.id=:idCommande
                AND dc.finished=false";

        $duree= $this->getEntityManager()
            ->createQuery($dql)
            ->setParameters(array('idCommande'=>$idCommande,'tempsEcoule'=>$tempsEcoule))
            ->getSingleScalarResult();

        return $duree===null?0:$duree;

    }

    public function findByTypeDelivered($delivered=false,$idRestaurant=''){

        return $this->mainQueryBuilder()
            ->where('r.id LIKE :idRestaurant')
            ->andWhere('c.delivered=:delivered')
            ->setParameters(array(
                'idRestaurant'=>$idRestaurant,
                'delivered'=>$delivered))
            ->orderBy('c.dateCommande','DESC')
            ->getQuery()
            ->getArrayResult();
    }

    public function findByUser($idUser){

        $commandes = $this->mainQueryBuilder()
//            ->leftJoin('c.user','u')
            ->where('u.id LIKE :idUser')
            ->andWhere('c.delivered=false')
            ->setParameter('idUser',$idUser)
            ->orderBy('c.dateCommande','DESC')
            ->getQuery()
            ->getArrayResult();
        return $commandes;
    }

    public function refreshMenu($idRestaurant = null,\DateTime $from,\DateTime $to){

        return $this->mainQueryBuilder()
            ->where('r.id LIKE :idRestaurant')
            ->andWhere('c.delivered=false')
            ->andWhere('c.dateCommande BETWEEN :from and :to')
            ->setParameters(array(
                'idRestaurant'=>$idRestaurant,
                'from'=>$from,
                'to'=>$to))
            ->orderBy('c.dateCommande','DESC')
            ->getQuery()
            ->getArrayResult();
    }

    public function findById($id){

        $commande = $this->mainQueryBuilder()
            ->where('c.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getArrayResult();
        return count($commande)===1?$commande[0]:null;
    }

    /**
     * Verifie si tous les plats dans la commande sont marqués terminés,
     * avant que le restaurant ne remette au livreur
     * @param $idCommande
     * @return bool
     */
    public function allDetailIsFinished($idCommande){
        $details = $this->minQueryBuilder()
            ->leftJoin('c.detailCommandes','dc')
            ->where('c.id=:idCommande')
            ->andWhere('dc.finished=false')
            ->setParameter('idCommande',$idCommande)
            ->getQuery()
            ->getArrayResult();
        return count($details)===0?true:false;

    }

    /**
     * Trouver une commande sans livreur
     * @return Commande
     */
    public function findCommandeWithoutLivreur(){
        $commandes = $this->minQueryBuilder()
                    ->leftJoin('c.livraison','livraison')
                    ->where($this->createQueryBuilder('c')->expr()->isNull('livraison.livreur'))
                    ->orderBy('c.dateCommande','DESC')
                    ->getQuery()
                    ->getResult();

        return count($commandes)!==0?$commandes[0]:null;

    }

}