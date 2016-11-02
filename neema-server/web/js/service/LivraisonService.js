/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';


app.service('LivraisonService',
    ['$rootScope','Restangular','CommandeService',
        function($rootScope,Restangular,CommandeService){

            var that=this;

            var _livraisonService = Restangular.all('livraisons');

            this.getLivrisonEncoursByLivreurConnected = function(callback,callbackError){
                _livraisonService.one('current').get().then(function(response){
                    var livraison = response;
                    $rootScope.$broadcast('livraison.current',{livraison:livraison});
                    callback(livraison);
                },function(error){
                    callbackError(error.data.alert);
                });
            };

            function calculDureeLivraison(livraisons){
                if(!_.isArray(livraisons)){
                    var dureeLivraison = moment(livraisons.dateFinLivraison).diff(moment(livraisons.dateDebutLivraison));
                    livraisons.dureeLivraison = humanizeDuration(dureeLivraison,{ language: 'fr',round:true });
                    return livraisons;
                }
                angular.forEach(livraisons,function(livraison){
                    var dureeLivraison = moment(livraison.dateFinLivraison).diff(moment(livraison.dateDebutLivraison));
                    livraison.dureeLivraison = humanizeDuration(dureeLivraison,{ language: 'fr',round:true });
                });
                return livraisons;
            }

            this.getHistoriqueByLivreurConnected = function(callback){
                _livraisonService.one('historique/livreur-connected').get().then(function(response){
                    var livraisons = response;
                    livraisons = calculDureeLivraison(livraisons);
                    callback(livraisons);
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:error.data});
                    log(error);
                });
            };

            this.livrer = function(livraison,callback){
                _livraisonService.customPUT(null,'commandes/'+livraison.commande.id+'/finished').then(function(response){
                    callback(response.data.alert)
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:error.data});
                    log(error);
                });
            };


            this.get = function(idLivraison,callback){
                _livraisonService.customGET(idLivraison).then(function(response){
                    var livraison = calculDureeLivraison(response);
                    callback(livraison);
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
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