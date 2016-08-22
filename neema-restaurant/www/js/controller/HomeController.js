/**
 * Created by touremamadou on 06/05/2016.
 */

app.controller('HomeController',
    ['$scope','CommandeService','SpinnerService','PopupService',
    'FirstLoad','SocketService','$rootScope',
    function($scope,CommandeService,SpinnerService,PopupService,
    FirstLoad,SocketService,$rootScope){

        if($rootScope.userConnected){
            var restaurant = $rootScope.userConnected.restaurant;
        
            SocketService.emit('restaurant:registred',restaurant);
        }
        SpinnerService.start();
        var dateLastChargement = new Date();
        var loading = true;
        $scope.countNewCommande = 0;
        $scope.textNewCommande = 'nouvelle commande';
        $scope.newCommande = false;

        SocketService.on('commande:new',function(){
            $scope.countNewCommande ++;
            $scope.newCommande = true;
        });

        $scope.refreshListCommande = function(){
            if(!loading){
                SpinnerService.start();
                loading = true;
                CommandeService.refreshListByRestaurant(dateLastChargement,function(commandes){
                    loading = false;
                    dateLastChargement = new Date();
                    calculTempsPreparationRestant(commandes);

                    $scope.commandes = _.concat(commandes,$scope.commandes);
                    $scope.countNewCommande = 0;
                    $scope.newCommande = false;
                    SpinnerService.stop();

                },function(error){
                    loading = false;
                    SpinnerService.stop();                    
                });
            }
        };


        CommandeService.listByRestaurant(function(commandes){
            loading = false;
                $scope.commandes = commandes;
                SpinnerService.stop();
                calculTempsPreparationRestant($scope.commandes);
        });

        /**
         * calcul le temps de preparation restant pour chaque commande
         * en fonction de la date de commande
         */
        function calculTempsPreparationRestant(commandes){
            angular.forEach(commandes,function(commande){
                angular.forEach(commande.detailCommandes,function(detailCommande){
                    detailCommande.plat.dureePreparation = getDureeRestant(commande.dateCommande,detailCommande.plat.dureePreparation*1000);
                })
            });
        };
        

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

        $scope.$watch('countNewCommande',function(newValue,oldValue){
            if(newValue===1)
                $scope.textNewCommande = 'nouvelle commande';
            else
                $scope.textNewCommande = 'nouvelles commandes';

        });

    }


]);
