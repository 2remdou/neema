<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 05/10/2016
 */

namespace AppBundle\Service;



use OldSound\RabbitMqBundle\RabbitMq\Producer;

class RabbitMQManager
{
    private $producer;

    public function __construct(Producer $producer){
        $this->producer = $producer;
        $producer->setContentType('application/json');
    }

    public function publish($message,$queue){
        $this->producer->publish(json_encode($message),$queue);
    }
}