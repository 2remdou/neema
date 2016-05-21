<?php

namespace AppBundle\Entity;

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
     * @ORM\Column(name="dateLivraison", type="datetime")
     * @Expose()
     * @SerializedName("dateLivraison")
     */
    private $dateLivraison;

    /**
     * @ORM\ManyToOne(targetEntity="Commande")
     * @ORM\JoinColumn(nullable=false)
     * @Expose()
     * @Assert\NotNull(message="La commande est obligatoire")
     */
    private $commande;

    /**
     * @ORM\ManyToOne(targetEntity="Livreur")
     * @ORM\JoinColumn(nullable=false)
     * @Expose()
     * @Assert\NotNull(message="Le livreur est obligatoire")
     */
    private $livreur;



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
