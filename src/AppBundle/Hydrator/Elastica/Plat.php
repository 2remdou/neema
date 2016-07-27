<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 27/07/2016
 * Time: 16:10
 */

namespace AppBundle\Hydrator\Elastica;


use AppBundle\Entity\Commune;
use AppBundle\Entity\ImagePlat;
use AppBundle\Entity\Quartier;
use AppBundle\Entity\Restaurant;

class Plat
{

    public function hydrate(array $plats){
        $platsHydrate = array();
        foreach($plats as $plat){
            $platHydrate = new \AppBundle\Entity\Plat();
            $imagePlat = new ImagePlat();
            $restaurant  = new Restaurant();
            $quartier = new Quartier();
            $commune = new Commune();
            foreach($plat as $key => $value){
                if($key==='id')
                    $platHydrate->setId($value);
                if($key==='nom')
                    $platHydrate->setNom($value);
                if($key==='description')
                    $platHydrate->setDescription($value);
                if($key==='prix')
                    $platHydrate->setPrix($value);
                if($key==='imagePlat.imageName')
                    $imagePlat->setImageName($value);
                if($key==='imagePlat.webPath')
                    $imagePlat->setWebPath($value);
                if($key==='restaurant.id')
                    $restaurant->setId($value);
                if($key==='restaurant.nom')
                    $restaurant->setNom($value);
                if($key==='restaurant.telephone')
                    $restaurant->setTelephone($value);
                if($key==='restaurant.email')
                    $restaurant->setEmail($value);
                if($key==='restaurant.siteWeb')
                    $restaurant->setSiteWeb($value);
                if($key==='restaurant.description')
                    $restaurant->setDescription($value);
                if($key==='restaurant.quartier.id')
                    $quartier->setId($value);
                if($key==='restaurant.quartier.nom')
                    $quartier->setNom($value);
                if($key==='restaurant.quartier.commune.id')
                    $commune->setId($value);
                if($key==='restaurant.quartier.commune.nom')
                    $commune->setNom($value);

                $quartier->setCommune($commune);
                $restaurant->setQuartier($quartier);
                $platHydrate->setRestaurant($restaurant);
                $platHydrate->setImagePlat($imagePlat);
            }
            $platsHydrate[]=$platHydrate;
        }
        return $platsHydrate;
    }

}