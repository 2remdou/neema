/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';


app.service('ParameterService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            this.fraisCommande = 6000;

            this.timeLivraison = 600; // 10 minutes

            this.getFraisCommande = function(){
                return this.fraisCommande;
            };

            this.getTimeLivraison = function(){
                return this.timeLivraison;
            }

}]);