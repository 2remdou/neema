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
            .state('infoLivraison', {
                url: '/livraisons/:idLivraison',
                cache: enabledCache,
                templateUrl: 'js/view/infoLivraison.html',
                controller:'InfoLivraisonController'
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
            .state('forgotPassword', {
                url: '/forgotPassword',
                cache: enabledCache,
                templateUrl: 'js/view/forgotPassword.html',
                controller:'ForgotPasswordController'
            })
            .state('codeForReset', {
                url: '/codeForReset',
                cache: enabledCache,
                templateUrl: 'js/view/codeForReset.html',
                controller:'CodeForResetController'
            })
            .state('codeForActivation', {
                url: '/codeForActivation',
                cache: enabledCache,
                templateUrl: 'js/view/codeForReset.html',
                controller:'CodeForActivationController'
            })
            .state('newPassword', {
                url: '/newPassword/:telephone',
                cache: enabledCache,
                templateUrl: 'js/view/newPassword.html',
                controller:'NewPasswordController'
            })
            .state('changePassword', {
                url: '/changePassword',
                cache: enabledCache,
                templateUrl: 'js/view/changePassword.html',
                controller:'NewPasswordController'
            })
            .state('restaurant', {
                url: '/restaurant/:idRestaurant',
                cache: enabledCache,
                templateUrl: 'js/view/restaurant.html',
                controller:'RestaurantController'
            })
            .state('listRestaurants', {
                url: '/restaurants',
                cache: enabledCache,
                templateUrl: 'js/view/listRestaurant.html',
                controller:'ListRestaurantController'
            })
            .state('listPlatsByRestaurant', {
                url: '/plat/restaurant/:idRestaurant',
                cache: enabledCache,
                templateUrl: 'js/view/home.html',
                controller:'PlatByRestaurantController'
            })
            .state('contact', {
                url: '/contact',
                cache: enabledCache,
                templateUrl: 'js/view/contact.html'
            })
            .state('historiqueLivraison', {
                url: '/historique-livraison',
                cache: enabledCache,
                templateUrl: 'js/view/historiqueLivraison.html',
                controller: 'HistoriqueLivraisonController'
            })
        ;
    }]);