/**
 * Created by touremamadou on 08/05/2016.
 */

'use strict';

app.service('LieuLivraisonService',
    ['$rootScope','Restangular','$q',
        function($rootScope,Restangular,$q){

            var that=this;

            this.lieuLivraisons = [];

            var _lieuLivraisonService = Restangular.all('lieu-livraisons');

            this.post = function(lieuLivraison,callback){
                _lieuLivraisonService.post(lieuLivraison).then(function(response){
                    ///var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('lieuLivraison.created',{lieuLivraison:response.data.lieuLivraison});
                    callback(response.data.alert);
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };



            this.list = function(callback){
                _lieuLivraisonService.getList().then(function(response){
                    $rootScope.$broadcast('lieuLivraison.list',{lieuLivraisons:response});
                    callback(response);
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.get = function(id,callback){
                _lieuLivraisonService.get(id).then(function(response){
                    callback(response);
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.update = function(lieuLivraison,callback){
                lieuLivraison.put().then(function(response){
                    $rootScope.$broadcast('lieuLivraison.updated', {lieuLivraison:response.data.lieuLivraison})
                    callback(response.data.alert);
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };

            this.delete = function(lieuLivraison,callback){
                lieuLivraison.remove().then(function(response){
                    $rootScope.$broadcast('lieuLivraison.deleted', {alert:response.data})
                    callback(response.data.alert);
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                })
            };
}]);