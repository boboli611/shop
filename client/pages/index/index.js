const util = require('../../utils/util.js');
const api = require('../../config/api.js');
const user = require('../../services/user.js');
const search = require('../public/search/search.js');

//获取应用实例
const app = getApp()
Page({
  data: {
    data: [],
    word:""
  },
  onShareAppMessage: function () {
    return {
      title: 'NideShop',
      desc: '仿网易严选微信小程序商城',
      path: '/pages/index/index'
    }
  },

  getIndexData: function () {
    let that = this;
    util.request(api.IndexUrl).then(function (res) {
      if (res.errno === 0) {
        that.setData({
          data: res.data,
          word:"",
        });
      }
    });
  },
  onLoad: function (options) {
    this.getIndexData();
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

  searchHandle:function(e){
    var word = e.detail.value
   
    search.search(word)
  },

  searchWord:function(e){
    this.setData({
      word: e.detail.value
    })
  },

  search: function (e) {
    var word = this.data.word
    search.search(word)
  },
})
