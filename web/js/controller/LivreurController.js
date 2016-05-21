/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('LivreurController',
    ['$scope','usSpinnerService','LivreurService','ModalService',
    function($scope,usSpinnerService,LivreurService,ModalService){

        $scope.livreur={};
        LivreurService.list();

        $scope.save = function(livreur){
            $scope.formIsSubmit=true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            LivreurService.post(livreur);

            $scope.forUpdate = false;


        };

        $scope.selected = function(livreur){
            $scope.forUpdate = true;
            $scope.livreur = livreur;
        };

        $scope.update = function(livreur){
            $scope.formIsSubmit=true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            LivreurService.update(livreur);

            $scope.forUpdate = false;
        };

        $scope.delete = function(livreur){
            ModalService.showModal({
                templateUrl : 'js/view/modalConfirmation.html',
                controller: 'ModalConfirmationController',
                inputs:{
                    texte : 'Voulez vous supprimer ce livreur'
                },
            }).then(function(modal){
                modal.element.modal();
                modal.close.then(function(result){
                    if(!result) return;
                    usSpinnerService.spin('nt-spinner');
                    LivreurService.delete(livreur);
                })
            });


        };




        $scope.$on('livreur.list',function(event,args){
            $scope.livreurs = args.livreurs;
            usSpinnerService.stop('nt-spinner');
            $scope.formIsSubmit=false;
            $scope.livreur={};
        });

        $scope.$on('livreur.created',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            LivreurService.list();
        });

        $scope.$on('livreur.updated',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            LivreurService.list();
        });

        $scope.$on('livreur.deleted',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            LivreurService.list();
        });
}]);