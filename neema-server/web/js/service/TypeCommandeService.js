/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';


app.service('TypeCommandeService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            var self=this;

            var _typeCommandeService = Restangular.all('type-commandes');

            this.post = function(typeCommande){
                _typeCommandeService.post(typeCommande).then(function(response){
                    var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('typeCommande.created',{typeCommande:response.data,alert:alert});
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.list = function(callback){
                _typeCommandeService.getList().then(function(response){
                    callback(response);
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:error.data});
                   log(error);
                });
            };

            this.update = function(typeCommande){
                _typeCommandeService.customPUT(typeCommande,typeCommande.code).then(function(response){
                    $rootScope.$broadcast('typeCommande.updated', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };

            this.delete = function(typeCommande){
                _typeCommandeService.customDELETE(typeCommande.code).then(function(response){
                    $rootScope.$broadcast('typeCommande.deleted', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                })
            }
}]);