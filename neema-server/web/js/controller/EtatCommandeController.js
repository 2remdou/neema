/**
 * Created by touremamadou on 30/06/2016.
 */
'use strict';

app.controller('EtatCommandeController',
    ['$scope','usSpinnerService','EtatCommandeService','ModalService',
    function($scope,usSpinnerService,EtatCommandeService,ModalService){

        usSpinnerService.spin('nt-spinner');
        $scope.etatCommande={};
        EtatCommandeService.list();

        $scope.save = function(etatCommande){
            $scope.formIsSubmit=true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            EtatCommandeService.post(etatCommande);

            $scope.forUpdate = false;


        };

        $scope.selected = function(etatCommande){
            $scope.forUpdate = true;
            $scope.etatCommande = etatCommande;
        };

        $scope.update = function(etatCommande){
            $scope.formIsSubmit=true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            EtatCommandeService.update(etatCommande);

            $scope.forUpdate = false;
        };

        $scope.delete = function(etatCommande){
            ModalService.showModal({
                templateUrl : 'js/view/modalConfirmation.html',
                controller: 'ModalConfirmationController',
                inputs:{
                    texte : 'Voulez vous supprimer cet etat'
                },
            }).then(function(modal){
                modal.element.modal();
                modal.close.then(function(result){
                    if(!result) return;
                    usSpinnerService.spin('nt-spinner');
                    EtatCommandeService.delete(etatCommande);
                })
            });


        };




        $scope.$on('etatCommande.list',function(event,args){
            $scope.etatCommandes = args.etatCommandes;
            usSpinnerService.stop('nt-spinner');
            $scope.formIsSubmit=false;
            $scope.etatCommande={};
        });

        $scope.$on('etatCommande.created',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            EtatCommandeService.list();
        });

        $scope.$on('etatCommande.updated',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            EtatCommandeService.list();
        });

        $scope.$on('etatCommande.deleted',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            EtatCommandeService.list();
        });
}]);