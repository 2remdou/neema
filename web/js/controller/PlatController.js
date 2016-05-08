/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('PlatController',
    ['$scope','UserService','usSpinnerService','$state','FileUploader','PlatService','UrlApi',
    function($scope,UserService,usSpinnerService,$state,FileUploader,PlatService,UrlApi){

        var uploader=$scope.uploader = new FileUploader({
            url : UrlApi+'/plats/image'
        });
        $scope.plat = {};


        uploader.onAfterAddingFile = function(item){
            uploader.clearQueue();
            uploader.queue[0]=item;//une seule image par plat
        };

        uploader.onBeforeUploadItem = function(item) {
            item.formData.push({plat: $scope.plat.id});
        };

        uploader.onCompleteAll = function() {
            var alert = {textAlert:'Enregistrement effectué',typeAlert:'success'};
            $scope.$emit('show.message',{alert:alert});
            usSpinnerService.stop('nt-spinner');
        };


        $scope.save = function(plat){

          if(uploader.queue.length===0){
              var alert = {textAlert:'Veuillez selectionner une image',typeAlert:'danger'};
              $scope.$emit('show.message',{alert:alert});
              return;
          }
            usSpinnerService.spin('nt-spinner');
            PlatService.post(plat);
        };


        $scope.$on('plat.created',function(event,args){
            $scope.plat = args.plat;
            uploader.uploadAll();
        });

}]);