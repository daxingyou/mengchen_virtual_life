var view,__extends=this&&this.__extends||function(){var t=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(t,e){t.__proto__=e}||function(t,e){for(var n in e)e.hasOwnProperty(n)&&(t[n]=e[n])};return function(e,n){function o(){this.constructor=e}t(e,n),e.prototype=null===n?Object.create(n):(o.prototype=n.prototype,new o)}}();!function(t){var e=function(t){function e(){var e=t.call(this)||this;return e.btn_stocks.clickHandler=new Handler(e,e.onStocksClicked),e.btn_trades.clickHandler=new Handler(e,e.onTradesClicked),e.btn_pet.clickHandler=new Handler(e,e.onPetClicked),e}return __extends(e,t),e.prototype.onStocksClicked=function(){NetHelper.getInstance().httpGetStockTrend(this,this.onGetStocks)},e.prototype.onGetStocks=function(t){Stocks.setStocksData(t),Dispatcher.getInstance().send(ConstEvent.PUSH_VIEW,ConstViews.STOCK)},e.prototype.onTradesClicked=function(){Dispatcher.getInstance().send(ConstEvent.PUSH_VIEW,ConstViews.WALLET)},e.prototype.onPetClicked=function(){Dispatcher.getInstance().send(ConstEvent.PUSH_VIEW,ConstViews.PET)},e}(ui.HomeViewUI);t.HomeView=e}(view||(view={}));