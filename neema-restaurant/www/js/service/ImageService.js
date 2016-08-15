'use strict';

app.factory('ImageService',[
    '$cordovaCamera',function($cordovaCamera){
        var self = this;

        var options = {
            destinationType : 1,// Camera.DestinationType.FILE_URI,
		    sourceType : 1, //Camera.PictureSourceType.PHOTOLIBRARY, // Camera.PictureSourceType.PHOTOLIBRARY
		    allowEdit : false,
		    encodingType: 0 //Camera.EncodingType.JPEG,
        };

        function selectImage(){

            ionic.Platform.ready(function(){
                $cordovaCamera.getPicture(options).then(
                    function(imageData) {
                        alert(imageData);
                    }, function(err) {
                        alert(err);
                    }
                );
            })
        }

        return {
            selectImage: selectImage
        }
    }
])