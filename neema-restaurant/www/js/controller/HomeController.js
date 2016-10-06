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
                    $scope.$broadcast('scroll.refreshComplete');
                },function(error){
                    loading = false;
                    SpinnerService.stop(); 
                    $scope.$broadcast('scroll.refreshComplete');                   
                });
            }else{
                $scope.$broadcast('scroll.refreshComplete');
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
                    detailCommande.plat.dureePreparation = getDureeRestant(commande.dateCommande,detailCommande.plat.dureePreparation*1000)*detailCommande.quantite;
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
                var index = _.findIndex($scope.commandes,function(c){return c.id==$scope.commande.id});
                $scope.commandes.splice(index,1);
            });
        };

        $scope.$watch('countNewCommande',function(newValue,oldValue){
            if(newValue===1)
                $scope.textNewCommande = 'nouvelle commande';
            else
                $scope.textNewCommande = 'nouvelles commandes';

        });

        $scope.doRefresh = function(){
            if(!CommandeDataService.lastTimeToLoad) return;
            CommandeService.refreshListByClient(CommandeDataService.lastTimeToLoad,function(commandes){
                CommandeService.defineDureeRestante(commandes);               
                if(commandes.length!==0){
                    angular.forEach(commandes,function(commande){
                        CommandeDataService.data.list.unshift(commande);
                    });
                } 
                CommandeDataService.lastTimeToLoad = new Date();
                $scope.$broadcast('scroll.refreshComplete');
            },function(err){
                log(err);
            });
        };

    }


]);
