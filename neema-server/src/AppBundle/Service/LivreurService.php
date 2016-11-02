<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 21/10/2016
 */

namespace AppBundle\Service;


use AppBundle\Entity\Commande;
use AppBundle\Entity\Livreur;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;


class LivreurService implements LivreurServiceInterface
{

    private $em;
    private $commandeManager;
    private $livraisonService;

    public function __construct(EntityManager $em,CommandeManagerInterface $commandeManager){
        $this->em = $em;
        $this->commandeManager = $commandeManager;
    }
    /**
     * Retourne un livreur disponible
     * @return Livreur
     */
    public function getFreeLivreur()
    {
        $livreur = $this->em->getRepository('AppBundle:Livreur')->findFree();

        return $livreur;
    }

    /**
     * Retourne le livreur correspondant à l'utilisateur passé en parametre
     * @param User $user
     * @return Livreur
     */
    public function getLivreurByUser(User $user)
    {
        $livreur = $this->em->getRepository('AppBundle:Livreur')->findOneBy(array('user'=>$user));
        return $livreur;
    }


}