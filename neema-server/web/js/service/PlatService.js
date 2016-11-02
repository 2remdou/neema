/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';


app.service('PlatService',
    ['$rootScope','Restangular','$q',
        function($rootScope,Restangular,$q){

            var self=this;

            this.plats={onMenu:[],other:[],typePlat:'onMenu'};

            this.currentPage = {onMenu:0,byRestaurant:0}; //la derniere page chargée


            var _platService = Restangular.all('plats');

            this.post = function(plat,callback){
                _platService.post(plat).then(function(response){
                    var idPlat = response.data.idPlat;
                    $rootScope.$broadcast('plat.created',{idPlat:idPlat});
                    callback(idPlat);
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.postImage = function(formData,idPlat,callback){
                log('formData');
                log(formData);
                _platService
                    //.withHttpConfig({transformRequest: angular.identity})
                    .customPOST(formData, idPlat+'/image', undefined).then(function(response){
                        log(response);
                        callback(response);
                    },function(error){
                        log(error);
                        $rootScope.$broadcast('show.message',{alert:error.data});
                    });
            };

            this.get = function(id){
                return _platService.get(id);
            };

            this.getWithCallback = function(id,callback){
                 _platService.get(id).then(function(response){
                     var plat = response;
                     plat.dureePreparation = Math.floor(plat.dureePreparation/60);
                     callback(plat);
                 },function(error){
                     var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                     $rootScope.$broadcast('show.message',{alert:alert});
                 });
            };


            this.list = function(){
                _platService.getList().then(function(response){
                    $rootScope.$broadcast('plat.list',{plats:response});
                },function(error){
                    log(error);
                });
            };

            this.listOnMenu = function(page,callback,callbackError){

                _platService.one('on-menu').customGET(null,{page:page}).then(function(response){
                    $rootScope.$broadcast('plat.list',{plats:response.plats});
                    if(typeof  callback === 'function')
                        callback(response.plats,response.paginator);
                },function(error){
                    log(error);
                    if(typeof  callbackError === 'function')
                        callbackError(error);
                });
            };

            this.search = function(key,callback){
                _platService.customGET('search',{key:key}).then(function(response){
                    var plats = _.uniqBy(response.plats,'id');
                    callback(plats);
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                    log(error);
                });

            };

            this.listOnMenuByRestaurant = function(idRestaurant,page,callback){
                _platService.one('by-restaurant/'+idRestaurant).customGET(null,{page:page}).then(function(response){
                    $rootScope.$broadcast('plat.list',{plats:response});
                    callback(response.plats,response.paginator);
                },function(error){
                    log(error);
                });
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

            this.listByRestaurantByUserConnected = function(callback){
                _platService.one('restaurant/userConnected').getList().then(function(response){
                    $rootScope.$broadcast('plat.list',{plats:response});
                    if(typeof  callback === 'function')
                    callback(response);
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                    log(error);
                });
            };

            this.updateMenu = function(menu,callback){
                Restangular.one('updateMenu').customPUT({plats:menu}).then(function(response){
                    var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('menu.updated',{alert:alert,fail:response.data.fail});
                    if(typeof  callback === 'function')
                        callback({alert:alert,fail:response.data.fail})
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:alert});
                    log(error);
                });
            };

            this.update = function(plat,callback){
                delete plat.imagePlat;
                delete plat.restaurant;
                plat.put().then(function(response){
                    var alert = response.data;
                    $rootScope.$broadcast('plat.updated', {alert:alert});
                    if(typeof  callback === 'function')
                        callback(alert);
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };


        }]);