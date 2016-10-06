/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('InscriptionController',
    ['$scope','UserService','usSpinnerService','$state','RestaurantService',
    function($scope,UserService,usSpinnerService,$state,RestaurantService){

        usSpinnerService.spin();
        RestaurantService.list(function(restaurants){
            $scope.restaurants = restaurants;
            usSpinnerService.stop();
        },function(error){
            $scope.$emit('show.message',{alert:error.data});
        });

        $scope.inscription = function(user){
            $scope.formIsSubmit = true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            UserService.inscription(user);

            $scope.formIsSubmit = false;
        };

        $scope.$on('user.registred',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});

            $scope.$emit('stop.spinner');
            $state.go('neema');
        });

    }]);