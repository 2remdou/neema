/**
 * Created by touremamadou on 06/05/2016.
 */
'use strict';

app.controller('InfoCommandeController',
    ['$scope','SpinnerService','$state','CommandeService','FRAIS_COMMANDE',
    function($scope,SpinnerService,$state,CommandeService,FRAIS_COMMANDE
    ){
        $scope.codeCommandeVisible = false;

        CommandeService.listByUserConnected();

        SpinnerService.start();

        $scope.indexSelected = 0;

        $scope.selectCommande = function($selected){
            $scope.codeCommandeVisible = false;
            SpinnerService.start();
            //deselectionner le precedent, avant de recuperer le nouvel index
            $scope.commandes[$scope.indexSelected].selected = false;

            //recuperer le nouvel index
            $scope.indexSelected=_.findIndex($scope.commandes, {id: $selected});
            if($scope.indexSelected === -1) return; 

            $scope.commandes[$scope.indexSelected].selected = true;

            SpinnerService.stop();


        };
        
        $scope.showCodeCommande = function(){
            $scope.codeCommandeVisible = !$scope.codeCommandeVisible;
        };

        //***************LISTENER*******************

        $scope.$on('commande.list',function(event,args){
            $scope.commandes = args.commandes;
            angular.forEach($scope.commandes,function(commande){
                commande.dureeRestant = Math.round(getDureeRestant(commande.dateCommande,commande.durationEstimative*1000));
                if(!commande.totalCommande){
                    commande.montantCommande = 0;
                    angular.forEach(commande.detailCommandes,function(detail){
                        commande.montantCommande += detail.prix;
                    });
                    commande.fraisCommande = commande.montantCommande * FRAIS_COMMANDE;
                    commande.totalCommande = commande.montantCommande+commande.fraisCommande;
                }

            });
            
            //selectionner la premiere commande à afficher par defaut
            if($scope.commandes.length !== 0)
                $scope.commandes[$scope.indexSelected].selected = true;


            SpinnerService.stop();

        });
    }]);
