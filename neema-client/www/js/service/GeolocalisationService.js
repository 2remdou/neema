/**
 * Created by touremamadou on 23/05/2016.
 */
'use strict';

app.service('GeoLocalisationService',[
    '$cordovaGeolocation','$rootScope','$q',
    function($cordovaGeolocation,$rootScope,$q){

        var that = this;
        var position = null;
        var map = null;
        var markers = [];



        this.getPosition = function(){
            var deferred = $q.defer();

            var posOptions = {timeout: 10000, enableHighAccuracy: false};
            $cordovaGeolocation
                .getCurrentPosition(posOptions)
                .then(function (position) {
                    that.position = position;
                    deferred.resolve(that.position);
                    $rootScope.$broadcast('geolocalisation.success',{position:that.position});
                }, function(err) {
                    var message='';
                    if(err.code === 1){//PositionError.PERMISSION_DENIED
                        message='Veuillez activer la geolocalisation(GPS), pour nous permettre d\' obtenir votre adresse de livraison';
                    }else if(err.code === 2){ //PositionError.POSITION_UNAVAILABLE
                        message='Veuillez vous connectez à internet, pour nous permettre d\'obtenir votre adresse de livraison';
                    }else if(err.code === 3){ //PositionError.TIMEOUT
                        message='Impossible de vous géolocaliser, pour nous permettre d\' obtenir votre adresse de livraison';
                    }
                    deferred.reject(message);
                });

            return deferred.promise;
        };

        this.getMap = function(){
            var deferred = $q.defer();
             that.getPosition().then(function(position){
                var coord = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
                 var mapOptions = {
                    center: coord,
                    zoom: 10,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                 map = new google.maps.Map(document.getElementById("map"), mapOptions);
                 deferred.resolve({map:map,position:position});
                },function(message){
                    deferred.reject(message);
                });
            return deferred.promise;
        };

/*        infoMarker = {
                position:coordClient,
                title:'Votre position lors de la commande',
                icon:'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=C|FF0000|000000'
            };
*/
        this.addMarker = function(infoMarker){

            var marker = new google.maps.Marker({
                animation: google.maps.Animation.DROP,
                position: infoMarker.position,
                title: infoMarker.title,
                map:map,
                icon:infoMarker.icon
            });
            var pos = markers.length;
            markers.push(marker);

            google.maps.event.addListenerOnce(map,'idle',function(){

                var infoWindow = new google.maps.InfoWindow({
                    content: marker.title
                });

                google.maps.event.addListener(markers[pos], 'click', function () {
                    infoWindow.open(map, markers[pos]);
                });

            });

        };

        this.clearAllMarker = function(){
            angular.forEach(markers,function(marker){
                marker.setMap(null);
                marker=null;
            });
        }
    }
]);