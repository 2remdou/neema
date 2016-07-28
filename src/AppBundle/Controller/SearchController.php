<?php
/**
*	mdoutoure 01/05/2016	
*/

namespace AppBundle\Controller;

use AppBundle\Hydrator\Elastica\Plat;
use Elastica\Aggregation\Terms;
use FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Request\ParamFetcher,
    FOS\RestBundle\Controller\Annotations\RequestParam,
    FOS\RestBundle\View\View,
    FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SearchController extends FOSRestController
{
	/**
     * Faire une recherche en fonction d'une key
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Faire une recherche en fonction d'une key",
     *   statusCodes = {
     *     404 = "Not Found",
     *   }
     * )
	 * @Route("api/search",name="post_restaurant", options={"expose"=true})
     * @Method({"GET"})
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
	public function searchKeyAction(Request $request){
        $key = $request->query->get('key');


        if($key){
            $boolPlat = new \Elastica\Query\BoolQuery();

            $boolRestaurant = new \Elastica\Query\BoolQuery();
            $boolQuartier = new \Elastica\Query\BoolQuery();
            $boolCommune = new \Elastica\Query\BoolQuery();

            $boolRestaurant->addShould(new \Elastica\Query\Match('restaurant.nom',$key));
            $boolQuartier->addShould(new \Elastica\Query\Match('restaurant.quartier.nom',$key));
            $boolCommune->addShould(new \Elastica\Query\Match('restaurant.quartier.commune.nom',$key));

            $nestedRestaurant = new \Elastica\Query\Nested();
            $nestedRestaurant->setPath('restaurant');
            $nestedRestaurant->setQuery($boolRestaurant);

            $nestedQuartier = new \Elastica\Query\Nested();
            $nestedQuartier->setPath('restaurant.quartier');
            $nestedQuartier->setQuery($boolQuartier);
            $boolRestaurant->addShould($nestedQuartier);

            $nestedCommune = new \Elastica\Query\Nested();
            $nestedCommune->setPath('restaurant.quartier.commune');
            $nestedCommune->setQuery($boolCommune);
            $boolQuartier->addShould($nestedCommune);


            $boolPlat->addShould(new \Elastica\Query\Match('nom',$key));
            $boolPlat->addShould($nestedRestaurant);

            $query = \Elastica\Query::create($boolPlat);
        }
        else{
            $match = new \Elastica\Query\MatchAll();

            $query = \Elastica\Query::create($match);
        }

        $type = $this->get('fos_elastica.index.neema.plat');
        $platsElastica = $type->search($query);
        $plats = array();
        foreach($platsElastica->getResults() as $plat){
            $plats[] = $plat->getData();
        }
        return array('plats'=>$plats);



    }
}