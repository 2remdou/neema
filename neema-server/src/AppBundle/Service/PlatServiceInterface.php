<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 09/07/2016
 * Time: 08:37
 */

namespace AppBundle\Service;


use AppBundle\Entity\Restaurant;

interface PlatServiceInterface
{
    /**
     * @param int $page
     * @return array Plat $plats
     *
     */
    public function getPlatOnMenu($page=1);


    }