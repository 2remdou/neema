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
 * Livraison
 *
 * @ORM\Table(name="livraison")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LivraisonRepository")
 * @ExclusionPolicy("all")
 * @UniqueEntity(fields="commande", message="Les informations de livraison déjà enrégistrées pour cette commande.")
 */
class Livraison
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateLivraison", type="datetime", nullable=true)
     * @Expose()
     * @SerializedName("dateLivraison")
     */
    private $dateLivraison;

    /**
     * @ORM\OneToOne(targetEntity="Commande", inversedBy="livraison")
     * @ORM\JoinColumn(nullable=false)
     * @Expose()
     * @Assert\NotNull(message="La commande est obligatoire")
     */
    private $commande;

    /**
     * @ORM\ManyToOne(targetEntity="Livreur")
     * @ORM\JoinColumn(nullable=true)
     * @Expose()
     * @Assert\NotNull(message="Le livreur est obligatoire")
     */
    private $livreur;

    /**
     * @var boolean
     *
     * @ORM\Column(name="finished", type="boolean", options={"default":false})
     * @Expose()
     * @SerializedName("finished")
     */
    private $finished;



    public function __construct(){
        $this->finished = false;
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
     * @return boolean
     */
    public function isFinished()
    {
        return $this->finished;
    }

    /**
     * @param boolean $finished
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * Set dateLivraison
     *
     * @param \DateTime $dateLivraison
     *
     * @return Livraison
     */
    public function setDateLivraison($dateLivraison)
    {
        $this->dateLivraison = $dateLivraison;

        return $this;
    }

    /**
     * Get dateLivraison
     *
     * @return \DateTime
     */
    public function getDateLivraison()
    {
        return $this->dateLivraison;
    }


    /**
     * Set commande
     *
     * @param \AppBundle\Entity\Commande $commande
     *
     * @return Livraison
     */
    public function setCommande(\AppBundle\Entity\Commande $commande)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \AppBundle\Entity\Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set livreur
     *
     * @param \AppBundle\Entity\Livreur $livreur
     *
     * @return Livraison
     */
    public function setLivreur(\AppBundle\Entity\Livreur $livreur)
    {
        $this->livreur = $livreur;

        return $this;
    }

    /**
     * Get livreur
     *
     * @return \AppBundle\Entity\Livreur
     */
    public function getLivreur()
    {
        return $this->livreur;
    }
}
