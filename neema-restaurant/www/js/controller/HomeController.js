/**
 * Created by touremamadou on 06/05/2016.
 */

app.controller('HomeController',
    ['$scope','CommandeService','SpinnerService','PopupService',
    'FirstLoad',
    function($scope,CommandeService,SpinnerService,PopupService,
    FirstLoad){
        SpinnerService.start();

        CommandeService.listByRestaurant(function(commandes){
                $scope.commandes = commandes;
                SpinnerService.stop();

                //determination du temps de preparation restant pour chaque plat
                // en fonction de la date de la commande
                angular.forEach($scope.commandes,function(commande){
                    angular.forEach(commande.detailCommandes,function(detailCommande){
                        detailCommande.plat.dureePreparation = getDureeRestant(commande.dateCommande,detailCommande.plat.dureePreparation*1000);
                   })
                });
        });

        $scope.finishPreparation = function(detailCommande){
            if(!detailCommande.finished) {
                detailCommande.finished=true;
                return;
            }
            var popup = {
                title: 'Confirmation',
                message: 'Avez-vous terminer ce plat?'
            };
            PopupService.confirmation(popup).then(function(res) {
                if(res){
                    SpinnerService.start();
                    CommandeService.finishPreparation(detailCommande,function(alert){
                        SpinnerService.stop();
                        $scope.$emit('show.message',{alert:alert});
                    });
                }else{
                    detailCommande.finished=false;
                }
            });
        };

        $scope.giveToClient = function(commande){
            form.$submitted = true;
            if(form.$invalid) return;

            $scope.commande = commande;

            angular.forEach($scope.commandes,function(commande){
                commande.error=false;
            });
            if(!$scope.commande.code){
                $scope.commande.error=true;
                return;
            }

            SpinnerService.start();
            CommandeService.delivered($scope.commande,function(alert){
                SpinnerService.stop();
                $scope.$emit('show.message',{alert:alert});
            });
        };

    }


]);
