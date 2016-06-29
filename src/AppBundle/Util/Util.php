<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 25/06/2016
 * Time: 10:54
 */

namespace AppBundle\Util;


trait Util
{
    public function is_assoc($array) {
        if(!is_array($array)) return false;

        foreach (array_keys($array) as $k => $v) {
            if ($k !== $v)
                return true;
        }
        return false;
    }

    public function getClassName($object){
        return strtolower(substr(get_class($object),strrpos(get_class($object),'\\')+1));
    }


}