/**
 * Created by touremamadou on 16/05/2016.
 */
app
    .controller('CommandeController',
        ['$scope','PlatService','PanierService','$state',
            'SpinnerService','CommandeService','PopupService','FRAIS_COMMANDE',
            function($scope,PlatService,PanierService,$state,
                    SpinnerService,CommandeService,PopupService,FRAIS_COMMANDE){

                if(PanierService.isEmpty()){
                    $state.go('home');
                    return;
                }
                $scope.commande={};
                $scope.commande.total=0;
                $scope.commande.aEmporter=false;
                $scope.plats = PanierService.getPanier();
                $scope.commande.restaurant = $scope.plats[0].restaurant;//car tous les plats viennent du même restaurant
                if($scope.userConnected){
                    $scope.commande.telephone = $scope.userConnected.telephone;
                }

                var refreshCommande = function(){
                    $scope.commande.total = 0;
                    $scope.commande.dureeCommande = 0;
                    $scope.commande.detailCommandes = [];
                    angular.forEach($scope.plats,function(plat){
                        $scope.commande.detailCommandes.push({
                            quantite:plat.quantite,
                            prix:plat.prix,
                            plat:plat.id
                        });
                        $scope.commande.total += parseInt(plat.prix)*parseInt(plat.quantite);
                        $scope.commande.dureeCommande += parseInt(plat.dureePreparation)*parseInt(plat.quantite);
                        // $scope.commande.fraisCommande = $scope.commande.total*FRAIS_COMMANDE;
                        $scope.commande.totalCommande=$scope.commande.total;
                    });
                };
                refreshCommande();

                $scope.plusQuantite = function(plat){
                    plat.quantite ++;
                    refreshCommande();
                    // $scope.commande.totalCommande += plat.prix;
                };

                $scope.moinsQuantite = function(plat){
                    if(plat.quantite>1){
                        plat.quantite --;
                        refreshCommande();
                        // $scope.commande.totalCommande -= plat.prix;
                    }
                };

                $scope.removeInPanier = function(plat){
                    if($scope.plats.length===1){
                        $scope.clearPanier();
                        return;
                    }
                    var popup = {
                        title: 'Confirmation',
                        message: 'Voulez-vous retirer ce plat de la commande ?'
                    };
                    PopupService.confirmation(popup).then(function(res) {
                        if(res){
                            PanierService.remove(plat);
                            refreshCommande();
                        }
                    });

                };

                $scope.valider = function(commande){
                    var c = angular.copy(commande);
                    c.restaurant = c.restaurant.id;
                    var popup = {
                        title: 'Confirmation',
                        message: 'Voulez-vous passer cette commande ?'
                    };
                    PopupService.confirmation(popup).then(function(res) {
                        if(res){
                            SpinnerService.start();
                            CommandeService.post(c);
                        }
                    });
                };

                $scope.clearPanier = function() {
                    var popup = {
                        title: 'Confirmation',
                        message: 'Voulez-vous vider le panier'
                    };
                    PopupService.confirmation(popup).then(function(res) {
                        if(res){
                            PanierService.clear();
                            $state.go('home');
                        }
                    });
                };


                //***************LISTENER*******************

                $scope.$on('commande.created',function(event,args){
                    var idCommande = args.commande.id;
                    SpinnerService.stop();
                    PanierService.clear();
                    var popup = {
                        title: 'Commande',
                        message: 'Votre commande a été reçu',
                        cssClass: 'popupSuccess'
                    };
                    PopupService.show(popup).then(function(res) {
                        $state.go('infoCommande',{idCommande:idCommande});
                    });

                });
    }])
    .controller('ListCommandeController',
    ['$scope','SpinnerService','CommandeService','CommandeDataService',
    function($scope,SpinnerService,CommandeService,CommandeDataService){
                
        $scope.moreDataCanBeLoaded = true;
        $scope.commandes = CommandeDataService.data.list;
        var loading = false;
        $scope.loadMore = function(){
            if(loading) return;
            SpinnerService.start();
            loading=true;
            CommandeService.listByUserConnected(function(commandes,currentPage){
                CommandeService.defineDureeRestante(commandes);
                CommandeDataService.lastTimeToLoad = new Date();
                CommandeDataService.data.list = CommandeDataService.data.list.concat(commandes);
                $scope.commandes = CommandeDataService.data.list;
                CommandeDataService.currentPage.list=currentPage;
                SpinnerService.stop();
                $scope.$broadcast('scroll.infiniteScrollComplete');
                if(commandes.length===0){
                    $scope.moreDataCanBeLoaded = false;
                }else{
                    CommandeDataService.currentPage.list++;
                }
            },CommandeDataService.currentPage.list);
        };

        $scope.doRefresh = function(){
            if(!CommandeDataService.lastTimeToLoad) return;
            CommandeService.refreshListByClient(CommandeDataService.lastTimeToLoad,function(commandes){
                CommandeService.defineDureeRestante(commandes);               
                if(commandes.length!==0){
                    angular.forEach(commandes,function(commande){
                        CommandeDataService.data.list.unshift(commande);
                    });
                } 
                CommandeDataService.lastTimeToLoad = new Date();
                $scope.$broadcast('scroll.refreshComplete');
            },function(err){
                log(err);
            });
        };

        $scope.$on('scroll.infiniteScrollComplete',function(){
            loading=false;
        });

         $scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
             if(toState.name !== 'listCommande') return;
             if($scope.commandes.length!==0) return;
             $scope.loadMore();
             $scope.doRefresh();
         });


    }])
    .controller('InfoCommandeController',
    ['$scope','SpinnerService','$state','CommandeService','$stateParams',
    function($scope,SpinnerService,$state,CommandeService,$stateParams
    ){
        $scope.codeCommandeVisible = false;
        $scope.commandes = []; // pour que le timer puisse marcher

        SpinnerService.start();

        CommandeService.get($stateParams.idCommande,function(commande){
            $scope.commandes.push(CommandeService.defineDureeRestante(angular.copy(commande)));
            SpinnerService.stop();
        });

        $scope.showCodeCommande = function(){
            $scope.codeCommandeVisible = !$scope.codeCommandeVisible;
        };

    }])
;
