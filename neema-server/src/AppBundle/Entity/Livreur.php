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
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @Expose()
     * @Assert\NotBlank(message="le code est obligatoire")
     */
    private $code;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isFree", type="boolean", options={"default":true})
     * @Expose()
     * @SerializedName("isFree")
     */
    private $isFree;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     * @Expose()
     * @Assert\NotNull(message="Un utilisateur doit être associé à un livreur")
     */
    private $user;




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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Livreur
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
}
