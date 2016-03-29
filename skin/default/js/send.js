/**
 * Created by Administrator on 2015/7/22.
 */

var GET_TIME = 120;
var time = GET_TIME;
var getCodeStatus = '00';

//点击发送事件
function send_mobile_to(_this,type){
    if(getCodeStatus === '01'){  //触发点击事件
        getCodeStatus = '11';
        $(_this).attr("disabled","disabled");
        getsendhandle(_this,type);
    }else if(getCodeStatus==='00'){ //默认值的情况下
        getCodeStatus = '10';
    }
}

function getsendhandle(_this,type){
    if(time < GET_TIME) return;
    var _this = $(_this);
    var send_mobile = $(_this).closest("form").find($(".mobile")).val();
    _this.html('发送短信中');
    getToken();
    $.ajax({
        type:'post',
        url:DTPath+'ajax.php?action=member&job=sendmobile&moduleid=2',
        data:{mobile:send_mobile,type:type},
        dataType:'json',
        async:false,
        success:function(data){
            sendsl(data,_this);
        }
    })

}



function sendsl(data,_this){

    if(data.status=='y'){
        timefn = setInterval(function () {
            if (time > 0) {
                $(_this).html(time-- + 's后可重发');
            } else {
                clearInterval(timefn);
                $(_this).html('点击即可重发');
                $(_this).removeAttr('disabled');
                time = GET_TIME;
                getCodeStatus = '01';
            }
        }, 1000);
    }else{
        if (isNaN(parseInt(data.info))){
            $(_this).html(data.info);
            getCodeStatus = '01';
        }else{
            time = parseInt(data.info);
            timefn = setInterval(function () {
                if (time > 0) {
                    $(_this).html(time-- + 's后可重发');
                } else {
                    clearInterval(timefn);
                    $(_this).html('点击即可重发');
                    time = GET_TIME;
                    $(_this).removeAttr('disabled');
                    getCodeStatus = '01';
                }
            }, 1000);
        }
    }


}




