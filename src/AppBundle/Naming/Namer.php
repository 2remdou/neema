<?php
/**
 * Created by PhpStorm.
 * User: mdoutoure
 * Date: 30/10/2015
 * Time: 15:15
 */

namespace AppBundle\Naming;


use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Naming\UniqidNamer;

class Namer implements NamerInterface {

    /**
     * Creates a name for the file being uploaded.
     *
     * @param object $object The object the upload is attached to.
     * @param PropertyMapping $mapping The mapping to use to manipulate the given object.
     *
     * @return string The file name.
     */
    public function name($object, PropertyMapping $mapping)
    {
        $namer = new UniqidNamer();

        return $namer->name($object,$mapping);
        // TODO: Implement name() method.
    }
}