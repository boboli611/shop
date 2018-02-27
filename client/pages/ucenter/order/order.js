var util = require('../../../utils/util.js');
var api = require('../../../config/api.js');

Page({
  data:{
    orderType:0,
    page:1,
    empty:1,
    orderList: [],
    title:['', '待付款','待发货','待收货','已收货'],
  },
  onLoad:function(options){
    // 页面初始化 options为页面跳转所带来的参数
    var title = this.data.title[options.type]
    this.data.orderType = options.type
    wx.setNavigationBarTitle({
      title: "我的订单-"+title,
    })
    this.getOrderList(options);
  },
  getOrderList(options){
    let that = this;
    var url = api.OrderList + "?type=" + this.data.orderType + "&p=" + this.data.page
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        that.data.page++
        var list = that.data.orderList.concat(res.data)
        that.setData({
          orderList: list,
          empty: list.length > 0 ? 1 : 0,
        });
      }
    });
  },
  payOrder(e){
    var order_id = e.currentTarget.dataset.orderId
    var price = e.currentTarget.dataset.price
    wx.redirectTo({
      url: '/pages/pay/pay?order_id=' + order_id + '&price=' + price,
    })
  },
  refundOrder(e) {
    let that = this;
    var order_id = e.currentTarget.dataset.orderId
    var index = e.currentTarget.dataset.index
    var url = api.OrderRefund + "?order_id=" + order_id
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        that.data.orderList[index].order_status_text = res.data.order_status_text
        that.setData({
          orderList: that.data.orderList,
        });
      }
    });
  },
  receveOrder(e) {
    let that = this;
    var order_id = e.currentTarget.dataset.orderId
    var index = e.currentTarget.dataset.index
    var url = api.OrderReceve + "?order_id=" + order_id
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        that.data.orderList[index].order_status_text = res.data.order_status_text
        that.setData({
          orderList: that.data.orderList,
        });
      }
    });
  },
  onReady:function(){
    // 页面渲染完成
  },
  onShow:function(){
    // 页面显示
  },
  onHide:function(){
    // 页面隐藏
  },
  onUnload:function(){
    // 页面关闭
  },
  onReachBottom: function () {
    this.getOrderList()
  },
})