/**
 * Created by touremamadou on 02/06/2016.
 */
'use strict';

app.service('SpinnerService',
    ['$ionicLoading',
    function($ionicLoading){
        this.start = function(){
            $ionicLoading.show({
                templateUrl: 'js/view/spinner.html'
            });
        };

        this.stop = function(){
            $ionicLoading.hide();
        }
}]);