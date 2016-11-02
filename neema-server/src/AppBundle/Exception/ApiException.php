<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 04/06/2016
 * Time: 15:16
 */

namespace AppBundle\Exception;


class ApiException extends \RuntimeException
{
    private $typeAlert;
    public function __construct($message = 'Access denied',$code = 400,$typeAlert = 'danger', \Exception $previous = null)
    {
        $this->typeAlert = $typeAlert;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getTypeAlert()
    {
        return $this->typeAlert;
    }

}