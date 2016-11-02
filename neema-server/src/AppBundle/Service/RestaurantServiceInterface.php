<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 09/07/2016
 * Time: 08:37
 */

namespace AppBundle\Service;


interface RestaurantServiceInterface
{
    /**
     * @param int $page
     * @return array Restaurant
     *
     */
    public function getList($page=1);
}