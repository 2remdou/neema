/**
 * Created by touremamadou on 02/06/2016.
 */
'use strict';

app.service('FirstLoad',
    [
    function(){
        self = this;

        this.controller = {HomeController:true,PlatByRestaurantController:true};
}]);