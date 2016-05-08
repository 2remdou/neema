/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app
    .controller('RestaurantController',
        ['$scope','usSpinnerService','RestaurantService','QuartierService','ModalService','FileUploader',
            'UrlApi','CommuneService','$stateParams','$state',
        function($scope,usSpinnerService,RestaurantService,QuartierService,ModalService,FileUploader,
                 UrlApi,CommuneService,$stateParams,$state){

            if($stateParams.idRestaurant){
                $scope.forUpdate=true;
                RestaurantService.get($stateParams.idRestaurant).then(function(response){
                    $scope.restaurant = response;
                },function(error){
                    log(error);
                });
            }

            var uploader=$scope.uploader = new FileUploader({
                url : UrlApi+'/restaurants/image'
            });

            $scope.nbreLoader = 2;
            usSpinnerService.spin('nt-spinner');
            QuartierService.list();
            CommuneService.list();
            var errorUpload=[];

            uploader.onBeforeUploadItem = function(item) {
                if(angular.isDefined($scope.restaurant.id))
                    item.formData.push({restaurant: $scope.restaurant.id});
            };

            $scope.onErrorItem = function(item, response, status, headers){
                errorUpload.push(item);
            };

            uploader.onCompleteAll = function() {
                if(errorUpload.length!==0) return;

                var alert = {textAlert:'Enregistrement effectué',typeAlert:'success'};
                $scope.$emit('show.message',{alert:alert});
                usSpinnerService.stop('nt-spinner');
                errorUpload.splice(0,errorUpload.length-1);//nettoyer le tableau errorUpload
                $state.go('listRestaurant');

            };

            $scope.save = function(restaurant){
                $scope.formIsSubmit=true;
                if($scope.form.$invalid) return;

                if(uploader.queue.length===0){
                    var alert = {textAlert:'Selectionner au moins une image',typeAlert:'info'};
                    $scope.$emit('show.message',{alert:alert});
                    return;
                }

                usSpinnerService.spin('nt-spinner');
                RestaurantService.post(angular.copy(restaurant));

                $scope.forUpdate = false;


            };

            $scope.selectedRestaurant = function(restaurant){
                $scope.forUpdate = true;
                $scope.restaurant = restaurant;
            };


            $scope.update = function(restaurant){
                $scope.formIsSubmit=true;
                if($scope.form.$invalid) return;

                if(uploader.queue.length===0 && $scope.restaurant.images.length === 0){
                    var alert = {textAlert:'Selectionner au moins une image',typeAlert:'info'};
                    $scope.$emit('show.message',{alert:alert});
                    return;
                }


                usSpinnerService.spin('nt-spinner');
                RestaurantService.update(restaurant);

                $scope.forUpdate = false;
            };

            $scope.delete = function(restaurant){

                ModalService.showModal({
                    templateUrl : 'js/view/modalConfirmation.html',
                    controller: 'ModalConfirmationController',
                    inputs:{
                        texte : 'Voulez vous supprimer ce restaurant'
                    },
                }).then(function(modal){
                    modal.element.modal();
                    modal.close.then(function(result){
                        if(!result) return;
                        usSpinnerService.spin('nt-spinner');
                        RestaurantService.delete(restaurant);
                    })
                })

            };

            $scope.removeImage = function(image,$index){
                image.$index = $index;
                ModalService.showModal({
                    templateUrl : 'js/view/modalConfirmation.html',
                    controller: 'ModalConfirmationController',
                    inputs:{
                        texte : 'Voulez vous supprimer cette image ?'
                    },
                }).then(function(modal){
                    modal.element.modal();
                    modal.close.then(function(result){
                        if(!result) return;
                        usSpinnerService.spin('nt-spinner');
                        RestaurantService.deleteImage(image);
                    })
                })

            };

            $scope.$watch('nbreLoader', function(newValue, oldValue, scope) {
                if($scope.nbreLoader<=0) usSpinnerService.stop('nt-spinner');
            });





            $scope.$on('restaurant.list',function(event,args){
                $scope.restaurants = args.restaurants;
                $scope.nbreLoader--;
                $scope.formIsSubmit=false;
                $scope.restaurant={};
            });

            $scope.$on('quartier.list',function(event,args){
                $scope.quartiers = args.quartiers;
                $scope.nbreLoader--;
            });

            $scope.$on('commune.list',function(event,args){
                $scope.communes = args.communes;
                $scope.nbreLoader--;
            });


            $scope.$on('restaurant.created',function(event,args){
                $scope.restaurant = args.restaurant;
                uploader.uploadAll();
            });

            $scope.$on('restaurant.updated',function(event,args){
                $scope.restaurant = args.restaurant;
                if(uploader.queue.length===0){
                    var alert = {textAlert:'Modification effectuée avec success',typeAlert:'success'};
                    $scope.$emit('show.message',{alert:alert});
                    usSpinnerService.stop('nt-spinner');
                    $state.go('listRestaurant');
                }else{
                    uploader.uploadAll();
                }
            });

            $scope.$on('restaurant.deleted',function(event,args){
                $scope.$emit('show.message',{alert:args.alert});
                usSpinnerService.stop('nt-spinner');
            });
            $scope.$on('restaurant.image.deleted',function(event,args){
                $scope.$emit('show.message',{alert:args.alert});
                $scope.restaurant.images.splice(args.image.$index,1);
                usSpinnerService.stop('nt-spinner');
            });

        }])
.controller('ListRestaurantController',
    ['$scope','$state','RestaurantService','usSpinnerService',
        function($scope,$state,RestaurantService,usSpinnerService){

            $scope.nbreLoader = 1;
            usSpinnerService.spin('nt-spinner');
            RestaurantService.list();
            $scope.edit = function($restaurant){

            };

            $scope.selectedRestaurant = function(restaurant){
                $state.go('editRestaurant',{idRestaurant:restaurant.id});
            };



            $scope.$on('restaurant.list',function(event,args){
                $scope.restaurants = args.restaurants;
                $scope.nbreLoader--;
            });

            $scope.$watch('nbreLoader', function(newValue, oldValue, scope) {
                if($scope.nbreLoader<=0) usSpinnerService.stop('nt-spinner');
            });





        }]);