/**
 * Created by mdoutoure on 30/10/2015.
 */

app.directive('ntBtnImage',[function(){
   return {
       restrict: 'A',
       scope: {
         cible: '@',
         replace: '@'
       },
       link : function(scope,element,attributes){
            element.on('click',function(e){
                cible = $('#'+attributes.cible);
                replace = $('#'+attributes.replace);
                $(cible).trigger('click');
                $(replace).remove();
                e.preventDefault();
            });
       }
   }
}]);