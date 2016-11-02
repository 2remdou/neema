/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';


app.service('LivreurService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            var self=this;

            var _livreurService = Restangular.all('livreurs');

            this.post = function(livreur){
                _livreurService.post(livreur).then(function(response){
                    var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('livreur.created',{livreur:response.data,alert:alert});
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.list = function(callback){
                _livreurService.getList().then(function(response){
                    log(response);
                    $rootScope.$broadcast('livreur.list',{livreurs:response});
                    if(typeof callback==='function')
                        callback(response);
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:error.data});
                   log(error);
                });
            };

            this.update = function(livreur){
                livreur.put().then(function(response){
                    $rootScope.$broadcast('livreur.updated', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };

            this.delete = function(livreur){
                livreur.remove().then(function(response){
                    $rootScope.$broadcast('livreur.deleted', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                })
            }
}]);