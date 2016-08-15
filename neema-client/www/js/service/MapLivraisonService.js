/**
 * Created by touremamadou on 23/05/2016.
 */
'use strict';

app.service('MapLivraisonService',[
    '$rootScope','$q',
    function($rootScope,$q){
        var that = this;
        var livraison = null;

        this.getLivraison = function() {
            return livraison;  
        };

        this.setLivraison = function(_livraison){
            livraison=_livraison;
        };

    }

]);