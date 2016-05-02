<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 01/05/2016
 * Time: 09:05
 */

namespace AppBundle\Util;


use Symfony\Component\HttpFoundation\ParameterBag;

trait FillAttributes
{

    private function fill(ParameterBag $parameterBag,$object){

        foreach($parameterBag->keys() as $attribute){
            $nameMethod='set'.ucfirst($attribute);
            $reflectionClass = new \ReflectionClass(get_class($object));
            if($reflectionClass->hasMethod($nameMethod)){
                $reflectionClass = new \ReflectionMethod(get_class($object),$nameMethod);
                $reflectionClass->invoke($object,$parameterBag->get($attribute));
            }
        }
        return $object;
    }

}