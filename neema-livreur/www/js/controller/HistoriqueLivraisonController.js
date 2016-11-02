/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';
app
    .controller('HistoriqueLivraisonController',
        ['$scope','LivraisonService','SpinnerService','$rootScope','$state',
        function($scope,LivraisonService,SpinnerService,$rootScope,$state){
            SpinnerService.start();

            LivraisonService.getHistoriqueByLivreurConnected(function(livraisons){
                $scope.livraisons = livraisons;
                SpinnerService.stop();
            }); 

            $scope.showDetail = function(livraison){
                $state.go(notification.routeName,notification.routeParams);
            };
        }])
;
