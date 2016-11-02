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
                _commandeService.customPOST({from:from},'refresh-restaurant').then(function(response){
                    $rootScope.$broadcast('commande.list',{commandes:response});
                    callback(response);
                },function(error){
                    callbackError(error);
                    log(error)
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });

            };

            this.getHistoriqueByClientConnected = function(page,from,callback){
                _commandeService.customGET('historique/client-connected',{page:page,from:from}).then(function(response){
                    var commandes = response.commandes;
                    callback(commandes,response.paginator);
                },function(error){
                    log(error);
                });
            };

            this.refreshListByClient = function(from,callback,callbackError){
                _commandeService.customPOST({from:from},'refresh-client').then(function(response){
                    $rootScope.$broadcast('commande.list',{commandes:response});
                    callback(response);
                },function(error){
                    callbackError(error);
                    log(error)
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });

            };

            this.listByUserConnected = function(callback,page){
                _commandeService.customGET('userConnected',{page:page}).then(function(response){
                    $rootScope.$broadcast('commande.list',{commandes:response.commandes});
                    callback(response.commandes,response.currentPage);
                },function(error){
                   log(error)
                });

            };

            this.get = function(id,callback){
                _commandeService.customGET(id).then(function(response){
                    callback(response);
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.finishPreparation = function(detail,callback,callbackError){
                _commandeService.customPUT(null,'details/'+detail.id+'/finish').then(function(response){
                    var alert = response.data;
                    $rootScope.$broadcast('commande.detail.updated',{alert:alert});
                    callback(alert)
                },function(error){
                    callbackError(error.data.alert);
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
                if(commande.aLivrer){
                    _commandeService.customPUT(null,commande.id+'/give-to-livreur').then(function(response){
                        log(response);
                        callback(response.data.alert);
                    },function(error){
                        log(error);
                        $rootScope.$broadcast('show.message',{alert:error.data});
                    })
                }else{
                    _commandeService.customPUT({code:commande.code},commande.id+'/delivered').then(function(response){
                        var alert = response.data;
                        $rootScope.$broadcast('commande.delivered',{alert:alert});
                        callback(alert);
                    },function(error){
                        $rootScope.$broadcast('show.message',{alert:error.data});
                    });
                }
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
            };

            this.defineDureeRestante = function(commandes){
                function dureeRestante(dateOperation,dureeOperation){
                    var tempsEcoule = moment().format('x') - moment(dateOperation).format('x');
                    var dureeRestant = dureeOperation - tempsEcoule;
                    return dureeRestant<0?0:dureeRestant;

                };
                if(angular.isArray(commandes)){
                    angular.forEach(commandes,function(commande){
                        commande.dureeRestante = Math.round(dureeRestante(commande.dateCommande,commande.durationEstimative*1000));
                    })
                }else if(angular.isObject(commandes)){
                    commandes.dureeRestante = Math.round(dureeRestante(commandes.dateCommande,commandes.durationEstimative*1000));
                }

                return commandes;
            };
}]);