<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 27/07/2016
 * Time: 11:47
 */

namespace AppBundle\Elastica\Repository;

use FOS\ElasticaBundle\Repository;

class PlatRepository extends Repository
{
    public function searchKey($key){

        if($key){
            $bool = new \Elastica\Query\BoolQuery();

            $bool->addShould(new \Elastica\Query\Match('nom',$key));
            $bool->addShould(new \Elastica\Query\Match('restaurant.nom',$key));
            $bool->addShould(new \Elastica\Query\Match('restaurant.quartier.nom',$key));
            $bool->addShould(new \Elastica\Query\Match('restaurant.quartier.commune.nom',$key));

            $query = \Elastica\Query::create($bool);
        }
        else{
            $match = new \Elastica\Query\MatchAll();

            $query = \Elastica\Query::create($match);
        }



        return $this->find($query);
    }
}