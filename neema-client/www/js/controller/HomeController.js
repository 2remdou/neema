/**
 * Created by touremamadou on 06/05/2016.
 */

app.controller('HomeController',
    ['$scope','PlatService','PlatDataService','SpinnerService','INTERVAL_TIME_FOR_TRY_AGAIN_LOADING',
    'FirstLoad',
    function($scope,PlatService,PlatDataService,SpinnerService,INTERVAL_TIME_FOR_TRY_AGAIN_LOADING,
    FirstLoad){

        var loading = false;
        PlatDataService.data.type='onMenu';

        if(!Array.isArray($scope.plats)) $scope.plats = [];

        $scope.loadMore = function(){
            if(PlatDataService.data.type==='other') return;
            SpinnerService.start();
            if(loading) return;//encours de chargement

            //toujours recharger, si la liste des plats est vide
            //  if($scope.plats.length === 0) PlatDataService.allPlatAreAlreadyLoaded.onMenu = false;
            //si l'intervalle est expiré
            //ou tous les plats ne sont pas chargés(pour la pagination)
            if(PlatDataService.timeForLoadingExpired() || 
                !PlatDataService.allPlatAreAlreadyLoaded.onMenu ||
                $scope.plats.length === 0) {
                loading=true;
                PlatService.listOnMenu(PlatDataService.currentPage.onMenu+1,function(plats,currentPage,pageCount){
                    loading=false;
                    PlatDataService.lastTimeToLoad = new Date().getTime();
                    if(plats.length === 0){
                        // // PlatDataService.currentPage.onMenu=pageCount;
                        PlatDataService.allPlatAreAlreadyLoaded.onMenu = true;
                    }else{
                        PlatDataService.setData(plats);
                        PlatDataService.currentPage.onMenu++;
                    }
                    $scope.plats = PlatDataService.getData();            
                    $scope.$broadcast('scroll.infiniteScrollComplete');
                    SpinnerService.stop();

                },function(err){
                    log(err);
                });
            }else{
                if(PlatDataService.allPlatAreAlreadyLoaded.onMenu){
                    $scope.plats = PlatDataService.getData();
                }
                SpinnerService.stop();
            }
        };


        if(FirstLoad.controller.HomeController){
            $scope.loadMore();
            FirstLoad.controller.HomeController=false;
        }else{
            $scope.plats = PlatDataService.getData();
        }





        //***************LISTENER*******************

        $scope.$on('search.finished',function(event,args){
            PlatDataService.data.type='other';//pouvoir parcourir les resultats de la recherche
            $scope.plats = args.plats;
            PlatDataService.setData(args.plats);
        });

        $scope.$on('scroll.infiniteScrollComplete',function(){
            $scope.$broadcast('scroll.refreshComplete');
        });
/*
        $scope.$on('$stateChangeSuccess', function() {
            $scope.loadMore();
        });

*/}
]);
