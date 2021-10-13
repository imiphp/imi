/**
 * 页面加载器
 * author: lovefc
 * git：https://gitee.com/lovefc
 * time: 2021/09/28 17:41
 */
 
import loadpage from './src/loadpage.js';

import './css/animate.css';

import './css/default.css';

let div_str = `
   <div class="loader twink" style="width: 70px;height: 70px;text-align: center;"><img src="assgin/images/logo.svg" alt="imi 框架 Logo" class="logo" style="width:70px;"></div>
`;

let load = new loadpage({ delayTime: 2000, divHtml: div_str, loadMode:'all'});

load.loading();

export default loadpage;