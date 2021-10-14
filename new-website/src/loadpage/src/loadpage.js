/**
 * 页面加载器
 * author: lovefc
 * blog：https://lovefc.cn
 * github: https://github.com/lovefc/loadpage
 * gitee: https://gitee.com/lovefc/loadpage
 * time: 2021/09/28 17:41
 */

; (function (exports) {
	let doc = exports.document,
		a = {},
		expose = +new Date(),
		rExtractUri = /((?:http|https|file):\/\/.*?\/[^:]+)(?::\d+)?:\d+/,
		isLtIE8 = ('' + doc.querySelector).indexOf('[native code]') === -1;
	exports.getCurrAbsPath = function () {
		// FF,Chrome
		if (doc.currentScript) {
			return doc.currentScript.src;
		}
		let stack;
		try {
			a.b();
		}
		catch (e) {
			stack = e.fileName || e.sourceURL || e.stack || e.stacktrace;
		}
		// IE10
		if (stack) {
			let absPath = rExtractUri.exec(stack)[1];
			if (absPath) {
				return absPath;
			}
		}
		// IE5-9
		for (var scripts = doc.scripts,
			i = scripts.length - 1,
			script; script = scripts[i--];) {
			if (script.className !== expose && script.readyState === 'interactive') {
				script.className = expose;
				return isLtIE8 ? script.getAttribute('src', 4) : script.src;
			}
		}
	};
}(window));

let srcPath = getCurrAbsPath();

const NowSrcPath = srcPath.substring(0, srcPath.lastIndexOf("/"));

class loadpage {
	constructor(options) {

		let that = this;
			
		this.srcPath = NowSrcPath;

		this.themeCss = ''; // 要加载的css

		this.defaultCss = NowSrcPath + '/css/default.css'; // 要加载的默认css

		this.animateName = 'fadeOut'; // 要执行的动画名称

		this.delayTime = 3000; // 延迟时间 

		this.loadMode = 'all'; // 加载方式,part(局部,也就是dom渲染完),all(等待图片等资源)

		this.divHtml = `
            <div class="fc_load_inner"><div class="fc_inner one"></div><div class="fc_inner two"></div><div class="fc_inner three"></div></div>		
		`;
		this.loadID = 'fc_loader';
		for (let key in options) {
			if (key in that) {
				that[key] = options[key];
			}
		}
		
        this.addLoadIngDiv(this.loadID);
		
		if (loadpage.isSystem() === 'win') {
			this.loadStyle(this.defaultCss, 'head');
			if(this.themeCss){
			    this.loadStyle(this.themeCss, 'head');
			}			
		}
	}
	static isSystem() {
		if ("undefined" != typeof __webpack_modules__) {
			return 'webpack';
		} else if (typeof window === 'object') {
			return 'win';
		} else if (Object.prototype.toString.call(process) === '[object process]') {
			return 'node';
		}
	}
	loading() {
		this.addHeadJs();
	}
	closeLoading() {
		this.closePageLoading(this.loadID, this.animateName, this.delayTime);
	}
	closePageLoading(loadID, animateName, delayTime) {
		let box = document.getElementById(loadID);		
		if(!box){
			return false;
		}
		let a_time = Math.round(delayTime / 1000);
		let animation = `${animateName} ${a_time}s`;
		box.style.animation = animation;
		setTimeout(function () {
			if (box) {
				box.remove();
			}
		}, (delayTime));
	}
	addHeadJs() {
		let head = document.getElementsByTagName('head')[0];
		let script = document.createElement('script');
		script.type = 'text/javascript';
		if (loadpage.isSystem() === 'webpack') {
			this.closeLoading2 = `${this.closePageLoading}`;
		} else {
			this.closeLoading2 = `function ${this.closePageLoading}`;
		}
		let dom_load = `
			document.addEventListener('DOMContentLoaded',function(){
	            setTimeout(${this.closeLoading2}("${this.loadID}","${this.animateName}",${this.delayTime}),${this.delayTime});
            });		
	    `;
		let all_load = `
            document.onreadystatechange = runLoading; 
			function runLoading(){
				if(document.readyState == "complete"){
					setTimeout(${this.closeLoading2}("${this.loadID}","${this.animateName}",${this.delayTime}),${this.delayTime});
				}
			}
		`;
		let load = all_load;
		if (this.loadMode === 'part') {
			load = dom_load;
		}
		script.text = load;
		head.appendChild(script);
	}
	loadStyle(url, tagname) {
		let link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;
		let head = document.getElementsByTagName(tagname)[0];
		let em = (tagname == 'body') ? document.body : head;
		em.appendChild(link);
	}
	removeStyle(filename) {
		let targetelement = "link";
		let targetattr = "href";
		let allsuspects = document.getElementsByTagName(targetelement);
		for (let i = allsuspects.length; i >= 0; i--) {
			if (allsuspects[i] && allsuspects[i].getAttribute(targetattr) != null && allsuspects[i].getAttribute(targetattr).indexOf(filename) != -1)
				allsuspects[i].parentNode.removeChild(allsuspects[i]);
		}
	}
	loadScript(url, tagname) {
		let script = document.createElement("script");
		script.type = "text/javascript";
		script.src = url;
		let head = document.getElementsByTagName(tagname)[0];
		let em = (tagname == 'body') ? document.body : head;
		em.appendChild(script);
	}
	removeScript(filename) {
		let targetelement = "script";
		let targetattr = "src";
		let allsuspects = document.getElementsByTagName(targetelement);
		for (let i = allsuspects.length; i >= 0; i--) {
			if (allsuspects[i] && allsuspects[i].getAttribute(targetattr) != null && allsuspects[i].getAttribute(targetattr).indexOf(filename) != -1)
				allsuspects[i].parentNode.removeChild(allsuspects[i]);
		}
	}
	addLoadIngDiv(loadid) {
		let parent = document.body;
		let div = document.createElement("div");
		let divhtml = this.divHtml;
		let html = `
		    <div id="${loadid}">${divhtml}</div>
		`;
		div.innerHTML = html;
		parent.appendChild(div);
	}
	setStyle() {
		let body = document.body;
		let html = document.documentElement;
		html.style.overflow = "visible";
		html.style.height = "auto";
		body.style.overflow = "visible";
		body.style.height = "auto";
	}
}
; (function (factory) {
	if (typeof exports === "object") {
		module.exports = factory();
	} else if (typeof define === "function" && define.amd) {
		define(factory);
	} else {
		let glob;
		try {
			glob = window;
		} catch (e) {
			glob = self;
		}
		glob.loadpage = factory();
	}
})(function () {
	'use strice';
	return loadpage;
});
