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

    private function exist($repository,$criteria){
        if(is_array($criteria)){
            $object = $this->em->getRepository($repository)->findOneBy($criteria);
        }else{
            $object = $this->em->getRepository($repository)->findOneBy(array('id'=>$criteria));
        }
        return $object;
    }

    public function getClassName($object){
        return strtolower(substr(get_class($object),strrpos(get_class($object),'\\')+1));
    }

    public function getClassNameInRepositoryName($repository){
        return ucfirst(substr($repository,strrpos($repository,':')+1));
    }


    public function fill(ParameterBag $parameterBag,$object){

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

        return MessageResponse::message('Enregistrement effectué','success',201,
            array($this->getClassName($object) =>$object));
    }

    public function all($repository){

        $objects = $this->em->getRepository($repository)->findAll();
        if(count($objects)===0){
            return MessageResponse::message('Aucune donnée','info',400);
        }
        return $objects;
    }

    public function getByCriteria($repository,array $criteria){
        $objects = $this->em->getRepository($repository)->findBy($criteria);

        if(!$objects){
            return MessageResponse::message('Aucune donnée','info',400);
        }
        return View::create($objects,200);
    }


    public function get($repository,$criteria){
        $object =$this->exist($repository,$criteria);
        if(!$object){
            return MessageResponse::message($this->getClassNameInRepositoryName($repository).' introuvable','info',404);
        }

        return $object;
    }

    public function put(Request $request,$repository,$criteria){
        $object = $this->get($repository,$criteria);

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

        return MessageResponse::message('Modification effectuée','success',200,
            array($this->getClassName($object) =>$this->em->getRepository($repository)->findById($object->getId())));

    }

    public function delete($repository,$criteria){
        $object = $this->get($repository,$criteria);

        //objet introuvable
        if($object instanceof View){
            return $object;
        }

        $this->em->remove($object);
        $this->em->flush();

        return MessageResponse::message('Suppression effectuée','success',200);

    }


}