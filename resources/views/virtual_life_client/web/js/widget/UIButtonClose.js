var widget,__extends=this&&this.__extends||function(){var t=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(t,n){t.__proto__=n}||function(t,n){for(var e in n)n.hasOwnProperty(e)&&(t[e]=n[e])};return function(n,e){function o(){this.constructor=n}t(n,e),n.prototype=null===e?Object.create(e):(o.prototype=e.prototype,new o)}}();!function(t){var n=function(t){function n(){var n=t.call(this)||this;return n.clickHandler=new Handler(n,n.onclick),n}return __extends(n,t),n.prototype.onclick=function(){Dispatcher.getInstance().send(ConstEvent.DESTORY_VIEW)},n}(laya.ui.Button);t.UIButtonClose=n}(widget||(widget={}));