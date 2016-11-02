/**
 * Created by touremamadou on 24/07/2016.
 */

app
    .controller('RestaurantController',
        ['$scope','RestaurantService','SpinnerService',
        'PopupService','$stateParams','$state','PaginatorService',
        function($scope,RestaurantService,SpinnerService,
        PopupService,$stateParams,$state,PaginatorService){

        $scope.restaurants=[]; // pour le slider des images, il ne marche pas sans lui fournir un tableau
        if(!$stateParams.idRestaurant) {
            $state.go('home');
            return;
        }

        SpinnerService.start();
        PaginatorService.getRestaurant($stateParams.idRestaurant,function(restaurant){
            if(restaurant){
                $scope.restaurants.push(restaurant);
                SpinnerService.stop();
            }else{
                RestaurantService.get($stateParams.idRestaurant,function(restaurant){
                    $scope.restaurants.push(restaurant);
                    SpinnerService.stop();
                });
            }
        })


    }])
    .controller('ListRestaurantController',
        ['$scope','RestaurantService','SpinnerService','PaginatorService',
        'PopupService','$timeout','$ionicScrollDelegate','TIME_FOR_TRY_TO_LOAD',
        function($scope,RestaurantService,SpinnerService,PaginatorService,
        PopupService,$timeout,$ionicScrollDelegate,TIME_FOR_TRY_TO_LOAD){

            var tempsEcoule = new Date().getTime()- new Date(PaginatorService.getListRestaurants().dateToLastLoad).getTime();        
            if( tempsEcoule  >= TIME_FOR_TRY_TO_LOAD){
                PaginatorService.remove('listRestaurants');
            }

            $scope.restaurants = PaginatorService.getListRestaurants().restaurants;
            $scope.canBeLoad = true;

            $scope.load = function(callback){
                SpinnerService.start();
                RestaurantService.list(PaginatorService.getListRestaurants().paginator.nextPage,
                    function(restaurants,paginator){
                        PaginatorService.addRestaurant({restaurants:restaurants,paginator:paginator});
                        if(restaurants.length !== 0){
                            $scope.restaurants= PaginatorService.getListRestaurants().restaurants;
                        }
                        $scope.canBeLoad = PaginatorService.getListRestaurants().canBeLoad;
                        SpinnerService.stop();
                        if(typeof callback === 'function')
                            callback();
                });
            };

            if($scope.restaurants.length===0){
                $scope.load();
            }


            $scope.search = function(searchKey){ // utilisé dans modalListLieuLivraison.html
                $scope.restaurants= _.filter(PaginatorService.getListRestaurants().restaurants,function(restaurant){
                                            return _.toLower(restaurant.nom).search(_.toLower(searchKey))!=-1 ||
                                                    _.toLower(restaurant.quartier.nom).search(_.toLower(searchKey))!=-1 ||
                                                    _.toLower(restaurant.quartier.commune.nom).search(_.toLower(searchKey))!=-1
                                            ;
                                        });
            };

            $scope.$on('not.found',function(event,args){
                $scope.notFound(event,args);
            });

            $scope.onInfiniteLoad = function(){
                $scope.load(function(){
                    var posTop = $ionicScrollDelegate.$getByHandle('mainScroll').getScrollPosition().top;
                    $ionicScrollDelegate.$getByHandle('mainScroll').scrollTo(0,posTop-200);

                    $timeout(function() {
                        $scope.$broadcast('scroll.infiniteScrollComplete');
                    },1000);
                });
            };




    }])
    .controller('PlatByRestaurantController',
    ['$scope','PlatService','SpinnerService','$stateParams','$state',
    'PaginatorService','$ionicScrollDelegate','$timeout',
    function($scope,PlatService,SpinnerService,$stateParams,$state,
    PaginatorService,$ionicScrollDelegate,$timeout){

        if(!$stateParams.idRestaurant) {
            $state.go('home');
            return;
        }
        $scope.plats = [];
        $scope.canBeLoad = true;

        $scope.load = function(callback){
            SpinnerService.start();
            PlatService.listOnMenuByRestaurant($stateParams.idRestaurant,
            PaginatorService.getListPlatByRestaurant().paginator.nextPage,
            function(plats,paginator){
                PaginatorService.addListPlatByRestaurant({plats:plats,paginator:paginator});
                if(plats.length !== 0){
                    $scope.plats = _.concat($scope.plats,plats);
                }
                $scope.canBeLoad = PaginatorService.getListPlatByRestaurant().canBeLoad;
                SpinnerService.stop();
                if(typeof callback === 'function')
                    callback();
            }); 
        }

        $scope.load();

        $scope.onInfiniteLoad = function(){
            $scope.load(function(){
                var posTop = $ionicScrollDelegate.$getByHandle('mainScroll').getScrollPosition().top;
                $ionicScrollDelegate.$getByHandle('mainScroll').scrollTo(0,posTop-200);

                $timeout(function() {
                    $scope.$broadcast('scroll.infiniteScrollComplete');
                },1000);
            });
        };

         $scope.$on('not.found',function(event,args){
            $scope.$emit('show.message',{
                alert:args.alert,
                callback:function(){
                    $state.go('restaurant',{idRestaurant:$stateParams.idRestaurant});
                }
            });
         });

}
])
;

