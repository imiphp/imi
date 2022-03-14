/*
 * @Author       : lovefc
 * @Date         : 2021-08-31 14:36:35
 * @LastEditTime : 2021-10-12 13:57:10
 */

/* 这里一些常量,用于定义一些设置 */
const lunbo_time = 5000; // 轮播速度

function leave(func, runtime) {
   let timer = setInterval(func, runtime);
   document.addEventListener("visibilitychange", function () {
      if (document.visibilityState === 'hidden') {
         clearTimeout(timer);
      } else {
         timer = setInterval(func, runtime);
      }
   });
}

/* 首页轮播动画库 */
$(function () {
   let count = $(".ribbon").length;
   let index = 0;
   $(".ribbon").hide().eq(index).show();
   let func = function () {
      index = (index == (count - 1)) ? 0 : index + 1;
      $(".ribbon").hide(500).eq(index).show(500);
   };
   leave(func, lunbo_time);
});

function loadImg(url,callback){
	if(!url) return;
	let image = new Image();
	image.src = url;  
    image.onload = function() {
      typeof callback == 'function' ? callback() : '';
    }	
}

function loadImgage(id){
    $(id).hide();
    $(id).each(function(i){
	    let that = $(this);
	    let url = that.attr('src');
        window.setTimeout(function(){
		    loadImg(url,function(){
			    that.show();
		    });
        },500 * i);	
    });
}

loadImgage(".imi_contact img"); // 依次加载此布局内的图片,加载成功一张就显示一张出来,加载不出来就隐藏吧


/** 数字累加特效 **/
/*
function numAutoPlusAnimation(targetEle, options) {
   options = options || {};
   let $this = document.getElementById(targetEle),
      time = options.time || $this.data('time'),
      finalNum = options.num || $this.data('value'),
      regulator = options.regulator || 100,
      step = finalNum / (time / regulator),
      count = 0,
      initial = 0;
   let timer = setInterval(function () {
      count = count + step;
      if (count >= finalNum) {
         clearInterval(timer);
         count = finalNum;
      }
      let t = Math.floor(count);
      if (t == initial) return;
      initial = t;
      $this.innerHTML = initial;
   }, 30);
}
*/

/* imi的安装数量 */
/* <span id="install_num"></span> */
/*
$(document).ready(function () {
   let htmlobj = $.ajax({
      url: "https://packagist.org/packages/imiphp/imi",
      async: false
   });
   let htmlstr = htmlobj.responseText;
   let str = htmlstr.match(/Installs<\/a>:\s*<\/span>\s*(\d+)\s*<\/p>/)[1];
   numAutoPlusAnimation("install_num", {
      time: 2000,
      num: str,
      regulator: 50
   })
});
*/
