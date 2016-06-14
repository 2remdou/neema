/**
 * Created by touremamadou on 07/05/2016.
 */

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

            this.list = function(){
                _commandeService.getList().then(function(response){
                    $rootScope.$broadcast('commande.list',{commandes:response.commandes});
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:error.data});
                   log(error);
                });
            };

            this.listByRestaurant = function(){
                _commandeService.one('restaurantConnected').customGET().then(function(response){
                    $rootScope.$broadcast('commande.list',{commandes:response});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                    log(error)
                });

            };

            this.listByUserConnected = function(){
                _commandeService.one('userConnected').getList().then(function(response){
                    $rootScope.$broadcast('commande.list',{commandes:response});
                },function(error){
                   log(error)
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