/**
 * Created by touremamadou on 07/05/2016.
 */
'use strict';

var urlApi = window.location.origin+'/api';

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
    'angularModalService',
    'permission',
    'permission.ui',
    'timer'
])
    .constant('UrlApi',urlApi);
