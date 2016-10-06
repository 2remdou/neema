<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Expose,
    JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationRepository")
 * @ExclusionPolicy("all")
 */
class Notification
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Expose()
     * @Assert\NotBlank(message="le title est obligatoire")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Expose()
     * @Assert\NotBlank(message="le type est obligatoire")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="idType", type="string", length=255)
     * @Expose()
     * @SerializedName("idType")
     * @Assert\NotBlank(message="l'id du type est obligatoire")
     */
    private $idType;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     * @Expose()
     * @Assert\NotBlank(message="le message est obligatoire")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNotification", type="datetime",nullable=true)
     * @Expose()
     * @SerializedName("dateNotification")
     */
    private $dateNotification;


    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Le user est obligatoire")
     */
    private $user;

    /**
     * Notification constructor.
     * @param int $id
     */
    public function __construct()
    {
        $this->dateNotification = new \DateTime();
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
     * Set title
     *
     * @param string $title
     *
     * @return Notification
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Notification
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }


    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Notification
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
     * Set type
     *
     * @param string $type
     *
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set idType
     *
     * @param string $idType
     *
     * @return Notification
     */
    public function setIdType($idType)
    {
        $this->idType = $idType;

        return $this;
    }

    /**
     * Get idType
     *
     * @return string
     */
    public function getIdType()
    {
        return $this->idType;
    }
}
