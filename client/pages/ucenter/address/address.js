var util = require('../../../utils/util.js');
var api = require('../../../config/api.js');
var app = getApp();

Page({
  data: {
    isBuy:0,
    back:0,
    addressList: [],
  },
  onLoad: function (options) {
    // 页面初始化 options为页面跳转所带来的参数
    this.data.isBuy = options.isBuy ? 1 : 0
    this.data.back = options.back ? 1 : 0
    
  },
  onReady: function () {
    // 页面渲染完成
  },
  onShow: function () {

    // 页面显示
    if (this.data.back === 1){
      wx.navigateBack({
        delta: 3
      })
      return
    }
    this.getAddressList();
  },
  getAddressList (){
    let that = this;
    util.request(api.AddressList).then(function (res) {
      if (res.errno === 0) {
        that.setData({
          addressList: res.data,
          isBuy: that.data.isBuy
        });
      }
    });
  },
  addressAddOrUpdate (event) {
    wx.navigateTo({
      url: '/pages/ucenter/addressAdd/addressAdd?id=' + event.currentTarget.dataset.addressId+'&isBuy='+this.data.isBuy
    })
  },
  deleteAddress(event){
    let that = this;
    wx.showModal({
      title: '',
      content: '确定要删除地址？',
      success: function (res) {
        if (res.confirm) {
          let addressId = event.target.dataset.addressId;
          util.request(api.AddressDelete, { id: addressId }, 'POST').then(function (res) {
            if (res.errno === 0) {
              that.getAddressList();
            }
          });
          console.log('用户点击确定')
        }
      }
    })
    return false;
    
  },
  bindIsDefault(event) {
    let index = event.target.dataset.addressIndex;
    var address= this.data.addressList[index]
    if(address.status){
      return
    }

    let that = this;
    util.request(api.AddressSave, {
      id: address.id,
      name: address.name,
      mobile: address.mobile,
      province_id: address.province_id,
      city_id: address.city_id,
      address: address.address,
      full_region: address.full_region,
      status: 1,
    }, 'POST').then(function (res) {
      if (res.errno === 0) {
        that.getAddressList();
      }
    });
  },
  onHide: function () {
    // 页面隐藏
  },
  onUnload: function () {
    // 页面关闭
  }
})