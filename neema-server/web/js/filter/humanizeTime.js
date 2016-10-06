/**
 * Created by touremamadou on 06/06/2016.
 */
'use strict';

app.filter('humanizeTime',function(){
    return function humanizeTime(_duration) {
        return humanizeDuration(_duration,{ language: 'fr',round:true });
    }
});