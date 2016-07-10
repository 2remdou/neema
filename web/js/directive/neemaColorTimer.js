/**
 * Created by touremamadou on 02/10/2015.
 */
'use strict';

app.directive('neemaColorTimer', [function() {
    return {
        restrict: 'AE',
        scope:{
            minute:'=',
            seconde: '='
        },
        controller:['$scope','$element','$attrs',function($scope,$element,$attrs){
           $scope.$watch('minute',function(newValue,oldValue){
               if(newValue <= 5){
                   $element.css('color','red');
               }else{
                   $element.css('color','black');
               }
           });
        }]
    };
}]);