/**
 * Created by Steven on 2015/12/04/0004.
 *
 *});
 *
 */
(function(){
    var AlertC;
    AlertC = (function(){
        function AlertC() {}
        var $Poput = $("<div class='popup'><div class='box'><div class='title'><span class='fl'></span><span class='fr' ><i></i></span></div><div class='center'></div></div></div>");
        var $Poput2 = $("<div class='reminder'><div class='center'></div></div>");
        var $Layer = $("<div class='Layer'></div>");
        AlertC.prototype.openRun = function() {
            if ($('.popup').length > 1) {
                $('.popup').remove();
                $('.Layer').remove();
            }
            $('.popup .title .fr').click(this.close);
            $('.Layer').click(this.close);
            return this.success();
        };
        AlertC.prototype.Popup = function(MoDel){
            this.alertO = MoDel || {};
            this.success = MoDel.success || function(){};
            this.Width = MoDel.Width;
            $('.mh-pop.user-pop').remove();
            switch (MoDel.type){
                case 1:
                    $Poput.appendTo('body');
                    $Layer.appendTo('body');
                    $('.popup').css("width",MoDel.Width);
                    $('.popup .center').html(MoDel.content);
                    $('.popup .title .fl').html(MoDel.title);
                    break;
                case 2:
                    $Poput2.appendTo('body');
                    $Layer.appendTo('body');
                    $('.popup').css("width",MoDel.Width);
                    $('.reminder .center').html(MoDel.content);
            }
            $('.popup .hide').removeClass('hide');
            return this.openRun();
        };
        AlertC.prototype.close = function(){
            if ($('.popup').length > 0) {
                $('.popup').remove();
                $('.Layer').remove();
            }
            if($('.reminder').length > 0){
                $('.reminder').remove();
                $('.Layer').remove();
            }
        };
        return AlertC;
    })();
    return window.Alert = new AlertC;
}).call(this);
