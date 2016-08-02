/**
 * Created by mdoutoure on 24/11/2015.
 */
'use strict';

var displayAlert = function(message,typeAlert,scope){
    var response={};
    response.data = [{texte:message,'typeAlert':typeAlert}];
    successRequest(response,scope);

};
var successRequest = function(response,scope){
    scope.$emit('showMessage',response.data);
};

var log = function(message){
    console.log(message);
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

var isDefined = function(object){
    return angular.isDefined(object);
};

/**
 * Mettre la première lettre en majuscule
 * @returns {string}
 */
String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
};
/**
 *
 * @param dateOperation la date de debut de l'operation
 * @param dureeOperation le temps prevu pour l'operation(en milliseconde)
 *
 * @return dureeRestant(en milliseconde)
 */
var getDureeRestant = function(dateOperation,dureeOperation){
    var tempsEcoule = moment().format('x') - moment(dateOperation).format('x');
    var dureeRestant = dureeOperation - tempsEcoule;

    return dureeRestant<0?0:dureeRestant;

};