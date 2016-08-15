/**
 * Created by touremamadou on 24/07/2016.
 */

app
    .controller('RestaurantController',
        ['$scope','RestaurantService','SpinnerService','INTERVAL_TIME_FOR_TRY_AGAIN_LOADING',
        'PopupService',
        function($scope,RestaurantService,SpinnerService,INTERVAL_TIME_FOR_TRY_AGAIN_LOADING,
        PopupService){

            var firstLoading = true;
            var timeLastLoading =new Date().getTime();
            $scope.restaurants = [];

            $scope.loadRestaurant = function(){
                SpinnerService.start();
                RestaurantService.list().then(function(restaurants){
                    timeLastLoading = new Date().getTime();
                    $scope.restaurants = restaurants;
                    SpinnerService.stop();
                });
            };

            if(RestaurantService.getRestaurants().length===0){
                $scope.loadRestaurant();
            }else{
                if(new Date().getTime() - timeLastLoading >= INTERVAL_TIME_FOR_TRY_AGAIN_LOADING){
                    $scope.loadRestaurant();
                }else{
                    $scope.restaurants = RestaurantService.getRestaurants();
                }

            }
            //***************LISTENER*******************

    }])
    .controller('PlatByRestaurantController',
    ['$scope','PlatService','PlatDataService','SpinnerService','INTERVAL_TIME_FOR_TRY_AGAIN_LOADING',
    '$stateParams','$state','PopupService','FirstLoad',
    function($scope,PlatService,PlatDataService,SpinnerService,INTERVAL_TIME_FOR_TRY_AGAIN_LOADING,
    $stateParams,$state,PopupService,FirstLoad){

        if(!$stateParams.idRestaurant) {
            $state.go('home');
            return;
        }
        var loading = false;
        var allPlatAreLoaded=false;
        var page=0;
        PlatDataService.data.type='other';
        $scope.plats = [];
        $scope.loadMore = function(){
            if(loading || allPlatAreLoaded) return;
            SpinnerService.start();
            PlatService.listByRestaurantWithPaginator($stateParams.idRestaurant,++page).then(function(response){
                if(response.plats.length === 0){
                    if(page==1){
                        $scope.plats=[];
                        var popup = {
                            title:'Aucun plat',
                            message:'Aucun plat au menu de ce restaurant',
                            cssClass:'popupInfo'
                        };
                        PopupService.show(popup).then(function(){
                            $state.go('restaurants');
                            return;
                        });
                    }
                    page=response.pageCount;
                    allPlatAreLoaded = true;

                }else{
                    $scope.plats = response.plats.length===0?response.plats:$scope.plats.concat(response.plats);
                    PlatDataService.setData($scope.plats);
                    $scope.$broadcast('scroll.infiniteScrollComplete');
                }
                SpinnerService.stop();
                loading=false;
            });
        };

        $scope.loadMore();


        //***************LISTENER*******************
/*
        $scope.$on('$stateChangeSuccess', function() {
            $scope.loadMore();
        });
*/        
}
])
;

