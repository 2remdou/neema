/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

app.controller('LivreurController',
    ['$scope','usSpinnerService','LivreurService','ModalService','UserService',
    function($scope,usSpinnerService,LivreurService,ModalService,UserService){

        $scope.livreur={};
        LivreurService.list();

        $scope.save = function(livreur){
            $scope.formIsSubmit=true;
            if($scope.form.$invalid) return;

            usSpinnerService.spin('nt-spinner');
            var user = {
                username:livreur.code,
                nom:livreur.nom,
                prenom:livreur.prenom,
                password:livreur.password,
                telephone:livreur.telephone
            };
            UserService.inscriptionLivreur(user);

        };

        $scope.resetPassword = function(livreur){
            usSpinnerService.spin('nt-spinner');

            UserService.reset(livreur.user);
        };



        $scope.$on('user.registred',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});
            LivreurService.list();
            usSpinnerService.stop('nt-spinner');
        });

        $scope.$on('user.password.reseted',function(event,args){
            $scope.$emit('show.message',{alert:args.alert});

            usSpinnerService.stop('nt-spinner');
        });


        $scope.$on('livreur.list',function(event,args){
            $scope.livreurs = args.livreurs;
            usSpinnerService.stop('nt-spinner');
            $scope.formIsSubmit=false;
            $scope.livreur={};
        });

    }]);