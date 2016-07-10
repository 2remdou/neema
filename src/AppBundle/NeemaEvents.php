<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 22/05/2016
 * Time: 06:28
 */

namespace AppBundle;


final class NeemaEvents
{

    //*********************COMMANDE*****************************

    /**
     * après de l'enregistrement d'une commande
     */
    const COMMANDE_ENREGISTRE = 'commande.enregistre';

    /**
     * Lorsque le restaurant remet les plats au livreur
     */
    const COMMANDE_GIVE_LIVREUR = 'commande.give.livreur';

    /**
     * Lorsque le restaurant marque un plat dans une commande comme termine
     */
    const DETAIL_COMMANDE_FINISHED = 'detail.commande.finished';

    /**
     * Lorsque la commande a été livré au client
     */
    const COMMANDE_LIVREE = 'commande.delivered';

    //*********************LIVREUR*****************************

    /**
     * Lorsqu'un livreur devient free
     */
    const LIVREUR_IS_FREE = 'livreur.is.free';

    //*********************LIVRAISON*****************************

    /**
     * Lorsqu'un qu'une livraison est marquée finished
     */
    const LIVRAISON_IS_FINISHED = 'livraison.is.finished';
}