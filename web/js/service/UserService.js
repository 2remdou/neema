/**
 * Created by touremamadou on 07/05/2016.
 */

'use strict';

app.service('UserService',
    ['$rootScope','Restangular','localStorageFactory','jwtHelper','$q',
        function($rootScope,Restangular,localStorageFactory,jwtHelper,$q){

            var that=this;
            var user=null;

            var _userService = Restangular.all('users');


            this.list = function(){
                _userService.getList().then(function(response){
                    $rootScope.$broadcast('user.list',{users:response});
                },function(error){
                    log(error);
                });
            };

            this.login = function(user){
                _userService.one('token').post('',user).then(function(response){
                    that.setToken(response.token);
                    that.setRefreshToken(response.refresh_token);
                    $rootScope.userConnnected = that.getUser();
                    $rootScope.$broadcast('user.connected',{token:response.token});
                },function(error){
                    that.clear();
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.refreshToken = function(){
                var deffered = $q.defer();
                _userService.one('token/refresh').post('',{refresh_token:that.getRefreshToken()}).then(function(response){
                    that.setToken(response.token);
                    that.setRefreshToken(response.refresh_token);
                    deffered.resolve(response);
                },function(error){
                    deffered.reject(error);
                });
                return deffered.promise;
            };

            this.inscription = function(user){
                user.restaurant = extractId(user.restaurant);
                _userService.one('userRestaurant').post('',user).then(function(response){
                    $rootScope.$broadcast('user.registred',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.inscriptionClient = function(user){
                _userService.post(user).then(function(response){
                    that.setToken(response.data.token);
                    $rootScope.userConnnected = that.getUser();
                    $rootScope.$broadcast('user.registred',{alert:response.data});
                },function(error){
                    that.clear();
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.inscriptionLivreur = function(user){
                _userService.one('user-livreur').post('',user).then(function(response){
                    $rootScope.$broadcast('user.registred',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.addDeviceToken = function(token,callback){
                _userService.one('device-token').post('',{token:token}).then(function(response){
                    callback(response);
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.changePassword = function(user){
                _userService.customPUT(user,'changePassword').then(function(response){
                    $rootScope.$broadcast('user.password.changed',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.newPassword = function(username,newPassword,confirmationPassword){
                _userService.customPUT({username:username,newPassword:newPassword,confirmationPassword:confirmationPassword},'newPassword').then(function(response){
                    $rootScope.$broadcast('user.password.changed',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.reset = function(user){
                _userService.customPUT(user,'reset/'+user.id).then(function(response){
                    $rootScope.$broadcast('user.password.reseted',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.resetClient = function(user){
                _userService.customPUT(null,'resetClient/'+user.telephone).then(function(response){
                    $rootScope.$broadcast('user.password.reseted',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.enabled = function(code){
                _userService.customPUT(code,'enabled').then(function(response){
                    $rootScope.$broadcast('user.account.enabled',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.sendBackCodeActivation = function(telephone){
                _userService.customPUT({username:telephone},'sendBackActivationCode').then(function(response){
                    $rootScope.$broadcast('user.code.sendback',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.checkCode = function(telephone,code){
                _userService.customPUT({username:telephone,code:code},'checkCode').then(function(response){
                    $rootScope.$broadcast('user.code.checked',{alert:response.data});
                },function(error){
                    $rootScope.$broadcast('show.message',{alert:error.data});
                });
            };

            this.get = function(id){
                return _userService.get(id);
            };

            this.edit = function(user){
                _userService.customPUT(user,'edit/'+user.id).then(function(response){
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
                    var tokenDecoded= jwtHelper.decodeToken(that.getToken());
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

            this.initUser = function(){
                $rootScope.userConnected = that.getUser();
                $rootScope.isClient = that.isClient();
                $rootScope.isLivreur = that.isLivreur();
            };

            this.isClient = function(){
                return _.findIndex(that.getRoles(),function(role){
                            return role==='ROLE_CLIENT';
                        })
                        ===-1?false:true;
            };

            this.isLivreur = function(){
                return _.findIndex(that.getRoles(),function(role){
                    return role==='ROLE_LIVREUR';
                })
                ===-1?false:true;
            };

            this.getRefreshToken = function(){
                return localStorageFactory.get('refresh_token');
            };

            this.setRefreshToken = function(refreshToken){
                localStorageFactory.set('refresh_token',refreshToken);
            };

            this.clear = function(){
                localStorageFactory.clear();
                user = null;
                delete $rootScope.userConnnected;
            };

}]);