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

            $scope.finishPreparation = function(detail){
                $scope.detail = detail; //afin de pouvoir mettre finished à true dans le listener
                usSpinnerService.spin('nt-spinner');
                CommandeService.finishPreparation(detail);
            };




            //*************LISTENER***************
            $scope.$on('commande.list',function(event,args){
                $scope.commandes = args.commandes;
                usSpinnerService.stop('nt-spinner');

                //determination du temps de preparation restant pour chaque plat
                // en fonction de la date de la commande
                angular.forEach($scope.commandes,function(commande){
                    angular.forEach(commande.detailCommandes,function(detailCommande){
                        detailCommande.dureePreparationRestant = getDureeRestant(new Date(commande.dateCommande).getTime(),detailCommande.plat.dureePreparation*1000)/1000 ;
                   })
                });
            });
            $scope.$on('commande.detail.updated',function(event,args){
                $scope.detail.finished = true;
                usSpinnerService.stop('nt-spinner');
                $scope.$emit('show.message',{alert:args.alert});
            });

        }]);