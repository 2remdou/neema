<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 10/05/2016
 * Time: 21:12
 */

namespace AppBundle\Exception;


use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class ChangePasswordException extends \RuntimeException
{
    public function __construct($message = 'Access denied', \Exception $previous = null)
    {
        parent::__construct($message, 409, $previous);
    }

}