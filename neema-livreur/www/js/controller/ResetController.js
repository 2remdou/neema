/**
 * Created by touremamadou on 16/05/2016.
 */

'use strict';
app
    //permet le saisir le numero de telephone, qui sera utilisé pour le code d'activation
    .controller('ForgotPasswordController',
        ['$scope','UserService','$state','SpinnerService','localStorageFactory',
        function($scope,UserService,$state,SpinnerService,localStorageFactory){
            $scope.user = {};

            $scope.reset = function(form){
                form.$submitted = true;
                if(form.$invalid) return;

                SpinnerService.start();
                UserService.resetClient($scope.user);

            };


            //*********LISTENER**********

            $scope.$on('user.password.reseted',function(event,args){
                SpinnerService.stop();
                //pour pouvoir le recuperer lors de l'envoi du code
                localStorageFactory.set('telephone',$scope.user.telephone);
                $state.go('codeForReset');
            });


        }])
    //permet de saisir de code reçu par sms
    .controller('CodeForResetController',
        ['$scope','UserService','SpinnerService','$state','localStorageFactory',
        function($scope,UserService,SpinnerService,$state,localStorageFactory){

            $scope.user = {};
            $scope.valider = function(form){
                form.$submitted = true;
                if(form.$invalid) return;

                SpinnerService.start();
                UserService.checkCode(localStorageFactory.get('telephone'),$scope.user.code);

            };

            $scope.sendBackCode = function(){
                SpinnerService.start();
                UserService.sendBackCodeActivation(localStorageFactory.get('telephone'));

            };

            //*********LISTENER**********

            $scope.$on('user.code.checked',function(event,args){
                SpinnerService.stop();
                $state.go('newPassword');
            });

            $scope.$on('user.code.sendback',function(event,args){
                SpinnerService.stop();
                $scope.$emit('show.message',{alert:args.alert});
            });

    }])
    .controller('NewPasswordController',
        ['$scope','UserService','SpinnerService','PopupService','localStorageFactory','$state','$rootScope',
        function($scope,UserService,SpinnerService,PopupService,localStorageFactory,$state,$rootScope){

            $scope.user = {};
            $scope.reset = function(form){
                form.$submitted = true;
                if(form.$invalid) return;

                if($scope.user.newPassword !== $scope.user.confirmationPassword){
                    var popup = {
                        title:'Mot de passe',
                        message:'Les deux mots de passe doivent être identique',
                        cssClass:'popupDanger'
                    };
                    PopupService.show(popup);
                    return;
                }

                SpinnerService.start();
                if($rootScope.isClient){
                    UserService.newPassword(localStorageFactory.get('telephone'),$scope.user.newPassword,$scope.user.confirmationPassword);
                }else if($rootScope.isLivreur){
                    UserService.changePassword($scope.user);
                }

            };


            //*********LISTENER**********

            $scope.$on('user.password.changed',function(event,args){
                SpinnerService.stop();
                var popup = {
                    title:'Neema',
                    message:args.alert.textAlert,
                    cssClass:'popupSuccess'
                };
                PopupService.show(popup).then(function(res){
                    $state.go('login');
                });

            });


        }])

;
