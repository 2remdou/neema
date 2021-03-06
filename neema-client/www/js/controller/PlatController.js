/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';
app.controller('PlatController',
    ['$scope','PlatService','$stateParams','$state','PanierService',
    'PopupService','PaginatorService',
        function($scope,PlatService,$stateParams,$state,PanierService,
        PopupService,PaginatorService){

            if(!$stateParams.idPlat) $state.go('home');
            $scope.isFirst = true;
            $scope.isLast = false;
            $scope.index = 0;
            var plats=PaginatorService.getMenu().plats;

            $scope.index=_.findIndex(plats,{id:$stateParams.idPlat})
            if($scope.index===-1){
                $state.go('home');
                return;
            }

            if(plats.length===1){
                $scope.isFirst = true;
                $scope.isLast = true;
            }

            $scope.plat = plats[$scope.index];
/*
            PlatService.get($stateParams.idPlat).then(function(response){
                $scope.plat = response;
            },function(error){
                $state.go('home')
            });

*/            

            $scope.addInPanier = function(plat){
                var popup = {
                    title: 'Quantite',
                    message: 'Entrez la quantite à commander',
                    inputType: 'number',
                    inputPlaceholder: 'La quantite',
                    defaultText: 1
                };
                PopupService.prompt(popup).then(function(res){
                    if(res && typeof res === 'number'){
                        plat.quantite = res;
                        PanierService.add(plat);
                    }
                });
            };

            $scope.removeInPanier = function(plat){
                PanierService.remove(plat);
            };

            $scope.nextPlat = function(){
                if($scope.isLast) return;
                $scope.plat = plats[++$scope.index]
            };
            $scope.previewPlat = function(){
                if($scope.isFirst) return;
                $scope.plat = plats[--$scope.index]
            };

             $scope.$watch('index', function(newValue, oldValue) {
                if(newValue===0){
                    $scope.isFirst = true;
                }else if(newValue===plats.length-1){
                    $scope.isLast = true;
                    $scope.isFirst = false;
                }else{
                    $scope.isFirst = false;
                    $scope.isLast = false;
                }
            });

            //***************LISTENER*******************
            $scope.$on('plat.list',function(event,args){
                $scope.plats = args.plats;
            });

}]);
