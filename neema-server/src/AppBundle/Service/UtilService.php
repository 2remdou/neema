<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 25/10/2016
 */

namespace AppBundle\Service;



use AppBundle\Exception\ApiException;
use AppBundle\Util\Util;
use Doctrine\ORM\EntityManager;

class UtilService implements UtilServiceInterface
{
    use Util;
    private $em;

    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    /**
     * Pour attacher une entity à une autre
     * @param $object
     * @param string $nameEntityAAttacher
     * @param array $criteria
     * @return mixed
     */
    public function attach($object, $nameEntityAAttacher, array $criteria)
    {
        $nameEntityAAttacher = ucfirst($nameEntityAAttacher);
        $objectAAttacher = $this->em->getRepository('AppBundle:'.ucfirst($nameEntityAAttacher))->findOneBy($criteria);

        if(!$objectAAttacher){
            throw new ApiException($nameEntityAAttacher.' introuvable');
        }

        $reflectionClass = new \ReflectionClass(get_class($object));
        $nameMethod = 'set'.$nameEntityAAttacher;
        if($reflectionClass->hasMethod($nameMethod)){
            $reflectionClass = new \ReflectionMethod(get_class($object),$nameMethod);
            $reflectionClass->invoke($object,$objectAAttacher);
        }
        return $object;


    }


}