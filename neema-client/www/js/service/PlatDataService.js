/**
 * Created by touremamadou on 02/06/2016.
 */
'use strict';

app.service('PlatDataService',
    ['PlatService','INTERVAL_TIME_FOR_TRY_AGAIN_LOADING',
    function(PlatService,INTERVAL_TIME_FOR_TRY_AGAIN_LOADING){
        var self = this;

        this.data = {onMenu:[],other:[],type:'onMenu'};

        this.currentPage = {onMenu:0,other:0};

        this.allPlatAreAlreadyLoaded = {onMenu:false,other:false};

        this.lastTimeToLoad = null;

        this.timeForLoadingExpired = function(){
            var b = false;
            if(!self.lastTimeToLoad) b = true;
            if(new Date().getTime() - self.lastTimeToLoad >= INTERVAL_TIME_FOR_TRY_AGAIN_LOADING){
                b = true;
            }else{
                b= false;
            }
            if(b){
                if(self.data.type==='onMenu')
                    self.allPlatAreAlreadyLoaded.onMenu = false;
                else if(self.data.type==='other')
                    self.allPlatAreAlreadyLoaded.onMenu = false;
            }

            return b;
        };

        this.getData = function(){
            if(self.data.type==='onMenu')
                return self.data.onMenu;
            else if(self.data.type==='other')
                return self.data.other;
        };

        this.setData = function(data){
            if(self.data.type==='onMenu')
                self.data.onMenu.length===0?self.data.onMenu = data:self.data.onMenu.concat(data);
            else if(self.data.type==='other')
                self.data.other = data;

        };

}]);