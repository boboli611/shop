//app.js
var qcloud = require('./vendor/wafer2-client-sdk/index')
var config = require('./config')
var userService = require('./services/user.js')

App({
    onLaunch: function () {
        qcloud.setLoginUrl(config.service.loginUrl)
        console.log("loginiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii")
        userService.login()
    }
})