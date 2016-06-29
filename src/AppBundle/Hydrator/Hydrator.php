<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 24/06/2016
 * Time: 13:21
 */

namespace AppBundle\Hydrator;


use AppBundle\Util\Util;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Tests\Compiler\A;

trait Hydrator
{
    use Util;

    /**
     * @param array $row
     */
    public function fetchForHydrate(array $row){
        foreach($row as $key=>$value){
            if(is_array($value) && !$this->is_assoc($value)){
                foreach($value as $v){
                    $this->hydrate($key,$v);
                }
            }else{
                $this->hydrate($key,$value);
            }

        }
    }

    public function hydrate($key,$value){
        if(is_array($value)){
            //si la classe est au pluriel, supprimer le s
            $key = ucfirst(preg_replace('/s$/','${1}',$key));
            $class ="AppBundle\\Entity\\". $key;

            if(!class_exists($class)) return;

            $object = new $class();

            $object->fetchForHydrate($value);

            $value = $object;
        }

        $this->fill($key,$value);
    }


    public function fill($key,$value){
        $reflectionClass = new \ReflectionClass(get_class($this));

        $nameMethod=null;
        if($reflectionClass->hasMethod('set'.ucfirst($key))){
            $nameMethod='set'.ucfirst($key);
        }elseif($reflectionClass->hasMethod('add'.ucfirst($key))){
            $nameMethod='add'.ucfirst($key);
        }

        $reflectionClass = new \ReflectionClass(get_class($this));

        if($reflectionClass->hasMethod($nameMethod)){
            $reflectionMethod = new \ReflectionMethod(get_class($this),$nameMethod);
            $reflectionMethod->invoke($this,$value);
        }

    }

}