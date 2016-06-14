<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Expose,
    JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;



/**
 * Image
 *
 * @ORM\Table(name="ImageRestaurant")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 * @Vich\Uploadable
 * @ExclusionPolicy("all")
 */
class ImageRestaurant
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
     * @ORM\Column(name="imageName", type="string", length=255, unique=true)
     * @Expose()
     * @SerializedName("imageName")
     */
    private $imageName;

    /**
     *
     * @Vich\UploadableField(mapping="restaurant_image", fileNameProperty="imageName")
     * @var File
     * @SerializedName("imageFile")
     * @Assert\Image(
     *      maxSize="3M",
     *      maxSizeMessage="La taille ne peux exceder 3M"
     * )
     */
    private $imageFile;

    /**
     * @var string
     * @ORM\Column(name="webPath", type="string", length=255)
     * @Expose()
     * @SerializedName("webPath")
     */
    private $webPath;

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant",inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     * @Expose()
     * @Assert\NotNull(message="Veuillez fournir un restaurant")
     */
    private $restaurant;


    public function __construct(){
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
     * Set imageName
     *
     * @param string $imageName
     *
     * @return Image
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }


    /**
     * Get imageName
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }


    /**
     * Set restaurant
     *
     * @param \AppBundle\Entity\Restaurant $restaurant
     *
     * @return ImageRestaurant
     */
    public function setRestaurant(\AppBundle\Entity\Restaurant $restaurant)
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

    public function getWebPath(){
        return $this->webPath;
    }

    public function setWebPath($webPath){
        $this->webPath= $webPath;

        return $this;
    }
}
