var view,__extends=this&&this.__extends||function(){var e=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(e,t){e.__proto__=t}||function(e,t){for(var r in t)t.hasOwnProperty(r)&&(e[r]=t[r])};return function(t,r){function n(){this.constructor=t}e(t,r),t.prototype=null===r?Object.create(r):(n.prototype=r.prototype,new n)}}();!function(e){var t=function(e){function t(){var t=e.call(this)||this;return t.list_orders.renderHandler=new Handler(t,t.onListRender),t.tab_order.selectHandler=new Handler(t,t.onTabChanged),t.tab_order.selectedIndex=0,t.onTabChanged(0),t}return __extends(t,e),t.prototype.onOrdersYetRecieved=function(e){console.log("get orders !");var t=JSON.parse(e);null!=t&&(UserInfo.UserOrdersYet=t),this.list_orders.array=UserInfo.UserOrdersYet},t.prototype.onOrdersDoneRecieved=function(e){console.log("get orders !");var t=JSON.parse(e);null!=t&&(UserInfo.UserOrdersDone=t),this.list_orders.array=UserInfo.UserOrdersDone},t.prototype.onTabChanged=function(e){0==e?NetHelper.getInstance().httpGetOrdersYet(this,this.onOrdersYetRecieved):1==e&&NetHelper.getInstance().httpGetOrdersDone(this,this.onOrdersDoneRecieved)},t.prototype.onListRender=function(e,t){if(!(t>this.list_orders.array.length)){var r=this.list_orders.array[t],n=e.getChildByName("code"),o=e.getChildByName("action"),s=e.getChildByName("price"),i=e.getChildByName("count");null!=n&&(n.text=r.stock_code),null!=o&&(o.text=r.direction),null!=i&&(i.text=Number(r.shares).toFixed(0)),null!=s&&(s.text=(Number(r.price)*Number(r.shares)).toFixed(2));var d=e.getChildByName("delete");null!=d&&(0==this.tab_order.selectedIndex?(d.visible=!0,d.clickHandler=new Handler(this,this.onDeleteButtonClicked,[t])):d.visible=!1)}},t.prototype.onDeleteButtonClicked=function(e){if(console.log("delete button clicked index : %d",e),UserInfo.UserOrdersYet.length>e){var t=UserInfo.UserOrdersYet[e],r=Number(t.id);null!=r&&r>0&&NetHelper.getInstance().httpCancelOrder(r,this,this.onCancelOrder)}},t.prototype.onCancelOrder=function(e){var t=JSON.parse(e);null!=t&&t.hasOwnProperty("code")&&(t.code>=0?alert(t.error):(alert(t.message),NetHelper.getInstance().httpGetOrdersYet(this,this.onOrdersYetRecieved)))},t}(ui.OrdersViewUI);e.OrdersView=t}(view||(view={}));