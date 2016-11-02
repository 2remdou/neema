<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 09/07/2016
 * Time: 08:37
 */

namespace AppBundle\Service;


use AppBundle\Entity\Commande;
use AppBundle\Entity\Livraison;
use AppBundle\Entity\Livreur;

interface LivraisonServiceInterface
{
    /**
     * Pour creer une livraison
     * @param Commande $commande
     * @param Livreur $livreur
     * @return Livraison
     */
    public function create(Commande $commande);
    /**
     * Marque une livraison commme finished
     * @param Livraison $livraison
     * @return mixed
     */
    public function livrer(Livraison $livraison);

    /**
     * Retourne la livraison active en fonction d'un livreur
     * @param Livreur $livreur
     * @return Livraison
     */
    public function getLivraionEncours(Livreur $livreur);

    /**
     * @param Commande $commande
     * @return mixed
     */
    public function getLivraionByCommande(Commande $commande);

    /**
     * Declanche le debut de la livraison,
     * en fournissant une valeur à dateDebutLivraison
     * @Param Livraison $livraison
     * @return mixed
     */
    public function startLivraison(Livraison $livraison);

    /**
     * Verifie s'il existe une livraison sans livreur,
     * et associe cette livraison au livreur passé en parametre
     * @param Livreur $livreur
     * @param Commande $commande
     * @return mixed
     */
    public function attachLivreurOnLivraisonWithoutLivreur(Livreur $livreur,Commande $commande=null);

    /**
     * @param Livreur $livreur
     * @return array Livreur
     */
    public function getHistorique(Livreur $livreur);

}