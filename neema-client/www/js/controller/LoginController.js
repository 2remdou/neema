/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';
app
    .controller('LoginController',
        ['$scope','UserService','SpinnerService','$rootScope','$state','$cordovaPushV5',
        function($scope,UserService,SpinnerService,$rootScope,$state,$cordovaPushV5){

            $scope.user = {};

            $scope.login = function(form){
                form.$submitted = true;
                if(form.$invalid) return;

                SpinnerService.start();

                UserService.clear();
                UserService.login($scope.user);

            };



            //**********LISTENER************
            $scope.$on('user.connected',function(event,args){
                UserService.initUser();
                SpinnerService.stop();
                if($rootScope.isClient)
                    $state.go('home'); 
            });


        }])
    .controller('LogoutController',
        ['$scope','UserService','$rootScope','$state',
        function($scope,UserService,$rootScope,$state){
            UserService.logout();
            delete $rootScope.userConnected;
            $state.go('login');
    }])
;
