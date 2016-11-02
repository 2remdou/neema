/**
 * Created by touremamadou on 02/06/2016.
 */
'use strict';

app.service('PaginatorService',
    ['localStorageFactory',function(localStorageFactory){
        var params = {
            paginator:{currentPage:0,nextPage:1},
            canBeLoad:true,
            dateToLastLoad:new Date()
        };
        this.historiqueCommande = _.clone(params);
        this.historiqueCommande.commandes = [];

        this.listPlatByRestaurant = _.clone(params);
        
        this.menu = localStorageFactory.getObject('menu');
        if(!this.menu){
            this.menu = _.clone(params);
            this.menu.plats = [];
        }

        this.listRestaurants = localStorageFactory.getObject('listRestaurants');
        if(!this.listRestaurants){
            this.listRestaurants = _.clone(params);
            this.listRestaurants.restaurants = [];
        }


        this.getHistoriqueCommande = function(){
            return this.historiqueCommande;
        };

        this.getListPlatByRestaurant = function(){
            return this.listPlatByRestaurant;
        };

        this.getMenu = function(){
            return localStorageFactory.getObject('menu')||this.menu;
        }

        this.getListRestaurants = function(){
            return localStorageFactory.getObject('listRestaurants')||this.listRestaurants;
        };

        /**
         * @param boolean pos (si true,inserer au debut)
         */
        this.addHistoriqueCommande = function(historiqueCommande,pos){
            this.historiqueCommande.dateToLastLoad = new Date();
            if(historiqueCommande.paginator.currentPage === historiqueCommande.paginator.nextPage){
                this.historiqueCommande.canBeLoad = false;
            }
            if(historiqueCommande.commandes){
                if(pos){
                    this.historiqueCommande.commandes= _.concat(historiqueCommande.commandes,this.historiqueCommande.commandes);
                }else{
                    this.historiqueCommande.commandes= _.concat(this.historiqueCommande.commandes,historiqueCommande.commandes);
                    this.historiqueCommande.paginator = historiqueCommande.paginator;
                }
            }
        };

        this.addListPlatByRestaurant = function(listPlatByRestaurant){
            this.listPlatByRestaurant.dateToLastLoad = new Date();
            if(listPlatByRestaurant.paginator.currentPage === listPlatByRestaurant.paginator.nextPage){
                this.listPlatByRestaurant.canBeLoad = false;
            }
            if(listPlatByRestaurant.plats){
                this.listPlatByRestaurant.paginator = listPlatByRestaurant.paginator;
            }
        };
        /**
         * @param boolean pos (si true,inserer au debut)
         */

        this.addPlatOnMenu = function(menu,pos){
            this.menu.dateToLastLoad = new Date();
            if(menu.paginator.currentPage === menu.paginator.nextPage){
                this.menu.canBeLoad = false;
            }
            if(menu.plats){
                if(pos){
                    this.menu.plats= _.concat(menu.plats,this.getMenu().plats);
                }else{
                    this.menu.plats= _.concat(this.getMenu().plats,menu.plats);
                    this.menu.paginator = menu.paginator;
                }
                localStorageFactory.setObject('menu',this.menu);
            }
        };
        /**
         * @param boolean pos (si true,inserer au debut)
         */

        this.addRestaurant = function(listRestaurants,pos){
            this.getListRestaurants().dateToLastLoad = new Date();
            if(listRestaurants.paginator.currentPage === listRestaurants.paginator.nextPage){
                this.listRestaurants.canBeLoad = false;
            }
            if(listRestaurants.restaurants){
                if(pos){
                    this.listRestaurants.restaurants= _.concat(listRestaurants.restaurants,this.getListRestaurants().restaurants);
                }else{
                    this.listRestaurants.restaurants= _.concat(this.getListRestaurants().restaurants,listRestaurants.restaurants);
                    this.listRestaurants.paginator = listRestaurants.paginator;
                }

                localStorageFactory.setObject('listRestaurants',this.listRestaurants);
            }
        };

        this.getRestaurant = function(id,callback){
            var index = _.findIndex(this.getListRestaurants().restaurants,{'id':id});
            if(index !== -1) callback(this.getListRestaurants().restaurants[index]);
            else    callback(null);
        };

        this.remove = function(key){
            localStorageFactory.remove(key);
        };
}]);