/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('CommuneController',
    ['$scope','usSpinnerService','CommuneService','ModalService',
    function($scope,usSpinnerService,CommuneService,ModalService){

        CommuneService.list();

        $scope.save = function(commune){
            $scope.formIsSubmit=true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            CommuneService.post(commune);

            $scope.forUpdate = false;


        };

        $scope.selected = function(commune){
            $scope.forUpdate = true;
            $scope.commune = commune;
        };

        $scope.update = function(commune){
            $scope.formIsSubmit=true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            CommuneService.update(commune);

            $scope.forUpdate = false;
        };

        $scope.delete = function(commune){
            ModalService.showModal({
                templateUrl : 'js/view/modalConfirmation.html',
                controller: 'ModalConfirmationController',
                inputs:{
                    texte : 'Voulez vous supprimer cette commune'
                },
            }).then(function(modal){
                modal.element.modal();
                modal.close.then(function(result){
                    if(!result) return;
                    usSpinnerService.spin('nt-spinner');
                    CommuneService.delete(commune);
                })
            })


        };




        $scope.$on('commune.list',function(event,args){
            $scope.communes = args.communes;
            usSpinnerService.stop('nt-spinner');
            $scope.formIsSubmit=false;
            $scope.commune={};
        });

        $scope.$on('commune.created',function(event,args){
            CommuneService.list();
        });

        $scope.$on('commune.updated',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            CommuneService.list();
        });

        $scope.$on('commune.deleted',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            CommuneService.list();
        });
}]);