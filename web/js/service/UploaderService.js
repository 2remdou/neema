/**
 * Created by touremamadou on 10/05/2016.
 */

'use strict';


app.service('UploaderService',
    ['FileUploader','UserService','UrlApi',
        function(FileUploader,UserService,UrlApi){
            var that = this;

            this.getUploader = function (route) {
                return new FileUploader({
                    url : UrlApi+route,
                    headers : {
                        'Authorization': 'Bearer '+ UserService.getToken()
                    }
                });
            }
        }]);