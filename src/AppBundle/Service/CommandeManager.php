<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 09/07/2016
 * Time: 08:37
 */

namespace AppBundle\Service;


use AppBundle\Entity\Commande;

class CommandeManager implements CommandeManagerInterface
{

    private $majorationTimeLivraison;

    public function __construct($majorationTimeLivraison){
        $this->majorationTimeLivraison = $majorationTimeLivraison;
    }
    /**
     * calcule la duree exacte du processus(temps au restaurant+temps de livraison)
     * durationExact=dateLivraison-dateCommande(en seconde)
     *
     * @param Commande $commande
     * @return mixed
     */
    public function calculDurationExact(Commande $commande)
    {
        $tempsEcoule = $commande->getLivraison()->getDateLivraison()->getTimestamp()-$commande->getDateCommande()->getTimestamp();
        $commande->setDurationExact($tempsEcoule);
    }

    /**
     * Calcule la duree estimative du processus
     * durationEstimative = la somme des temps de prepations de chaque plat + le temps de livraison + un temps de majoration
     * @param Commande $commande
     * @return mixed
     */
    public function calculDurationEstimative(Commande $commande)
    {
        $durationEstimative = $commande->getDurationLivraison()+$this->majorationTimeLivraison;

        foreach($commande->getDetailCommandes() as $detailCommande){
            $durationEstimative += $detailCommande->getPlat()->getDureePreparation();
        }

        $commande->setDurationEstimative($durationEstimative);
    }
}