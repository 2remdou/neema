/**
 * Created by touremamadou on 07/05/2016.
 */

app.service('UserService',
    ['$rootScope','Restangular','localStorageFactory','jwtHelper',
        function($rootScope,Restangular,localStorageFactory,jwtHelper){

            var that=this;
            var user=null;

            var _loginService = Restangular.all('users');


            this.list = function(){
                _loginService.getList().then(function(response){
                    $rootScope.$broadcast('user.list',{users:response});
                },function(error){
                    log(error);
                });
            };

            this.login = function(user){
                _loginService.one('token').post('',user).then(function(response){
                    that.setToken(response.token);
                    $rootScope.userConnnected = that.getUser();
                    $rootScope.$broadcast('user.connected',{token:response.token});
                },function(error){
                    that.clear();
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.inscription = function(user){
                user.restaurant = extractId(user.restaurant);
                _loginService.one('userRestaurant').post('',user).then(function(response){
                    $rootScope.$broadcast('user.registred',{alert:response.data});
                },function(error){
                    that.clear();
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.changePassword = function(user){
                _loginService.customPUT(user,'changePassword').then(function(response){
                    $rootScope.$broadcast('user.password.changed',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.reset = function(user){
                _loginService.customPUT(user,'reset/'+user.id).then(function(response){
                    $rootScope.$broadcast('user.password.reseted',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.get = function(id){
                return _loginService.get(id);
            };

            this.edit = function(user){
                _loginService.customPUT(user,'edit/'+user.id).then(function(response){
                    that.setToken(response.data.token);
                    var alert = {textAlert:response.data.textAlert,typeAlert:response.data.typeAlert};
                    $rootScope.$broadcast('user.edited',{user:response.data.user,alert:alert});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.logout = function(){
                that.clear();
                $rootScope.$broadcast('user.logout');

            };

            this.setToken = function(token){
                localStorageFactory.set('token',token);
            };

            this.getToken = function(){
                return localStorageFactory.get('token');
            };

            this.getUser= function(){
                if(user) return user; //si la fonction déja executée

                if(that.getToken()){ // si un token existe
                    tokenDecoded= jwtHelper.decodeToken(that.getToken());
                    user = {
                        id : tokenDecoded.id,
                        nom:tokenDecoded.nom,
                        prenom:tokenDecoded.prenom,
                        username:tokenDecoded.username,
                        roles : tokenDecoded.roles,
                        restaurant : tokenDecoded.restaurant
                    };
                    return user;
                }
            };

            this.getRoles = function(){
                if(user) return user.roles;
                return ['ANONYMOUS'];
            };

            this.getRefreshToken = function(){
                return localStorageFactory.get('refresh_token');
            };

            this.setRefreshToken = function(refreshToken){
                localStorageFactory.set('refresh_token',refreshToken);
            };

            this.clear = function(){
                localStorageFactory.clear();
                delete $rootScope.userConnnected;
            };

}]);