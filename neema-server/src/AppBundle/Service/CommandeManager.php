<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 09/07/2016
 * Time: 08:37
 */

namespace AppBundle\Service;


use AppBundle\Entity\Commande;
use AppBundle\Entity\DetailCommande;
use AppBundle\Entity\Livraison;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use AppBundle\Event\CommandeEnregistreEvent;
use AppBundle\Event\DetailCommandeEvent;
use AppBundle\Exception\ApiException;
use AppBundle\NeemaEvents;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use AppBundle\MessageResponse\MessageResponse;

class CommandeManager implements CommandeManagerInterface
{

    private $timeMarge;
    private $timeLivraison;
    private $em;
    private $utilService;
    private $validator;
    private $tokenStorage;
    private $dispatcher;

    public function __construct(EntityManager $em, $timeMarge, $timeLivraison,UtilServiceInterface $utilService,
                                ValidatorInterface $validator,TokenStorage $tokenStorage,EventDispatcherInterface $dispatcher){
        $this->em = $em;
        $this->timeMarge = $timeMarge;
        $this->timeLivraison = $timeLivraison;
        $this->utilService = $utilService;
        $this->validator = $validator;
        $this->tokenStorage = $tokenStorage;
        $this->dispatcher = $dispatcher;
    }

    private function getRepository(){
        return $this->em->getRepository('AppBundle:Commande');
    }

    public function create(Commande $commande, ParamFetcher $paramFetcher){

        $commande = $this->utilService->attach($commande,'Restaurant',array('id'=>$paramFetcher->get('restaurant')));

        //verifier si le restaurant n'est pas fermé
        if($commande->getRestaurant()->isClose()){
            throw new ApiException('Désolé,vous ne pouvez passer de commande. Le restaurant "'
                .$commande->getRestaurant()->getNom(). '" est fermé',400,'info');
        }
        if($paramFetcher->get('lieuLivraison')){
            $commande = $this->utilService->attach($commande,'LieuLivraison',array('id'=>$paramFetcher->get('lieuLivraison')));
        }
        $details = $paramFetcher->get('detailCommandes');

        if(!$details){
            throw new ApiException('Une commande ne peut exister sans plat');
        }

        $commande->setTelephone($paramFetcher->get('telephone'));
        $commande->setUser($this->tokenStorage->getToken()->getUser());
        $this->changeEtatCommande($commande,'CN1');
        $montantCommande = 0;

        foreach($details as $detail){
            $detailCommande = new DetailCommande();
            $detailCommande->setCommande($commande);
            if(!array_key_exists('plat',$detail)){
                throw new ApiException('le plat est obligatoire dans le parametre detailCommande');
            }
            $this->utilService->attach($detailCommande,'Plat',array('id'=>$detail['plat']));

            //verifier si le plat est sur le menu
            if(!$detailCommande->getPlat()->getOnMenu()){
                throw new ApiException('Désolé, le plat "'.$detailCommande->getPlat()->getNom().'" a été retiré du menu',
                    400,'info');
            }

            if($detailCommande->getPlat()->getRestaurant() !== $commande->getRestaurant() ){
                throw new ApiException('Une commande ne peut avoir des plats de differents restaurants');
            }
            if(!array_key_exists('plat',$detail)){
                throw new ApiException('le quantité est obligatoire dans le parametre detailCommande');
            }
            $detailCommande->setQuantite($detail['quantite']);
            $detailCommande->setPrix($detailCommande->getPlat()->getPrix());

            $montantCommande += $detailCommande->getPrix()*$detailCommande->getQuantite();

            if($messages = MessageResponse::messageAfterValidation($this->validator->validate($detailCommande))){
                throw new ApiException($messages);
            }

            $this->em->persist($detailCommande);
        }

        $commande->setALivrer($paramFetcher->get('aLivrer'));
        if($commande->getALivrer() && !$commande->getLieuLivraison()){
            throw new ApiException('Veuillez specifier une adresse de livraison');
        }

        $this->calculDurationEstimative($commande);
        $commande->setMontantCommande($montantCommande);
        $commande->setTotalCommande($montantCommande);

        return $commande;

    }


