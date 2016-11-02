/**
 * Created by touremamadou on 06/05/2016.
 */

'use strict';

app
    .config(['RestangularProvider','UrlApi',function(RestangularProvider,UrlApi){
        RestangularProvider.setBaseUrl(UrlApi);

    }])
    .config(['NotificationProvider',function(NotificationProvider) {
        NotificationProvider.setOptions({
            delay: 10000,
            startTop: 50,
            startRight: 20,
            verticalSpacing: 20,
            horizontalSpacing: 20,
            positionX: 'center',
            positionY: 'top',
            replaceMessage: true
        });
    }])
    .config(['usSpinnerConfigProvider', function (usSpinnerConfigProvider) {
        var opts = {
            lines: 13 // The number of lines to draw
            , length: 28 // The length of each line
            , width: 14 // The line thickness
            , radius: 42 // The radius of the inner circle
            , scale: 1 // Scales overall size of the spinner
            , corners: 1 // Corner roundness (0..1)
            , color: '#000' // #rgb or #rrggbb or array of colors
            , opacity: 0.25 // Opacity of the lines
            , rotate: 0 // The rotation offset
            , direction: 1 // 1: clockwise, -1: counterclockwise
            , speed: 1 // Rounds per second
            , trail: 60 // Afterglow percentage
            , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
            , zIndex: 2e9 // The z-index (defaults to 2000000000)
            , className: 'spinner' // The CSS class to assign to the spinner
            , top: '50%' // Top position relative to parent
            , left: '50%' // Left position relative to parent
            , shadow: false // Whether to render a shadow
            , hwaccel: false // Whether to use hardware acceleration
            , position: 'fixed' // Element positioning
        }; //loading(spinner)

        usSpinnerConfigProvider.setDefaults(opts);
    }])
    .config(['$httpProvider', function ($httpProvider) {
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/json; charset=utf-8';
        $httpProvider.defaults.headers.put['Content-Type'] = 'application/json; charset=utf-8';
        $httpProvider.defaults.headers.common['Content-Type'] = 'application/json; charset=utf-8';
        $httpProvider.defaults.headers.patch['Content-Type'] = 'application/json; charset=utf-8';

    }])
    .config(['$httpProvider','jwtInterceptorProvider',function Config($httpProvider, jwtInterceptorProvider) {

        //definir la version de l'application
        $httpProvider.defaults.headers.common.version = '1.0.0';

        var requestForRefreshAlreadySend = false;
        jwtInterceptorProvider.tokenGetter = ['jwtHelper','UserService', function(jwtHelper,UserService) {

            var token = UserService.getToken();
            //var refreshToken = UserService.getRefreshToken();
            //
            if(requestForRefreshAlreadySend) return;
            //
            if(!token) return;

            if (jwtHelper.isTokenExpired(token)) {
                requestForRefreshAlreadySend=true;
                UserService.refreshToken().then(function(response){
                    requestForRefreshAlreadySend=false;
                    return UserService.getToken();
                });
            }
            else{
                return token;
            }
        }];

        $httpProvider.interceptors.push('jwtInterceptor');
    }])
;