<?php

namespace AppBundle\Entity;

use AppBundle\Hydrator\Hydrator;
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
 * @ORM\Table(name="userRestaurant")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRestaurantRepository")
 * @UniqueEntity(fields="username", message="Ce username existe déjà.")
 * @ExclusionPolicy("all")
 */
class UserRestaurant
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
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="Restaurant")
     * @ORM\JoinColumn(nullable=false)
     * @Expose()
     * @Assert\NotNull(message="Le restaurant est obligatoire")
     */
    private $restaurant;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="userRestaurant")
     * @Expose()
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Le user est obligatoire")
     */
    private $user;

    /**
     * Set restaurant
     *
     * @param \AppBundle\Entity\Restaurant $restaurant
     *
     * @return User
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
     * Get id
     *
     * @return guid
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return UserRestaurant
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
