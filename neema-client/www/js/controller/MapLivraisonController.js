/**
 * Created by touremamadou on 06/05/2016.
 */

app.controller('MapLivraisonController',
    ['$scope','GeoLocalisationService','SpinnerService','$state','CommandeService',
    'DistanceMatrixService','PopupService','MapLivraisonService',
    function($scope,GeoLocalisationService,SpinnerService,$state,CommandeService,
    DistanceMatrixService,PopupService,MapLivraisonService){

        $scope.livraison = MapLivraisonService.getLivraison();
        if(!$scope.livraison){
            var popup = {
                title:'Neema',
                message:'Aucune Livraison encours',
                cssClass:'popupDanger'
            };
            PopupService.show(popup).then(function(res){
                $state.go('home');
            });
            return;
        }

        SpinnerService.start();

        GeoLocalisationService.getMap().then(function(args){
            $scope.map = args.map;
            $scope.position = args.position;
            SpinnerService.stop();

            var coordClient = new google.maps.LatLng($scope.livraison.commande.latitude,$scope.livraison.commande.longitude);
            var coordRestaurant = new google.maps.LatLng($scope.livraison.commande.restaurant.latitude,$scope.livraison.commande.restaurant.longitude);
            var coordLivreur = new google.maps.LatLng($scope.position.coords.latitude,$scope.position.coords.longitude);

            var infoMarkerClient = {
                position:coordClient,
                title:'Position du client',
                icon:'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=C|FF0000|000000'
            };
            var infoMarkerRestaurant = {
                position:coordRestaurant,
                title:'La position du restaurant',
                icon:'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=R|FFFF00|000000'
            };
            var infoMarkerLivreur = {
                position:coordLivreur,
                title:'La position du Livreur',
                icon:'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=L|FFF000|000000'
            };

            GeoLocalisationService.clearAllMarker();
            

            GeoLocalisationService.addMarker(infoMarkerClient);
            GeoLocalisationService.addMarker(infoMarkerRestaurant);
            GeoLocalisationService.addMarker(infoMarkerLivreur);


        },function(message){
            SpinnerService.stop();
            var popup = {
                title:'Echec Géolocalisation',
                message:message,
                cssClass:'popupDanger'
            };
            PopupService.show(popup).then(function(res){
                $state.go('home');
            });
        });

        //***************LISTENER*******************


    }]);
