/**
 * Created by mdoutoure on 07/05/2016.
 */
app.config(['$stateProvider','$urlRouterProvider',function($stateProvider, $urlRouterProvider){

    $urlRouterProvider.otherwise("/");
    $urlRouterProvider.otherwise( function($injector) {
        var $state = $injector.get("$state");
        $state.go('neema');
    });

    var ROLE_CONNECTED = ['ROLE_CLIENT','ROLE_RESTAURANT','ROLE_ADMIN','ROLE_SUPER_ADMIN'];
    var ROLE_RESTAURANT = ['ROLE_RESTAURANT','ROLE_ADMIN','ROLE_SUPER_ADMIN'];
    var ROLE_CLIENT = ['ROLE_CLIENT','ROLE_ADMIN','ROLE_SUPER_ADMIN'];
    var ROLE_ADMIN = ['ROLE_ADMIN','ROLE_SUPER_ADMIN'];
    var ROLE_SUPER_ADMIN = ['ROLE_SUPER_ADMIN'];

    var nav = {templateUrl: 'js/view/nav.html',controller: 'NavController'};
   $stateProvider
       .state('neema',{
           url:'/',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/index.html',
                   controller: 'IndexController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_CONNECTED,
                   redirectTo:'login'
               }
           }

       })
       .state('login',{
           url:'/login',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/login.html',
                   controller: 'LoginController'
               }
           }
       })
       .state('plat',{
           url:'/plat',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/plat.html',
                   controller: 'PlatController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_RESTAURANT,
                   redirectTo:'neema'
               }
           }

       })
       .state('editPlat',{
           url:'/editPlat/:idPlat',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/editPlat.html',
                   controller: 'PlatController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_RESTAURANT,
                   redirectTo:'neema'
               }
           }

       })
       .state('menu',{
           url:'/menu',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/menu.html',
                   controller: 'PlatController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_RESTAURANT,
                   redirectTo:'neema'
               }
           }
       })
       .state('commune',{
           url:'/commune',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/commune.html',
                   controller: 'CommuneController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_ADMIN,
                   redirectTo:'neema'
               }
           }

       })
       .state('quartier',{
           url:'/quartier',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/quartier.html',
                   controller: 'QuartierController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_ADMIN,
                   redirectTo:'neema'
               }
           }
       })
       .state('restaurant',{
           url:'/restaurant',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/restaurant.html',
                   controller: 'RestaurantController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_ADMIN,
                   redirectTo:'neema'
               }
           }
       })
       .state('lieuLivraison',{
           url:'/lieu-livraison',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/lieuLivraison.html',
                   controller: 'LieuLivraisonController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_ADMIN,
                   redirectTo:'neema'
               }
           }
       })
       .state('inscription',{
           url:'/inscription',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/inscription.html',
                   controller: 'InscriptionController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_ADMIN,
                   redirectTo:'neema'
               }
           }
       })
       .state('profil',{
           url:'/profil',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/profil.html',
                   controller: 'ProfilController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_CONNECTED,
                   redirectTo:'neema'
               }
           }
       })
       .state('resetPassword',{
           url:'/resetPassword',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/resetPassword.html',
                   controller: 'ResetPasswordController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_ADMIN,
                   redirectTo:'neema'
               }
           }
       })
       .state('editRestaurant',{
           url:'/restaurant/edit/:idRestaurant',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/restaurant.html',
                   controller: 'RestaurantController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_RESTAURANT,
                   redirectTo:'neema'
               }
           }
       })
       .state('listRestaurant',{
           url:'/restaurant/list/',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/listRestaurant.html',
                   controller: 'ListRestaurantController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_ADMIN,
                   redirectTo:'neema'
               }
           }
       })
       .state('livreur',{
           url:'/livreur',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/livreur.html',
                   controller: 'LivreurController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_ADMIN,
                   redirectTo:'neema'
               }
           }

       })
       .state('etatCommande',{
           url:'/etat-commande',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/etatCommande.html',
                   controller: 'EtatCommandeController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_ADMIN,
                   redirectTo:'neema'
               }
           }

       })
       .state('typeCommande',{
           url:'/type-commande',
           views:{
               'nav':nav,
               'content':{
                   templateUrl: 'js/view/typeCommande.html',
                   controller: 'TypeCommandeController'
               }
           },
           data: {
               permissions: {
                   only: ROLE_ADMIN,
                   redirectTo:'neema'
               }
           }

       })


   ;
}]);