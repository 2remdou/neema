/**
 * Created by touremamadou on 06/05/2016.
 */

app.controller('SuiviLivraisonController',
    ['$scope','LivraisonService','PopupService','SpinnerService',
    '$state','CommandeService','MapLivraisonService','DistanceMatrixService','GeoLocalisationService',
    function($scope,LivraisonService,PopupService,SpinnerService,
    $state,CommandeService,MapLivraisonService,DistanceMatrixService,GeoLocalisationService){


        $scope.dureeRestante = 0;
        LivraisonService.getCurrentLivrisonByLivreurConnected();

        $scope.delivered = function(livraison){
            var popup = {
                title: 'Confirmation',
                message: 'Avez-vous livré la commande ?'
            };
            PopupService.confirmation(popup).then(function(res) {
                if(res){
                    SpinnerService.start();
                    CommandeService.delivered(livraison.commande);
                }
            });
        };

        $scope.showMap = function(livraison){
            MapLivraisonService.setLivraison(livraison);
            $state.go('showMapLivraison');
        };


        SpinnerService.start();

        //***************LISTENER*******************

        $scope.$on('livraison.current',function(event,args){
            if(!args.livraison){
                var alert = {textAlert:'Aucune livraison encours',typeAlert:'info'};
                var popup = {
                    title:'Neema',
                    message:alert.textAlert,
                    cssClass:'popup'+alert.typeAlert.capitalizeFirstLetter()
                };
                PopupService.show(popup).then(function(res){
                    $state.go('home');
                });
                return;
            }      

            $scope.livraison = args.livraison;

            GeoLocalisationService.getPosition().then(function(position){
                    $scope.position = position;
                    var origin = {lat:parseFloat($scope.position.coords.latitude),lng:parseFloat($scope.position.coords.longitude)};
                    var destination = null;
                    //si le restaurant a donné au livreur, c'est la duree restante de livraison qu'il faudra determiner
                    //sinon la duree restante de preparation
                    if($scope.livraison.commande.giveLivreur){
                        $scope.dureeRestante = LivraisonService.getDureeRestanteLivraison($scope.livraison);
                        //destination = chez le client
                        destination = {lat:parseFloat($scope.livraison.commande.latitude),lng:parseFloat($scope.livraison.commande.longitude)};
                    }else{
                        $scope.dureeRestante = LivraisonService.getDureeRestantePreparation($scope.livraison);
                        //destination = au restaurant
                        destination = {lat:parseFloat($scope.livraison.commande.restaurant.latitude),lng:parseFloat($scope.livraison.commande.restaurant.longitude)};
                    }

                    //actualiser le timer
                    $scope.$broadcast('timer-add-cd-seconds', $scope.dureeRestante);

                    DistanceMatrixService.getDistanceMatrix(origin,destination).then(function(distanceMatrice){
                        SpinnerService.stop();
                        $scope.distanceRestante=distanceMatrice.distance.value;
                    },function(message){
                        SpinnerService.stop();
                        var popup = {
                            title:'Echec lors du calcul de la distance',
                            message:message,
                            cssClass:'popupDanger'
                        };
                        PopupService.show(popup).then(function(res){
                            $state.go('home');
                        });
                    });
                },function(message){
                SpinnerService.stop();
                var popup = {
                    title:'Géolocalisation',
                    message:message,
                    cssClass:'popupDanger'
                };
                PopupService.show(popup).then(function(res){
                    $state.go('home');
                });

            });
        });

        $scope.$on('commande.delivered',function(event,args){
            SpinnerService.stop();
            var alert = args.alert;
            var popup = {
                title:'Neema',
                message:alert.textAlert,
                cssClass:'popup'+alert.typeAlert.capitalizeFirstLetter()
            };
            PopupService.show(popup).then(function(res){
                $state.go('home');
            });        
        });

        $scope.$on('modal.hidden', function() {
            $state.go('home');
        });

    }]);
