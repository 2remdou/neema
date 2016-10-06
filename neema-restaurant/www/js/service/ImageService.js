
app.factory('ImageService',[
    '$cordovaCamera','$cordovaFileTransfer','UrlApi','UserService','$rootScope',
    function($cordovaCamera,$cordovaFileTransfer,UrlApi,UserService,$rootScope){
        var self = this;

        var options = {
            destinationType : 1,// Camera.DestinationType.FILE_URI,
		    sourceType : 0, //Camera.PictureSourceType.PHOTOLIBRARY, // Camera.PictureSourceType.PHOTOLIBRARY
		    allowEdit : false,
		    encodingType: 0 //Camera.EncodingType.JPEG,
        };

        function selectImage(callback){

            ionic.Platform.ready(function(){
                $cordovaCamera.getPicture(options).then(
                    function(imageData) {
                        window.resolveLocalFileSystemURL(imageData,function(fileEntry){
                            var nativePath = fileEntry.toURL();
                            callback({nativePath:nativePath,fileData:fileEntry});
                        },error);
                    }, function(err) {
                        error(err);
                    }
                );
            })
        };

        function uploadImagePlat(pathImage,idPlat,callback){
                var server = UrlApi+'/plats/'+idPlat+'/image';
                     var options = {
                        fileKey: "file",
                        fileName: pathImage.substr(pathImage.lastIndexOf('/') + 1),
                        chunkedMode: true,
                        mimeType: "image/jpeg",
                        headers: {
                            "Authorization": 'Bearer '+UserService.getToken()
                        }
                    };
                $cordovaFileTransfer.upload(server,pathImage,options,true).then(function(response){
                    log(response);
                    callback(response);
                },function(err){
                    error(err);
                })
        };

        function error(err){
            log(err);
            var alert = {textAlert:"Erreur lors du choix de l'image",typeAlert:'danger'};
            $rootScope.$broadcast('show.message',{alert:alert});
            log(err);
        }

        return {
            selectImage: selectImage,
            uploadImagePlat:uploadImagePlat
        }
    }
])