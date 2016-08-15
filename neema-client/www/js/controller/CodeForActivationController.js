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
                UserService.enabled($scope.user);

            };

            $scope.sendBackCode = function(){
                SpinnerService.start();
                UserService.sendBackCodeActivation();

            };

            //*********LSITENER**********

            $scope.$on('user.account.enabled',function(event,args){
                SpinnerService.stop();
                $state.go('home');
            });

            $scope.$on('user.code.sendback',function(event,args){
                SpinnerService.stop();
                $scope.$emit('show.message',{alert:args.alert});
            });

    }])
;
