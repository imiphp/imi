; (function (root, factory) {
  'use strict';

  if (typeof module === 'object' && typeof module.exports === 'object') {
    factory(require('jquery'), root);
  }
  if (typeof define === "function") {
    if (define.cmd) {
      define(function (require, exports, module) {
        var $ = require("jquery");
        factory($, root);
      });
    } else {
      define(["jquery"], function ($) {
        factory($, root);
      });
    }
  } else {
    factory(root.jQuery, root);
  }
}
  (typeof window !== "undefined" ? window : this, function ($, root, undefined) {
    'use strict';
    if (!$) {
      $ = root.jQuery || null;
    }
    if (!$) {
      throw new TypeError("必须引入jquery库方可正常使用！");
    }

    function mModal(options) {
      options = options || {};
      this.options = $.extend(true, {}, mModal.defaultOptions, options);
      // this.renderDom();
    }

    mModal.defaultOptions = {
      title: "提示", // 标题，默认：提示
      width: "25%", // 弹出框宽度，默认 25%
      top: "15vh", // 距离可视区域顶部距离 CSS中 margin-top 值
      content: "正文内容什么", // 正文，默认：正文内容
      cancelText: "取 消", // 取消按钮文本
      confirmText: "确 定", // 确定按钮文本
      showCancelButton: true, // 是否显示取消按钮
      showConfirmButton: true, // 是否显示确定按钮
      showClose: true, // 是否显示关闭按钮
      modal: true, // 是否需要遮罩层
      customClass: "", // 自定义类名confirm
      confirm: null, // 确认后 回调函数
      cancel: null, // 取消后 回调函数
    }

    mModal.prototype = {
      constructor: mModal,
      initElement: function () {
        // 先创建modal框架
        // 1. 创建 m-modal__wrapper
        var mWrapper = document.createElement("div");
        mWrapper.className = "m-modal__wrapper";
        // 2. 创建 m-modal__container 并且 append m-modal__wrapper
        var mContainer = document.createElement("div");
        mContainer.className = "m-modal__container";
        mWrapper.appendChild(mContainer);
        // 3. 创建 m-modal__header
        var mHeader = document.createElement("div");
        mHeader.className = "m-modal__header";
        // 4. 创建 title close 并且 append m-modal__header
        var mTitle = document.createElement("span");
        mTitle.className = "m-modal__title";
        mHeader.appendChild(mTitle);
        var mHeaderbtn = document.createElement("button");
        mHeaderbtn.className = "m-modal__headerbtn";
        var mClose = document.createElement("i");
        mClose.className = "m-modal__close fa fa-times";
        mHeaderbtn.appendChild(mClose);
        mHeader.appendChild(mHeaderbtn);
        // 5. 创建 m-modal__body
        var mBody = document.createElement("div");
        mBody.className = "m-modal__body";
        var mBodySpan = document.createElement("div");
        mBody.appendChild(mBodySpan);
        // 6. 创建 m-modal__footer
        var mFooter = document.createElement("div");
        mFooter.className = "m-modal__footer";
        // 7. 创建 操作按钮
        var mButtonLeft = document.createElement("button");
        mButtonLeft.className = "m-modal-button m-modal--primary";
        var mButtonRight = document.createElement("button");
        mButtonRight.className = "m-modal-button m-modal--default";
        var mBtnSpanL = document.createElement("span");
        var mBtnSpanR = document.createElement("span");
        mFooter.appendChild(mButtonLeft).appendChild(mBtnSpanL);
        mFooter.appendChild(mButtonRight).appendChild(mBtnSpanR);
        // 8. header body footer append m-modal__container
        mContainer.appendChild(mHeader);
        mContainer.appendChild(mBody);
        mContainer.appendChild(mFooter);
        document.body.appendChild(mWrapper);
        // 返回可能用到的dom
        this.mWrapper = mWrapper;
        this.mContainer = mContainer;
        this.mHeader = mHeader;
        this.mHeaderbtn = mHeaderbtn;
        this.mBody = mBody;
        this.mFooter = mFooter;
        this.mButtonLeft = mButtonLeft;
        this.mButtonRight = mButtonRight;
        // return this;
      },
      renderDom: function () {
        var options = this.options;
        this.initElement();
        // 宽度
        if (options.width) {
          document.querySelector(".m-modal__container").style.width = options.width;
        }
        // 标题
        if (options.title) {
          document.querySelector(".m-modal__title").innerHTML = options.title;
        }
        // 距离顶部距离
        if (options.top) {
          document.querySelector(".m-modal__container").style.marginTop = options.top;
        }
        // 正文
        if (options.content) {
          document.querySelector(".m-modal__body div").innerHTML = options.content;
        }
        // 是否显示确定按钮
        if (options.showConfirmButton) {
          if (options.confirmText) {
            document.querySelector(".m-modal__footer button:first-child span").innerHTML = options.confirmText;
          }
        } else {
          this.mFooter.removeChild(this.mButtonLeft);
        }
        // 是否显示取消按钮
        if (options.showCancelButton) {
          if (options.cancelText) {
            document.querySelector(".m-modal__footer button:last-child span").innerHTML = options.cancelText;
          }
        } else {
          this.mFooter.removeChild(this.mButtonRight);
        }
        // 是否显示关闭按钮
        if (!options.showClose) {
          this.mHeader.removeChild(this.mHeaderbtn);
        }
        // 是否需要遮罩层
        if (!options.modal) {
          document.querySelector(".m-modal__wrapper").style.background = "rgba(0, 0, 0, 0)";
        }
        // 自定义类名
        if (options.customClass) {
          document.querySelector(".m-modal__container").classList.add(options.customClass);
        }
        // 确定按钮方法回调
        if (options.showConfirmButton && options.confirm) {
          this.mButtonLeft.onclick = function () {
            options.confirm();
          }
        }
        // 取消按钮方法回调
        if (options.showCancelButton) {
          this.mButtonRight.onclick = function () {
            if (options.cancel) {
              options.cancel();
              mModal.prototype.close();
            } else {
              mModal.prototype.close();
            }
          }
        }
        // 关闭按钮点击方法
        if (options.showClose) {
          this.mHeaderbtn.onclick = function () {
            mModal.prototype.close();
          }
        }
      },
      show: function () {
        document.querySelector(".m-modal__wrapper").style.display = "block";
      },
      close: function () {
        document.querySelector(".m-modal__wrapper").style.display = "none";
        var timer = null;
        timer = setTimeout(function () {
          clearTimeout(timer);
          mModal.prototype.destroy();
        }, 200);
      },
      destroy: function () {
        var mWrapper = document.querySelector(".m-modal__wrapper");
        var parentWrapper = mWrapper.parentNode;
        parentWrapper.removeChild(mWrapper);
      }
    }

    window.mModal = mModal

  }));