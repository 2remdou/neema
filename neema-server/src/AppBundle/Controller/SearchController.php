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

}