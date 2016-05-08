/**
 * Created by mdoutoure on 07/05/2016.
 */
app.config(['$stateProvider','$urlRouterProvider',function($stateProvider, $urlRouterProvider){

    //$urlRouterProvider.otherwise("/");
    $urlRouterProvider.otherwise( function($injector) {
        var $state = $injector.get("$state");
        $state.go('neema');
    });
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
           }
       })

   ;
}]);