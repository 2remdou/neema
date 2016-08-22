/**
 * Created by touremamadou on 06/05/2016.
 */

app.controller('MenuController',
    ['$scope','PlatService','SpinnerService','PopupService','$filter',
    function($scope,PlatService,SpinnerService,PopupService,$filter){
        SpinnerService.start();


        PlatService.listByRestaurantByUserConnected(function(plats){
            $scope.plats = plats;
            $scope.menuInitial = $filter('filter')($scope.plats,{onMenu:true});
            $scope.platsOnMenu = angular.copy($scope.menuInitial);

            SpinnerService.stop();
        });

        $scope.onMenu = function(plat) {
            var p = _.find($scope.menuInitial, {id: plat.id});

            if(p){ // si le plat existe dans le menu initial
                $scope.platsOnMenu[_.findIndex($scope.platsOnMenu, {id: p.id})].onMenu = plat.onMenu;
            }else{ // si le plat n'existe pas
                if(plat.onMenu){ // si il a été coché
                    $scope.platsOnMenu.push(plat); // on l'insert dans le menu
                }else{ // n'existe pas dans le menu initial,déja inseré dans platsOnMenu,il faut le supprimer
                    $scope.platsOnMenu.splice(_.findIndex($scope.platsOnMenu, {id: plat.id}),1); // on le supprime du menu
                }
            }
        };

        $scope.valider = function(){
            SpinnerService.start();
            PlatService.updateMenu($scope.platsOnMenu,function(response){
                if(response.fail.length!=0){
                    var alert = {textAlert:'le menu a été mis à jour avec certaines erreurs',typeAlert:'warning'};
                    $scope.$emit('show.message',{alert:response.alert});
                }else{
                    $scope.$emit('show.message',{alert:response.alert});
                }
                SpinnerService.stop();

            });
        };

    }

]);
