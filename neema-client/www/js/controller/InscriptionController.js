/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';

app.controller('InscriptionController',
        ['$scope','UserService','SpinnerService','$rootScope','$state',
        function($scope,UserService,SpinnerService,$rootScope,$state){

            $scope.user = {};
            $scope.inscription = function(form){
                form.$submitted = true;
                if(form.$invalid) return;


                SpinnerService.start();
                $scope.user.username = $scope.user.telephone;
                UserService.inscriptionClient($scope.user);
            };


            //***********LISTENER*****************
            $scope.$on('user.registred',function(event,args){
                $rootScope.userConnected = UserService.getUser();
                SpinnerService.stop();
                $state.go('home');
            });
        }]);
