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
        var li = $('<li></li>');
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
        createLi('首页', 0, 'pager-first-li-disable');
        createLi('上一页', 0, 'pager-previous-li-disable');
    } else {
        createLi('首页', 1, 'pager-first-li');
        createLi('上一页', index - 1, 'pager-previous-li');
    }
    if (size <= 8) {
        for (var i = 1; i <= size; i++) {
            if (i != index) {
                createLi(i, i, 'pager-normal-li');
            } else {
                createLi(i, 0, 'pager-index-li');
            }
        }
    } else {
        if (index - 3 <= 1) {//当前页在前几页
            for (var i = 1; i <= index + 5; i++) {
                if (i != index) {
                    createLi(i, i, 'pager-normal-li');
                } else {
                    createLi(i, 0, 'pager-index-li');
                }
            }
            createLi('···', 0, 'pager-space-li');
            createLi(size, size, 'pager-normal-li');
        } else if (index + 5 >= size) {//当前项在后5项
            createLi('1', 1, 'pager-normal-li');
            createLi('···', 0, 'pager-space-li');
            for (var i = index - 5; i <= size; i++) {
                if (i != index) {
                    createLi(i, i, 'pager-normal-li');
                } else {
                    createLi(i, 0, 'pager-index-li');
                }
            }

        } else {
            createLi('1', 1, 'pager-normal-li');
            createLi('···', 0, 'pager-space-li');
            for (var i = index - 2; i <= index + 2; i++) {
                if (i != index) {
                    createLi(i, i, 'pager-normal-li');
                } else {
                    createLi(i, 0, 'pager-index-li');
                }
            }
            createLi('···', 0, 'pager-space-li');
            createLi(size, size, 'pager-normal-li');
        }
    }
    if (index == size) {
        createLi('下一页', 0, 'pager-next-li-disable');
        createLi('尾页', 0, 'pager-last-li-disable');
    } else if ((index < size)) {
        createLi('下一页', index + 1, 'pager-next-li');
        createLi('尾页', size, 'pager-last-li');
    }
}