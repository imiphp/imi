/*
 * @Author       : lovefc
 * @Date         : 2021-09-24 10:03:27
 * @LastEditTime : 2021-10-12 13:52:10
 */
import './css/modal.css';

import './js/modal.js';

import pay from './images/pay.png';
import wechat from './images/wechat.png';


let kaifa = `
 <p>想要加入我们吗？开源项目不能只靠一两个人，而是要靠大家的努力来完善~</p>
 <p>我们需要你的加入，你可以做的事（包括但不限于以下）：</p>
 <ul>
 <li>纠正拼写、错别字</li>
 <li>完善注释</li>
 <li>bug修复</li>
 <li>功能开发</li>
 <li>文档编写（<a href="https://github.com/imiphp/imi" target="_blank">https://github.com/imiphp/imi</a> 中的 doc 目录）</li>
 <li>教程、博客分享</li>
 </ul>
`;

let modal_1 = new mModal({
   title: "",
   width: "30%",
   content: "<p style=\"text-align:center;\">给项目点个star,也是一种帮助哦<br /><br />开源不求盈利，多少都是心意，生活不易，随缘随缘<br /><br /><br /><img src=\"" + pay + "\"></p>",
   cancelText: "star为敬", // 取消按钮文本
   confirmText: "点我查看捐赠列表", // 确定按钮文本
   showCancelButton: true, // 是否显示取消按钮
   showConfirmButton: true, // 是否显示确定按钮	  
   showClose: true, // 是否显示关闭按钮

   confirm: function () {
      modal_1.close();
      window.location.href = "./donate.html";
   },
   cancel: function () {
      window.open("https://github.com/imiphp/imi");
   }
});

let modal_2 = new mModal({
   title: "",
   width: "28%",
   content: kaifa,
   showCancelButton: false, // 是否显示取消按钮
   showConfirmButton: false, // 是否显示确定按钮	  
   showClose: true, // 是否显示关闭按钮
});

let modalWechat = new mModal({
   title: "",
   width: "30%",
   content: "<p style=\"text-align:center;\"><img src=\"" + wechat + "\"></p>",
   confirmText: "确定", // 确定按钮文本
   showCancelButton: false, // 是否显示取消按钮
   showConfirmButton: true, // 是否显示确定按钮	  
   showClose: true, // 是否显示关闭按钮

   confirm: function () {
      modalWechat.close();
   },
});

$(function () {
   $(".donation").on("click", function () {
      modal_1.renderDom();
   });
   $(".developer").on("click", function () {
      modal_2.renderDom();
   });
   $(".btn-wechat").on("click", function () {
      modalWechat.renderDom();
   });
});