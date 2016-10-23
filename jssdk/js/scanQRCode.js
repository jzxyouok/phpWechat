
$(function () {
    var config={};
    var ajaxUrl='https://a703919c.ngrok.io/';
    var url=ajaxUrl+'JsConfig.php?jsurl='+location.href.split('#')[0];
    sessionStorage.removeItem('serverId');
    $.ajax({
        url: url,
        type: 'GET',
        dataType:'json',
        timeout:1000,
        async:false,
        success:function(data){
            if (data.state=='scussed') {
                config=data.data;
                console.log(JSON.stringify(config));
            }else{
                alert('请求失败');
            }
        }
    });
    wx.config({
        debug:false,
        appId: config.appId,
        timestamp: config.timestamp,
        nonceStr: config.nonceStr,
        signature: config.signature,
        jsApiList: [
            'scanQRCode',
            'getLocation'
        ]
    });
    wx.ready(function() {
        document.getElementById('scanQRCode0').addEventListener('touchstart',function () {
            wx.scanQRCode();
        });
        document.getElementById('scanQRCode1').addEventListener('touchstart',function () {
            wx.scanQRCode({
                needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                success: function (res) {
                    var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果a
                    location.href=result;
                }
            });
        });
    });
    wx.error(function() {
        alert('连接错误');
    });
});
