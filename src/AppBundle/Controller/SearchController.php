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

/*        $elasticManager = $this->get('fos_elastica.manager');

        $plats = $elasticManager->getRepository('AppBundle:Plat')->searchKey($key);

        dump($plats);

        return array();*/

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

        $type = $this->get('fos_elastica.index.neema.plat');
        $platsElastica = $type->search($query);
        $plats = array();
        foreach($platsElastica->getResults() as $plat){
            $plats[] = $plat->getData();
        }
        $platHydrate = new Plat();
        $plats = $platHydrate->hydrate($plats);
        return array('plats'=>$plats);



    }
}