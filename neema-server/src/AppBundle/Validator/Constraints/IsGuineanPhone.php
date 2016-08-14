<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 04/03/2016
 * Time: 06:13
 */

namespace AppBundle\Validator\Constraints;



use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsGuineanPhone extends Constraint
{
    public $message= 'Le numero "%telephone%" n\'est pas valide';
}