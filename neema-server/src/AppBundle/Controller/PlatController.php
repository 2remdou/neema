<?php
/**
*	mdoutoure 01/05/2016	
*/

namespace AppBundle\Controller;

use AppBundle\Entity\ImagePlat;
use AppBundle\Entity\Restaurant;
use AppBundle\Exception\ApiException;
use AppBundle\Util\FillAttributes;
use Elastica\Aggregation\Terms;
use FOS\RestBundle\Controller\FOSRestController,
	FOS\RestBundle\Request\ParamFetcher,
	FOS\RestBundle\Controller\Annotations\RequestParam,
	FOS\RestBundle\View\View,
	FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\MessageResponse\MessageResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request,
	Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\Plat;

/**
 * Class PlatController
 * @package AppBundle\Controller
 */
class PlatController extends FOSRestController
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
     * @Route("api/plats/search",name="search_restaurant", options={"expose"=true})
     * @Method({"GET"})
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
        if(count($platsElastica->getResults())===0){
            throw new ApiException("Aucun resultat",404,'info');
        }
        $plats = array();
        foreach($platsElastica->getResults() as $plat){
            $plats[] = $plat->getData();
        }

        return array('plats'=>$plats);
    }
	/**
     * Ajouter un plat
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un plat",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="nom du plat")
     * @RequestParam(name="description",nullable=false, description="description du plat")
     * @RequestParam(name="prix",nullable=false, description="prix du plat")
     * @RequestParam(name="dureePreparation",nullable=false, description="la durée de preparation du plat")
     * @RequestParam(name="restaurant",nullable=false, description="id du restaurant")
     * @Route("api/plats",name="post_plat", options={"expose"=true})
     * @Method({"POST"})
	 * @Security("has_role('ROLE_RESTAURANT')")
     */
	public function postPlatAction(Request $request,ParamFetcher $paramFetcher){
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            return MessageResponse::message('Un administrateur ne peut creer de plat','danger',400);
        }

        $em = $this->getDoctrine()->getManager();
        $operation = $this->get('app.operation');

        $plat = new Plat();
        $plat = $operation->fill($request->request,$plat);
        if($plat instanceof View){
            return $plat;
        }
        $userRestaurant = $em->getRepository('AppBundle:UserRestaurant')->findOneBy(array('user'=>$this->getUser()->getId()));
        if(!$userRestaurant){
            return MessageResponse::message('Cet utilisateur n\'est lié à aucun restaurant','danger',400);
        }
        $restaurant = $userRestaurant->getRestaurant();
        $plat->setRestaurant($restaurant);
        $validator = $this->get('validator');

        if($messages = MessageResponse::messageAfterValidation($validator->validate($plat))){
            return MessageResponse::message($messages,'danger',400);
        }

        $em->persist($plat);
        $em->flush();

        return MessageResponse::message('Enregistrement effectué','success',201, array('idPlat' =>$plat->getId()));


    }

    /**
     * Ajouter une image à un plat
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter une image à un plat",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @Route("api/plats/{id}/image",name="post_image_plat", options={"expose"=true})
     * @ParamConverter("plat", class="AppBundle:Plat")
     * @Method({"POST"})
	 * @Security("has_role('ROLE_RESTAURANT')")
	 */
    public function postPlatImageAction(Plat $plat,Request $request){
        $em = $this->getDoctrine()->getManager();

        try{
            $em->getConnection()->beginTransaction();
            if($plat->getImagePlat()){ //s'il existe une autre image, je le supprime
                $em->remove($plat->getImagePlat());
                $em->flush();
            }

            $file = $request->files->get('file');
            if(!$file){
                return MessageResponse::message('Image introuvable','danger',400);
            }
            $image = new ImagePlat();
            $image->setWebPath($this->getParameter('urlimages').'/plats');
            $image->setImageFile($file);
            $image->setPlat($plat);

            $em->persist($image);
            $em->flush();
            $em->getConnection()->commit();
            return MessageResponse::message('Enregistrement effectué avec succès','success',200);

        }catch (Exception $e){
            $em->getConnection()->rollBack();
            throw $e;
        }

    }


    /**
     * Lister les plats
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les plats",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/plats",name="get_plats", options={"expose"=true})
     * @Method({"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

	public function getPlatsAction(){
		$operation = $this->get('app.operation');
        return array('plats'=>$operation->all('AppBundle:Plat'));
	}
    /**
     * Lister les plats sur des menus
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les plats sur des menus",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/plats/on-menu",name="get_plats_onmenu", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getPlatsOnMenuAction(Request $request){
        $platService = $this->get('app.plat.service');

        $page = $request->query->getInt('page', 1);
        $page = $page<=0?1:$page;

        $plats = $platService->getPlatOnMenu($page);

        if(!$plats){
            $paginator = array('currentPage'=>$page,'nextPage'=>$page);
        }else{
            $paginator = array('currentPage'=>$page,'nextPage'=>$page+1);
        }

        return array('plats'=>$plats,'paginator'=>$paginator);
	}

    /**
     * Lister les plats par restaurant en fonction du user connecté
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les plats par restaurant en fonction du user connecté",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/plats/restaurant/userConnected",name="get_plats_restaurant_user", options={"expose"=true})
     * @Method({"GET"})
     * @Security("has_role('ROLE_RESTAURANT')")
     */

    public function getPlatsByRestaurantByUserAction(){
        $em = $this->getDoctrine()->getManager();

        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $operation = $this->get('app.operation');
            return $operation->all('AppBundle:Plat');
        }
        $userRestaurant = $em->getRepository('AppBundle:UserRestaurant')->findOneBy(array('user'=>$this->getUser()->getId()));
        if(!$userRestaurant){
            return MessageResponse::message('Cet utilisateur n\'est lié à aucun restaurant','danger',400);
        }
        $restaurant = $userRestaurant->getRestaurant();
        if(!$restaurant){
            return MessageResponse::message('Restaurant introuvable','info',400);
        }
        $operation = $this->get('app.operation');
        return $em->getRepository('AppBundle:Plat')->findByRestaurant($restaurant->getId());
    }


    /**
     * Lister les plats par restaurant
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les plats par restaurant",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/plats/restaurant/{restaurant}",name="get_plats_restaurant", options={"expose"=true})
     * @Method({"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

    public function getPlatsByRestaurantAction($restaurant){
        $em = $this->getDoctrine()->getManager();
        $restaurant = $em->getRepository('AppBundle:Restaurant')->findOneBy(array('id'=>$restaurant));
        if(!$restaurant){
            return MessageResponse::message('Restaurant introuvable','info',400);
        }
        return $em->getRepository('AppBundle:Plat')->findByRestaurantOnMenu($restaurant->getId());
    }

    /**
     * Lister les plats par restaurant avec un paginator
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les plats par restaurant avec un paginator",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/plats/by-restaurant/{id}",name="get_plats_on_menu_by_restaurant", options={"expose"=true})
     * @ParamConverter("restaurant", class="AppBundle:Restaurant")
     * @Method({"GET"})
     */

    public function getPlatsOnMenuByRestaurantAction(Restaurant $restaurant,Request $request){

        $platService = $this->get('app.plat.service');

        $page = $request->query->getInt('page', 1);
        $page = $page<=0?1:$page;

        $plats = $platService->getPlatByRestaurant($restaurant,$page);

        if(!$plats && $page==1) throw new ApiException('Aucun plat retrouvé',404,'info');

        if(!$plats){
            $paginator = array('currentPage'=>$page,'nextPage'=>$page);
        }else{
            $paginator = array('currentPage'=>$page,'nextPage'=>$page+1);
        }

        return array('plats'=>$plats,'paginator'=>$paginator);

    }


    /**
     * retourner un plat
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner un plat",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/plats/{id}",name="get_plat", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getPlatAction($id){
        $em = $this->getDoctrine()->getManager();
        $plat=$em->getRepository('AppBundle:Plat')->findById($id);
        if(!$plat){
            return MessageResponse::message('Plat introuvable','danger',400);
        }

        return $plat;


    }

	/**
     * Modifier un plat
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier un plat",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="nom",nullable=false, description="nom du plat")
     * @Route("api/plats/{id}",name="put_plat", options={"expose"=true})
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_RESTAURANT')")
	 */
	public function putPlatAction($id,Request $request,ParamFetcher $paramFetcher){
		$operation = $this->get('app.operation');
		return $operation->put($request,'AppBundle:Plat',$id);
	}	

	/**
     * Supprimer un plat
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer un plat",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/plats/{id}",name="delete_plat", options={"expose"=true})
     * @Method({"DELETE"})
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 */
	public function deletePlatAction($id){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Plat',$id);

	}

    /**
     * Met a jour le menu d'un restaurant en activant ou desactivant des plats
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Met a jour le menu d'un restaurant en activant ou desactivant des plats",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="plats", array=true,nullable=false, description="les plats a mettre à jour")
     * @Route("api/updateMenu",name="update_menu", options={"expose"=true})
     * @Method({"PUT"})
     * @Security("has_role('ROLE_RESTAURANT')")
     */

    public function updateMenu(ParamFetcher $paramFetcher){

        $em = $this->getDoctrine()->getManager();
        $plats = $paramFetcher->get('plats');
        if($plats){
            $fail = array();
            $success = array();
            foreach($plats as $p){
                $plat = $em->getRepository('AppBundle:Plat')->findOneBy(array('id'=>$p['id']));
                if($plat){
                    $plat->setOnMenu($p['onMenu']);
                    $success[]=$p['id'];
                }else{
                    $fail[]=$p['id'];
                }
            }
            $em->flush();

            return MessageResponse::message('Le menu a été mis à jour','success',200,array('fail'=>$fail,'success'=>$success));
        }{
            return MessageResponse::message('Veuillez fournir des plats à mettre à jour','danger',400);
        }
    }
}