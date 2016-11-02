/**
 * Created by touremamadou on 24/07/2016.
 */

app
    .controller('RestaurantController',
        ['$scope','RestaurantService','SpinnerService',
        'PopupService','$stateParams',
        function($scope,RestaurantService,SpinnerService,
        PopupService,$stateParams){

        $scope.restaurants=[]; // pour le slider des images, il ne marche pas sans lui fournir un tableau
        if(!$stateParams.idRestaurant) {
            $state.go('home');
            return;
        }
        SpinnerService.start();
        RestaurantService.get($stateParams.idRestaurant,function(restaurant){
            $scope.restaurants.push(restaurant);
            SpinnerService.stop();
        });


    }])
    .controller('ListRestaurantController',
        ['$scope','RestaurantService','SpinnerService','INTERVAL_TIME_FOR_TRY_AGAIN_LOADING',
        'PopupService',
        function($scope,RestaurantService,SpinnerService,INTERVAL_TIME_FOR_TRY_AGAIN_LOADING,
        PopupService){

            var firstLoading = true;
            var timeLastLoading =new Date().getTime();
            $scope.restaurants = [];

            $scope.loadRestaurant = function(){
                SpinnerService.start();
                RestaurantService.list(function(restaurants){
                    timeLastLoading = new Date().getTime();
                    $scope.restaurantsOriginal = restaurants;
                    $scope.restaurants = restaurants;
                    SpinnerService.stop();
                },function(){
                    
                });
            };

            if(RestaurantService.getRestaurants().length===0){
                $scope.loadRestaurant();
            }else{
                if(new Date().getTime() - timeLastLoading >= INTERVAL_TIME_FOR_TRY_AGAIN_LOADING){
                    $scope.loadRestaurant();
                }else{
                    $scope.restaurantsOriginal = RestaurantService.getRestaurants();
                }

            }

            $scope.search = function(searchKey){ // utilisé dans modalListLieuLivraison.html
                $scope.restaurants= _.filter($scope.restaurantsOriginal,function(restaurant){
                                            return _.toLower(restaurant.nom).search(_.toLower(searchKey))!=-1 ||
                                                    _.toLower(restaurant.quartier.nom).search(_.toLower(searchKey))!=-1 ||
                                                    _.toLower(restaurant.quartier.commune.nom).search(_.toLower(searchKey))!=-1
                                            ;
                                        });
            };

            //***************LISTENER*******************

    }])
;

