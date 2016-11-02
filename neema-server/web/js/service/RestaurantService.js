/**
 * Created by touremamadou on 08/05/2016.
 */

'use strict';

app.service('RestaurantService',
    ['$rootScope','Restangular','$q',
        function($rootScope,Restangular,$q){

            var that=this;

            this.restaurants = [];

            var _restaurantService = Restangular.all('restaurants');

            this.post = function(restaurant){
                restaurant.quartier = extractId(restaurant.quartier);
                _restaurantService.post(restaurant).then(function(response){
                    ///var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('restaurant.created',{restaurant:response.data.restaurant});
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };



            this.list = function(page,callback,callbackError){
                _restaurantService.customGET(null,{page:page}).then(function(response){
                    $rootScope.$broadcast('restaurant.list',{restaurants:response});
                    if(typeof callback == 'function')
                        callback(response.restaurants,response.paginator);
                },function(error){
                    if(typeof callbackError == 'function') callbackError(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.getRestaurants = function(){
                return that.restaurants;
            };

            this.get = function(id,callback){
                _restaurantService.get(id).then(function(response){
                    if(typeof callback === 'function')
                        callback(response);
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };

            this.update = function(restaurant){
                restaurant.quartier = extractId(restaurant.quartier);
                restaurant.put().then(function(response){
                    $rootScope.$broadcast('restaurant.updated', {restaurant:response.data.restaurant})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                });
            };

            this.delete = function(restaurant){
                restaurant.remove().then(function(response){
                    $rootScope.$broadcast('restaurant.deleted', {alert:response.data})
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message', {alert:error.data});
                })
            };

            this.deleteImage = function(image){
              _restaurantService.customDELETE('image/'+image.id).then(function(response){
                  $rootScope.$broadcast('restaurant.image.deleted', {alert:response.data,image:image})
              },function(error){
                  log(error);
              });
            };
}]);