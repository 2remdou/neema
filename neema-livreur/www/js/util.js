/**
 * Created by touremamadou on 06/05/2016.
 */

var log = function(log){
    console.log(log);
};

String.prototype.capitalizeFirstLetter = function() {
        return this.charAt(0).toUpperCase() + this.slice(1);
};