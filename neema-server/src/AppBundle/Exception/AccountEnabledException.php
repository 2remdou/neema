<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 04/06/2016
 * Time: 15:16
 */

namespace AppBundle\Exception;


class AccountEnabledException extends \RuntimeException
{
    public function __construct($message = 'Access denied', \Exception $previous = null)
    {
        parent::__construct($message, 409, $previous);
    }

}