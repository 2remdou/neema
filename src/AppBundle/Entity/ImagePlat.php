<?php

namespace AppBundle\Entity;

use AppBundle\Hydrator\Hydrator;
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
 * @ORM\Table(name="imagePlat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 * @Vich\Uploadable
 * @ExclusionPolicy("all")
 */
class ImagePlat
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
     * @ORM\Column(name="imageName", type="string", length=255, unique=true)
     * @Expose()
     * @SerializedName("imageName")
     */
    private $imageName;

    /**
     *
     * @Vich\UploadableField(mapping="plat_image", fileNameProperty="imageName")
     * @var File
     * @Expose()
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
     * @ORM\OneToOne(targetEntity="Plat", inversedBy="imagePlat")
     * @ORM\JoinColumn(nullable=false)
     * @Expose()
     * @Assert\NotNull(message="Veuillez fournir un plat")
     */
    private $plat;





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
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Set plat
     *
     * @param \AppBundle\Entity\Plat $plat
     *
     * @return ImagePlat
     */
    public function setPlat(\AppBundle\Entity\Plat $plat)
    {
        $this->plat = $plat;

        $this->plat->setImage($this);

        return $this;
    }

    /**
     * Get plat
     *
     * @return \AppBundle\Entity\Plat
     */
    public function getPlat()
    {
        return $this->plat;
    }

    /**
     * Set webPath
     *
     * @param string $webPath
     *
     * @return ImagePlat
     */
    public function setWebPath($webPath)
    {
        $this->webPath = $webPath;

        return $this;
    }

    /**
     * Get webPath
     *
     * @return string
     */
    public function getWebPath()
    {
        return $this->webPath;
    }
}
