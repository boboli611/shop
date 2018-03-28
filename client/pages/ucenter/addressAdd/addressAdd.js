var util = require('../../../utils/util.js');
var api = require('../../../config/api.js');
var app = getApp();
Page({
  data: {
    customItem: '全部',
    region: [],
    isBuy:0,
    address: {
      id:0,
      province_id: 0,
      city_id: 0,
      address: '',
      full_region: '',
      name: '',
      mobile: '',
      status: 0,
      selected:""
    },
    addressId: 0,
    openSelectRegion: false,
    regionType: 1,
    regionList: [],
    selectRegionDone: false
  },
  bindinputMobile(event) {
    let address = this.data.address;
    address.mobile = event.detail.value;
    this.setData({
      address: address
    });
  },
  bindinputName(event) {
    let address = this.data.address;
    address.name = event.detail.value;
    this.setData({
      address: address
    });
  },
  bindinputAddress (event){
    let address = this.data.address;
    address.address = event.detail.value;
    this.setData({
      address: address
    });
  },
  bindIsDefault(){
    let address = this.data.address;
    address.status = !address.status;
    this.setData({
      address: address
    });
  },
  getAddressDetail() {
    let that = this;
    var region
    util.request(api.AddressDetail, { id: that.data.addressId }).then(function (res) {
      if (res.errno === 0) {
        if (res.data.full_region){
          region = res.data.full_region.split(",");
        }
        
        that.setData({
          address: res.data,
          region: region
        });
      }
    });
  },
  setRegionDoneStatus() {
    let that = this;
    let doneStatus = that.data.selectRegionList.every(item => {
      return item.id != 0;
    });

    that.setData({
      selectRegionDone: doneStatus
    })

  },
  chooseRegion(event) {
    let address = this.data.address;
    console.log("address" , event.detail.value)
    address.full_region = event.detail.value;
    this.setData({
      address: address
    });

  },
  onLoad: function (options) {
    // 页面初始化 options为页面跳转所带来的参数
    if (options.id) {
      console.log(options)
      this.setData({
        addressId: options.id,
        isBuy: options.isBuy
      });

      if (options.id === "0"){
        var address = {"status":1};
        this.setData({
          address: address
        })
        return
      }
      this.getAddressDetail();
    }

  },
  onReady: function () {

  },
  selectRegionType(event) {
    let that = this;
    let regionTypeIndex = event.target.dataset.regionTypeIndex;
    let selectRegionList = that.data.selectRegionList;

    //判断是否可点击
    if (regionTypeIndex + 1 == this.data.regionType || (regionTypeIndex - 1 >= 0 && selectRegionList[regionTypeIndex-1].id <= 0)) {
      return false;
    }

    this.setData({
      regionType: regionTypeIndex + 1
    })
    
    let selectRegionItem = selectRegionList[regionTypeIndex];

    this.setRegionDoneStatus();

  },
  selectRegion(event) {
    let that = this;
    let regionIndex = event.target.dataset.regionIndex;
    let regionItem = this.data.regionList[regionIndex];
    let regionType = regionItem.type;
    let selectRegionList = this.data.selectRegionList;
    selectRegionList[regionType - 1] = regionItem;


    if (regionType != 3) {
      this.setData({
        selectRegionList: selectRegionList,
        regionType: regionType + 1
      })
    } else {
      this.setData({
        selectRegionList: selectRegionList
      })
    }


    this.setData({
      selectRegionList: selectRegionList
    })


    that.setData({
      regionList: that.data.regionList.map(item => {

        //标记已选择的
        if (that.data.regionType == item.type && that.data.selectRegionList[that.data.regionType - 1].id == item.id) {
          item.selected = true;
        } else {
          item.selected = false;
        }

        return item;
      })
    });

    this.setRegionDoneStatus();

  },
  doneSelectRegion() {
    if (this.data.selectRegionDone === false) {
      return false;
    }

    let address = this.data.address;
    let selectRegionList = this.data.selectRegionList;
    address.province_id = selectRegionList[0].id;
    address.city_id = selectRegionList[1].id;
    address.province_name = selectRegionList[0].name;
    address.city_name = selectRegionList[1].name;
    address.district_name = selectRegionList[2].name;
    address.full_region = selectRegionList.map(item => {
      return item.name;
    }).join('');

    this.setData({
      address: address,
      openSelectRegion: false
    });

  },
  cancelSelectRegion() {
    this.setData({
      openSelectRegion: false,
      regionType: this.data.regionDoneStatus ? 3 : 1
    });

  },

  cancelAddress(){
    wx.navigateTo({
      url: '/pages/ucenter/address/address',
    })
  },
  saveAddress(){
    let address = this.data.address;

    if (address.name == '') {
      util.showErrorToast('请输入姓名');

      return false;
    }

    if (address.mobile == '') {
      util.showErrorToast('请输入手机号码');
      return false;
    }


    if (address.full_region == "") {
      util.showErrorToast('请输入省市区');
      return false;
    }

    if (address.address == '') {
      util.showErrorToast('请输入详细地址');
      return false;
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
      status: address.status ? 1 : 0,
    }, 'POST').then(function (res) {
      if (res.errno === 0) {
        if (that.data.isBuy === "0"){
          wx.navigateBack({
            delta: 1
          })
        }else{
          wx.navigateTo({
            url: '/pages/ucenter/address/address?back=1',
          })
        }
       
      }
    });

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
  bindRegionChange: function (e) {
    var region = e.detail.value
    var full_region = region[0] + ',' + region[1] + ',' + region[2]
    this.data.address.full_region = full_region
    this.setData({
      region: e.detail.value,
      address: this.data.address,
    })
  }
})