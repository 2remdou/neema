/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';


app.service('NotificationService',
    ['$rootScope','Restangular',
        function($rootScope,Restangular){

            var self=this;

            var _notificationService = Restangular.all('notifications');

            this.getByUser = function(callback){
                _notificationService.customGET('user-connected').then(function(response){
                    callback(response);
                },function(error){
                    var alert = {textAlert:error.data.textAlert,typeAlert:error.data.typeAlert};
                    $rootScope.$broadcast('show.message',{alert:error.data});
                   log(error);
                });
            };

            this.configRoute = function(notifications){
                _.forEach(notifications,function(notification){
                    if(notification.type === 'commande'){
                        notification.routeName = 'infoCommande';
                        notification.routeParams = {idCommande:notification.idType};
                    }
                });
            };
}]);