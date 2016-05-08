/**
 * Created by touremamadou on 07/05/2016.
 */

app.service('PlatService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            var self=this;

            var _platService = Restangular.all('plats');

            this.post = function(plat){
                _platService.post(plat).then(function(response){
                    $rootScope.$broadcast('plat.created',{plat:response.data.plat});
                },function(error){
                    log(error);
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };
}]);