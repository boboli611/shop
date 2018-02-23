const util = require('../../utils/util.js');
const api = require('../../config/api.js');
const user = require('../../services/user.js');
const search = require('../public/search/search.js');

//获取应用实例
const app = getApp()
Page({
  data: {
    goods: [],
    page: 1,
  },
  onShareAppMessage: function () {
    return {
      title: 'NideShop',
      desc: '仿网易严选微信小程序商城',
      path: '/pages/index/index'
    }
  },

  getIndexData: function (options) {
    let that = this;
    var url = api.GoodsIndex + "?page=" + that.data.page
    util.request(url).then(function (res) {

      if (res.errno === 0 && res.data.list.length > 0) {
        that.data.goods = that.data.goods.concat(res.data.list)
        that.data.page++
      } else if(res.errno !== 0) {
        util.showModel('请求失败', res.msg)
      }

      that.setData({
        goods: that.data.goods,
      });
    });
  },
  onLoad: function (options) {
    this.data.word = options.word
    this.getIndexData(options);
  },
  onReady: function () {
    // 页面渲染完成
  },
  onShow: function () {
    // 页面显示
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
    wx.navigateTo({
      url: '../itemGoods/itemGoods?word=' + searchWord,
    })
  },
  search: function (e) {
    var word = this.data.word
    //search.search(word)
    wx.navigateTo({
      url: '../itemGoods/itemGoods?word=' + word,
    })
    //this.getIndexData(this.data);
  },
  searchWord: function (e) {
    this.setData({
      word: e.detail.value
    })
  },
  onReachBottom: function (options) {
    this.getIndexData(options)
  },

})
