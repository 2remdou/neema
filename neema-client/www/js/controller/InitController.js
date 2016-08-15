/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';
app.controller('InitController',
    ['$scope','UserService','$state','$rootScope',
        function($scope,UserService,$state,$rootScope){

            if(!$rootScope.userConnected){
                $state.go('login');
            }


        }]);
