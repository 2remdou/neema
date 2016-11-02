/**
 * Created by touremamadou on 06/05/2016.
 */

app.controller('HomeController',
    ['$scope','PlatService','SpinnerService',
    'PaginatorService','$timeout','$ionicScrollDelegate','TIME_FOR_TRY_TO_LOAD',
    function($scope,PlatService,SpinnerService,
    PaginatorService,$timeout,$ionicScrollDelegate,TIME_FOR_TRY_TO_LOAD){

        var tempsEcoule = new Date().getTime()- new Date(PaginatorService.getMenu().dateToLastLoad).getTime();        
        if( tempsEcoule  >= TIME_FOR_TRY_TO_LOAD){
            PaginatorService.remove('menu');
        }

        $scope.plats = PaginatorService.getMenu().plats||[];
        

        $scope.canBeLoad = true;

        $scope.load = function(callback){
            SpinnerService.start();
            PlatService.listOnMenu(PaginatorService.getMenu().paginator.nextPage,
                function(plats,paginator){
                    PaginatorService.addPlatOnMenu({plats:plats,paginator:paginator});
                    if(plats.length!==0) {
                        $scope.plats = PaginatorService.getMenu().plats;
                    }
                    $scope.canBeLoad = PaginatorService.getMenu().canBeLoad;
                    SpinnerService.stop();
                    if(typeof callback === 'function')
                        callback();
                }
            )

        };


        if($scope.plats.length===0){
            $scope.load();
        }

        $scope.onInfiniteLoad = function(){
            $scope.load(function(){
                //bug que je ne comprends pas dans safari et ios
                var posTop = $ionicScrollDelegate.$getByHandle('mainScroll').getScrollPosition().top;
                $ionicScrollDelegate.$getByHandle('mainScroll').scrollTo(0,posTop-200);

                $timeout(function() {
                    $scope.$broadcast('scroll.infiniteScrollComplete');
                },1000);
            });
        };

        //***************LISTENER*******************

        $scope.$on('search.finished',function(event,args){
            $scope.plats = args.plats;
        });

        $scope.$on('not.found',function(event,args){
             $scope.notFound(event,args);
         });

}
]);
