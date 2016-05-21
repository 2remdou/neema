/**
 * Created by touremamadou on 20/08/2015.
 */

'use strict';

app
    .run(['$rootScope','Notification','usSpinnerService','UserService',
        function($rootScope,Notification,usSpinnerService,UserService){

            $rootScope.userConnnected = UserService.getUser();
            $rootScope.$on('show.message',function(event,args){
                var alert = args.alert;
                var opt= {'message': alert.textAlert}
                if(alert.typeAlert==='danger'){
                    Notification.error(opt);
                    usSpinnerService.stop('nt-spinner');
                }
                else if(alert.typeAlert==='success'){
                    Notification.success(opt);
                }
                else if(alert.typeAlert==='info'){
                    Notification.info(opt);
                }
                else if(alert.typeAlert==='warning'){
                    Notification.warning(opt);
                }
            });

            $rootScope.$on('stop.spinner',function(event,args){
                usSpinnerService.stop('nt-spinner');
            });
}])

    .run(['Restangular','$rootScope','usSpinnerService','$state','ModalService','UserService',
        function(Restangular,$rootScope,usSpinnerService,$state,ModalService,UserService){

        Restangular.setErrorInterceptor(function(response, deferred, responseHandler) {
            if(response.status === 401) {
                usSpinnerService.stop('nt-spinner');
                var alert = {textAlert:'Vous devez vous connectez pour effectuer cette opération',typeAlert:'danger'};
                $rootScope.$broadcast('show.message',{alert:alert});
                $state.go('login');


                return false; // error handled
            }
            if(response.status === 403) {
                usSpinnerService.stop('nt-spinner');
                var alert = {textAlert:response.data,typeAlert:'danger'};
                $rootScope.$broadcast('show.message',{alert:alert});


                return false; // error handled
            }
            if(response.status === 409) {
                usSpinnerService.stop('nt-spinner');
                var alert = {textAlert:response.data,typeAlert:'danger'};
                $rootScope.$broadcast('show.message',{alert:alert});

                return false; // error handled

                ModalService.showModal({
                    templateUrl : 'js/view/modalChangePassword.html',
                    controller: 'ProfilController',
                }).then(function(modal){
                    modal.element.modal();
                    modal.close.then(function(){
                    });
                });

                return false; // error handled
            }
            if(response.status === 500) {
                usSpinnerService.stop('nt-spinner');
                var alert = {textAlert:'Une erreur inconnue est survenue, veuillez contacter l\'administrateur',typeAlert:'danger'};
                $rootScope.$broadcast('show.message',{alert:alert});


                return false; // error handled
            }

            return true; // error not handled
        });

    }])

    .run(['PermissionStore','UserService','$rootScope',function(PermissionStore,UserService,$rootScope){

        function checkRole(roleName){
            var roles = UserService.getRoles();
            if(roles.indexOf(roleName) !== -1) return true;

            return false;
        }
        PermissionStore.defineManyPermissions(['ROLE_RESTAURANT','ROLE_ADMIN','ROLE_SUPER_ADMIN'],function(permissionName, transitionProperties){
            return checkRole(permissionName);
        });

        $rootScope.$on('$stateChangePermissionDenied', function(event, toState, toParams, options) {
            var alert = {textAlert:'Vous n\'êtes pas autorisé à acceder à cette partie',typeAlert:'danger'};
            $rootScope.$broadcast('show.message',{alert:alert});
        });
    }])


;