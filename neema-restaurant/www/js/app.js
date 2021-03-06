// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
'use strict';

/*window.onerror = function (errorMsg, url, lineNumber) {
         alert('Error: ' + errorMsg + ' Script: ' + url + ' Line: ' + lineNumber);
    };
*/
var app = angular.module('neema',
    [
        'ionic',
        'ngCordova',
        'ngMessages',
        'restangular',
        'angular-jwt',
        'permission',
        'permission.ui',
        'btford.socket-io'
    ]);
    
    app
            .constant('UrlApi','http://localhost:8000/api') 
            .constant('URL_SOCKET','localhost:5000') 
                //   .constant('UrlApi','http://192.168.43.21:8000/api') 
                //   .constant('URL_SOCKET','192.168.43.21:5000') 
         .constant('INTERVAL_TIME_FOR_TRY_AGAIN_LOADING',300000) //5 minutes
         .constant('FRAIS_COMMANDE',0) //0% du montant de la commande
                //  .constant('UrlApi','https://neema.herokuapp.com/api')
                //  .constant('URL_SOCKET','https://neema-nodejs.herokuapp.com') 
    ;


