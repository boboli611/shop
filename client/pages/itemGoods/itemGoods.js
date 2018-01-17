const util = require('../../utils/util.js');
const api = require('../../config/api.js');
const user = require('../../services/user.js');

//获取应用实例
const app = getApp()
Page({
  data: {
    goods: [],
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
    var url = api.GoodsList + "?word=" + options.word
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        that.setData({
          goods: res.data.list,
          word: options.word
        });
      }else{
        that.setData({
          goods: res.data.list,
          word: options.word
        });
      }
    });
  },
  onLoad: function (options) {
    console.log("options \n")
    console.log(options)
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

  searchHandle:function(e){
    var searchWord = e.detail.value;
    var options = {}; 
    options["word"] = searchWord;
    this.onLoad(options)
  },

})
