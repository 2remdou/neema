/**
 * Created by touremamadou on 06/06/2016.
 */
'use strict';

app.filter('montant',function(){
    return function numberFormat(_montant) {
        var _sep=' ';
        _montant = typeof _montant != "undefined" && _montant > 0 ? _montant : "";
        _montant = _montant.toString();
        _montant = _montant.replace(new RegExp("^(\\d{" + (_montant.length%3? _montant.length%3:0) + "})(\\d{3})", "g"), "$1 $2").replace(/(\d{3})+?/gi, "$1 ").trim();
        if(typeof _sep != "undefined" && _sep != " ") {
            _montant = _montant.replace(/\s/g, _sep);
        }
        return _montant+' GNF';
    }
});