var view,__extends=this&&this.__extends||function(){var e=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(e,t){e.__proto__=t}||function(e,t){for(var n in t)t.hasOwnProperty(n)&&(e[n]=t[n])};return function(t,n){function o(){this.constructor=t}e(t,n),t.prototype=null===n?Object.create(n):(o.prototype=n.prototype,new o)}}();!function(e){var t=function(e){function t(){var t=e.call(this)||this;return t.btn_friend.clickHandler=new Handler(t,t.onFriendClicked),t.btn_rank.clickHandler=new Handler(t,t.onRankClicked),t.list_stock.selectHandler=new Handler(t,t.onListCellSelected),t.list_stock.renderHandler=new Handler(t,t.onListRender),t.list_stock.vScrollBarSkin="",t.refreshStocksList(),t}return __extends(t,e),t.prototype.onFriendClicked=function(){console.log("on friend clicked"),Dispatcher.getInstance().send(ConstEvent.POPUP_VIEW,ConstViews.FRIEND)},t.prototype.onRankClicked=function(){console.log("on rank clicked"),Dispatcher.getInstance().send(ConstEvent.POPUP_VIEW,ConstViews.RANK)},t.prototype.onListCellSelected=function(e){Stocks.currentSelected=e,Dispatcher.getInstance().send(ConstEvent.PUSH_VIEW,ConstViews.CARDINFO)},t.prototype.onListRender=function(e,t){if(!(t>Stocks.data.length)){var n=Stocks.data[t],o=e.getChildByName("username"),r=e.getChildByName("stockid"),i=e.getChildByName("price"),c=e.getChildByName("diff"),s=e.getChildByName("total_price");if(null!=o&&(o.text=n.owner.nickname),null!=r&&(r.text=n.stock_code),null!=i&&(i.text=Number(n.last_price).toFixed(2)),null!=s&&(s.text=Number(n.owner.rong_yao_points).toFixed(2)),null!=c){var l=100*Number(n.changing_rate),a=l.toFixed(2);c.color=l>=0?"#FF0000":"#00FF00",c.text=a+"%"}}},t.prototype.refreshStocksList=function(){this.list_stock.array=Stocks.data},t}(ui.StockViewUI);e.StockView=t}(view||(view={}));