var search = function (word) {
  wx.navigateTo({
    url: '../itemGoods/itemGoods?word=' + word
  })
}

module.exports = {
  search: search,
};