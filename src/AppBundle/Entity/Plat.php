<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Expose,
    JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Plat
 *
 * @ORM\Table(name="plat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlatRepository")
 * @UniqueEntity(fields={"nom","restaurant"}, message="Ce plat existe déjà dans le menu.")
 */
class Plat
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
     */
    private $nom;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text")
     * @Expose()
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="ImagePlat")
     * @Expose()
     */
    private $image;
    /**
     * @ORM\ManyToOne(targetEntity="Restaurant")
     * @Expose()
     */
    private $restaurant;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float")
     * @Assert\NotBlank(message="Le prix est obligatoire")
     * @Expose()
     */
    private $prix;



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
     * @return Plat
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
     * Set description
     *
     * @param string $description
     *
     * @return Plat
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
     * Set image
     *
     * @param \AppBundle\Entity\ImagePlat $image
     *
     * @return Plat
     */
    public function setImage(\AppBundle\Entity\ImagePlat $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\ImagePlat
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return Plat
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set restaurant
     *
     * @param \AppBundle\Entity\Restaurant $restaurant
     *
     * @return Plat
     */
    public function setRestaurant(\AppBundle\Entity\Restaurant $restaurant = null)
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
}