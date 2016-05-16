/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('PlatController',
    ['$scope','UserService','usSpinnerService','$state','UploaderService','PlatService','ModalService','$filter','$stateParams',
    function($scope,UserService,usSpinnerService,$state,UploaderService,PlatService,ModalService,$filter,$stateParams){

        if($state.current.name === 'menu'){
            PlatService.listByRestaurantByUserConnected();
            usSpinnerService.spin('nt-spinner');

        }else if($state.current.name === 'editPlat'){
            if(!$stateParams.idPlat){
                var alert = {textAlert:'Veuillez fournir le plat à modifier',typeAlert:'danger'};
                $scope.$emit('show.message',{alert:alert});
            }
            PlatService.get($stateParams.idPlat).then(function(response){
                $scope.plat = response;
            },function(error){
                var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                $scope.$emit('show.message',{alert:alert});
                $state.go('menu');

            });
        }
        var uploader=$scope.uploader = UploaderService.getUploader('/plats/image');

        $scope.plat = {};
        $scope.nbreLoader=1;


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

        var imageSelected = function(){
            if(uploader.queue.length===0 && !$scope.plat.image){
                return false
            }
            return true;
        };

        $scope.save = function(plat){
            if(!imageSelected()){
                var alert = {textAlert:'Veuillez selectionner une image',typeAlert:'danger'};
                $scope.$emit('show.message',{alert:alert});
                return;
            }
            usSpinnerService.spin('nt-spinner');
            PlatService.post(plat);
        };

        $scope.edit = function(plat){
            if(!imageSelected()){
                var alert = {textAlert:'Veuillez selectionner une image',typeAlert:'danger'};
                $scope.$emit('show.message',{alert:alert});
                return;
            }
            usSpinnerService.spin('nt-spinner');
            PlatService.update(plat);


        };



        $scope.onMenu = function(plat) {
            var p = _.find($scope.menuInitial, {id: plat.id});

            if(p){ // si le plat existe dans le menu initial
                $scope.platsOnMenu[_.findIndex($scope.platsOnMenu, {id: p.id})].onMenu = plat.onMenu;
            }else{ // si le plat n'existe pas
                if(plat.onMenu){ // si il a été coché
                    $scope.platsOnMenu.push(plat); // on l'insert dans le menu
                }else{ // n'existe pas dans le menu initial,déja inseré dans platsOnMenu,il faut le supprimer
                    $scope.platsOnMenu.splice(_.findIndex($scope.platsOnMenu, {id: plat.id}),1); // on le supprime du menu
                }
            }
        };

        $scope.valider = function(){
            usSpinnerService.spin('nt-spinner');
            PlatService.updateMenu($scope.platsOnMenu);
        };

        $scope.$watch('nbreLoader', function(newValue, oldValue, scope) {
            if($scope.nbreLoader<=0) usSpinnerService.stop('nt-spinner');
        });

//***************EVENT***************************

        $scope.$on('plat.created',function(event,args){
            $scope.plat = args.plat;
            uploader.uploadAll();
        });
        $scope.$on('plat.updated',function(event,args){
            if(uploader.queue.length === 0){
                $scope.$emit('show.message',{alert:args.alert});
                usSpinnerService.stop('nt-spinner');
            }else{
                uploader.uploadAll();
            }
            $state.go('menu');
        });

        $scope.$on('plat.list',function(event,args){
            $scope.plats = args.plats;
            $scope.menuInitial = $filter('filter')($scope.plats,{onMenu:true});
            $scope.platsOnMenu = angular.copy($scope.menuInitial);
            $scope.nbreLoader--;
        });

        $scope.$on('menu.updated',function(event,args){
            if(args.fail.length!=0){
                var alert = {textAlert:'le menu a été mis à jour avec certaines erreurs',typeAlert:'warning'};
                $scope.$emit('show.message',{alert:args.alert});
            }else{
                $scope.$emit('show.message',{alert:args.alert});
            }
            usSpinnerService.stop('nt-spinner');
        });


    }]);