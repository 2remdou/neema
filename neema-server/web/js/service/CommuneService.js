/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';


app.service('CommuneService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            var self=this;

            var _communeService = Restangular.all('communes');

            this.post = function(commune){
                _communeService.post(commune).then(function(response){
                    var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('commune.created',{commune:response.data,alert:alert});
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.list = function(callback){
                _communeService.getList().then(function(response){
                    $rootScope.$broadcast('commune.list',{communes:response});
                    if(typeof callback === 'function')
                        callback(response);
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:error.data});
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