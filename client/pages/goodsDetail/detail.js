const util = require('../../utils/util.js');
const api = require('../../config/api.js');
const user = require('../../services/user.js');

//获取应用实例
const app = getApp()
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
    options.id=46
    var url = api.GoodsDetail + "?id=" + options.id
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        that.setData({
          goods: res.data.info,
          storageList:res.data.info.storage,
          storage: that.storage(res.data.info.storage),
          recommend: res.data.recommend,
          buy_button: "gray",
          chart_button: "gray",
          show:"hidden"
        });

        wx.setNavigationBarTitle({
          title: res.data.info.title//页面标题为路由参数
        })
      }
    });
  },
  onLoad: function (options) {
    console.log("options \n")
    console.log(options)
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

  //购买商品
  addBuy:function(e){
    var status
    if (this.data.show == "hidden"){
      var status = "show"
    }else{
      var status = "hidden"
    }

    this.setData({
      show: status
    })

    if (this.data.selectPrice <= 0){
      return
    }

    wx.navigateTo({
      url: '../shop/shop?type=buy&id=' + this.data.goods.storage_id
    })
  },

  addChart:function(){

    let that = this
    if (that.data.show == "hidden") {
      that.setData({
        show: "show"
      })
    } 

    if (that.data.selectPrice <= 0) {
      return
    }

    if (that.data.httpstatus){
      return 
    }
    that.data.httpstatus = true
    var url = api.AddChart + "?id=" 
    util.request(url).then(function (res) {
      if (res.errno === 0) {
        util.showSuccess('添加成功!')
        that.setData({
          show: "hidden",
          selectStyle:"",
          selectSize:""
        })
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
      this.setData({
        selectSize: "",
        selectStyle:"",
        buy_button: "gray",
        chart_button: "gray"
      })
      return
    }

    this.data.selectPrice = storageList[size].price
    var good = this.data.goods
    good.price = storageList[size].price
    good.storage_id = storageList[size].id
    this.setData({
      goods: good,
      storage: storageList,
      buy_button: "black",
      chart_button: "white"
    })
  }

})
