/**
 * Created by Administrator on 2016/1/15.
 */
//加入收藏
function AddFavorite(sURL, sTitle) {
    try {
        window.external.addFavorite(sURL, sTitle);
    }
    catch (e) {
        try {
            window.sidebar.addPanel(sTitle, sURL, "");
        }
        catch (e) {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
}

/**
 * @name 点击喜欢
 * @param id ID
 * @param type 数字类型
 * @param _this 当前对象
 */
function like(id,type,_this,typeid){
    var _this = $(_this);
    _this.attr("disabled",true);
    getToken();
    $.ajax({
        type:'post',
        url:'/ajax.php?action=ajax&at=like&type='+type+'&id='+id,
        async:false,
        dataType:'json',
        success:function(data){
            if(typeid==1){
                layer.open({
                    content: data.info,
                    title:'提示'
                });
            }else{
                layer.msg(data.info);
            }

            if(data.status=='y'){
                $(".like-num").html(parseInt($(".like-num").html())+1);
            }
        }
    })
    _this.removeAttr("disabled");
}
function catonchange(_this){
    var _this = $(_this);
    var spanobj = _this.prev();
    var val = _this.val();
    spanobj.attr("data-id",val).html(_this.find("option:selected").text());
    var olddataid = spanobj.attr("olddata-id");
    $("#catmontable td span").css({"color":"#000","background-color":"#fff"});
    if(parseInt(olddataid)==parseInt(val)){
        $("#selectcatnow").removeAttr("data-id");
    }else{
        $("#selectcatnow").attr("data-id",val).html(_this.find("option:selected").text());

        spanobj.css({"color":"#fff","background-color":"#e9544e"})
    }

}

function lfdtd(_this){
    var _this = $(_this);
    if(_this.find('select').size()==0){
        var spanobj = _this.find('span');
        $("#selectcatnow").attr("data-id",spanobj.attr('data-id')).html(spanobj.text());
        $("#catmontable td span").css({"color":"#000","background-color":"#fff"});
        spanobj.css({"color":"#fff","background-color":"#e9544e"});
    }
}

/**
 * @name 获取token
 */
function getToken(){
    $.ajax({
        type:'post',
        url:'/ajax.php?action=ajax&at=gettoken',
        async:false,
        success:function(data){

        }
    })
    return true;
}

function thumbo(){
    $("#local_url td").css('position','relative');
    $("#upthumb").before('<span style="padding:3px 5px;color:#fff;background-color:red;position:absolute;top:0px;left:0px;width:200px">请选择文件<span>');
}



/**
 * @name 是否登录
 */
function isLogin(){
    var islogin = false;
    $.ajax({
        type:'post',
        url:'/ajax.php?action=ajax&at=islogin',
        async:false,
        dataType:'json',
        success:function(data){
            if(data.status=='n'){
                alert('请先登录');
            }else{
                islogin = true;
            }
        }
    })
    return islogin;
}

/**
 * @name 评论
 */
var iscommentsing = false;
function comments(){
    if(iscommentsing==true) return false;
    iscommentsing = true;
    if(!isLogin()) return false;
    getToken();
    var id = $("#commentsid").val();
    var content = $("#commentscontent").val();
    if(content.length>200 || content.length<5){
        iscommentsing = false;
        alert('请输入5-200个字');
        return false;
    }
    var type = $("#commentstype").val();
    $.ajax({
        type:'post',
        url:'/ajax.php?action=ajax&at=comment',
        data:{'id':id,'content':content,'type':type},
        async:false,
        dataType:'json',
        success:function(data){
            alert(data.info);
            $("#commentscontent").val('');
        }
    })
    iscommentsing = false;
    return true;
}

//投票
function vote(id,type,_this){
    var _this = $(_this);
    _this.attr("disabled",true);
    getToken();
    $.ajax({
        type:'post',
        url:'/ajax.php?action=ajax&at=vote&type='+type+'&id='+id,
        async:false,
        dataType:'json',
        success:function(data){
            if(data.status=='n'){
                alert(data.info);
            }else{
                _this.next().find('i').html(parseInt(_this.next().find('i').html())+1);
            }
        }
    })
    _this.removeAttr("disabled");
}

function is_mobile(val) {
    var msg = /^1[3|4|5|7|8]{1}[0-9]{9}$/;
    return  msg.test(val)?true:false;
}
function is_phone(val){
    var msg = /^((\d{3,4})|\d{3,4}-)?\d{7,8}$/i;
    return  msg.test(val)?true:false;
}
function is_price(val){
    var msg = /^(0|[1-9][0-9]{0,8})(\.[0-9]{1,2})?$/;
    return  msg.test(val)?true:false;
}

function is_username(val){
    var msg = /^[0-9a-z]{6,20}$/;
    return  msg.test(val)?true:false;
}

function getmorecommont(url,_this,appenddiv){
    var _this = $(_this);
    var page = _this.attr('data-page');
    page = parseInt(page)+1;
    _this.find('a').html('加载中...');
    $.ajax({
        type:'post',
        url:url,
        data:{'page':page,'action':'ajax'},
        dataType:'json',
        success:function(data){
            if(typeof(appenddiv)=='undefined'){
                _this.prev().append(data.info);
            }else{
                $("."+appenddiv).append(data.info);
            }

            if(data.totalpage==page){
                _this.remove();
            }else{
                _this.attr('data-page',page).find('a').html('查看更多>>');
            }
        }
    })
}

function getmorecomment(url,_this){
    var _this = $(_this);
    var page = _this.attr('data-page');
    page = parseInt(page)+1;
    _this.find('a').html('加载中...');
    $.ajax({
        type:'post',
        url:url,
        data:{'page':page,'action':'ajax'},
        dataType:'json',
        success:function(data){
            _this.before(data.info);
            if(data.HasNextPage==0){
                _this.remove();
            }else{
                _this.attr('data-page',page).find('a').html('查看更多>>');
            }
        }
    })
}

$(function(){
    //查看更多评论
    $("#morecomment").click(function(){
        var id = $(this).attr("data-id");
        var type = $(this).attr("data-type");
        var page = parseInt($(this).attr("data-page"))+1;

        var _this = $(this);
        $.ajax({
            type:'post',
            url:'/ajax.php?action=ajax&at=morecomment',
            data:{'id':id,'type':type,'page':page},
            async:false,
            dataType:'json',
            success:function(data){
                if(!data.HasNextPage){
                    _this.remove();
                }else{
                    _this.attr("data-page",page);
                }
                if(data.info!=''){
                    var str = '';
                    $.each(data.info,function(k,v){
                        var thumb = k.thumb? k.thumb:defaultmemberlogo;
                        str  +=  '<div class="discuss-main-box clearfix" style="margin-bottom:10px;">'+
                        '<img class="person-img fl" src="'+thumb+'" alt=""/>'+
                        '<div class="contentlist">'+ v.content +'</div>'+
                        '</div>';
                    })
                    $("#commentsform").before(str);
                }
            }
        })
    })

    $('#roll-top').click(function () {
        $('html,body').animate({
            scrollTop: '0px'
        },300);
    });
    $("#roll-bottom").click(function(){
        $("#commentscontent").focus();
    })

    $(window).scrollTop(0);

})

function publishsuresdfs(type,url){
    var catid = $("#selectcatnow").attr("data-id");
    var catname = $("#selectcatnow").text();
    if(typeof(catid)=='undefined' || catid==''){
        alert('请选择分类');
    }else{
        if(type==0){
            $("#showcat").html(catname);
            $("#catid").val(catid);
            $("#showcat").prev().removeClass('Validform_wrong').html('');
            $(".layermbox").remove();
        }else if(type==1){
            location.href=url+'&catid='+catid;
        }

    }

}

function publishquitsdfs(type,url){
    $(".layermbox").remove();
}

function publishallsdfs(type,url){
    if(type==0){
        $("#showcat").html('');
        $("#catid").val('');
        $(".layermbox").remove();
    }else if(type==1){
        location.href=url;
    }

}

function showcat(moduleid,type,url){
    $.ajax({
        type:'post',
        url:'/ajax.php?action=ajax&at=category',
        data:{'moduleid':moduleid,'type':type,'url':url},
        async:false,
        dataType:'json',
        success:function(data){
            layer.open({
                type: 1,
                content: data.info,
                anim: 0,
                style: 'position:fixed; bottom:0; left:0; width:100%; height:100%;overflow-y:scroll;background-color:#e6e6e6; border:none;'
            });
        }
    })

}


function goTop(){
    $(window).scrollTop(0);
}

