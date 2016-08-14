<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Expose,
    JMS\Serializer\Annotation\SerializedName,
    JMS\Serializer\Annotation\MaxDepth;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * EtatCommande
 *
 * @ORM\Table(name="etatCommande")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EtatCommandeRepository")
 * @UniqueEntity(fields={"code"}, message="Ce code existe déjà.")
 */
class EtatCommande
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @ORM\Id
     * @Expose()
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     * @Expose()
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity="Commande",mappedBy="etatCommande")
     * @Expose()
     */
    private $commandes;


    /**
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param string $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commandes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add commande
     *
     * @param \AppBundle\Entity\Commande $commande
     *
     * @return EtatCommande
     */
    public function addCommande(\AppBundle\Entity\Commande $commande)
    {
        $this->commandes[] = $commande;

        return $this;
    }

    /**
     * Remove commande
     *
     * @param \AppBundle\Entity\Commande $commande
     */
    public function removeCommande(\AppBundle\Entity\Commande $commande)
    {
        $this->commandes->removeElement($commande);
    }

    /**
     * Get commandes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommandes()
    {
        return $this->commandes;
    }
}
