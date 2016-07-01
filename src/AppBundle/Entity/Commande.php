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
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255)
     * @Expose()
     * @Assert\NotBlank(message="le numero du client est obligatoire")
     */
    private $telephone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDelivered", type="boolean", options={"default":false})
     * @Expose()
     * @SerializedName("isDelivered")
     */
    private $isDelivered;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="string", length=255)
     * @Expose()
     * @Assert\NotBlank(message="la longitude est obligatoire")
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="string", length=255)
     * @Expose()
     * @Assert\NotBlank(message="la latitude est obligatoire")
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="fraisTransport", type="float")
     * @Assert\NotBlank(message="Le transport est obligatoire")
     * @Expose()
     * @SerializedName("fraisTransport")
     */
    private $fraisTransport;

    /**
     * La durée de livraison entre le client et le restaurant en seconde
     * @var float
     *
     * @ORM\Column(name="durationLivraison", type="float")
     * @Assert\NotBlank(message="La durée de livraison  est obligatoire")
     * @Expose()
     * @SerializedName("durationLivraison")
     */
    private $durationLivraison;

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
     * @var float
     * en metre
     * @ORM\Column(name="distance", type="float")
     * @Assert\NotBlank(message="La distance entre le restaurant et le client  est obligatoire")
     * @Expose()
     */
    private $distance;


    /**
     * @ORM\OneToMany(targetEntity="DetailCommande",mappedBy="commande")
     * @Expose()
     * @SerializedName("detailCommandes")
     */
    private $detailCommandes;

    /**
     * @ORM\OneToOne(targetEntity="Livraison", mappedBy="commande")
     * @Expose()
     */
    private $livraison;

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
        $this->isDelivered = false;
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
     * Set isDelivered
     *
     * @param boolean $isDelivered
     *
     * @return Commande
     */
    public function setIsDelivered($isDelivered)
    {
        $this->isDelivered = $isDelivered;

        return $this;
    }

    /**
     * Get isDelivered
     *
     * @return boolean
     */
    public function getIsDelivered()
    {
        return $this->isDelivered;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Commande
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Commande
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
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
     * Set livraison
     *
     * @param \AppBundle\Entity\Livraison $livraison
     *
     * @return Commande
     */
    public function setLivraison(\AppBundle\Entity\Livraison $livraison = null)
    {
        $this->livraison = $livraison;

        return $this;
    }

    /**
     * Get livraison
     *
     * @return \AppBundle\Entity\Livraison
     */
    public function getLivraison()
    {
        return $this->livraison;
    }

    /**
     * Set fraisTransport
     *
     * @param float $fraisTransport
     *
     * @return Commande
     */
    public function setFraisTransport($fraisTransport)
    {
        $this->fraisTransport = $fraisTransport;

        return $this;
    }

    /**
     * Get fraisTransport
     *
     * @return float
     */
    public function getFraisTransport()
    {
        return $this->fraisTransport;
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
     * Set duration
     *
     * @param float $duration
     *
     * @return Commande
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return float
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set distance
     *
     * @param float $distance
     *
     * @return Commande
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
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
     * Set durationLivraison
     *
     * @param float $durationLivraison
     *
     * @return Commande
     */
    public function setDurationLivraison($durationLivraison)
    {
        $this->durationLivraison = $durationLivraison;

        return $this;
    }

    /**
     * Get durationLivraison
     *
     * @return float
     */
    public function getDurationLivraison()
    {
        return $this->durationLivraison;
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
}
