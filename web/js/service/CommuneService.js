/**
 * Created by touremamadou on 07/05/2016.
 */

app.service('CommuneService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            var self=this;

            var _communeService = Restangular.all('communes');

            this.post = function(commune){
                _communeService.post(commune).then(function(response){
                    $rootScope.$broadcast('commune.created',{commune:response.data});
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.list = function(){
                _communeService.getList().then(function(response){
                    $rootScope.$broadcast('commune.list',{communes:response});
                },function(error){
                   log(error);
                });
            };

            this.update = function(commune){
                commune.put().then(function(response){
                    $rootScope.$broadcast('commune.updated', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };

            this.delete = function(commune){
                commune.remove().then(function(response){
                    $rootScope.$broadcast('commune.deleted', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                })
            }
}]);