/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('QuartierController',
    ['$scope','usSpinnerService','QuartierService','CommuneService','ModalService',
    function($scope,usSpinnerService,QuartierService,CommuneService,ModalService){

        $scope.quartier={};
        $scope.nbreLoader = 2;
        usSpinnerService.spin('nt-spinner');
        QuartierService.list();
        CommuneService.list();

        $scope.save = function(quartier){
            $scope.formIsSubmit=true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            QuartierService.post(angular.copy(quartier));

            $scope.forUpdate = false;


        };

        $scope.selectedQuartier = function(quartier){
            $scope.forUpdate = true;
            $scope.quartier = quartier;
        };


        $scope.update = function(quartier){
            $scope.formIsSubmit=true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            QuartierService.update(quartier);

            $scope.forUpdate = false;
        };

        $scope.delete = function(quartier){

            ModalService.showModal({
                templateUrl : 'js/view/modalConfirmation.html',
                controller: 'ModalConfirmationController',
                inputs:{
                    texte : 'Voulez vous supprimer ce quartier'
                },
            }).then(function(modal){
                modal.element.modal();
                modal.close.then(function(result){
                    if(!result) return;
                    usSpinnerService.spin('nt-spinner');
                    QuartierService.delete(quartier);
                })
            })

        };

        $scope.$watch('nbreLoader', function(newValue, oldValue, scope) {
            if($scope.nbreLoader<=0) usSpinnerService.stop('nt-spinner');
        });





        $scope.$on('quartier.list',function(event,args){
            $scope.quartiers = args.quartiers;
            $scope.nbreLoader--;
            $scope.formIsSubmit=false;
            $scope.quartier={};
        });

        $scope.$on('commune.list',function(event,args){
            $scope.communes = args.communes;
            $scope.nbreLoader--;
        });

        $scope.$on('quartier.created',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            QuartierService.list();
        });

        $scope.$on('quartier.updated',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            QuartierService.list();
        });

        $scope.$on('quartier.deleted',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            QuartierService.list();
        });
}]);