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
            CommandeService.listByRestaurant(function(commandes){
                $scope.commandes = commandes;
                usSpinnerService.stop('nt-spinner');

                //determination du temps de preparation restant pour chaque plat
                // en fonction de la date de la commande
                angular.forEach($scope.commandes,function(commande){
                    angular.forEach(commande.detailCommandes,function(detailCommande){
                        detailCommande.plat.dureePreparation = getDureeRestant(commande.dateCommande,detailCommande.plat.dureePreparation*1000);
                    })
                });

            });

            $scope.finishPreparation = function(detail){
                $scope.detail = detail; //afin de pouvoir mettre finished à true dans le listener, pour faire réagir le code html
                usSpinnerService.spin('nt-spinner');
                CommandeService.finishPreparation(detail);
            };

            $scope.giveToClient = function(commande){
                $scope.commande = commande;
                angular.forEach($scope.commandes,function(commande){
                   commande.error=false;
                });
                if(!$scope.commande.code){
                    $scope.commande.error=true;
                    return;
                }

                usSpinnerService.spin('nt-spinner');
                CommandeService.delivered($scope.commande);
            };




            //*************LISTENER***************
            $scope.$on('commande.detail.updated',function(event,args){
                $scope.detail.finished = true;
                usSpinnerService.stop('nt-spinner');
                $scope.$emit('show.message',{alert:args.alert});
            });

            $scope.$on('commande.give.livreur',function(event,args){
                usSpinnerService.stop('nt-spinner');
                $scope.$emit('show.message',{alert:args.alert});
            });

            $scope.$on('commande.delivered',function(event,args){
                $scope.commande.delivered=true;
                usSpinnerService.stop('nt-spinner');
                $scope.$emit('show.message',{alert:args.alert});
            });

        }]);