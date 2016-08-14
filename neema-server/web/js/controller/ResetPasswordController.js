/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('ResetPasswordController',
    ['$scope','UserService','$state','usSpinnerService',
        function($scope,UserService,$state,usSpinnerService){

            $scope.nbreLoader = 1;
            $scope.user = {};
            UserService.list();

            $scope.reset = function(user){
                $scope.formIsSubmit = true;
                if($scope.form.$invalid) return;

                usSpinnerService.spin('nt-spinner');

                UserService.reset(user);

                $scope.formIsSubmit = false;

            };

            $scope.$watch('nbreLoader', function(newValue, oldValue, scope) {
                if($scope.nbreLoader<=0) usSpinnerService.stop('nt-spinner');
            });


            $scope.$on('user.password.reseted',function(event,args){
                $scope.$emit('show.message',{alert:args.alert});

                $scope.$emit('stop.spinner');
                $state.go('neema');
            });

            $scope.$on('user.list',function(event,args){
                $scope.users = args.users;
                $scope.nbreLoader--;
            });

        }]);