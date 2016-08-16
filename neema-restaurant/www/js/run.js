'use strict'
app
    .run(
    ['$ionicPlatform','$state','$location',
    function($ionicPlatform,$state,$location) {
        $ionicPlatform.ready(function() {
            if(window.cordova && window.cordova.plugins.Keyboard) {
                // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
                // for form inputs)
                cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);

                // Don't remove this line unless you know what you are doing. It stops the viewport
                // from snapping when text inputs are focused. Ionic handles this internally for
                // a much nicer keyboard experience.
                cordova.plugins.Keyboard.disableScroll(true);
            }
            if(window.StatusBar) {
                StatusBar.styleDefault();
            }
             // $state.go('suivi');
        });


    }])
    .run(
    ['$rootScope','UserService','SpinnerService','$state','Restangular',
        function($rootScope,UserService,SpinnerService,$state,Restangular){

            UserService.initUser();

            if(!$rootScope.userConnected) $state.go('login');

            $rootScope.search = function(key){
                SpinnerService.start();
                Restangular.all('search').customGET(null,{key:key}).then(function(response){
                    var plats = _.uniqBy(response.plats,'id');
                    $rootScope.$broadcast('search.finished',{plats:plats});
                    SpinnerService.stop();
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                    log(error);
                });
            };

    }])
    .run(
    ['PopupService','$rootScope','SpinnerService',
        function(PopupService,$rootScope,SpinnerService){

            $rootScope.$on('show.message',function(event,args){
                log(args);
                SpinnerService.stop();
                if(!args.alert){
                    args.alert = {textAlert:'Ooops, nous allons régler ce petit souci dans quelques instants',typeAlert:'danger'}
                }
                var alert = args.alert;
                var popup = {
                    title:'Neema',
                    message:alert.textAlert,
                    cssClass:'popup'+capitalizeFirstLetter(alert.typeAlert)
                };
                PopupService.show(popup);
            });

        }])
    .run(
    ['Restangular','$state','SpinnerService','$rootScope',
        function(Restangular,$state,SpinnerService,$rootScope){
            Restangular.setErrorInterceptor(function(response, deferred, responseHandler) {

                if(response.status === 401) {
                    $state.go('login');
                }

                if(response.status === 409) {
                    if($rootScope.isClient)
                        $state.go('codeForActivation');
                    else if($rootScope.isLivreur)
                        $state.go('changePassword');
                }

                SpinnerService.stop();

            });

        }])
    .run(['PermissionStore','UserService','$rootScope','PopupService',
            function(PermissionStore,UserService,$rootScope,PopupService){
        function checkRole(roleName){
            var roles = UserService.getRoles();
            if(roles.indexOf(roleName) !== -1) return true;

            return false;
        }
        PermissionStore.defineManyPermissions(['ROLE_LIVREUR','ROLE_CLIENT'],function(permissionName, transitionProperties){
            return checkRole(permissionName);
        });

        $rootScope.$on('$stateChangePermissionDenied', function(event, toState, toParams, options) {
            var alert = {textAlert:'Vous n\'êtes pas autorisé à acceder à cette partie',typeAlert:'danger'};
                var popup = {
                    title:'Neema',
                    message:alert.textAlert,
                    cssClass:'popup'+capitalizeFirstLetter(alert.typeAlert)
                };
                PopupService.show(popup);
        });
    }])
    .run(['$cordovaPushV5','$ionicPlatform','UserService','$rootScope',
            function($cordovaPushV5,$ionicPlatform,UserService,$rootScope){   
                ionic.Platform.ready(function(){
                    var options = {
                        android: { 
                            senderID: "842997542833"
                        },
                        ios: {
                            alert: "true",
                            badge: "true",
                            sound: "true"
                        },
                        windows: {}
                    };
                    $cordovaPushV5.initialize(options).then(function(){
                        // start listening for new notifications
                        $cordovaPushV5.onNotification();
                        // start listening for errors
                        $cordovaPushV5.onError();

                        $cordovaPushV5.register().then(function(data) {
                            data = {token:data,os:ionic.Platform.platform()};
                            UserService.addDeviceToken(data,function(response){
                                null;
                            });
                        });

                        $rootScope.$on('$cordovaPushV5:notificationReceived', function(event, data){
                            console.log(data.message);
                        });



                    });

                });
    }]) 
   ;


