const util = require('../../utils/util.js');
const api = require('../../config/api.js');
const user = require('../../services/user.js');

//获取应用实例
const app = getApp()
Page({
  data: {
    id :0,
    content: "",
    goods: [],
    address: "",
    order: {},
  },

  getBuyData: function () {
    let that = this;
    var url = api.GoodsInfo + "?id=" + this.data.id
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        that.setData({
          goods: [res.data.info],
          order: res.data.order,
          address: res.data.address ? res.data.address : "",
        });
      }
    });
  },
  onLoad: function (options) {
    //options.id = 75
    this.data.id = options.id
    //this.getBuyData();
  },
  onReady: function () {
    // 页面渲染完成
  },
  onShow: function (options) {
    // 页面显示
    //console.log('onShow', this.data.id)
    this.getBuyData(options);
  },
  onHide: function () {
    // 页面隐藏
  },
  onUnload: function () {
    // 页面关闭
  },

  searchHandle: function (e) {
    var searchWord = e.detail.value;
    var options = {};
    options["word"] = searchWord;
    this.onLoad(options)
  },
  close: function (e) {
    var id = e.currentTarget.dataset.id
    console.log(id)
    var goods = this.data.goods
    goods.splice(id, 1)
    this.setData({
      goods: goods,
    })
  },
  buy: function (e) {

    let that = this;
    if (that.data.goods.length == 0) {
      return
    }

    if (!that.data.address) {
      that.setData({
        warning: "red",
      })
      return
    }

    var ids = []
    that.data.goods.forEach(function (item, index, array) {
      console.log(item.storage_id, index)
      ids.push(item.storage_id)
    })

    var url = api.Createorder
    var content = this.data.content
    var param = { "ids": ids, "address_id": this.data.address.id, "content": content }
    console.log(param)
    util.request(url, param, "POST").then(function (res) {
      if (res.errno === 0) {

        wx.requestPayment({
          'timeStamp': res.data.timeStamp,
          'nonceStr': res.data.nonceStr,
          'package': res.data.package,
          'signType': 'MD5',
          'paySign': res.data.sign,
          'success': function (res) {
            wx.redirectTo({
              url: '/pages/ucenter/order/order?type=2',
            })
          },
          'fail': function (res) {
            console.log("fail", res)
            if (res.errMsg == "requestPayment:fail cancel") {
              return
            }
            /*
            wx.redirectTo({
              url: '/pages/payResult/payResult?status=false',
            })
            */
          }
        })
      }
    });

  },

  bindContent: function (event) {
    var content = event.detail.value;
    this.setData({
      content: content
    });
  },

})
