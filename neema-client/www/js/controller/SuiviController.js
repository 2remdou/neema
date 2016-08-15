/**
 * Created by touremamadou on 06/05/2016.
 */

app.controller('SuiviController',
    ['$scope','SpinnerService','$ionicModal','$state','CommandeService',
    function($scope,SpinnerService,$ionicModal,$state,CommandeService){

        $scope.distanceMatrixIsDefined = false;

        $ionicModal.fromTemplateUrl('js/view/modalMessage.html',{
            scope: $scope,
            animation: 'slide-in-up'
        }).then(function(modal){
            $scope.modal = modal;
        });

        CommandeService.listByUserConnected();

        SpinnerService.start();

        $scope.selectCommande = function($selected){
            $scope.distanceMatrixIsDefined = false;
            var index=_.findIndex($scope.commandes, {id: $selected});
            if(index === -1) return;
            var commande = $scope.commandes[index];
        };

        //***************LISTENER*******************

        $scope.$on('commande.list',function(event,args){
            $scope.commandes = args.commandes;
        });

        $scope.$on('$destroy', function() {
            $scope.modal.remove();
        });
        $scope.$on('modal.hidden', function() {
            $state.go('home');
        });

    }]);
