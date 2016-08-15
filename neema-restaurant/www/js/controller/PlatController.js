/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';
app
    .controller('AddPlatController',
        ['$scope','ImageService',
        function($scope,ImageService){
            $scope.images=[{nom:'image'}]

            $scope.selectImage = function(index){
                $scope.images.push({nom:'image'});
                ImageService.selectImage();
            }
        }])
    .controller('EditImageController',
        ['$scope',,
        function($scope){

    }])
;
