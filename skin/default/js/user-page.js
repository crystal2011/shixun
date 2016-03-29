/**
 * Created by Steven on 2015/04/11/
 */
function pager(
    /*int*/ index,
    /*int*/ size,
    /*String*/ ulClassName
) {
    $('.' + ulClassName).empty();
    var createLi = function (/*String*/content,
                             /*int*/ pageNum,
                             /*String*/ className) {
        var li = $('<a></a>');
        li.html(content);
        li.addClass(className);
        if (pageNum > 0) {      //Only 'pagenum' attribute to the clickable li tag
            li.attr('pagenum', pageNum);
        }
        $('.' + ulClassName).append(li);
    };
    if (index < 1) {
        return;
    } else if (index == 1) {
        createLi('', 0, 'icon-double-angle-left');
    } else {
        createLi('', index - 1, 'icon-double-angle-left');
    }
    if(size==1){
        if (1 != index) {
            createLi(1, 1, '');
        } else {
            createLi(1, 0, 'active');
        }
    }else if (size <= 4) {
        for (var i = 1; i <= size; i++) {
            if (i != index) {
                createLi(i, i, '');
            } else {
                createLi(i, 0, 'active');
            }
        }
    }else if (parseInt(size) - parseInt(index) < 4) {
        for (var i = size-3; i <= size; i++) {
            if (i != index) {
                createLi(i, i, '');
            } else {
                createLi(i, 0, 'active');
            }
        }
    } else {
        for (var i = index; i < index+4; i++) {
            if (i != index) {
                createLi(i, i, '');
            } else {
                createLi(i, 0, 'active');
            }
        }
    }
    if (index == size) {
        createLi('', 0, 'icon-double-angle-right');
    } else if ((index < size)) {
        createLi('', index + 1, 'icon-double-angle-right');
    }
}