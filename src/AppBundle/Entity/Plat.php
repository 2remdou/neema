<?php

namespace AppBundle\Entity;

use AppBundle\Hydrator\Hydrator;
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
     * @ORM\Column(name="nom", type="string", length=255, unique=false)
     * @Expose()
     */
    private $nom;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     * @Expose()
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="ImagePlat",mappedBy="plat")
     * @Expose()
     * @SerializedName("images")
     */
    private $imagePlat;
    /**
     * @ORM\ManyToOne(targetEntity="Restaurant")
     * @Expose()
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="le restaurant est obligatoire")
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
     * @var float
     * Le temps de preparation du plat en seconde
     * @ORM\Column(name="dureePreparation", type="float")
     * @Expose()
     * @SerializedName("dureePreparation")
     */
    private $dureePreparation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="onMenu", type="boolean", options={"default":true})
     * @Expose()
     * @SerializedName("onMenu")
     */
    protected $onMenu;


    public function __construct(){
        $this->onMenu = false;
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
     * @return mixed
     */
    public function getImagePlat()
    {
        return $this->imagePlat;
    }

    /**
     * @param mixed $imagePlat
     */
    public function setImagePlat($imagePlat)
    {
        $this->imagePlat = $imagePlat;
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

    /**
     * Set onMenu
     *
     * @param boolean $onMenu
     *
     * @return Plat
     */
    public function setOnMenu($onMenu=1)
    {
        $this->onMenu = $onMenu;

        return $this;
    }

    /**
     * Get onMenu
     *
     * @return boolean
     */
    public function getOnMenu()
    {
        return $this->onMenu;
    }

    /**
     * Set dureePreparation
     *
     * @param float $dureePreparation
     *
     * @return Plat
     */
    public function setDureePreparation($dureePreparation)
    {
        $this->dureePreparation = $dureePreparation;

        return $this;
    }

    /**
     * Get dureePreparation
     *
     * @return float
     */
    public function getDureePreparation()
    {
        return $this->dureePreparation;
    }
}
