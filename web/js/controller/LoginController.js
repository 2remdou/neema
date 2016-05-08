/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('LoginController',
    ['$scope','UserService','usSpinnerService','$state',
    function($scope,UserService,usSpinnerService,$state){

    $scope.login = function(user){
        $scope.formSubmit = true;
        if($scope.form.$invalid) return;

        usSpinnerService.spin('nt-spinner');
        UserService.login(user);

        $scope.formSubmit = false;
    };

    $scope.$on('user.connected',function(event,args){
        $scope.$emit('stop.spinner');
        $state.go('neema');
    });
}]);