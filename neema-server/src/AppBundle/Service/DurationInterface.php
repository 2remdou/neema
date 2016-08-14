<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 24/06/2016
 * Time: 09:50
 */

namespace AppBundle\Service;


interface DurationInterface
{
    /**
     * Returns le temps mis en seconde
     * @return float
     *
     */
    public function getDuration();

    /**
     * Ajoute une durée en seconde au temps total
     * @return float
     *
     * @param float $duration le temps à ajouter
     *
     */

    public function addDuration($duration);
}