/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';


app.service('PlatService',
    ['$rootScope','Restangular','$q',
        function($rootScope,Restangular,$q){

            var self=this;

            var _platService = Restangular.all('plats');

            this.post = function(plat){
                _platService.post(plat).then(function(response){
                    $rootScope.$broadcast('plat.created',{plat:response.data.plat});
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.get = function(id){
                return _platService.get(id);
            };


            this.list = function(){
                _platService.getList().then(function(response){
                    $rootScope.$broadcast('plat.list',{plats:response});
                },function(error){
                    log(error);
                });
            };

            this.listOnMenu = function(page){
                var deffered = $q.defer();
                _platService.one('onMenu').getList(null,{page:page}).then(function(response){
                    $rootScope.$broadcast('plat.list',{plats:response});
                    deffered.resolve(response);
                },function(error){
                    deffered.reject(error);
                    log(error);
                });
                return deffered.promise;
            };

            this.listByRestaurant = function(restaurant){
                _platService.customGET(null,'restaurant/'+restaurant.id).then(function(response){
                    //_platService.getList().then(function(response){
                    var plats = Restangular.restangularizeCollection(_platService,response.plats);
                    $rootScope.$broadcast('plat.list',{plats:plats});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                    log(error);
                });
            };

            this.listByRestaurantByUserConnected = function(){
                _platService.one('restaurant/userConnected').getList().then(function(response){
                    $rootScope.$broadcast('plat.list',{plats:response});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                    log(error);
                });
            };

            this.updateMenu = function(menu){
                Restangular.one('updateMenu').customPUT({plats:menu}).then(function(response){
                    var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('menu.updated',{alert:alert,fail:response.data.fail});
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:alert});
                    log(error);
                });
            };

            this.update = function(plat){
                delete plat.image;
                delete plat.restaurant;
                plat.put().then(function(response){
                    $rootScope.$broadcast('plat.updated', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };


        }]);