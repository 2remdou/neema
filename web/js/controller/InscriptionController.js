/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('InscriptionController',
    ['$scope','UserService','usSpinnerService','$state','RestaurantService',
    function($scope,UserService,usSpinnerService,$state,RestaurantService){

        RestaurantService.list();
        $scope.nbreLoader=1;

        $scope.inscription = function(user){
            $scope.formIsSubmit = true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            UserService.inscription(user);

            $scope.formIsSubmit = false;
        };


        $scope.$watch('nbreLoader', function(newValue, oldValue, scope) {
            if($scope.nbreLoader<=0) usSpinnerService.stop('nt-spinner');
        });

        $scope.$on('user.registred',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});

            $scope.$emit('stop.spinner');
            $state.go('neema');
        });

        $scope.$on('restaurant.list',function(event,args){
            $scope.restaurants = args.restaurants;
            $scope.nbreLoader--;
        });

    }]);