    /**
     * calcule la duree exacte du processus(temps au restaurant+temps de livraison)
     * durationExact=dateLivraison-dateCommande(en seconde)
     *
     * @param Commande $commande
     * @return mixed
     */
    public function calculDurationExact(Commande $commande)
    {
        if($commande->getALivrer()){
            $tempsEcoule = $commande->getLivraison()->getDateFinLivraison()->getTimestamp()-$commande->getDateCommande()->getTimestamp();
        }else{
            $tempsEcoule = $commande->getDateDelivered()->getTimestamp()-$commande->getDateCommande()->getTimestamp();
        }
        $commande->setDurationExact($tempsEcoule);
    }


    public function calculDurationEstimative(Commande $commande){
        $durationEstimative = $this->timeMarge;

        if($commande->getALivrer()) $durationEstimative += $this->timeLivraison;

        $maxTime = 0;
        foreach($commande->getDetailCommandes() as $detailCommande){
            $timeDetail = $detailCommande->getPlat()->getDureePreparation()*$detailCommande->getQuantite();

            if($maxTime===0) $maxTime = $timeDetail;

            if($timeDetail>$maxTime) $maxTime = $timeDetail;
        }

        $durationEstimative += $maxTime;


        $commande->setDurationEstimative($durationEstimative);

    }

    /**
     * Retourne la commande la plus ancienne sans livreur
     * @return Commande
     */
    public function getCommandeWithoutLivreur()
    {
        return $this->getRepository()->findCommandeWithoutLivreur();
    }

    /**
     * Pour marquer qu'une commande a été livré
     * @param Commande $commande
     * @return mixed
     */
    public function closeCommande(Commande $commande)
    {
        if(!$this->allPlatOnCommandeAreFinished($commande)){
            throw new ApiException('Veuillez marquer les plats comme terminés');
        }
        $this->changeEtatCommande($commande,'CL2');
        $commande->setDelivered(true);
        $commande->setDateDelivered(new \DateTime());
        $this->calculDurationExact($commande);
    }

    /**
     * Pour marquer qu'un plat est prêt dans une commande
     * @param DetailCommande $detailCommande
     * @return mixed
     */
    public function closeDetailCommande(DetailCommande $detailCommande)
    {
        $detailCommande->setFinished(true);
        $detailCommande->setDateFinished(new \DateTime());

        $this->dispatcher->dispatch(NeemaEvents::DETAIL_COMMANDE_FINISHED,new DetailCommandeEvent($detailCommande));


        $details = $detailCommande->getCommande()->getDetailCommandes();

        $allPlatAreFinished = true;
        foreach($details as $d){
            if(!$d->isFinished()){
                $allPlatAreFinished = false;
                break;
            }
        }
        if($allPlatAreFinished){
            $this->changeEtatCommande($detailCommande->getCommande(),'CP');

            $this->dispatcher->dispatch(NeemaEvents::COMMANDE_PRETE,new CommandeEnregistreEvent($detailCommande->getCommande()));
        }


    }


    /**
     * Verifie si tous les plats de la commande ont été marqué comme terminé
     * @param Commande $commande
     * @return boolean
     */
    public function allPlatOnCommandeAreFinished(Commande $commande)
    {
        return $this->getRepository()->allDetailIsFinished($commande->getId());
    }

    /**
     * Changer l'etat d'une commande
     * @param Commande $commande
     * @param string $codeEtatCommande
     * @return mixed
     */
    public function changeEtatCommande(Commande $commande, $codeEtatCommande)
    {
        $etatCommande = $this->em->getRepository('AppBundle:EtatCommande')->findOneBy(array('code'=>$codeEtatCommande));

        if(!$etatCommande){
            throw new ApiException('Cet etat de commande est introuvable');
        }
        $commande->setEtatCommande($etatCommande);

    }

    /**
     * Retourne l'historique des commandes livrées à un client
     * @param User $user
     * @param String $page
     * @return Array Commande
     */
    public function getHistoriqueByClient(User $user,\DateTime $from=null,\DateTime $to=null, $page=1)
    {
        $commandes = $this->getRepository()->findHistoriqueByClient($user->getId(),$from,$to,$page);
        return $commandes;
    }

    /**
     * Retourne les commandes encours d'un restaurant
     * CN1 : nouvelle commande
     * CP : Commande prête
     * @param Restaurant $restaurant
     * @param $page
     * @return mixed
     */
    public function getNotFinishedByRestaurant($page=1)
    {
        $commandes = $this->getRepository()->findByEtat(array('CP','CN1'),$page);
        return $commandes;
    }



}