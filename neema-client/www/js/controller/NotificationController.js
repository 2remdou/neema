/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';
app
    .controller('NotificationController',
        ['$scope','NotificationService','SpinnerService','$rootScope','$state',
        function($scope,NotificationService,SpinnerService,$rootScope,$state){
            SpinnerService.start();

            NotificationService.getByUser(function(notifications){
                $scope.notifications = notifications;
                NotificationService.configRoute(notifications);
                SpinnerService.stop();
            }); 

            $scope.showDetail = function(notification){
                $state.go(notification.routeName,notification.routeParams);
            };
        }])
;
