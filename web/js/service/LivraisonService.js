/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';


app.service('LivraisonService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            var that=this;

            var _livraisonService = Restangular.all('livraisons');

            this.getCurrentLivrisonByLivreurConnected = function(){
                _livraisonService.one('current').get().then(function(response){
                    $rootScope.$broadcast('livraison.current',{livraison:response});
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:error.data});
                    log(error);
                });
            };

            /**
             * retourne la duree restante d'une livraison fournit en parametre
             * la dureeRestante = durée totale de livraison(commande.durationLivraison) - temps ecoulé( l'heure actuelle -  l'heure à laquelle la commande a été donné au livreur)
             * @param livraison
             * @returns int
             */
            this.getDureeRestanteLivraison = function(livraison){
                if(!livraison.commande.dateGiveLivreur){
                    var alert = {textAlert:'La date de debut de livraison est inexistante',typeAlert:'danger'};
                    $rootScope.$broadcast('show.message',{alert:alert});
                    return;
                }
                var dureeEcoule = new Date().getTime()-new Date(livraison.commande.dateGiveLivreur).getTime();
                //conversion en seconde
                dureeEcoule = Math.round(dureeEcoule/1000);
                var dureeRestante = livraison.commande.durationLivraison - dureeEcoule;

                return dureeRestante<=0?0:dureeRestante;
            };


            /**
             * retourne la duree restante de préparation des plats de la commande
             * dureeRestantePreparation = somme des durées de préparation des plats - le temps ecoulé(l'heure actuelle -  l'heure à laquelle la commande a été passé par le client)
             * @param livraison
             */
            this.getDureeRestantePreparation = function(livraison){
                var dureeEcoule = new Date().getTime()-new Date(livraison.commande.dateCommande).getTime();
                //conversion en seconde
                dureeEcoule = Math.round(dureeEcoule/1000);
                var sommeDureePreparation = 0;
                angular.forEach(livraison.commande.detailCommandes,function(detailCommande){
                    sommeDureePreparation +=detailCommande.plat.dureePreparation;
                });

                var dureeRestante = sommeDureePreparation - dureeEcoule;

                return dureeRestante<=0?0:dureeRestante;


            };



}]);