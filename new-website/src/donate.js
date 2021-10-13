/*
 * @Author       : lovefc
 * @Date         : 2021-08-31 14:36:35
 * @LastEditTime : 2021-09-23 16:15:58
 */

// 捐赠数据
let data = [{
   money: "300.00",
   author: "<a href=\"https://blog.csdn.net/qq_32783703\"  target=\"_blank\" rel=\"nofollow\">张磊</a>",
   msg: "",
   channel: "支付宝"
},
{
   money: "200",
   author: "牛顿呀",
   msg: "imiphp 是永远的神",
   channel: "微信"
},
{
   money: "188.88",
   author: "<a href=\"https://www.dute.org\"  target=\"_blank\" rel=\"nofollow\">独特工具箱</a>",
   msg: "支持开源，支持imiphp",
   channel: "微信"
},
{
   money: "105.21",
   author: "EasySwoole 贡献者——仙士可",
   msg: "",
   channel: "微信"
},
{
   money: "100.00",
   author: "*能",
   msg: "",
   channel: "支付宝"
},
{
   money: "100.00",
   author: "Tius",
   msg: "支持imi，支持宇润开源作品",
   channel: "微信"
},
{
   money: "99.99",
   author: "*宇",
   msg: "希望开源产品长长久久",
   channel: "微信"
},
{
   money: "66.66",
   author: "会的不多",
   msg: "宇润的PaySDK用的很6，感谢啊！",
   channel: "支付宝"
},
{
   money: "66.66",
   author: "*生",
   msg: "",
   channel: "微信"
},
{
   money: "20.00",
   author: "<a href=\"https://blog.yangyj.com/\"  target=\"_blank\" rel=\"nofollow\">yangyj</a>",
   msg: "",
   channel: "QQ"
},
{
   money: "19.90",
   author: "*瑶",
   msg: "",
   channel: "微信"
},
{
   money: "16.66",
   author: "<a href=\"http://blog.mandian.vip/\"  target=\"_blank\" rel=\"nofollow\">慢点</a> (该博客使用 imi 开发)",
   msg: "",
   channel: "微信"
},
{
   money: "16.66",
   author: "北*Q",
   msg: "",
   channel: "微信"
},
{
   money: "12.16",
   author: "*森",
   msg: "支持开源项目/精神",
   channel: "微信"
},
{
   money: "11.00",
   author: "*健",
   msg: "大佬辛苦了",
   channel: "微信"
},
{
   money: "10.00",
   author: "*志伟",
   msg: "OneTake支持yurun开源",
   channel: "支付宝"
},
{
   money: "10.00",
   author: "<a href=\"http://lovefc.cn/\"  target=\"_blank\" rel=\"nofollow\">封尘</a>",
   msg: "",
   channel: "支付宝"
},
{
   money: "10.00",
   author: "摄像头",
   msg: "",
   channel: "支付宝"
},
{
   money: "10.00",
   author: "<a href=\"http://www.yajiji.cc/\"  target=\"_blank\" rel=\"nofollow\">五十岚</a>",
   msg: "感谢开源精神，感谢付出。",
   channel: "微信"
},
{
   money: "10.00",
   author: "J*U",
   msg: "支持开源，群主加油",
   channel: "微信"
},
{
   money: "10.00",
   author: "落幕",
   msg: "给imi加个鸡腿",
   channel: "微信"
},
{
   money: "8.88",
   author: "*愁",
   msg: "",
   channel: "微信"
},
{
   money: "6.66",
   author: "Eleven",
   msg: "",
   channel: "微信"
},
{
   money: "6.66",
   author: "J*w",
   msg: "",
   channel: "微信"
},
{
   money: "6.66",
   author: "*兰胤",
   msg: "imi加油",
   channel: "支付宝"
},
{
   money: "6.00",
   author: "daxia",
   msg: "",
   channel: "支付宝"
},
{
   money: "3.88",
   author: "努*n",
   msg: "",
   channel: "微信"
},
{
   money: "3.00",
   author: "aLuckyfellow",
   msg: "支持 yurun开源",
   channel: "支付宝"
},
{
   money: "1.00",
   author: "*锐",
   msg: "我是swoole5群的似是而非，支持一下",
   channel: "支付宝"
},
{
   money: "0.88",
   author: "小公主",
   msg: "希望老哥多写点demo",
   channel: "微信"
},
{
   money: "0.1",
   author: "*文文",
   msg: "",
   channel: "支付宝"
},
{
   money: "0.01",
   author: "*林",
   msg: "",
   channel: "支付宝"
},
];

// 遍历,放入初始加载事件里面
$(document).ready(function () {
   let donate = document.getElementById('donate');
   if (donate) {
      for (let i = 0; i < data.length; i++) {
         var tr = document.createElement('tr');
         var model = data[i].channel;
         var styles = '';
         if (model == '支付宝') {
            styles = 'style="color:#005687;"';
         } else if (model == '微信') {
            styles = 'style="color:#008F0A;"';
         } else if (model == 'QQ') {
            styles = 'style="color:#D9210D;"';
         }
         tr.innerHTML = `
            <td width="20%">${data[i].money}</td>
            <td width="20%">${data[i].author}</td>
            <td width="40%" class="liuyan">${data[i].msg}</td>						
            <td width="20%" class="qudao" ${styles}>${data[i].channel}</td>
        `
         donate.appendChild(tr);
      }
   }
});