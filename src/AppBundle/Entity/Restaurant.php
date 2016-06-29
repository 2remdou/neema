<?php

namespace AppBundle\Entity;

use AppBundle\Hydrator\Hydrator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Expose,
    JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Validator\Constraints as NeemaAssert;



/**
 * Restaurant
 *
 * @ORM\Table(name="restaurant")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RestaurantRepository")
 * @ExclusionPolicy("all")
 * @UniqueEntity(fields="nom", message="Ce nom de restaurant existe déjà.")
 * @UniqueEntity(fields="telephone", message="Deux restaurant ne peuvent pas avoir le même numero de telephone")
 */
class Restaurant
{
    use Hydrator;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @Expose()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     * @Expose()
     * @Assert\NotBlank(message="le nom du restaurant est obligatoire")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, unique=true)
     * @Expose()
     * @Assert\NotBlank(message="Un numero de telephone est obligatoire")
     * @NeemaAssert\IsGuineanPhone()
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Expose()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="siteWeb", type="string", length=255, nullable=true)
     * @Expose()
     * @SerializedName("siteWeb")
     */
    private $siteWeb;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Expose()
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Quartier")
     * @ORM\JoinColumn(nullable=false)
     * @Expose()
     * @Assert\NotNull(message="Le quartier est obligatoire")
     */
    private $quartier;

    /**
     * @ORM\OneToMany(targetEntity="ImageRestaurant",mappedBy="restaurant")
     * @Expose()
     * @SerializedName("images")
     */
    private $imageRestaurants;

    /**
     * @ORM\OneToMany(targetEntity="Commande",mappedBy="restaurant")
     * @Expose()
     */
    private $commandes;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return Restaurant
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Restaurant
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
     * Set email
     *
     * @param string $email
     *
     * @return Restaurant
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set siteWeb
     *
     * @param string $siteWeb
     *
     * @return Restaurant
     */
    public function setSiteWeb($siteWeb)
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    /**
     * Get siteWeb
     *
     * @return string
     */
    public function getSiteWeb()
    {
        return $this->siteWeb;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Restaurant
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set quartier
     *
     * @param \AppBundle\Entity\Quartier $quartier
     *
     * @return Restaurant
     */
    public function setQuartier(\AppBundle\Entity\Quartier $quartier)
    {
        $this->quartier = $quartier;

        return $this;
    }

    /**
     * Get quartier
     *
     * @return \AppBundle\Entity\Quartier
     */
    public function getQuartier()
    {
        return $this->quartier;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Restaurant
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
     * @return Restaurant
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
     * Add commande
     *
     * @param \AppBundle\Entity\Commande $commande
     *
     * @return Restaurant
     */
    public function addCommande(\AppBundle\Entity\Commande $commande)
    {
        $this->commandes[] = $commande;

        return $this;
    }

    /**
     * Remove commande
     *
     * @param \AppBundle\Entity\Commande $commande
     */
    public function removeCommande(\AppBundle\Entity\Commande $commande)
    {
        $this->commandes->removeElement($commande);
    }

    /**
     * Get commandes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    /**
     * Add imageRestaurant
     *
     * @param \AppBundle\Entity\ImageRestaurant $imageRestaurant
     *
     * @return Restaurant
     */
    public function addImageRestaurant(\AppBundle\Entity\ImageRestaurant $imageRestaurant)
    {
        $this->imageRestaurants[] = $imageRestaurant;

        return $this;
    }

    /**
     * Remove imageRestaurant
     *
     * @param \AppBundle\Entity\ImageRestaurant $imageRestaurant
     */
    public function removeImageRestaurant(\AppBundle\Entity\ImageRestaurant $imageRestaurant)
    {
        $this->imageRestaurants->removeElement($imageRestaurant);
    }

    /**
     * Get imageRestaurants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImageRestaurants()
    {
        return $this->imageRestaurants;
    }
}
