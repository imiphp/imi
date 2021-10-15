/**
 * 页面加载器
 * author: lovefc
 * git：https://gitee.com/lovefc/loadpage
 * time: 2021/09/28 17:41
 */

let div_str = `
   <div class="loader twink" style="width: 70px;height: 70px;text-align: center;"><img src="assgin/images/logo.svg" alt="imi 框架 Logo" class="logo" style="width:70px;"></div>
`;

new loadpage({ delayTime: 2000, divHtml: div_str, themeCss: 'assgin/loadpage/css/imi.css',loadMode:'all'}).loading();