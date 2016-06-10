/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('IndexController',
    ['$scope','CommandeService','UserService','usSpinnerService',
        function($scope,CommandeService,UserService,usSpinnerService){

            var user = UserService.getUser();

            if(!user){
                var alert = {textAlert:'Vous devez vous connectez pour effectuer cette opération',typeAlert:'danger'};
                $rootScope.$broadcast('show.message',{alert:alert});
                $state.go('login');
            }
            usSpinnerService.spin('nt-spinner');
            CommandeService.listByRestaurant();


            //*************LISTENER***************
            $scope.$on('commande.list',function(event,args){
                $scope.commandes = args.commandes;
                usSpinnerService.stop('nt-spinner');
            });



        }]);