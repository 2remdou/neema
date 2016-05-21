<?php

namespace AppBundle\Entity;

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




    public function __construct(){
        $this->dateCommande = new \DateTime();
        $this->isDelivered = false;

        $this->setLongitude('9.500124');
        $this->setLatitude('-13.714314');
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
}
