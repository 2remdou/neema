/**
 * Created by touremamadou on 06/05/2016.
 */
'use strict';
app
    .config(['$stateProvider','$urlRouterProvider',function($stateProvider,$urlRouterProvider) {
        var enabledCache = false;

        $urlRouterProvider.otherwise( function($injector) {
            var $state = $injector.get("$state");
            $state.go('home');
        });
        
        $stateProvider
            .state('home', {
                url: '/home',
                cache: enabledCache,
                templateUrl: 'js/view/home.html',
                controller:'HomeController'
            })
            .state('menu', {
                url: '/menu',
                cache: enabledCache,
                templateUrl: 'js/view/menu.html',
                controller:'MenuController'
            })
            .state('addPlat', {
                url: '/add-plat',
                cache: enabledCache,
                templateUrl: 'js/view/addPlat.html',
                controller:'AddPlatController'
            })
            .state('login', {
                url: '/login',
                cache: enabledCache,
                templateUrl: 'js/view/login.html',
                controller:'LoginController'
            })
            .state('logout', {
                url: '/logout',
                cache: enabledCache,
                controller:'LogoutController'
            })
        ;
    }]);