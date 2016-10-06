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
use AppBundle\Entity\Notification;


class NotificationController extends FOSRestController
{

	/**
     * retourner une notification
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner une notification",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"	
     *   }
     * )
     * @Route("api/notifications/{id}",name="get_notification", options={"expose"=true})
     * @ParamConverter("notification", class="AppBundle:Notification")
     * @Method({"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

	public function getNotificationAction(Notification $notification){
        return $notification;
	}

	/**
     * retourner une notification d'un user
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "retourner une notification d'un user",
     *   statusCodes = {
     *     	200 = "Succes",
     *		404= "Not found"
     *   }
     * )
     * @Route("api/notifications/user-connected",name="get_notification", options={"expose"=true})
     * @Method({"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

	public function getNotificationByUserAction(){
        $user = $this->getUser();
        if(!$user){
            return MessageResponse::message('Utilisateur incorrect','danger',400);
        }
        $em = $this->getDoctrine()->getManager();

        $notifications = $em->getRepository('AppBundle:Notification')->findByUser($user->getId());

        return $notifications;
	}

}