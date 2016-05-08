/**
 * Created by touremamadou on 20/08/2015.
 */

'use strict';

app.run(['$rootScope','Notification','usSpinnerService','UserService',
    function($rootScope,Notification,usSpinnerService,UserService){

        $rootScope.user = UserService.getUser();
        $rootScope.$on('show.message',function(event,args){
        var alert = args.alert;
        var opt= {'message': alert.textAlert}
        if(alert.typeAlert==='danger'){
            Notification.error(opt);
            usSpinnerService.stop('nt-spinner');
        }
        else if(alert.typeAlert==='success'){
            Notification.success(opt);
        }
        else if(alert.typeAlert==='info'){
            Notification.info(opt);
        }
        else if(alert.typeAlert==='warning'){
            Notification.warning(opt);
        }
    });

    $rootScope.$on('stop.spinner',function(event,args){
        usSpinnerService.stop('nt-spinner');
    });
}]);