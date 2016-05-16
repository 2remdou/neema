<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Expose,
    JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields="username", message="Ce username existe déjà.")
 * @ExclusionPolicy("all")
 */

class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @Expose()
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     * @Expose()
     * @Assert\NotBlank(message="username obligatoire")
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\NotBlank(message="password obligatoire")
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     * @Assert\NotBlank(message="salt obligatoire")
     */
    protected $salt;
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255,nullable=true)
     * @Expose()
     */
    protected $nom;
    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255,nullable=true)
     * @Expose()
     */
    protected $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="isReseted", type="boolean", options={"default":false})
     * @Expose()
     * @SerializedName("isReseted")
     */
    protected $isReseted;

    /**
     * @var array
     *
     * @ORM\Column(name="role", type="array", length=255)
     * @Assert\NotBlank(message="le role est obligatoire")
     * @Expose()
     */
    protected $roles;

    /**
     * @ORM\OneToOne(targetEntity="UserRestaurant",mappedBy="user")
     * @Expose()
     * @SerializedName("userRestaurant")
     */

    private $userRestaurant;



    public function __construct(){
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return User
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
     * @return User
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
     * Set isReseted
     *
     * @param boolean $isReseted
     *
     * @return User
     */
    public function setIsReseted($isReseted)
    {
        $this->isReseted = $isReseted;

        return $this;
    }

    /**
     * Get isReseted
     *
     * @return boolean
     */
    public function getIsReseted()
    {
        return $this->isReseted;
    }

    /**
     * Set userRestaurant
     *
     * @param \AppBundle\Entity\UserRestaurant $userRestaurant
     *
     * @return User
     */
    public function setUserRestaurant(\AppBundle\Entity\UserRestaurant $userRestaurant = null)
    {
        $this->userRestaurant = $userRestaurant;

        return $this;
    }

    /**
     * Get userRestaurant
     *
     * @return \AppBundle\Entity\UserRestaurant
     */
    public function getUserRestaurant()
    {
        return $this->userRestaurant;
    }
}
