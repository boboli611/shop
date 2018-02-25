const util = require('../../utils/util.js');
const api = require('../../config/api.js');
const user = require('../../services/user.js');

//获取应用实例
const app = getApp()
Page({
  data: {
    content: "",
    goods: [],
    address: "",
    order: {},
  },

  getBuyData: function (options) {
    let that = this;        
    var url = api.ShopList + "?p=1"
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        for (var i = 0; i < res.data.info.length; i++) {
          res.data.info[i].select = "select.png"
        }  

        that.setData({
          goods: res.data.info,
          order: res.data.order,
          address: res.data.address ? res.data.address : "",
        });
      }
    });
  },
  onLoad: function (options) {
      this.getBuyData(options);
  },
  onReady: function () {
    // 页面渲染完成
  },
  onShow: function (options) {
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
    let that = this;
    var id = e.currentTarget.dataset.id
    var goods = this.data.goods
    var good = goods[id]
    var url = api.ShopDrop + "?id=" + good.shop_id
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        goods.splice(id, 1)
        that.setData({
          goods: goods,
        })
      }
    });
  },
  select: function (e) {
    let that = this;
    var id = e.currentTarget.dataset.id
    var goods = this.data.goods

    goods[id].select = goods[id].select === "select.png" ? "select_pre.png" : "select.png";
    var countPrice = 0
    that.data.goods.forEach(function (item, index, array) {
      if (item.select === "select.png") {
        countPrice += item.price * 100
      }
    })

    that.data.order.price = countPrice / 100
    
    that.setData({
      goods: goods,
      order: that.data.order,
    })
  },
  buy: function (e) {

    let that = this;

    var ids = []
    that.data.goods.forEach(function (item, index, array) {
      if (item.select === "select.png"){
        ids.push(item.shop_id)
      }
    })

    var url = api.Createorder
    var content = this.data.content

    wx.navigateTo({
      url: '../shopBuy/shopBuy?ids=' + ids.join(',')
    })

  },

  bindContent: function (event) {
    var content = event.detail.value;
    this.setData({
      content: content
    });
  },

})
