/**
 * Created by touremamadou on 16/05/2016.
 */
'use strict';
app
    .controller('CommandeController',
        ['$scope','PlatService','PanierService','$state',
            'SpinnerService','CommandeService','PopupService','FRAIS_COMMANDE',
            function($scope,PlatService,PanierService,$state,
                    SpinnerService,CommandeService,PopupService,FRAIS_COMMANDE){

                if(PanierService.isEmpty()){
                    $state.go('home');
                    return;
                }
                $scope.commande={};
                $scope.commande.total=0;
                $scope.plats = PanierService.getPanier();
                $scope.commande.restaurant = $scope.plats[0].restaurant;//car tous les plats viennent du même restaurant
                $scope.commande.telephone = $scope.userConnected.username;

                var refreshCommande = function(){
                    $scope.commande.total = 0;
                    $scope.commande.detailCommandes = [];
                    angular.forEach($scope.plats,function(plat){
                        $scope.commande.detailCommandes.push({
                            quantite:1,
                            prix:plat.prix,
                            plat:plat.id
                        });
                        $scope.commande.total += parseInt(plat.prix);
                        $scope.commande.fraisCommande = $scope.commande.total*FRAIS_COMMANDE;
                        $scope.commande.totalCommande=$scope.commande.total+$scope.commande.fraisCommande;
                    });
                };
                refreshCommande();

                $scope.removeInPanier = function(plat){
                    if($scope.plats.length===1){
                        $scope.clearPanier();
                        return;
                    }
                    var popup = {
                        title: 'Confirmation',
                        message: 'Voulez-vous retirer ce plat de la commande ?'
                    };
                    PopupService.confirmation(popup).then(function(res) {
                        if(res){
                            PanierService.remove(plat);
                            refreshCommande();
                        }
                    });

                };

                $scope.valider = function(commande){
                    var c = angular.copy(commande);
                    c.restaurant = c.restaurant.id;
                    var popup = {
                        title: 'Confirmation',
                        message: 'Voulez-vous passer cette commande ?'
                    };
                    PopupService.confirmation(popup).then(function(res) {
                        if(res){
                            SpinnerService.start();
                            CommandeService.post(c);
                        }
                    });
                };

                $scope.clearPanier = function() {
                    var popup = {
                        title: 'Confirmation',
                        message: 'Voulez-vous vider le panier'
                    };
                    PopupService.confirmation(popup).then(function(res) {
                        if(res){
                            PanierService.clear();
                            $state.go('home');
                        }
                    });
                };


                //***************LISTENER*******************

                $scope.$on('commande.created',function(event,args){
                    SpinnerService.stop();
                    PanierService.clear();
                    var popup = {
                        title: 'Commande',
                        message: 'Votre commande a été reçu',
                        cssClass: 'popupSuccess'
                    };
                    PopupService.show(popup).then(function(res) {
                        $state.go('suivi');
                    });

                });

            }]);
