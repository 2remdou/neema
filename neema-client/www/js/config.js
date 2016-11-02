/**
 * Created by touremamadou on 06/05/2016.
 */

'use strict';

app
    .config(['RestangularProvider','UrlApi',function(RestangularProvider,UrlApi){
            RestangularProvider.setBaseUrl(UrlApi);
            // RestangularProvider.setDefaultHeaders({version: "1.0.0"});;

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