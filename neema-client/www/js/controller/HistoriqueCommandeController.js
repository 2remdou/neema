/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';
app
    .controller('HistoriqueCommandeController',
        ['$scope','CommandeService','SpinnerService','$rootScope','$state',
        'PaginatorService','$ionicScrollDelegate','$timeout',
        function($scope,CommandeService,SpinnerService,$rootScope,$state,
        PaginatorService,$ionicScrollDelegate,$timeout){
            
            $scope.commandes = PaginatorService.getHistoriqueCommande().commandes;
            $scope.canBeLoad = true;
            
            $scope.load = function(callback){
                SpinnerService.start();
                CommandeService.getHistoriqueByClientConnected(PaginatorService.getHistoriqueCommande().paginator.nextPage,
                null,function(commandes,paginator){
                    PaginatorService.addHistoriqueCommande({commandes:commandes,paginator:paginator});
                    if(commandes.length !== 0){
                        $scope.commandes = PaginatorService.getHistoriqueCommande().commandes;
                    }
                    $scope.canBeLoad = PaginatorService.getHistoriqueCommande().canBeLoad;
                    SpinnerService.stop();
                    if(typeof callback === 'function')
                        callback();
                }); 
            }

            if($scope.commandes.length===0){
                $scope.load();
            }

            $scope.onInfiniteLoad = function(){
                $scope.load(function(){
                    var posTop = $ionicScrollDelegate.$getByHandle('mainScroll').getScrollPosition().top;
                    $ionicScrollDelegate.$getByHandle('mainScroll').scrollTo(0,posTop-200);

                    $timeout(function() {
                        $scope.$broadcast('scroll.infiniteScrollComplete');
                    },1000);
                });
            };

            $scope.doRefresh = function(){
                CommandeService.getHistoriqueByClientConnected(1,
                    Math.floor(PaginatorService.getHistoriqueCommande().dateToLastLoad/1000),
                    function(commandes,paginator){
                        PaginatorService.addHistoriqueCommande({commandes:commandes,paginator:paginator},true);
                        $scope.commandes = PaginatorService.getHistoriqueCommande().commandes;
                        $scope.canBeLoad = PaginatorService.getHistoriqueCommande().canBeLoad;
                        $scope.$broadcast('scroll.refreshComplete');
                });
            };

            $scope.showDetail = function(livraison){
                $state.go(notification.routeName,notification.routeParams);
            };

            $scope.$on('not.found',function(event,args){
                $scope.notFound(event,args);
                $scope.$broadcast('scroll.refreshComplete');
            });
        }])
;
