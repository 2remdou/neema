<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 09/07/2016
 * Time: 08:37
 */

namespace AppBundle\Service;


use AppBundle\Entity\Commande;
use AppBundle\Entity\DetailCommande;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use FOS\RestBundle\Request\ParamFetcher;

interface CommandeManagerInterface
{

    public function create(Commande $commande,ParamFetcher $paramFetcher);
    /**
     * calcule la duree exacte du processus(temps au restaurant+temps de livraison)
     * durationExact=dateLivraison-dateCommande(en seconde)
     *
     * @param Commande $commande
     * @return mixed
     */

    public function calculDurationExact(Commande $commande);

    /**
     * Calcule la duree estimative du processus
     * durationEstimative = la somme des temps de prepations de chaque plat + le temps de livraison + un temps de majoration
     * @param Commande $commande
     * @return mixed
     */
    public function calculDurationEstimative(Commande $commande);

    /**
     * Retourne la commande la plus ancienne sans livreur
     * @return Commande
     */
    public function getCommandeWithoutLivreur();

    /**
     * Verifie si tous les plats de la commande ont été marqué comme terminé
     * @param Commande $commande
     * @return boolean
     */
    public function allPlatOnCommandeAreFinished(Commande $commande);

    /**
     * Pour marquer qu'une commande a été livré
     * @param Commande $commande
     * @return mixed
     */
    public function closeCommande(Commande $commande);

    /**
     * Pour marquer qu'un plat est prêt dans une commande
     * @param DetailCommande $detailCommande
     * @return mixed
     */
    public function closeDetailCommande(DetailCommande $detailCommande);

    /**
     * Changer l'etat d'une commande
     * @param Commande $commande
     * @param string $codeEtatCommande
     * @return mixed
     */
    public function changeEtatCommande(Commande $commande,$codeEtatCommande);

    /**
     * Retourne l'historique des commandes livrées à un client
     * @param User $user
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @param int $page
     * @return mixed
     */
    public function getHistoriqueByClient(User $user,\DateTime $from=null,\DateTime $to=null, $page=1);

    /**
     * Retourne les commandes encours d'un restaurant
     * @param Restaurant $restaurant
     * @param $page
     * @return mixed
     */
    public function getNotFinishedByRestaurant($page=1);
}