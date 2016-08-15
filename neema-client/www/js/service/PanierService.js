/**
 * Created by touremamadou on 02/06/2016.
 */
'use strict';

app.service('PanierService',
    ['localStorageFactory','$rootScope',
    function(localStorageFactory,$rootScope){
        var that = this;
        var panier = [];
        var restaurantPanier=null;

        this.getInLocalStorage = function(){
            panier = localStorageFactory.getArray('panier');
            if(panier.length===0) return;
            restaurantPanier = panier[0].restaurant;
        };

        this.getPanier = function(){
            return panier;
        };

        this.add = function(plat){
            if(!restaurantPanier){
                restaurantPanier = plat.restaurant;
            }
            if(restaurantPanier.id !== plat.restaurant.id){
                var alert = {
                    typeAlert : 'danger',
                    textAlert : 'Une commande ne peut avoir des plats de differents restaurants'
                };
                $rootScope.$broadcast('show.message',{alert:alert});
                return;
            }
            var index=_.findIndex(panier, {id: plat.id});

            if(index === -1){
                panier.push(plat);
            }

            localStorageFactory.setArray('panier',panier);
        };

        this.remove = function(plat){
            if(!plat) return;
            var index=_.findIndex(panier, {id: plat.id});
            if(index !== -1){
                panier.splice(index,1);
            }

            localStorageFactory.setArray('panier',panier);

            return index;
        };

        this.clear = function(){
            panier = [];
            restaurantPanier = null;
            localStorageFactory.setArray('panier',panier);

        };

        this.inPanier = function(plat){
            if(!plat) return;
            var index=_.findIndex(panier, {id: plat.id});
            if(index !== -1){
                return true;
            }
            return false;
        };

        this.isEmpty = function(){
            if(panier.length === 0) return true;

            return false;
        };
}]);