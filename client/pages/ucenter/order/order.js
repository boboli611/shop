var util = require('../../../utils/util.js');
var api = require('../../../config/api.js');

Page({
  data:{
    orderList: []
  },
  onLoad:function(options){
    // 页面初始化 options为页面跳转所带来的参数

    this.getOrderList(options);
  },
  getOrderList(options){
    let that = this;
    var url = api.OrderList + "?type=" + options.type
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        that.setData({
          orderList: res.data
        });
      }
    });
  },
  payOrder(e){
    console.log(e)
    var order_id = e.currentTarget.dataset.orderId
    var price = e.currentTarget.dataset.price
    wx.redirectTo({
      url: '/pages/pay/pay?order_id=' + order_id + '&price=' + price,
    })
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
  }
})