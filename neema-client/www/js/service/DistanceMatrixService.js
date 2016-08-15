/**
 * Created by touremamadou on 23/05/2016.
 */
'use strict';

app.service('DistanceMatrixService',[
    '$rootScope','$q',
    function($rootScope,$q){
        var that = this;
        var _distanceMatrix = new google.maps.DistanceMatrixService;

        this.getDistanceMatrix = function (origin,destination) {
            var deffered = $q.defer();
            _distanceMatrix.getDistanceMatrix({
                origins: [origin],
                destinations: [destination],
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC,
                avoidHighways: false,
                avoidTolls: false
            },function(response,status){ 
                if(status === google.maps.DistanceMatrixStatus.OK){
                     var distanceMatrix={};
                     distanceMatrix.distance = response.rows[0].elements[0].distance;
                     distanceMatrix.duration = response.rows[0].elements[0].duration;
                     deffered.resolve(distanceMatrix);
                    //  $rootScope.$broadcast('distance.duration.defined',{distance:distance,duration:duration});
                }else{
                    var message = 'Impossible de calculer la distance entre le restaurant et votre adresse de livraison';
                    deffered.reject(message);
                }
            });

            return deffered.promise;
        }
    }
]);