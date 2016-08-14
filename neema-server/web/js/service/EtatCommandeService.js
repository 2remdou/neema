/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';


app.service('EtatCommandeService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            var self=this;

            var _etatCommandeService = Restangular.all('etat-commandes');

            this.post = function(etatCommande){
                _etatCommandeService.post(etatCommande).then(function(response){
                    var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('etatCommande.created',{etatCommande:response.data,alert:alert});
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.list = function(){
                _etatCommandeService.getList().then(function(response){
                    $rootScope.$broadcast('etatCommande.list',{etatCommandes:response});
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:error.data});
                   log(error);
                });
            };

            this.update = function(etatCommande){
                _etatCommandeService.customPUT(etatCommande,etatCommande.code).then(function(response){
                    $rootScope.$broadcast('etatCommande.updated', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };

            this.delete = function(etatCommande){
                _etatCommandeService.customDELETE(etatCommande.code).then(function(response){
                    $rootScope.$broadcast('etatCommande.deleted', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                })
            }
}]);