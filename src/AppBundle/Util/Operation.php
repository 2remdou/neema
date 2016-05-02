<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 01/05/2016
 * Time: 10:16
 */

namespace AppBundle\Util;


use AppBundle\MessageResponse\MessageResponse;
use AppBundle\Util\FillAttributes;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use  Symfony\Component\Validator\Validator\ValidatorInterface;

class Operation
{
    use FillAttributes;
    private $em;
    private $validator;
    private $entities;

    public function __construct(EntityManager $em,ValidatorInterface $validator){
        $this->em=$em;
        $this->validator=$validator;

        $this->entities=array(
                            'Commune'=>array(),
                            'Quartier'=>array(),
                            'Restaurant'=>array(),
                            'Menu'=>array(),
                            'Plat'=>array()
                        );
    }

    private function exist($repository,$id){
        $object = $this->em->getRepository($repository)->find($id);
        return $object;
    }

    private function fill(ParameterBag $parameterBag,$object){

        foreach($parameterBag->keys() as $attribute){
            $nameMethod='set'.ucfirst($attribute);

            //verifier que l'attribut est un entity
            if(array_key_exists(ucfirst($attribute),$this->entities)){
                $value = $this->exist('AppBundle:'.ucfirst($attribute),$parameterBag->get($attribute));
                if(!$value){
                    return MessageResponse::message(ucfirst($attribute).' introuvable','info',404);
                }
            }
            else{
                $value= $parameterBag->get($attribute);
            }

            $reflectionClass = new \ReflectionClass(get_class($object));
            if($reflectionClass->hasMethod($nameMethod)){
                $reflectionClass = new \ReflectionMethod(get_class($object),$nameMethod);
                $reflectionClass->invoke($object,$value);
            }
        }
        return $object;
    }


    public function post(Request $request,$object){

        $object=$this->fill($request->request,$object);

        if($object instanceof View){
            return $object;
        }

        if($messages = MessageResponse::messageAfterValidation($this->validator->validate($object))){
            return MessageResponse::message($messages,'danger',400);
        }

        $this->em->persist($object);
        $this->em->flush();

        return MessageResponse::message('Enregistrement effectué','success',201);
    }

    public function all($repository){
        $objects = $this->em->getRepository($repository)->findAll();

        if(!$objects){
            return MessageResponse::message('Aucune donnée','info',400);
        }

        return View::create($objects,200);

    }
    public function get($repository,$id){
        $object =$this->exist($repository,$id);
        if(!$object){
            return MessageResponse::message('introuvable','info',404);
        }

        return $object;
    }

    public function put(Request $request,$repository,$id){
        $object = $this->get($repository,$id);

        //objet introuvable
        if($object instanceof View){
            return $object;
        }

        $object = $this->fill($request->request,$object);

        if($object instanceof View){
            return $object;
        }

        if($messages = MessageResponse::messageAfterValidation($this->validator->validate($object))){
            return MessageResponse::message($messages,'danger',400);
        }

        $this->em->flush();

        return MessageResponse::message('Modification effectuée','success',200);

    }

    public function delete($repository,$id){
        $object = $this->get($repository,$id);

        //objet introuvable
        if($object instanceof View){
            return $object;
        }

        $this->em->remove($object);
        $this->em->flush();

        return MessageResponse::message('Suppression effectuée','success',200);

    }

}