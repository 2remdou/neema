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
        'permission.ui'
    ]);
    
    app
         //.constant('UrlApi','http://localhost:8000/api') 
         .constant('UrlApi','http://192.168.1.6:8000/api') 
         .constant('INTERVAL_TIME_FOR_TRY_AGAIN_LOADING',300000) //5 minutes
         .constant('FRAIS_COMMANDE',0.1) //10% du montant de la commande
         //.constant('UrlApi','https://neema.herokuapp.com/api')
    ;


