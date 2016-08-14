<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 23/06/2016
 * Time: 20:49
 */

namespace AppBundle\Validator;


trait Validator
{
    public function validatePhoneNumber($number){
        return preg_match('#^((\+)?(0{2})?224)?6[2356][\d]{7}$#', preg_replace('#[.\- ]#','', $number)) && strlen($number)!==0;
    }

}