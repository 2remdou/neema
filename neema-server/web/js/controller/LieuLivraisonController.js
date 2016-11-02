/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app
    .controller('LieuLivraisonController',
        ['$scope','usSpinnerService','LieuLivraisonService','ModalService','$stateParams',
            'CommuneService','QuartierService',
        function($scope,usSpinnerService,LieuLivraisonService,ModalService,$stateParams,
                 CommuneService,QuartierService){

            $scope.lieuLivraison={};

            $scope.nbreLoader = 3;
            $scope.$watch('nbreLoader', function(newValue, oldValue, scope) {
                if($scope.nbreLoader<=0) usSpinnerService.stop('nt-spinner');
            });

            CommuneService.list(function(communes){
               $scope.communes = communes;
                $scope.nbreLoader--;
            });

            QuartierService.list(function(quartiers){
               $scope.quartiers = quartiers;
                $scope.nbreLoader--;
            });

            function loadList(){
                usSpinnerService.spin('nt-spinner');
                LieuLivraisonService.list(function(lieuLivraisons){
                    $scope.lieuLivraisons = lieuLivraisons;
                    usSpinnerService.stop('nt-spinner');
                    $scope.nbreLoader--;
                });
            }

            loadList();

            if($stateParams.idLieuLivraison){
                $scope.forUpdate=true;
                LieuLivraisonService.get($stateParams.idLieuLivraison,function(lieuLivraison){
                    $scope.lieuLivraison = lieuLivraison;
                });
            }
            $scope.save = function(lieuLivraison){
                $scope.formIsSubmit=true;
                if($scope.form.$invalid) return;

                var lieu = angular.copy(lieuLivraison);
                lieu.quartier = lieu.quartier.id;

                usSpinnerService.spin('nt-spinner');
                LieuLivraisonService.post(lieu,function(alert){
                    $scope.$emit('show.message',{alert:alert});
                    $scope.lieuLivraison = {};
                    usSpinnerService.stop('nt-spinner');
                    loadList();
                });

                $scope.forUpdate = false;


            };

            $scope.selectedLieuLivraison = function(lieuLivraison){
                $scope.forUpdate = true;
                $scope.lieuLivraison = lieuLivraison;
            };


            $scope.update = function(lieuLivraison){
                $scope.formIsSubmit=true;
                if($scope.form.$invalid) return;

                lieuLivraison.quartier = lieuLivraison.quartier.id;

                usSpinnerService.spin('nt-spinner');

                LieuLivraisonService.update(lieuLivraison,function(alert){
                    $scope.$emit('show.message',{alert:alert});
                    usSpinnerService.stop('nt-spinner');
                    $scope.lieuLivraison = {};
                    loadList();
                });

                $scope.forUpdate = false;
            };

            $scope.delete = function(lieuLivraison){

                ModalService.showModal({
                    templateUrl : 'js/view/modalConfirmation.html',
                    controller: 'ModalConfirmationController',
                    inputs:{
                        texte : 'Voulez vous supprimer ce lieu'
                    },
                }).then(function(modal){
                    modal.element.modal();
                    modal.close.then(function(result){
                        if(!result) return;
                        usSpinnerService.spin('nt-spinner');
                        LieuLivraisonService.delete(lieuLivraison,function(alert){
                            $scope.$emit('show.message',{alert:alert});
                            usSpinnerService.stop('nt-spinner');
                            loadList();
                        });
                    })
                })

            };


        }])
.controller('ListLieuLivraisonController',
    ['$scope','$state','LieuLivraisonService','usSpinnerService',
        function($scope,$state,LieuLivraisonService,usSpinnerService){

            usSpinnerService.spin('nt-spinner');
            LieuLivraisonService.list(function (lieuLivraisons) {
                $scope.lieuLivraisons = lieuLivraisons;
            },function(error){
                $scope.$emit('show.message',{alert:error.data});
            });
            $scope.edit = function($lieuLivraison){

            };

            $scope.selectedLieuLivraison = function(lieuLivraison){
                $state.go('editLieuLivraison',{idLieuLivraison:lieuLivraison.id});
            };

        }]);