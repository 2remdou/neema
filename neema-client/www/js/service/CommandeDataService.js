/**
 * Created by touremamadou on 02/06/2016.
 */
'use strict';

app.service('CommandeDataService',
    [function(){
        var self = this;

        this.data = {list:new Array};

        this.currentPage = {list:1};

        this.allPlatAreAlreadyLoaded = {onMenu:false,other:false};

        this.lastTimeToLoad;

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