<?php
/**
*	mdoutoure 02/05/2016
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
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request,
	Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;


class UserController extends FOSRestController
{

	private function updatePassword(UserInterface $user)
	{
		if (0 !== strlen($password = $user->getPassword())) {
			$encoder = $this->get('security.password_encoder');
			$user->setPassword($encoder->encodePassword($user,$user->getPassword()));
		}else{
			$user->setPassword(null);
		}

	}

	/**
     * Ajouter un user
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un user",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
	 * @RequestParam(name="username",nullable=false, description="username")
	 * @RequestParam(name="password",nullable=false, description="password")
     * @Route("api/users",name="post_user", options={"expose"=true})
     * @Method({"POST"})
     */
	public function postUserAction(Request $request,ParamFetcher $paramFetcher){
        
		$user = new User();

		$user->setUsername($paramFetcher->get('username'));
		$user->setPassword($paramFetcher->get('password'));

		//pour encoder le password
		$this->updatePassword($user);

		$validator = $this->get('validator');

		if($messages = MessageResponse::messageAfterValidation($validator->validate($user))){
			return MessageResponse::message($messages,'danger',400);
		}
		$em = $this->getDoctrine()->getManager();

		$em->persist($user);

		$em->flush();

		return MessageResponse::message('utilisateur ajouté avec succès','success',201);


	}
}