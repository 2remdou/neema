<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Expose,
    JMS\Serializer\Annotation\SerializedName,
    JMS\Serializer\Annotation\MaxDepth;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * DetailCommande
 *
 * @ORM\Table(name="detailCommande")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DetailCommandeRepository")
 * @ExclusionPolicy("all")
 * @UniqueEntity(fields={"commande","plat"}, message="Ce plat existe déjà dans cette commande.")
 */
class DetailCommande
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
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer")
     * @Expose()
     */
    private $quantite;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float")
     * @Expose()
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity="Commande",inversedBy="detailCommandes")
     * @ORM\JoinColumn(nullable=false)
     * @Expose()
     * @Assert\NotNull(message="La commande est obligatoire")
     */
    private $commande;
    /**
     * @ORM\ManyToOne(targetEntity="Plat")
     * @ORM\JoinColumn(nullable=false)
     * @Expose()
     * @Assert\NotNull(message="Le plat est obligatoire")
     */
    private $plat;



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
     * Set quantite
     *
     * @param integer $quantite
     *
     * @return DetailCommande
     */
    public function setQuantite($quantite = 1)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return int
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set prix
     *
     * @param integer $prix
     *
     * @return DetailCommande
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return int
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set commande
     *
     * @param \AppBundle\Entity\Commande $commande
     *
     * @return DetailCommande
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
     * Set plat
     *
     * @param \AppBundle\Entity\Plat $plat
     *
     * @return DetailCommande
     */
    public function setPlat(\AppBundle\Entity\Plat $plat)
    {
        $this->plat = $plat;
        $this->prix = $plat->getPrix();

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
}
