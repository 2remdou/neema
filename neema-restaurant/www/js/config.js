/**
 * Created by touremamadou on 06/05/2016.
 */

'use strict';

app
    .config(['RestangularProvider','UrlApi',function(RestangularProvider,UrlApi){
    RestangularProvider.setBaseUrl(UrlApi);
    }])
    .config(['$httpProvider','jwtInterceptorProvider',function Config($httpProvider, jwtInterceptorProvider) {
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