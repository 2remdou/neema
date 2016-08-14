<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 29/06/2016
 * Time: 08:31
 */

namespace AppBundle\Annotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * Class RestaurantIsAllow
 * @package AppBundle\Annotation
 * @Annotation
 * @Target({"METHOD"})
 */
class RestaurantIsAllow extends ConfigurationAnnotation
{
    /**
     * Id du plat qu'on veut modifier
     * @var
     */
    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    public function getAliasName()
    {
        return 'restaurant_is_allow';
    }

    public function allowArray()
    {
        return false;
    }
}