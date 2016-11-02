<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 09/07/2016
 * Time: 08:37
 */

namespace AppBundle\Service;


interface UtilServiceInterface
{
    /**
     * Pour attacher une entity à une autre
     * @param $object
     * @param string $nameEntityAAttacher
     * @param array $criteria
     *
     * @return mixed
     */
    public function attach($object, $nameEntityAAttacher, array $criteria);
}