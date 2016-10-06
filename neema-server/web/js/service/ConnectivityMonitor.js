
/**
 * Created by touremamadou on 02/06/2016.
 */
'use strict';

app.factory('ConnectivityMonitor',
    ['$cordovaNetwork','PopupService',
    function($cordovaNetwork,PopupService){
            function isOnline(){
                if(ionic.Platform.isWebView()){
                    return $cordovaNetwork.isOnline();    
                } else {
                    return navigator.onLine;
                }
            };
            function isOffline(){
                if(ionic.Platform.isWebView()){
                    return !$cordovaNetwork.isOnline();    
                } else {
                    return !navigator.onLine;
                }
            };
            function checkConnectivity(){
                ionic.Platform.ready(function(){
                    if(isOffline()){
                        var popup = {
                            title:'Aucun accès internet',
                            message:'Vous devez être connecter à internet',
                            cssClass:'popupInfo'
                        };
                        PopupService.show(popup);

                        return false;
                    }
                    return true;
                });
            };

            return{
                isOnline:isOnline,
                isOffline:isOffline,
                checkConnectivity: checkConnectivity,
            }
}]);