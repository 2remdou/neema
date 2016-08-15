/**
 * Created by touremamadou on 02/06/2016.
 */
'use strict';

app.service('PopupService',
    ['$ionicPopup',
    function($ionicPopup){
        /*
        popup={title:'titre','message':'contenu',cssClass:'popupDanger|popupSucess|popupWarnig'}
         */
        this.show = function(popup){
            return $ionicPopup.alert({
                title: popup.title,
                template: popup.message,
                cssClass: popup.cssClass
            });
        };

        this.confirmation = function(popup){
            return $ionicPopup.confirm({
                title: popup.title||'Confirmation',
                template: popup.message,
                cancelText:'Non',
                cancelType:'button-positive',
                okText:'Oui',
                okType:'button-default'
            });
        };

}]);