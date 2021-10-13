/*
 * @Author       : lovefc
 * @Date         : 2021-08-31 14:36:35
 * @LastEditTime : 2021-10-12 13:47:32
 */

/** 拉取视频数据 **/
$(document).ready(function () {
   let htmlobj = $.ajax({
      url: "./video/data.json",
      async: false
   });
   let data = JSON.parse(htmlobj.responseText);
   let video = document.getElementById('video_list');
   if (video) {
      for (let i in data) {
         video.innerHTML += `
            <div class="l-box pure-u-1 pure-u-md-1-2 pure-u-lg-1-4">
			    <a href="${data[i].url}" target="_blank" >
				    <div class="videos">
                    <p><img src="${data[i].pic}"></p>
				    <p>${data[i].title}</p>
				    </div>
			    </a>
            </div>			
        `
      }
   }
});