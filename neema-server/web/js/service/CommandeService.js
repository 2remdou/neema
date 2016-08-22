/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';

app.service('CommandeService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            var self=this;

            var _commandeService = Restangular.all('commandes');

            this.post = function(commande){
                _commandeService.post(commande).then(function(response){
                    var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('commande.created',{commande:response.data,alert:alert});
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.list = function(callback){
                _commandeService.getList().then(function(response){
                    $rootScope.$broadcast('commande.list',{commandes:response.commandes});
                    callback(response.commandes);
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:error.data});
                   log(error);
                });
            };

            this.listByRestaurant = function(callback){
                _commandeService.one('restaurantConnected').customGET().then(function(response){
                    $rootScope.$broadcast('commande.list',{commandes:response});
                    callback(response);
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });

            };
            this.refreshListByRestaurant = function(from,callback,callbackError){
                _commandeService.customPOST({from:from},'refresh').then(function(response){
                    $rootScope.$broadcast('commande.list',{commandes:response});
                    callback(response);
                },function(error){
                    callbackError(error);
                    log(error)
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });

            };

            this.listByUserConnected = function(){
                _commandeService.one('userConnected').getList().then(function(response){
                    $rootScope.$broadcast('commande.list',{commandes:response});
                },function(error){
                   log(error)
                });

            };

            this.finishPreparation = function(detail,callback){
                _commandeService.customPUT(null,'details/'+detail.id+'/finish').then(function(response){
                    var alert = response.data;
                    $rootScope.$broadcast('commande.detail.updated',{alert:alert});
                    callback(alert)
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.giveToLivreur = function(commande){
                _commandeService.customPUT(null,commande.id+'/give-livreur').then(function(response){
                    $rootScope.$broadcast('commande.give.livreur',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.delivered = function(commande,callback){
                _commandeService.customPUT({code:commande.code},commande.id+'/delivered').then(function(response){
                    var alert = response.data;
                    $rootScope.$broadcast('commande.delivered',{alert:alert});
                    callback(alert);
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.update = function(commande){
                commande.put().then(function(response){
                    $rootScope.$broadcast('commande.updated', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };

            this.delete = function(commande){
                commande.remove().then(function(response){
                    $rootScope.$broadcast('commande.deleted', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                })
            }
}]);