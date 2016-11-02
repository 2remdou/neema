/**
 * Created by touremamadou on 06/06/2016.
 */
'use strict';

app.filter('humanizeDate',function(){
    return function humanizeTime(_date) {
        moment.locale('fr');
        return moment(_date).format("dddd,  Do MMMM YYYY");
    }
});