'use strict'
app
    .run(
    ['$ionicPlatform','$state','$location','$rootScope',
    function($ionicPlatform,$state,$location,$rootScope) {
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
            if($rootScope.userConnected){
                $state.go('home');
            }else{
                $state.go('login');
            }
        });


    }])
    .run(
    ['$rootScope','UserService','SpinnerService','$state','Restangular',
        function($rootScope,UserService,SpinnerService,$state,Restangular){

            UserService.initUser();

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
                SpinnerService.stop();
                var defaultMessage = {textAlert:'Ooops, nous allons régler ce petit souci dans quelques instants',typeAlert:'danger'};
/*                
                ionic.Platform.ready(function(){
                    defaultMessage = {textAlert:'Vous devez être connecter à internet',typeAlert:'danger'};
                });
*/
                if(!args.alert){
                    args.alert = defaultMessage;
                }else{
                    if(!args.alert.textAlert) args.alert = defaultMessage;
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
                SpinnerService.stop();

                if(response.status === 401) {
                    $state.go('login');
                    return false;
                }

                if(response.status === 404) {
                    SpinnerService.stop();
                    $rootScope.$broadcast('show.message',{alert:response.data.alert});
                    $state.go('menu');
                    return false;
                }


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
/*    .run(['$rootScope','ConnectivityMonitor',
    function($rootScope,ConnectivityMonitor){ 
        $rootScope.$on("$stateChangeStart", function(event, toState, toParams, fromState, fromParams) {
            if(toState.name !== ''){
                if(!ConnectivityMonitor.checkConnectivity()) event.preventDefault();
            }
    });
    }
    ])
*/
   ;


