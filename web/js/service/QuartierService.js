/**
 * Created by touremamadou on 07/05/2016.
 */

app.service('QuartierService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            var self=this;

            var _quartierService = Restangular.all('quartiers');

            this.post = function(quartier){
                quartier.commune = extractId(quartier.commune);
                _quartierService.post(quartier).then(function(response){
                    var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('quartier.created',{quartier:response.data,alert:alert});
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.list = function(){
                _quartierService.getList().then(function(response){
                    $rootScope.$broadcast('quartier.list',{quartiers:response});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                    log(error);
                });
            };

            this.update = function(quartier){
                quartier.commune = extractId(quartier.commune);
                quartier.put().then(function(response){
                    $rootScope.$broadcast('quartier.updated', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };

            this.delete = function(quartier){
                quartier.remove().then(function(response){
                    $rootScope.$broadcast('quartier.deleted', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                })
            }
}]);