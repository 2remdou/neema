/**
 * Created by touremamadou on 02/10/2015.
 */
'use strict';

app.directive('neemaTimer', [function() {
    return {
        restrict: 'AE',
        scope:{
            time:'=',
        },
        template:'<span>{{humanizeTime}}</span>',
        controller:['$scope','$element','$attrs','$interval',function($scope,$element,$attrs,$interval){
            if($scope.time <= 0){
                $scope.humanizeTime ='00 minute 00 seconde';
                $element.css('color','red');
                return;
            }
            var timer = $interval(function(){
                $scope.time =$scope.time - 1000;
                if($scope.time === 0){
                    $scope.humanizeTime ='00 minute 00 seconde';
                    $element.css('color','red');
                    $interval.cancel(timer);
                }else{
                    $scope.humanizeTime = humanizeDuration($scope.time,{ language: 'fr',round:true });
                    if($scope.time <= 300000){ // 5 minutes
                        $element.css('color','red');
                    }else{
                        $element.css('color','black');
                    }
                }
            },1000,false);

            $scope.$on("$destroy",function(event){
                if(timer) $interval.cancel(timer);
            });

        }]
    };
}]);