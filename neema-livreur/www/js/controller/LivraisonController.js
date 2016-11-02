/**
 * Created by touremamadou on 22/10/2016.
 */

app.controller('InfoLivraisonController',
    ['$scope','LivraisonService','SpinnerService','PopupService','$stateParams',
    function($scope,LivraisonService,SpinnerService,PopupService,$stateParams){

        if(!$stateParams.idLivraison) $state.go('home');

        SpinnerService.start();
        LivraisonService.get($stateParams.idLivraison,function(livraison){
            $scope.livraison = livraison;
            SpinnerService.stop();
        });


        $scope.showCodeCommande = function(){
            $scope.codeCommandeVisible = !$scope.codeCommandeVisible;
        };



    }
]);
