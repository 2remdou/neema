<?php
/**
*   mdoutoure 01/05/2016
*/

namespace AppBundle\MessageResponse;

use FOS\RestBundle\View\View;

class MessageResponse {


    public static function message($textAlert='',$typeAlert='success',$codeStatus=200,$data=null){

        $message=array(
            'textAlert'=> $textAlert,
            'typeAlert'=> $typeAlert,
        );
        if($codeStatus>=400){
            return View::create(array_merge($message,is_array($data)?$data:array()),$codeStatus);
        }
        return View::create(array('data'=>array_merge($message,is_array($data)?$data:array())),$codeStatus);
    }

    public static function messageAfterValidation($errors){

        if(count($errors)>0){
            $message="";
            foreach($errors as $er){
                $message=$message.$er->getMessage().'<br>';
            }
            return $message;
        }
        return null;
    }

} 