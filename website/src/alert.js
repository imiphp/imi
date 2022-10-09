/*
 * @Author       : lovefc
 * @Date         : 2021-09-24 10:03:27
 * @LastEditTime : 2021-10-12 13:52:10
 */
import './css/modal.css';

import './js/modal.js';

import pay from './images/pay.png';
import wechat from './images/wechat.jpg';
import QRCode from 'qrcode'


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

let zanzhu = `
<div style="text-align:center;">
    <p>给 imi 加个鸡腿，会更有动力持续维护</p>
    <style>
    .button-pay {
        background: rgb(28, 184, 65);
    }
    .button-list {
        background: rgb(66, 184, 221);
    }
    </style>
    <form id="donate-form" class="pure-form">
        <fieldset class="pure-group">
            <input type="text" name="name" class="pure-input-1-2" placeholder="你的名字，必填" required="1" />
        </fieldset>
        <fieldset class="pure-group">
            <input type="text" name="amount" class="pure-input-1-2" placeholder="金额：元，必填" required="1" />
        </fieldset>
        <fieldset class="pure-group">
            <input type="text" name="link" class="pure-input-1-2" placeholder="链接地址（选填）" />
        </fieldset>
        <fieldset class="pure-group">
            <textarea style="width: 100%" name="message" class="pure-input-1-2" placeholder="留言内容（选填）"></textarea>
        </fieldset>
        <div class="pure-g">
            <div class="pure-u-1-2"><button type="submit" class="pure-button pure-button-primary button-payway" value="ZFBZF">支付宝</button></div>
            <div class="pure-u-1-2"><button type="submit" class="pure-button pure-button-primary button-pay button-payway" value="WXZF">微信支付</button></div>
            <input type="hidden" id="payWay" name="payWay" value="WXZF"/>
            <div style="text-align: center; margin-top: 1em; width: 100%"><button type="button" class="pure-button pure-button-primary button-list" onclick="window.open('/donate.html')">打赏赞助列表</button></div>
        </div>
    </form>
</div>
`;

let modal_1 = new mModal({
    title: "",
    width: "30%",
    content: zanzhu,
    showCancelButton: false, // 是否显示取消按钮
    showConfirmButton: false, // 是否显示确定按钮	  
    showClose: true, // 是否显示关闭按钮
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
    $(".donation").on("click", function (e) {
        e.preventDefault();
        modal_1.renderDom();
        var submiting = false;
        $('#donate-form').on('submit', function (e) {
            e.preventDefault();
            if (submiting) {
                return;
            }
            submiting = true;
            var originData = $(this).serializeArray();
            var data = {};
            $.each(originData, function (index, item) {
                if ('amount' === item.name) {
                    item.value = parseInt(item.value * 100);
                }
                data[item.name] = item.value;
            });
            var payWayText = data.payWay;
            switch (payWayText) {
                case 'WXZF':
                    payWayText = '微信';
                    break;
                case 'ZFBZF':
                    payWayText = '支付宝';
                    break;
            }
            $.ajax({
                method: 'POST',
                url: 'https://www.imiphp.com/api/donate/donate/pay',
                data: data,
                success: function (data) {
                    if (!('code' in data)) {
                        alert('数据错误！');
                        submiting = false;
                        return;
                    }
                    if (0 !== data.code) {
                        alert(data.message);
                        submiting = false;
                        return;
                    }
                    QRCode.toDataURL(data.jumpUrl, {
                        errorCorrectionLevel: 'H',
                        margin: 1,
                        width: 256,
                    })
                        .then(url => {
                            let modalQR = new mModal({
                                title: "",
                                width: "30%",
                                content: '<div style="text-align:center;"><p>请使用' + payWayText + '扫码支付</p><p><img src="' + url + '"/></p></div>',
                                confirmText: "完成支付", // 确定按钮文本
                                showConfirmButton: true, // 是否显示确定按钮	  
                                showClose: true, // 是否显示关闭按钮
                                confirm: function () {
                                    modalQR.close();
                                    window.location.href = '/donate.html';
                                },
                            });
                            modalQR.renderDom();
                        })
                        .catch(err => {
                            alert('生成二维码失败：' + err);
                        })
                        .finally(function () {
                            submiting = false;
                        });
                },
                error: function () {
                    alert('网络错误！');
                    submiting = false;
                },
            });
        });
        $('.button-payway').on('click', function () {
            $('#payWay').val($(this).val())
        });
    });
    $(".developer").on("click", function () {
        modal_2.renderDom();
    });
    $(".btn-wechat").on("click", function () {
        modalWechat.renderDom();
    });
});