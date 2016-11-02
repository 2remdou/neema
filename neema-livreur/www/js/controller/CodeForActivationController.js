/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';
app
    .controller('CodeForActivationController',
        ['$scope','UserService','SpinnerService','$state',
        function($scope,UserService,SpinnerService,$state){

            $scope.user = {};
            $scope.valider = function(form){
                form.$submitted = true;
                if(form.$invalid) return;

                SpinnerService.start();
                UserService.enabled($scope.user,function(alert){
                    SpinnerService.stop();
                    UserService.initUser();
                    $scope.$emit('show.message',{alert:alert});
                    $state.go('home');
                });

            };

            $scope.sendBackCode = function(){ 
                SpinnerService.start();
                UserService.sendBackCodeActivation(function(alert){
                    log('alert');
                    log(alert);
                    SpinnerService.stop();
                    $scope.$emit('show.message',{alert:alert});
                });

            };
    }])
;
