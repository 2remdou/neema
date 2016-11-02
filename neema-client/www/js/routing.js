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
            .state('inscription', {
                url: '/inscription',
                cache: enabledCache,
                templateUrl: 'js/view/inscription.html',
                controller:'InscriptionController'
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
            .state('plat', {
                url: '/plat/:idPlat',
                cache: enabledCache,
                templateUrl: 'js/view/plat.html',
                controller:'PlatController'
            })
            .state('restaurant', {
                url: '/restaurant/:idRestaurant',
                cache: enabledCache,
                templateUrl: 'js/view/restaurant.html',
                controller:'RestaurantController'
            })
            .state('commande', {
                url: '/commande',
                cache: enabledCache,
                templateUrl: 'js/view/commande.html',
                controller:'CommandeController'
            })
            .state('infoCommande', {
                url: '/commande/:idCommande',
                cache: enabledCache,
                templateUrl: 'js/view/infoCommande.html',
                controller:'InfoCommandeController'
            })
            .state('listCommande', {
                url: '/list-commandes',
                cache: enabledCache,
                templateUrl: 'js/view/listCommande.html',
                controller:'ListCommandeController'
            })
            .state('listRestaurants', {
                url: '/restaurants',
                cache: enabledCache,
                templateUrl: 'js/view/listRestaurant.html',
                controller:'ListRestaurantController'
            })
            .state('notifications', {
                url: '/notifications',
                cache: enabledCache,
                templateUrl: 'js/view/notification.html',
                controller:'NotificationController'
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
            .state('historiqueCommande', {
                url: '/historique-commande',
                cache: enabledCache,
                templateUrl: 'js/view/historiqueCommande.html',
                controller: 'HistoriqueCommandeController'
            })

        ;
    }]);