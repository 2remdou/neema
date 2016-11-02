/**
 * Created by touremamadou on 30/06/2016.
 */
'use strict';

app.controller('TypeCommandeController',
    ['$scope','usSpinnerService','TypeCommandeService','ModalService',
    function($scope,usSpinnerService,TypeCommandeService,ModalService){

        usSpinnerService.spin('nt-spinner');
        $scope.typeCommande={};
        TypeCommandeService.list();

        $scope.save = function(typeCommande){
            $scope.formIsSubmit=true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            TypeCommandeService.post(typeCommande);

            $scope.forUpdate = false;


        };

        $scope.selected = function(typeCommande){
            $scope.forUpdate = true;
            $scope.typeCommande = typeCommande;
        };

        $scope.update = function(typeCommande){
            $scope.formIsSubmit=true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            TypeCommandeService.update(typeCommande);

            $scope.forUpdate = false;
        };

        $scope.delete = function(typeCommande){
            ModalService.showModal({
                templateUrl : 'js/view/modalConfirmation.html',
                controller: 'ModalConfirmationController',
                inputs:{
                    texte : 'Voulez vous supprimer ce type'
                },
            }).then(function(modal){
                modal.element.modal();
                modal.close.then(function(result){
                    if(!result) return;
                    usSpinnerService.spin('nt-spinner');
                    TypeCommandeService.delete(typeCommande);
                })
            });


        };




        $scope.$on('typeCommande.list',function(event,args){
            $scope.typeCommandes = args.typeCommandes;
            usSpinnerService.stop('nt-spinner');
            $scope.formIsSubmit=false;
            $scope.typeCommande={};
        });

        $scope.$on('typeCommande.created',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            TypeCommandeService.list();
        });

        $scope.$on('typeCommande.updated',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            TypeCommandeService.list();
        });

        $scope.$on('typeCommande.deleted',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            TypeCommandeService.list();
        });
}]);