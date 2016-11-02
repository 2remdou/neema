/**
 * Created by touremamadou on 22/10/2016.
 */

app.controller('HomeController',
    ['$scope','LivraisonService','SpinnerService','PopupService','CommandeService',
    function($scope,LivraisonService,SpinnerService,PopupService,CommandeService){

        $scope.dureeRestante = 0;
        $scope.commandes = [];
        $scope.load = function(){
            SpinnerService.start();
            LivraisonService.getLivrisonEncoursByLivreurConnected(function(livraison){
                $scope.livraison = livraison;
                $scope.commandes.push(livraison.commande);
                CommandeService.defineDureeRestante($scope.commandes); 
                $scope.livraisonActiveExiste = true;
                SpinnerService.stop();
            },function(alert){
                $scope.$emit('show.message',{alert:alert});
                $scope.livraisonActiveExiste = false;
            }); 

        }

        $scope.load();

        $scope.showCodeCommande = function(){
            $scope.codeCommandeVisible = !$scope.codeCommandeVisible;
        };

        $scope.livrer = function(livraison){
            var l = angular.copy(livraison);
            var popup = {
                title: 'Confirmation',
                message: 'Avez-vous terminer cette livraison ?'
            };
            PopupService.confirmation(popup).then(function(res) {
                if(res){
                    SpinnerService.start();
                    LivraisonService.livrer(livraison,function(alert){
                        $scope.$emit('show.message',{alert:alert});
                        $scope.load();
                    });
                }
            });
        };



    }
]);
