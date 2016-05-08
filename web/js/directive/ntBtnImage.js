/**
 * Created by mdoutoure on 30/10/2015.
 */

app.directive('ntBtnImage',[function(){
   return {
       restrict: 'A',
       scope: {
         cible: '@'
       },
       link : function(scope,element,attributes){
            element.on('click',function(e){
                cible = $('#'+attributes.cible);
                $(cible).trigger('click');
                e.preventDefault();
            });
       }
   }
}]);