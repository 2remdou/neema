<?php
/**
*	mdoutoure 02/05/2016
*/

namespace AppBundle\Controller;

use AppBundle\Entity\UserRestaurant;
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
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\User;


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
     * Lister les users
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lister les users",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/users",name="get_users", options={"expose"=true})
     * @Method({"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */

    public function getUsersAction(){
        $operation = $this->get('app.operation');
        return $operation->all('AppBundle:User');
    }
    /**
     * Retourne un user
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Retourne un user",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/users/{id}",name="get_user", options={"expose"=true})
     * @Method({"GET"})
     * @Security("!has_role('IS_AUTHENTICATED_ANONYMOUSLY')")
     */

    public function getUserAction($id){
        $operation = $this->get('app.operation');
        return $operation->get('AppBundle:User',$id);
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
        $user->setRoles(array('ROLE_CLIENT'));


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
	/**
     * Ajouter un user pour gerer un restaurant
     *
     * @ApiDoc(
     *   resource = true,
     *   description = " Ajouter un user pour gerer un restaurant",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
	 * @RequestParam(name="username",nullable=false, description="username")
	 * @RequestParam(name="nom",nullable=true, description="nom")
	 * @RequestParam(name="prenom",nullable=true, description="prenom")
	 * @RequestParam(name="restaurant",nullable=false, description="id du restaurant")
	 * @RequestParam(name="password",nullable=false, description="password")
     * @Route("api/users/userRestaurant",name="post_user_restaurant", options={"expose"=true})
     * @Method({"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
	public function postUserRestaurantAction(ParamFetcher $paramFetcher){

        $em = $this->getDoctrine()->getManager();

        $restaurant = $em->getRepository('AppBundle:Restaurant')->findOneBy(array('id'=>$paramFetcher->get('restaurant')));

        if(!$restaurant){
            return MessageResponse::message('Ce restaurant n\'est pas valide','danger',400);
        }
        try{
            $user = new User();

            $user->setUsername($paramFetcher->get('username'));
            $user->setPassword($paramFetcher->get('password'));
            $user->setNom($paramFetcher->get('nom'));
            $user->setPrenom($paramFetcher->get('prenom'));
            $user->setRoles(array('ROLE_RESTAURANT'));

            //pour encoder le password
            $this->updatePassword($user);

            $validator = $this->get('validator');

            if($messages = MessageResponse::messageAfterValidation($validator->validate($user))){
                return MessageResponse::message($messages,'danger',400);
            }
            $em->getConnection()->beginTransaction();

            $em->persist($user);
            $em->flush();

            $userRestaurant = new UserRestaurant();
            $userRestaurant->setUser($user);
            $userRestaurant->setRestaurant($restaurant);

            $em->persist($userRestaurant);
            $em->flush();

            $em->getConnection()->commit();
        }catch (Exception $e){
            $em->getConnection()->rollBack();
            throw $e;
        }
		$em->persist($user);

		$em->flush();

		return MessageResponse::message('utilisateur ajouté avec succès','success',201);


	}

    /**
     * Modifier un utilisateur
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Modifier un utilisateur",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="nom",nullable=true, description="nom de l'utilisateur")
     * @RequestParam(name="prenom",nullable=true, description="prenom de l'utilisateur")
     * @Route("api/users/edit/{id}",name="put_edit_user", options={"expose"=true})
     * @Method({"PUT"})
     * @Security("!has_role('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function putUserAction($id,Request $request,ParamFetcher $paramFetcher){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if(!$user){
            return MessageResponse::message('utilisateur introuvable','danger',400);
        }

        $user->setNom($paramFetcher->get('nom'));
        $user->setPrenom($paramFetcher->get('prenom'));

        $em->flush();

        $jwt = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

        return MessageResponse::message('utilisateur modifié avec succès','success',200,array('token'=>$jwt));
    }

    /**
     * Reinitialiser le mot de passe d'un utilisateur
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Reinitialiser le mot de passe d'un utilisateur",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/users/reset/{id}",name="put_rest_user", options={"expose"=true})
     * @Method({"PUT"})
     * @Security("has_role('ROLE_ADMIN')")
     */

    public function resetAction($id){
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id'=>$id));

        if(!$user){
            return MessageResponse::message('utilisateur introuvable','danger',400);
        }

        $user->setPassword($this->getParameter('defaultPassword'));
        $user->setIsReseted(true);
        $this->updatePassword($user);

        $em->flush();

        return MessageResponse::message('Le mot de passe reinitialiser avec succès','success',200);

    }

    /**
     * Changer le mot de passe d'un utilisateur
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Changer le mot de passe d'un utilisateur",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="passwordActuel",nullable=false, description="Mot de passe actuel de l'utilisateur")
     * @RequestParam(name="newPassword",nullable=false, description="Le nouveau mot de passe de l'utilisateur")
     * @RequestParam(name="confirmationPassword",nullable=false, description="La confirmation du mot de passe de l'utilisateur")
     * @Route("api/users/changePassword",name="put_change_password", options={"expose"=true})
     * @Method({"PUT"})
     * @Security("!has_role('IS_AUTHENTICATED_ANONYMOUSLY')")
     */

    public function changePasswordAction(ParamFetcher $paramFetcher){
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if(!$user){
            return MessageResponse::message('utilisateur introuvable','danger',404);
        }

        if($paramFetcher->get('newPassword') !== $paramFetcher->get('confirmationPassword')){
            return MessageResponse::message('La confirmation doit être identique au nouveau mot de passe','danger',400);
        }

        $encoder = $this->container->get('security.password_encoder');

        if(!$encoder->isPasswordValid($user,$paramFetcher->get('passwordActuel'))){
            return MessageResponse::message('Le mot de passe actuel est incorrect','danger',400);
        }

        $user->setPassword($paramFetcher->get('newPassword'));
        $user->setIsReseted(false);
        $this->updatePassword($user);

        $em->flush();

        return MessageResponse::message('Votre mot de passe a été modifié avec succès','success',200);

    }


}