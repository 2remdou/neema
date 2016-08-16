/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';

app
    .controller('AddPlatController',
        ['$scope','ImageService','SpinnerService','PopupService','PlatService','$state',
        function($scope,ImageService,SpinnerService,PopupService,PlatService,$state){
            $scope.plat = {};

             $scope.savePlat = function(plat){
                if(form.$invalid) return;

                if(!$scope.imageSelected){
                    var alert = {textAlert:'Vous devez selectionner une image',typeAlert:'danger'};
                    $scope.$emit('show.message',{alert:alert});
                    return;
                }
                SpinnerService.start();
                //conversion de dureePreparation en seconde
                if(plat.dureePreparation) plat.dureePreparation *= 60;

                PlatService.post($scope.plat,function(idPlat){
                    ImageService.uploadImagePlat($scope.imageSelected.src,idPlat,function(){
                        var alert = {textAlert:'Enregistrement effectué',typeAlert:'success'};
                        $scope.$emit('show.message',{alert:alert});
                        SpinnerService.stop();
                        $state.go('menu');         
                    });
                });
                

             };
            
            $scope.selectImage = function(){
                ImageService.selectImage(function(file){
                    $scope.imageSelected = {src:file.nativePath};

                    $scope.$apply();
                });
             };

        }])
    .controller('EditPlatController',
        ['$scope','ImageService','SpinnerService','PopupService','PlatService','$state','$stateParams',
        function($scope,ImageService,SpinnerService,PopupService,PlatService,$state,$stateParams){
                if(!$stateParams.idPlat){
                    var alert = {textAlert:'Veuillez fournir le plat à modifier',typeAlert:'danger'};
                    $scope.$emit('show.message',{alert:alert});
                    $state.go('menu');
                    return;
                }
                var imageModified = false;
                SpinnerService.start();
                PlatService.getWithCallback($stateParams.idPlat,function(plat){
                    $scope.plat = plat;
                    $scope.imageSelected = {src:plat.imagePlat.webPath+'/'+plat.imagePlat.imageName};
                    SpinnerService.stop();
                });

            

                $scope.updatePlat = function(plat){
                    if(form.$invalid) return;

                    if(!$scope.imageSelected){
                        var alert = {textAlert:'Vous devez selectionner une image',typeAlert:'danger'};
                        $scope.$emit('show.message',{alert:alert});
                        return;
                    }
                    SpinnerService.start();
                    //conversion de dureePreparation en seconde
                    if(plat.dureePreparation) plat.dureePreparation *= 60;

                    PlatService.update($scope.plat,function(alert){
                        if(imageModified){
                            ImageService.uploadImagePlat($scope.imageSelected.src,$stateParams.idPlat,function(){
                                $scope.$emit('show.message',{alert:alert});
                                SpinnerService.stop();
                                $state.go('menu');         
                            });
                        }else{
                                $scope.$emit('show.message',{alert:alert});
                                SpinnerService.stop();
                                $state.go('menu');         

                        }
                    });
                    

                };
                
                $scope.selectImage = function(){
                    ImageService.selectImage(function(file){
                        imageModified = true;
                        $scope.imageSelected = {src:file.nativePath};

                        $scope.$apply();
                    });
                };

        }])
;
