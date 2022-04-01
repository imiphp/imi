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
