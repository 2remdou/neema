<?php
/**
*	mdoutoure 02/05/2016
*/

namespace AppBundle\Controller;

use AppBundle\Entity\DeviceToken;
use AppBundle\Entity\Livreur;
use AppBundle\Entity\UserRestaurant;
use AppBundle\Util\FillAttributes;
use AppBundle\Util\Util;
use AppBundle\Validator\Constraints\IsGuineanPhone;
use AppBundle\Validator\Constraints\IsGuineanPhoneValidator;
use AppBundle\Validator\Validator;
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
    use Validator;
    use Util;


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
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('AppBundle:User')->findUsersRestaurantAndLivreur();

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
     * Ajouter un user de type client
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Ajouter un user de type client",
     *   statusCodes = {
     *     201 = "Created",
     *   }
     * )
	 * @RequestParam(name="username",nullable=false, description="username")
	 * @RequestParam(name="password",nullable=false, description="password")
     * @RequestParam(name="nom",nullable=true, description="nom")
     * @RequestParam(name="prenom",nullable=true, description="prenom")
     * @Route("api/users",name="post_user", options={"expose"=true})
     * @Method({"POST"})
     */
	public function postUserAction(Request $request,ParamFetcher $paramFetcher){

        $phoneNumber=  preg_replace('#[.\- ]#','', $paramFetcher->get('username'));

        if(!$this->validatePhoneNumber($phoneNumber)){
            return MessageResponse::message($paramFetcher->get('username').' n\'est pas un numero valide','danger',400);
        }
        $em = $this->getDoctrine()->getManager();

        $em->getConnection()->beginTransaction();

        try{
            $user = new User();

            $user->setUsername($phoneNumber);
            $user->setPassword($paramFetcher->get('password'));
            $user->setNom($paramFetcher->get('nom'));
            $user->setPrenom($paramFetcher->get('prenom'));
            $user->setTelephone($phoneNumber);
            $user->setIsReseted(false);
            $user->setEnabled(false);
            $user->generateActivationCode();
            $user->setRoles(array('ROLE_CLIENT'));


            //pour encoder le password
            $this->updatePassword($user);

            $validator = $this->get('validator');

            if($messages = MessageResponse::messageAfterValidation($validator->validate($user))){
                return MessageResponse::message($messages,'danger',400);
            }

            $em->persist($user);

            $em->flush();

            $twilio = $this->get('twilio.api');

            $message = $twilio->account->messages->sendMessage(
                $this->getParameter('phonenumber'), // From a Twilio number in your account
                $this->addCountryCodeInPhoneNumber($phoneNumber), // Text any number
                "Hi, c'est l'equipe de neema. Votre code est ".$user->getActivationCode()
            );


            $jwt = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

            $em->getConnection()->commit();

            return MessageResponse::message('utilisateur ajouté avec succès','success',201,array('token'=>$jwt));

        }catch (\Exception $e){
            $em->getConnection()->rollBack();
            throw $e;
        }



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
	 * @RequestParam(name="telephone",nullable=true, description="Numero de telephone")
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
            $user->setTelephone($paramFetcher->get('telephone'));
            $user->setEnabled(true);
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
     * Ajouter un livreur
     *
     * @ApiDoc(
     *   resource = true,
     *   description = " Ajouter un livreur",
     *   statusCodes = {
     *     201 = "Created",
     *     404 = "Not found",
     *   }
     * )
	 * @RequestParam(name="username",nullable=false, description="username")
	 * @RequestParam(name="nom",nullable=true, description="nom")
	 * @RequestParam(name="prenom",nullable=true, description="prenom")
	 * @RequestParam(name="telephone",nullable=true, description="Numero de telephone")
	 * @RequestParam(name="password",nullable=false, description="password")
     * @Route("api/users/user-livreur",name="post_user_livreur", options={"expose"=true})
     * @Method({"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
	public function postUserLivreurAction(ParamFetcher $paramFetcher){

        $em = $this->getDoctrine()->getManager();

        try{
            $user = new User();

            $user->setUsername($paramFetcher->get('username'));
            $user->setPassword($paramFetcher->get('password'));
            $user->setNom($paramFetcher->get('nom'));
            $user->setPrenom($paramFetcher->get('prenom'));
            $user->setTelephone($paramFetcher->get('telephone'));
            $user->setEnabled(true);
            $user->setRoles(array('ROLE_LIVREUR'));

            //pour encoder le password
            $this->updatePassword($user);

            $validator = $this->get('validator');

            if($messages = MessageResponse::messageAfterValidation($validator->validate($user))){
                return MessageResponse::message($messages,'danger',400);
            }

            $em->persist($user);
            $em->flush();

            $livreur = new Livreur();
            $livreur->setCode($user->getUsername());
            $livreur->setUser($user);

            if($messages = MessageResponse::messageAfterValidation($validator->validate($livreur))){
                return MessageResponse::message($messages,'danger',400);
            }


            $em->persist($livreur);

            $em->flush();

            $em->getConnection()->beginTransaction();

            return MessageResponse::message('Livreur ajouté avec succès','success',201);

        }catch (\Exception $e){
            $em->getConnection()->rollBack();
            throw $e;

        }

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
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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
     * Reinitialiser le mot de passe d'un utilisateur restaurant
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Reinitialiser le mot de passe d'un utilisateur restaurant",
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

        $user->setPassword($this->getParameter('defaultpassword'));
        $user->setIsReseted(true);
        $this->updatePassword($user);

        $em->flush();

        return MessageResponse::message('Le mot de passe reinitialiser avec succès','success',200);

    }
    /**
     * Reinitialiser le mot de passe d'un client
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Reinitialiser le mot de passe d'un client",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @Route("api/users/resetClient/{telephone}",name="put_reset_client", options={"expose"=true})
     * @Method({"PUT"})
     */

    public function resetClientAction($telephone){
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneBy(array('username'=>$telephone));

        if(!$user){
            return MessageResponse::message('Numero de telephone introuvable','danger',400);
        }

//        $user->setIsReseted(true);
        $user->generateActivationCode();

        $em->flush();

        $twilio = $this->get('twilio.api');

        $message = $twilio->account->messages->sendMessage(
            $this->getParameter('phonenumber'), // From a Twilio number in your account
            $this->addCountryCodeInPhoneNumber($telephone), // Text any number
            "Hi, c'est l'equipe neema. Votre code est ".$user->getActivationCode()
        );


        return MessageResponse::message('Le mot de passe reinitialiser avec succès','success',200);

    }

    /**
     * Activation compte
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Activation compte",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="code",nullable=false, description="code d'activation")
     * @Route("api/users/enabled",name="put_user_enabled", options={"expose"=true})
     * @Method({"PUT"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

    public function enabledAction(ParamFetcher $paramFetcher){
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if($paramFetcher->get('code') != $user->getActivationCode()){
            return MessageResponse::message('Le code est incorrect','danger',400);
        }

        $user->setActivationCode(null);
        $user->setEnabled(true);

        $em->flush();

        return MessageResponse::message('Votre compte a été activé avec succès','success',200);

    }

    /**
     * Verifier le code pour la reinitialisation du mot de passe
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Verifier le code pour la reinitialisation du mot de passe",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="username",nullable=false, description="username")
     * @RequestParam(name="code",nullable=false, description="code d'activation")
     * @Route("api/users/checkCode",name="put_user_check_code", options={"expose"=true})
     * @Method({"PUT"})
     */

    public function checkCodeAction(ParamFetcher $paramFetcher){
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneBy(array('username'=>$paramFetcher->get('username')));
        if(!$user){
            return MessageResponse::message('Numero de telephone est introuvable','danger',400);
        }

        if($paramFetcher->get('code') != $user->getActivationCode()){
            return MessageResponse::message('Le code est incorrect','danger',400);
        }

        $user->setActivationCode(null);
        $user->setIsReseted(true);

        $em->flush();

        return MessageResponse::message('Le code est correct','success',200);

    }

    /**
     * Renvoyer le code activation
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Renvoyer le code activation",
     *   statusCodes = {
     *     	200 = "Success",
     *		404 = "Not found"
     *   }
     * )
     * @RequestParam(name="username",nullable=true, description="username")
     * @Route("api/users/sendBackActivationCode",name="put_user_sendback_code", options={"expose"=true})
     * @Method({"PUT"})
     */

    public function sendBackActivationCodeAction(ParamFetcher $paramFetcher){
        $em = $this->getDoctrine()->getManager();

        //s\'il est authentifié, c'est une activation de compte, non une réinitialisation
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            $user = $this->getUser();
            $user->setEnabled(false);
        }else{
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('username'=>$paramFetcher->get('username')));
            if(!$user){
                return MessageResponse::message('Numero de telephone introuvable','danger',400);
            }
        }

        $user->generateActivationCode();

        $em->flush();

        return MessageResponse::message('Code renvoyé','success',200);

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
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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
     * @RequestParam(name="username",nullable=false, description="Username")
     * @RequestParam(name="newPassword",nullable=false, description="Le nouveau mot de passe de l'utilisateur")
     * @RequestParam(name="confirmationPassword",nullable=false, description="La confirmation du mot de passe de l'utilisateur")
     * @Route("api/users/newPassword",name="put_new_password", options={"expose"=true})
     * @Method({"PUT"})
     */

    public function newPasswordAction(ParamFetcher $paramFetcher){
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneBy(array('username'=>$paramFetcher->get('username')));

        if(!$user){
            return MessageResponse::message('Numero de telephone introuvable','danger',400);
        }

        if(!$user->getIsReseted()){
            return MessageResponse::message('Vous devez réinitialiser le mot de passe, avant de le modifier','danger',400);
        }

        if($paramFetcher->get('newPassword') !== $paramFetcher->get('confirmationPassword')){
            return MessageResponse::message('La confirmation doit être identique au nouveau mot de passe','danger',400);
        }

        $user->setPassword($paramFetcher->get('newPassword'));
        $user->setIsReseted(false);
        $this->updatePassword($user);

        $em->flush();

        return MessageResponse::message('Votre mot de passe a été modifié avec succès','success',200);

    }

        /**
         * Ajouter un token d'un device à un user
         *
         * @ApiDoc(
         *   resource = true,
         *   description = "Ajouter un token d'un device à un user",
         *   statusCodes = {
         *     	200 = "Success",
         *		404 = "Not found"
         *   }
         * )
         * @RequestParam(name="token",nullable=false, description="le token du device")
         * @RequestParam(name="os",nullable=false, description="l'os du device")
         * @Route("api/users/device-token",name="put_device_token", options={"expose"=true})
         * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
         * @Method({"POST"})
         */

        public function addTokenForPush(ParamFetcher $paramFetcher){
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            $deviceToken = $em->getRepository('AppBundle:DeviceToken')->findOneBy(array('user'=>$user,'token'=>$paramFetcher->get('token')));
            if($deviceToken){
                return MessageResponse::message('','success',200,array('token'=>$deviceToken->getToken()));
            }

            $deviceToken = new DeviceToken();

            $deviceToken->setToken($paramFetcher->get('token'));
            $deviceToken->setUser($user);
            $deviceToken->setOs($paramFetcher->get('os'));

            $validator = $this->get('validator');

            if($messages = MessageResponse::messageAfterValidation($validator->validate($deviceToken))){
                return MessageResponse::message($messages,'danger',400);
            }


            $em->persist($deviceToken);

            $em->flush();


            return MessageResponse::message('Token enregistré avec succès','success',200,array('token'=>$deviceToken->getToken()));

        }



        }