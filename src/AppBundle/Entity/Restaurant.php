<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Expose,
    JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


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
     */
    private $images;


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
}
