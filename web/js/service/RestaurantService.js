/**
 * Created by touremamadou on 08/05/2016.
 */

'use strict';

app.service('RestaurantService',
    ['$rootScope','Restangular','$q',
        function($rootScope,Restangular,$q){

            var self=this;

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



            this.list = function(){
                var deffered = $q.defer();
                if(self.restaurants.length!==0){
                    deffered.resolve(self.restaurants);
                }else{
                    _restaurantService.getList().then(function(response){
                        self.restaurants=response;
                        deffered.resolve(response);
                        $rootScope.$broadcast('restaurant.list',{restaurants:response});
                    },function(error){
                        deffered.reject(error);
                        $rootScope.$broadcast('show.message',{alert:error.data});
                        log(error);
                    });
                }
                return deffered.promise;
            };

            this.getRestaurants = function(){
                return self.restaurants;
            };

            this.get = function(id){
                return _restaurantService.get(id);
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