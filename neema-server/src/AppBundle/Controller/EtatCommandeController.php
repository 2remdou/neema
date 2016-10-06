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
use AppBundle\Entity\Etatcommande;


class EtatCommandeController extends FOSRestController
{
	/**
     * Ajouter un etatcommande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un etatcommande",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
     * @RequestParam(name="code",nullable=false, description="le code de l'etat de la commande")
     * @RequestParam(name="libelle",nullable=false, description="le libelle de l'etat de la commande")
     * @Route("api/etat-commandes",name="post_etatcommande", options={"expose"=true})
     * @Method({"POST"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function postEtatcommandeAction(Request $request,ParamFetcher $paramFetcher){
        
		$operation = $this->get('app.operation');
		$etatcommande = new Etatcommande();
		return $operation->post($request,$etatcommande);
	}

	/**
     * Lister les etatcommandes
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les etatcommandes",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/etat-commandes",name="get_etatcommandes", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getEtatcommandesAction(){
		$operation = $this->get('app.operation');
		return $operation->all('AppBundle:EtatCommande');
	}

	/**
     * retourner un etatcommande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner un etatcommande",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/etat-commandes/{id}",name="get_etatcommande", options={"expose"=true})
     * @Method({"GET"})
     */

	public function getEtatcommandeAction($id){
		$operation = $this->get('app.operation');
		return $operation->get('AppBundle:EtatCommande',$id);
	}

	/**
     * Modifier un etatcommande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier un etatcommande",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="code",nullable=false, description="le code de l'etat de la commande")
     * @RequestParam(name="libelle",nullable=false, description="le libelle de l'etat de la commande")
     * @Route("api/etat-commandes/{code}",name="put_etatcommande", options={"expose"=true})
     * @ParamConverter("etatCommande", class="AppBundle:EtatCommande")
     * @Method({"PUT"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function putEtatcommandeAction(Etatcommande $etatcommande,ParamFetcher $paramFetcher){
        $em = $this->getDoctrine()->getManager();
        $etatcommande->setCode($paramFetcher->get('code'));
        $etatcommande->setLibelle($paramFetcher->get('libelle'));
        $em->flush();
        return MessageResponse::message('Modification effectuée','success',200,
            array($etatcommande));

    }

	/**
     * Supprimer un etatcommande
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Supprimer un etatcommande",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/etat-commandes/{code}",name="delete_etatcommande", options={"expose"=true})
     * @Method({"DELETE"})
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 */
	public function deleteEtatcommandeAction($code){
		$operation = $this->get('app.operation');
		return $operation->delete('AppBundle:Etatcommande',array('code'=>$code));

	}
}