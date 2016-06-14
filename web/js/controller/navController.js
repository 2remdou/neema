/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('NavController',['$scope','UserService','$state',function($scope,UserService,$state){
    $scope.logout = function(){
        UserService.logout();
    };

    $scope.$on('user.logout',function(event,args){
        $state.go('login');
    })
}]);