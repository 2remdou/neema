/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('ProfilController',
    ['$scope','UserService','usSpinnerService','$state','$rootScope',
    function($scope,UserService,usSpinnerService,$state,$rootScope){
        var user = null;
        if(user = UserService.getUser()){
            UserService.get(user.id).then(function(response){
                $scope.user = response;
            },function(error){
                log(error);
            });
        }else{
            $state.go('login');
        }

        $scope.edit = function(user){
            $scope.formIsSubmit = true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');

            UserService.edit(user);

            $scope.formIsSubmit = false;
        };

        $scope.changePassword = function(user){
            $scope.formPasswordIsSubmit = true;
            if($scope.formPassword.$invalid) return;

            if(user.newPassword !== user.confirmationPassword){
                var alert = {textAlert:'Le nouveau et la confirmation doivent être identique',typeAlert:'danger'};
                $scope.$emit('show.message',{alert:alert});
                $scope.formPasswordIsSubmit = false;
                return;
            }

            usSpinnerService.spin('nt-spinner');

            UserService.changePassword(user);

            $scope.formPasswordIsSubmit = false;
        };


        $scope.$on('user.edited',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});

            $scope.$emit('stop.spinner');
        });
        $scope.$on('user.password.changed',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});

            $scope.$emit('stop.spinner');
            $state.go('neema');
        });

    }]);