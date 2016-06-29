/**
 * Created by touremamadou on 06/06/2016.
 */
'use strict';

app.filter('conversion',function(){
    return function(value,uniteSource,uniteDestination){
            switch (uniteSource) {
                case 'second':
                    switch (uniteDestination) {
                        case 'minute':
                                value = Math.round(value/60)+' minutes';
                            break;
                        default:
                            break;
                    }
                    break;

                case 'minute':
                    switch (uniteDestination) {
                        case 'second':
                                value = Math.round(value*60)+' secondes';
                            break;
                        default:
                            break;
                    }
                    break;

                case 'metre':
                    switch (uniteDestination) {
                        case 'km':
                                value = Math.round(value/1000)+' km';
                            break;
                        default:
                            break;
                    }
                    break;

                case 'km':
                    switch (uniteDestination) {
                        case 'metre':
                                value = Math.round(value*1000)+' m';
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    break;

            }
            return value;

    }

});