<?php

namespace AppBundle\Entity;

use AppBundle\Hydrator\Hydrator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Expose,
    JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommandeRepository")
 * @ExclusionPolicy("all")
 */
class Commande
{

    use Hydrator;
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @Expose()
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCommande", type="datetime")
     * @Expose()
     * @SerializedName("dateCommande")
     * @Assert\NotBlank(message="la date est obligatoire")
     */
    private $dateCommande;

    /**
     * Le numero de telephone pour contacter le client
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255)
     * @Expose()
     * @Assert\NotBlank(message="le numero du client est obligatoire")
     */
    private $telephone;

    /**
     * Le code genere, qui permettra au client de recuperer sa commande
     * @var string
     *
     * @ORM\Column(name="codeCommande", type="string", length=255)
     * @Expose()
     * @Assert\NotBlank(message="Le code de la commande est obligatoire")
     */
    private $codeCommande;

    /**
     * pour savoir si le client à recuperer la commande
     * @var boolean
     *
     * @ORM\Column(name="delivered", type="boolean", options={"default":false})
     * @Expose()
     * @SerializedName("delivered")
     */
    private $delivered;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDelivered", type="datetime",nullable=true)
     * @Expose()
     * @SerializedName("dateDelivered")
     * @Assert\NotBlank(message="la date est obligatoire")
     */
    private $dateDelivered;


    /**
     * La somme des différentes durées pour determiner approximativement
     * le temps de livraison
     * @var float
     *
     * @ORM\Column(name="durationEstimative", type="float")
     * @Assert\NotBlank(message="La durée estimative de livraison  est obligatoire")
     * @Expose()
     * @SerializedName("durationEstimative")
     */
    private $durationEstimative;

    /**
     * @var float
     * La durée exacte, obtenue après la livraison
     * @ORM\Column(name="durationExact", type="float", nullable=true)
     * @Expose()
     */
    private $durationExact;


    /**
     * @ORM\OneToMany(targetEntity="DetailCommande",mappedBy="commande")
     * @Expose()
     * @SerializedName("detailCommandes")
     */
    private $detailCommandes;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Le user est obligatoire")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant", inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Le restaurant est obligatoire")
     */
    private $restaurant;

    /**
     * @ORM\ManyToOne(targetEntity="EtatCommande", inversedBy="commandes")
     * @ORM\JoinColumn(name="etatCommande",referencedColumnName="code",nullable=false)
     * @Assert\NotNull(message="L'etat est obligatoire")
     * @Expose()
     */
    private $etatCommande;




    public function __construct(){
        $this->dateCommande = new \DateTime();
        $this->delivered = false;
        $this->codeCommande = strtoupper(substr(uniqid(),1,8));
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set dateCommande
     *
     * @param \DateTime $dateCommande
     *
     * @return Commande
     */
    public function setDateCommande($dateCommande)
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    /**
     * Get dateCommande
     *
     * @return \DateTime
     */
    public function getDateCommande()
    {
        return $this->dateCommande;
//        return $this->dateCommande->getTimestamp();
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Commande
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }


    /**
     * @param boolean $delivered
     */
    public function setDelivered($delivered)
    {
        $this->delivered = $delivered;
    }

    /**
     * Add detailCommande
     *
     * @param \AppBundle\Entity\DetailCommande $detailCommande
     *
     * @return Commande
     */
    public function addDetailCommande(\AppBundle\Entity\DetailCommande $detailCommande)
    {
        $this->detailCommandes[] = $detailCommande;

        return $this;
    }

    /**
     * Remove detailCommande
     *
     * @param \AppBundle\Entity\DetailCommande $detailCommande
     */
    public function removeDetailCommande(\AppBundle\Entity\DetailCommande $detailCommande)
    {
        $this->detailCommandes->removeElement($detailCommande);
    }

    /**
     * Get detailCommandes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDetailCommandes()
    {
        return $this->detailCommandes;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Commande
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set restaurant
     *
     * @param \AppBundle\Entity\Restaurant $restaurant
     *
     * @return Commande
     */
    public function setRestaurant(\AppBundle\Entity\Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * Get restaurant
     *
     * @return \AppBundle\Entity\Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * Set durationExact
     *
     * @param float $durationExact
     *
     * @return Commande
     */
    public function setDurationExact($durationExact)
    {
        $this->durationExact = $durationExact;

        return $this;
    }

    /**
     * Get durationExact
     *
     * @return float
     */
    public function getDurationExact()
    {
        return $this->durationExact;
    }

    /**
     * Set durationEstimative
     *
     * @param float $durationEstimative
     *
     * @return Commande
     */
    public function setDurationEstimative($durationEstimative)
    {
        $this->durationEstimative = $durationEstimative;

        return $this;
    }

    /**
     * Get durationEstimative
     *
     * @return float
     */
    public function getDurationEstimative()
    {
        return $this->durationEstimative;
    }

    /**
     * Set etatCommande
     *
     * @param \AppBundle\Entity\EtatCommande $etatCommande
     *
     * @return Commande
     */
    public function setEtatCommande(\AppBundle\Entity\EtatCommande $etatCommande)
    {
        $this->etatCommande = $etatCommande;

        return $this;
    }

    /**
     * Get etatCommande
     *
     * @return \AppBundle\Entity\EtatCommande
     */
    public function getEtatCommande()
    {
        return $this->etatCommande;
    }

    /**
     * @return string
     */
    public function getCodeCommande()
    {
        return $this->codeCommande;
    }

    /**
     * @param string $codeCommande
     */
    public function setCodeCommande($codeCommande)
    {
        $this->codeCommande = $codeCommande;
    }

    /**
     * @return \DateTime
     */
    public function getDateDelivered()
    {
        return $this->dateDelivered;
    }

    /**
     * @param \DateTime $dateDelivered
     */
    public function setDateDelivered($dateDelivered)
    {
        $this->dateDelivered = $dateDelivered;
    }


}
