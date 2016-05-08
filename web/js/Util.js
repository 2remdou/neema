/**
 * Created by mdoutoure on 24/11/2015.
 */

var displayAlert = function(message,typeAlert,scope){
    var response={};
    response.data = [{texte:message,'typeAlert':typeAlert}];
    successRequest(response,scope);

}
    var successRequest = function(response,scope){
        scope.$emit('showMessage',response.data);
    };

    var log = function(message){
        console.log(message);
    };

    var errorRequest = function(response,scope){
        /*
         if(response.status==400){
         response.data = [{texte:"Ooops, il est ou l'administrateur ??? Erreur vraiment etonnante",'typeAlert':'danger'}];
         }
         else if(response.status==404){
         if(response.data.hasOwnProperty('data')){
         response.data = response.data.data;
         }
         }
         scope.$emit('showMessage',response.data);
         */
    };

    var extractId = function(object){
        if(typeof object != "undefined" && object){
            if(object.hasOwnProperty('id')){
                return object.id;
            }
        }
        return null;
    };

    var deleteProperty = function(object,property){
        if(object.hasOwnProperty(property)){
            delete  object[property];
        }
    };

    var getSizeWindow= function() {
          var myWidth = 0, myHeight = 0;
          if( typeof( window.innerWidth ) == 'number' ) {
            //Non-IE
            myWidth = window.innerWidth;
            myHeight = window.innerHeight;
          } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
            //IE 6+ in 'standards compliant mode'
            myWidth = document.documentElement.clientWidth;
            myHeight = document.documentElement.clientHeight;
          } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
            //IE 4 compatible
            myWidth = document.body.clientWidth;
            myHeight = document.body.clientHeight;
          }
          var result = {'width': myWidth,'height':myHeight};
          return result;
    };
    var isDefined = function(object){
        return angular.isDefined(object);
    }
