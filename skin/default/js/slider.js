// 觅晓风
(function() {

    return $(function() {
      var i, icon, iconCss, iconIndex, icons, imgIndex, li, liLen, liWidth, ref, setActiveClass, setActiveCss, setAn, slider, touchWrap;
      li = $('.slider li');
      imgIndex = 0;
      iconIndex = 0;
      liLen = li.length;
      liWidth = $(window).width();
      touchWrap = $('.sliderBox');
      slider = $('.slider');
      iconCss = {
        active: {
          'background-color': '#00b2fc',
          'border-color': '#00b2fc'
        },
        "default": {
          'background-color': '',
          'border-color': '#a5b2b2'
        }
      };
      setActiveClass = function(direction) {
        var an;
        an = function() {
          return slider.animate({
            'left': -liWidth * imgIndex + 'px'
          }, 500, 'ease-out');
        };
        if (direction === 'left') {
          if (imgIndex !== liLen - 1) {
            imgIndex += 1;
          } else {
            slider.css({
              'left': 0
            });
            imgIndex = 1;
          }
        } else if (direction === 'right') {
          if (imgIndex !== 0) {
            imgIndex -= 1;
          } else {
            slider.css({
              'left': (1 - liLen) * liWidth
            });
            imgIndex = liLen - 2;
          }
        }
        setTimeout(an, 1);
        return true;
      };
      setActiveCss = function(direction) {
        if (direction === 'left') {
          if (iconIndex !== liLen - 2) {
            iconIndex += 1;
          } else {
            iconIndex = 0;
          }
        } else if (direction === 'right') {
          if (iconIndex !== 0) {
            iconIndex -= 1;
          } else {
            iconIndex = liLen - 2;
          }
        }
        touchWrap.find('ul.clear').find('li').find('i').css(iconCss["default"]);
        return touchWrap.find('ul.clear').find('li').eq(iconIndex).find('i').css(iconCss.active);
      };
      touchWrap.swipeLeft(function() {
        setActiveClass('left');
        return setActiveCss('left');
      });
      touchWrap.swipeRight(function() {
        setActiveClass('right');
        return setActiveCss('right');
      });
      icons = '';
      icon = '<li style="float:left;font-size: 50px; line-height:20px;width: 20px; text-align: center; color:#fff;"> <i style="display: inline-block; width: 5px; height: 5px; border: 1px solid #a5b2b2; border-radius: 50%;"></i></li>';
      for (i = 1, ref = liLen; 1 <= ref ? i <= ref : i >= ref; 1 <= ref ? i++ : i--) {
        console.log(liLen);
        icons += icon;
      }
      touchWrap.append('<ul class="clear" style="position: absolute; z-index: 10;left: ' + (liWidth - liLen*20) / 2 + 'px; bottom: 10px;">' + icons + '</ul>');
      li.width(liWidth);
      slider.append(li.eq(0)[0].outerHTML);
      liLen += 1;
      slider.width(liWidth * liLen);
      touchWrap.find('ul.clear').find('li').eq(0).find('i').css(iconCss.active);
      setAn = function() {
        setActiveClass('left');
        setActiveCss('left');
        return setTimeout(setAn, 3000);
      };
      setTimeout(setAn, 3000);
      return setActiveClass(imgIndex);
    });


}).call(this);
