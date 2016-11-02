<?php
/**
*	mdoutoure 01/05/2016	
*/

namespace AppBundle\Controller;

use AppBundle\Util\FillAttributes;
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
use AppBundle\Entity\Typecommande;


class TypeCommandeController extends FOSRestController
{
	/**
     * Ajouter un typecommande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un typecommande",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="code",nullable=false, description="le code de l'type de la commande")
     * @RequestParam(name="libelle",nullable=false, description="le libelle de l'type de la commande")
     * @Route("api/type-commandes",name="post_typecommande", options={"expose"=true})
     * @Method({"POST"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function postTypecommandeAction(Request $request,ParamFetcher $paramFetcher){
        
		$operation = $this->get('app.operation');
		$typecommande = new Typecommande();
		return $operation->post($request,$typecommande);
	}

	/**
     * Lister les typecommandes
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les typecommandes",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/type-commandes",name="get_typecommandes", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getTypecommandesAction(){
		$operation = $this->get('app.operation');
		return $operation->all('AppBundle:TypeCommande');
	}

	/**
     * retourner un typecommande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner un typecommande",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/type-commandes/{id}",name="get_typecommande", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getTypecommandeAction($id){
		$operation = $this->get('app.operation');
		return $operation->get('AppBundle:TypeCommande',$id);
	}

	/**
     * Modifier un typecommande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier un typecommande",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="code",nullable=false, description="le code de l'type de la commande")
     * @RequestParam(name="libelle",nullable=false, description="le libelle de l'type de la commande")
     * @Route("api/type-commandes/{code}",name="put_typecommande", options={"expose"=true})
     * @ParamConverter("typeCommande", class="AppBundle:TypeCommande")
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function putTypecommandeAction(Typecommande $typecommande,ParamFetcher $paramFetcher){
        $em = $this->getDoctrine()->getManager();
        $typecommande->setCode($paramFetcher->get('code'));
        $typecommande->setLibelle($paramFetcher->get('libelle'));
        $em->flush();
        return MessageResponse::message('Modification effectuée','success',200,
            array($typecommande));

    }

	/**
     * Supprimer un typecommande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer un typecommande",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/type-commandes/{code}",name="delete_typecommande", options={"expose"=true})
     * @Method({"DELETE"})
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 */
	public function deleteTypecommandeAction($code){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Typecommande',array('code'=>$code));

	}
}