/**
 * Created by touremamadou on 28/09/2016.
 */

'use strict';


app.service('NumeroAutoriseService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            var self=this;

            var _numeroAutoriseService = Restangular.all('numero-autorises');

            this.post = function(numeroAutorise,callback,callbackError){
                _numeroAutoriseService.post(numeroAutorise).then(function(response){
                    callback(response);
                },function(error){
                    callbackError(error.data);
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.list = function(callback,callbackError){
                _numeroAutoriseService.getList().then(function(response){
                    callback(response);
                },function(error){
                    callbackError(error.data);
                   log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };
}]);