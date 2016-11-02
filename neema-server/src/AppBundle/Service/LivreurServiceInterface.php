<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 09/07/2016
 * Time: 08:37
 */

namespace AppBundle\Service;


use AppBundle\Entity\Commande;
use AppBundle\Entity\Livreur;
use AppBundle\Entity\User;

interface LivreurServiceInterface
{
    /**
     * Retourne un livreur disponible
     * @return Livreur
     */
    public function getFreeLivreur();

    /**
     * Retourne le livreur correspondant à l'utilisateur passé en parametre
     * @param User $user
     * @return Livreur
     */
    public function getLivreurByUser(User $user);




}