/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';
app
    .controller('LoginController',
        ['$scope','UserService','SpinnerService','$rootScope','$state',
        function($scope,UserService,SpinnerService,$rootScope,$state){
            
            $scope.user = {};

            $scope.login = function(form){
                form.$submitted = true;
                if(form.$invalid) return;

                SpinnerService.start();

                UserService.clear();
                UserService.login($scope.user,function(token){
                    UserService.initUser();
                    SpinnerService.stop();
                    $state.go('home'); 
                });

            };

        }])
    .controller('LogoutController',
        ['$scope','UserService','$rootScope','$state','PanierService',
        function($scope,UserService,$rootScope,$state,PanierService){
            UserService.logout();
            PanierService.clear();
            delete $rootScope.userConnected;
            $state.go('login');
    }])
;
