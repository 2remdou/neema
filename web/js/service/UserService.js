/**
 * Created by touremamadou on 07/05/2016.
 */

app.service('UserService',
    ['$rootScope','Restangular','localStorageFactory',
        function($rootScope,Restangular,localStorageFactory){

            var self=this;

            var _loginService = Restangular.all('users');

            this.login = function(user){
                _loginService.one('token').post('',user).then(function(response){
                    self.setToken(response.token);
                    self.setUser(response.user);
                    $rootScope.$broadcast('user.connected',{token:response.token});
                },function(error){
                    self.clear();
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.setToken = function(token){
                localStorageFactory.set('token',token);
            };

            this.getToken = function(){
                return localStorageFactory.get('token');
            };

            this.setUser = function(user){
                $rootScope.user = user;
                localStorageFactory.setObject('user',user);
                // $cookies.putObject('user',user);
            };

            this.getUser= function(){
                return localStorageFactory.getObject('user');
                // return $cookies.getObject('user');
            };

            this.getRefreshToken = function(){
                return localStorageFactory.get('refresh_token');
            };

            this.setRefreshToken = function(refreshToken){
                localStorageFactory.set('refresh_token',refreshToken);
            };

            this.clear = function(){
                localStorageFactory.clear();
                delete $rootScope.user;
            };

}]);