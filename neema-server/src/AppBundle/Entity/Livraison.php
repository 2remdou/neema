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
     * @ORM\Column(name="dateDebutLivraison", type="datetime", nullable=true)
     * @Expose()
     * @SerializedName("dateDebutLivraison")
     */
    private $dateDebutLivraison;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFinLivraison", type="datetime", nullable=true)
     * @Expose()
     * @SerializedName("dateFinLivraison")
     */
    private $dateFinLivraison;

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

    /**
     * @var float
     *
     * @ORM\Column(name="fraisCommande", type="float")
     * @Assert\NotBlank(message="Les frais de livraison sont obligatoire")
     * @Expose()
     */
    private $fraisCommande;




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

    /**
     * Set dateDebutLivraison
     *
     * @param \DateTime $dateDebutLivraison
     *
     * @return Livraison
     */
    public function setDateDebutLivraison($dateDebutLivraison)
    {
        $this->dateDebutLivraison = $dateDebutLivraison;

        return $this;
    }

    /**
     * Get dateDebutLivraison
     *
     * @return \DateTime
     */
    public function getDateDebutLivraison()
    {
        return $this->dateDebutLivraison;
    }

    /**
     * Set dateFinLivraison
     *
     * @param \DateTime $dateFinLivraison
     *
     * @return Livraison
     */
    public function setDateFinLivraison($dateFinLivraison)
    {
        $this->dateFinLivraison = $dateFinLivraison;

        return $this;
    }

    /**
     * Get dateFinLivraison
     *
     * @return \DateTime
     */
    public function getDateFinLivraison()
    {
        return $this->dateFinLivraison;
    }

    /**
     * Get finished
     *
     * @return boolean
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * Set fraisCommande
     *
     * @param float $fraisCommande
     *
     * @return Livraison
     */
    public function setFraisCommande($fraisCommande)
    {
        $this->fraisCommande = $fraisCommande;

        return $this;
    }

    /**
     * Get fraisCommande
     *
     * @return float
     */
    public function getFraisCommande()
    {
        return $this->fraisCommande;
    }
}
