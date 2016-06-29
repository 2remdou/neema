/**
 * Created by touremamadou on 17/03/2016.
 */

'use strict';


app.controller('ModalConfirmationController',['$scope','texte','close',
    function($scope,texte,close){
        $scope.texte = texte;
        $scope.close = function(result) {
            close(result, 500);
        };
    }]);

