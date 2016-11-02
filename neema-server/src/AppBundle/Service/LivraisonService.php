<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 21/10/2016
 */

namespace AppBundle\Service;


use AppBundle\Entity\Commande;
use AppBundle\Entity\Livraison;
use AppBundle\Entity\Livreur;
use AppBundle\Exception\ApiException;
use AppBundle\Util\Util;
use Doctrine\ORM\EntityManager;


class LivraisonService implements LivraisonServiceInterface
{

    use  Util;

    private $em;
    private $livreurService;
    private $commandeManager;
    private $fraisLivraison;
    private $rabbitMQService;

    public function __construct(EntityManager $em,LivreurServiceInterface $livreurService,CommandeManagerInterface $commandeManager,$fraisLivraison,RabbitMQService $rabbitMQService){
        $this->em = $em;
        $this->livreurService = $livreurService;
        $this->commandeManager = $commandeManager;
        $this->fraisLivraison = $fraisLivraison;
        $this->rabbitMQService = $rabbitMQService;
    }

    private function getRepository(){
        return $this->em->getRepository('AppBundle:Livraison');
    }

    /**
     * Pour creer une livraison
     * @param Commande $commande
     * @param Livreur $livreur
     * @return Livraison
     */
    public function create(Commande $commande)
    {
        $livraison = new Livraison();

        //a cause de la relation OneToOne
        $livraison->setCommande($commande);
        $commande->setLivraison($livraison);

        $livraison->setFraisCommande($this->fraisLivraison);


        $commande->setTotalCommande($commande->getMontantCommande()+$this->fraisLivraison);

        $livreur = $this->livreurService->getFreeLivreur();
        if($livreur){
            $livraison = $this->attachLivreurOnLivraisonWithoutLivreur($livreur,$commande);
        }

        return $livraison;

    }

    /**
     * Declanche le debut de la livraison,
     * en fournissant une valeur à dateDebutLivraison
     * @return mixed
     */
    public function startLivraison(Livraison $livraison)
    {
        if(!$livraison->getLivreur()){
            throw new ApiException('Aucun livreur disponible pour le moment');
        }

        if(!$this->commandeManager->allPlatOnCommandeAreFinished($livraison->getCommande())){
            throw new ApiException('Veuillez marquer les plats comme terminés');
        }

        //changer l'etat de la commande
        $this->commandeManager->changeEtatCommande($livraison->getCommande(),'CL1');

        $livraison->setDateDebutLivraison(new \DateTime());
    }

    /**
     * Marque une livraison commme finished
     * @param Livraison $livraison
     * @return mixed
     */
    public function livrer(Livraison $livraison)
    {
        if($livraison->getCommande()->getEtatCommande()->getCode()!=='CL1'){
            throw new ApiException('Le restaurant doit préalablement vous donner la commande,
                                    avant que vous ne fassiez la livraison au client.
                                    Merci de contacter le restaurant');
        }
        $livraison->setFinished(true);
        $livraison->setDateFinLivraison(new \DateTime());

        $this->commandeManager->closeCommande($livraison->getCommande());

        $livreur = $livraison->getLivreur();

        $livreur->setIsFree(true);
        $this->attachLivreurOnLivraisonWithoutLivreur($livreur);

    }


    /**
     * Retourne la livraison active en fonction d'un livreur
     * @param Livreur $livreur
     * @return Livraison
     */
    public function getLivraionEncours(Livreur $livreur)
    {
        return $this->getRepository()->findLivraisonEncours($livreur->getId());
    }

    /**
     * @param Commande $commande
     * @return mixed
     */
    public function getLivraionByCommande(Commande $commande)
    {
        return $this->getRepository()->findOneBy(array('commande'=>$commande));
    }

//


    /**
     * Verifie s'il existe une livraison sans livreur,
     * et associe cette livraison au livreur passé en parametre
     * @param Livreur $livreur
     * @return Livraison $livraison
     */
    public function attachLivreurOnLivraisonWithoutLivreur(Livreur $livreur,Commande $commande=null)
    {
        if($commande){
            $livraison = $commande->getLivraison();
        }else{
            $livraison = $this->getRepository()->findWithoutLivreur();
        }

        if($livraison){
            $livraison->setLivreur($livreur);
            $livreur->setIsFree(false);

            $contentMessage = 'Nouvelle livraison
Restaurant:'.$livraison->getCommande()->getRestaurant()->getNom().'('.$livraison->getCommande()->getRestaurant()->getQuartier()->getNom().')
Client:'.$livraison->getCommande()->getUser()->getNom().'(Tel:' .$livraison->getCommande()->getUser()->getTelephone().', Quartier:'.$livraison->getCommande()->getLieuLivraison()->getNom().')';

            $message = array(
                'telephone' => $this->addCountryCodeInPhoneNumber($livreur->getUser()->getTelephone()),
                'content'=>$contentMessage,
                'commande'=>$commande->getId(),
                'dateMessage'=> new \DateTime(),
            );

            $this->rabbitMQService->publish(json_encode($message),'notification.sms');

        }

        return $livraison;
    }

    /**
     * @param Livreur $livreur
     * @return array Livreur
     */
    public function getHistorique(Livreur $livreur)
    {
        $livraisons = $this->getRepository()->findHistorique($livreur->getId());

        return $livraisons;
    }


}