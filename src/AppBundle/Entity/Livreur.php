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
 * Livreur
 *
 * @ORM\Table(name="livreur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LivreurRepository")
 * @ExclusionPolicy("all")
 * @UniqueEntity(fields="code", message="Ce code existe déjà.")
 */
class Livreur
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
     * @ORM\Column(name="nom", type="string", length=255)
     * @Expose()
     * @Assert\NotBlank(message="le nom est obligatoire")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Expose()
     * @Assert\NotBlank(message="le prenom est obligatoire")
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @Expose()
     * @Assert\NotBlank(message="le code est obligatoire")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, unique=true)
     * @Expose()
     * @Assert\NotBlank(message="le telephone est obligatoire")
     * @NeemaAssert\IsGuineanPhone()
     */
    private $telephone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isFree", type="boolean", options={"default":true})
     * @Expose()
     * @SerializedName("isFree")
     */
    private $isFree;



    public function __construct(){
        $this->isFree = true;
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
     * @return Livreur
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Livreur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Livreur
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set isFree
     *
     * @param boolean $isFree
     *
     * @return Livreur
     */
    public function setIsFree($isFree)
    {
        $this->isFree = $isFree;

        return $this;
    }

    /**
     * Get isFree
     *
     * @return boolean
     */
    public function getIsFree()
    {
        return $this->isFree;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Livreur
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
}
