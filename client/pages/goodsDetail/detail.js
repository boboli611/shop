const util = require('../../utils/util.js');
const api = require('../../config/api.js');
const user = require('../../services/user.js');
var WxParse = require('../../wxParse/wxParse.js');
//获取应用实例
const app = getApp();
Page({
  data: {
    httpstatus:false,
    goods: {},
    storageList:{},
    selectStyle:"",
    selectSize:"",
    selectPrice:0,
  
  },
  onShareAppMessage: function () {
    return {
      title: 'NideShop',
      desc: '仿网易严选微信小程序商城',
      path: '/pages/index/index'
    }
  },

  getData: function (options) {
    let that = this;
    var url = api.GoodsDetail + "?id=" + options.id
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        var content = res.data.info.desc;
        WxParse.wxParse('article', 'html', content, that, 0);
        that.setData({
          goods: res.data.info,
          storageList:res.data.info.storage,
          storage: that.storage(res.data.info.storage),
          recommend: res.data.recommend,
          buy_button: "gray",
          chart_button: "gray",
          show:"hidden",
          sizeShow:"hidden"
        });

        
        wx.setNavigationBarTitle({
          title: res.data.info.title//页面标题为路由参数
        })
      }
    });
  },
  onLoad: function (options) {
    this.getData(options);
  },
  onReady: function () {
    // 页面渲染完成
  },
  onShow: function () {
    // 页面显示
  },
  onHide: function () {
    // 页面隐藏
    //this.data.goods = {};
    this.data.selectPrice = 0;
    this.buyInit()
    this.setData({
      show: "hidden"
    })
  },
  onUnload: function () {
    // 页面关闭
  },

  storage:function(data){

    var storage
    for (var key in data){
      storage = data[key]
      break;
    } 
    return storage
  },

  changeStyle:function(e){
    var storage, style, chart, buy
    style = e.currentTarget.dataset.style
    this.data.selectStyle = style
    this.data.selectSize = ""
    storage = this.data.storageList[style] 
    this.select()

    this.setData({
      storage: storage,
      selectStyle: style,
      sizeShow:"show",
    })
  },

  changeSize: function (e) {
    var storageList, storage, style, size
    style = this.data.selectStyle
    size = e.currentTarget.dataset.size
    
    this.data.selectSize = size
    storageList = this.data.storageList[style]
    if (storageList){
      storage = this.data.storageList[style][size]
    }

    this.select()
    this.setData({
      selectSize: size,
    })
  },
  close:function(e){
    this.buyInit()
    this.setData({
      show: "hidden",
    })
  },

  //购买商品
  showBuy: function (e) {
    var status
    var status = "show"
    this.setData({
      show: status,
      buyAction: "addBuy",
    })
  },

  showChart: function (e) {
    var status
    var status = "show"
    this.setData({
      show: status,
      buyAction:"addChart",
    })
  },

  //购买商品
  addBuy:function(e){

    
    if (this.data.selectPrice <= 0){
      util.showNotice('请选择样式和尺码')

      return
    }
    var stoage_id = this.data.goods.storage_id
    wx.navigateTo({
      url: '../buy/buy?type=buy&id=' + stoage_id
    })
  },
  //购物车
  addChart:function(){

    let that = this

    if (that.data.selectPrice <= 0) {
      util.showNotice('请选择样式和尺码')
      return
    }

    if (that.data.httpstatus){
      return 
    }
    that.data.httpstatus = true
    var url = api.AddChart + "?id=" + this.data.goods.storage_id
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        util.showSuccess('添加成功!')
        that.setData({
          show: "hidden",
        })
        that.data.goods.storage_id = 0
        that.buyInit()
      }
      
      that.data.httpstatus = false
    });
    
  },

  //选择商品
  select:function(){
    var  storageList, storageList, style, size
    style = this.data.selectStyle
    size = this.data.selectSize

    storageList = this.data.storageList[style]
    this.data.selectPrice = 0
    if (!storageList || !storageList[size]){
      this.buyInit()
      return
    }
   
    this.data.selectPrice = storageList[size].price
    this.data.goods.price = storageList[size].price
    this.data.goods.storage_id = storageList[size].id
    
    this.setData({
      goods: this.data.goods,
      storage: storageList,
      buy_button: "black",
      chart_button: "white"
    })
  },
  buyInit:function(){
    this.setData({
      selectSize: "",
      selectStyle: "",
      buy_button: "gray",
      chart_button: "gray",
      sizeShow: "hidden",
    })
  }

})
