/*
 * @Author       : lovefc
 * @Date         : 2021-08-31 14:36:35
 * @LastEditTime : 2021-10-13 11:01:10
 */
 
// 案例数据
// 键名要是文件名称,不带后缀
let data = {
   "adorecipe": {
      title: "Adorecipe鸡尾酒资源库",
      description: "本项目是基于imi 2.0的高性能 MVC 实战项目（一人完成开发），搭配 nginx 实现前端静态资源的访问及缓存，View 层很任性地采用了原生模板渲染。项目中大量使用了 imi提供的缓存注解，优化数据查询的性能，点赞等场景使用了 imi 提供的锁机制保证并发场景下的数据一致性。强推大家使用 imi 开发此类项目！",
      url: "#"
   },
   "kangedan": {
      pic: "./images/anli/",
      title: "看个蛋影视搜索 - 全网影视资源搜索平台",
      description: "从最早的建站初心是为了自己方便！放到网络的以来，当流量越来越大的时候是要考虑升级配置还是重构项目，前几天 git 上看到imiphp，索性就拿来实践一下，也是简单就重构出了所有页面，模版引擎引入了 TP 的 think-template，整个重构也就一天不到，所以 imiphp 确实很容易上手！加油！",
      url: "#"
   },
   "tengyue": {
      title: "腾悦物联",
      description: "基于imi开发的高性能高并发分布式（千万级设备）的能源销售管理平台，提供多场景充电桩、售电柜、换电柜、智能电池等设备接入，开放API第三方应用五分钟即可完成新能源物联网售电设备接入",
      url: "#"
   },
   "hupu": {
      title: "虎扑-上亿数据迁移服务",
      description: "如何以最快的速度完成数据迁移，将数据库中的数据迁移到ES中，是需要评估的一个重要技术点。在高IO密集的场景下，单次请求需要80毫秒，imi运用Swoole协程，不断在用户态和内核态之间进行切换，充分利用计算机CPU，从而能快速完成海量数据迁移。根据普罗米修斯的监控统计，在 两台 2C 4G的机器上，imi以每秒钟同步1000~1500条的同步速度，完成了上亿级别的数据迁移。<br 博文地址：https://blog.csdn.net/qq_32783703/article/details/113576741",
      url: "#"
   }
};

// 图片打包,看不懂别乱改
const ctx = require.context('./images/anli/', true, /\.jpg$/);
let anli_list = ctx.keys().map((key) => {
   let name = key.replace("./","");
   let k = name.substring(0, name.lastIndexOf("."));
   let pic = 'assgin/images/'+name;
   return Object.assign({}, data[k], {pic: pic});
});

// 生成模板
let anli_str = '', anli_str2 = '';
for (let i in anli_list) {
   let pic = anli_list[i]['pic'],
       title = anli_list[i]['title'],
       desc = anli_list[i]['description'],
       url = anli_list[i]['url'];
       anli_str += `
	    <div class="ribbon l-box-lrg pure-g">
                <div class="l-box-lrg is-center pure-u-1 pure-u-md-1-2 pure-u-lg-2-5">
                    <img class="pure-img-responsive" src="${pic}">
                </div>
                <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-3-5">
                    <h2 class="content-head content-head-ribbon">${title}</h2>
                    <p>${desc}</p>
                 </div>
        </div>`;
		anli_str2 += `
		<div class="l-box pure-u-1 pure-u-md-1-2 pure-u-lg-1-4">
		   	<a href="${url}" target="_blank" >
			    <div class="cases">
                    <p><img src="${pic}"></p>
				    <p>${title}</p>
			    </div>
			</a>
        </div>`;
}

// 渲染首页轮播图
$(document).ready(function () {
   let anli = document.getElementById('lunbo');
   if (anli) {
      anli.innerHTML = anli_str;
   }
   $(".ribbon").hide();
   let anli2 = document.getElementById('anli_list');
   if (anli2) {
       anli2.innerHTML = anli_str2;
   }   
});

