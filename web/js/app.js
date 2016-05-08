/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

var app = angular.module('neema',[
    'ui.router',
    'restangular',
    'ngCookies',
    'angularSpinner',
    'ui-notification',
    'angular-jwt',
    'angularFileUpload',
    'ui.select',
    'ngSanitize',
    'angularModalService'
])
    .constant('UrlApi','http://localhost:8000/api');
