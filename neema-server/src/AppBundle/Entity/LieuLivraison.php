<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Expose,
    JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Validator\Constraints as NeemaAssert;


/**
 * LieuLivraison
 *
 * @ORM\Table(name="lieuLivraison")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LieuLivraisonRepository")
 * @ExclusionPolicy("all")
 * @UniqueEntity(fields="nom", message="Ce nom du lieu existe déjà.")
 */
class LieuLivraison
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
     * @Assert\NotBlank(message="le nom du lieu est obligatoire")
     */
    private $nom;


    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="string", length=255, nullable=true)
     * @Expose()
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="string", length=255, nullable=true)
     * @Expose()
     */
    private $longitude;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return LieuLivraison
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
     * Set longitude
     *
     * @param string $longitude
     *
     * @return LieuLivraison
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
     * Set nom
     *
     * @param string $nom
     *
     * @return LieuLivraison
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
     * @return LieuLivraison
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
     * @return LieuLivraison
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
