<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 24/06/2016
 * Time: 09:50
 */

namespace AppBundle\Service;


use AppBundle\Entity\Commande;

class DurationCommande implements DurationInterface
{
    private $commande;

    public function __construct(Commande $commande){
        $this->commande = $commande;
        $this->commande->setDurationEstimative($commande->getDurationLivraison());
    }

    /**
     * Returns le temps mis en seconde
     * @return float
     *
     */
    public function getDuration()
    {
        return $this->commande->getDurationEstimative();
    }

    /**
     * Ajoute une durée en seconde au temps total
     * @return float
     *
     * @param float $duration le temps à ajouter
     *
     */
    public function addDuration($duration)
    {
        $this->commande->setDurationEstimative($this->commande->getDurationEstimative()+$duration);
    }


}