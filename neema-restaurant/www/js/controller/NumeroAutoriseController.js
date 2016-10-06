/**
 * Created by touremamadou on 28/09/2016.
 */

app
    .controller('NumeroAutoriseController',
        ['$scope','NumeroAutoriseService','SpinnerService','$rootScope',
        function($scope,NumeroAutoriseService,SpinnerService,$rootScope){
            $scope.numeroAutorise = {};

            SpinnerService.start();

            $scope.list = function(){
                NumeroAutoriseService.list(function(numeroAutorises){
                    $scope.numeroAutorises = numeroAutorises;
                    SpinnerService.stop();
                },function(error){
                    SpinnerService.stop();
                });
            };

            $scope.list();


            $scope.addNumero = function(numeroAutorise){
                SpinnerService.start();
                NumeroAutoriseService.post(numeroAutorise,function(response){
                    var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:alert});
                    SpinnerService.stop();
                    $scope.list();
                },function(error){
                    SpinnerService.stop();
                })
            };

        }])
;
