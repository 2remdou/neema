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

/*
            this.getPlats = function(){
                log(self.plats);
                if(self.plats.typePlat==='onMenu')
                    return self.getPlatsOnMenu();
                else if(self.plats.typePlat==='byRestaurant')
                    return self.getPlatsOther();
            };

            this.getPlatsOnMenu = function(){
                return self.plats.onMenu;
            };

            this.getPlatsOther = function(){
                return self.plats.other;
            };

            this.resetPlatsOther = function(){
                self.plats.other = [];
            };

            this.addPlatsOnMenu = function(newPlats){
                self.plats.typePlat = 'onMenu';
                self.plats.onMenu.length===0?self.plats.onMenu=newPlats:self.plats.onMenu.concat(newPlats);
            };

            this.addPlatsOther = function(newPlats){
                self.plats.typePlat = 'other';
                //self.plats.other=newPlats;
                self.plats.other.length===0?self.plats.other=newPlats:self.plats.other.concat(newPlats);
            };

*/

            this.list = function(){
                _platService.getList().then(function(response){
                    $rootScope.$broadcast('plat.list',{plats:response});
                },function(error){
                    log(error);
                });
            };

            this.listOnMenu = function(page){
                var deffered = $q.defer();

                _platService.one('onMenu').customGET(null,{page:page}).then(function(response){
                    $rootScope.$broadcast('plat.list',{plats:response.plats});
                    deffered.resolve({plats:response.plats,currentPage:response.currentPage,pageCount:response.pageCount});
                },function(error){
                    deffered.reject(error);
                    log(error);
                });

                return deffered.promise;
            };

            this.listByRestaurantWithPaginator = function(idRestaurant,page){
                var deffered = $q.defer();
                _platService.one('by-restaurant/'+idRestaurant).customGET(null,{page:page}).then(function(response){
                    $rootScope.$broadcast('plat.list',{plats:response});
                    deffered.resolve({plats:response.plats,currentPage:response.currentPage,pageCount:response.pageCount});
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

            this.listByRestaurantByUserConnected = function(callback){
                _platService.one('restaurant/userConnected').getList().then(function(response){
                    $rootScope.$broadcast('plat.list',{plats:response});
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
                    callback({alert:alert,fail:response.data.fail})
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:alert});
                    log(error);
                });
            };

            this.update = function(plat){
                delete plat.imagePlat;
                delete plat.restaurant;
                plat.put().then(function(response){
                    $rootScope.$broadcast('plat.updated', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };


        }]);