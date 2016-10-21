$(function () {
    var config={};
    var ajaxUrl='https://a1380f70.ngrok.io/';
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
        debug:true,
        appId: config.appId,
        timestamp: config.timestamp,
        nonceStr: config.nonceStr,
        signature: config.signature,
        jsApiList: [
            'hideOptionMenu',
            'showOptionMenu',
            'closeWindow'
        ]
    });
    wx.ready(function() {
        document.getElementById('hideOptionMenu').addEventListener('touchstart',function () {
            wx.hideOptionMenu();
        });
        document.getElementById('showOptionMenu').addEventListener('touchstart',function () {
            wx.showOptionMenu();
        });
        document.getElementById('closeWindow').addEventListener('touchstart',function () {
            wx.closeWindow();
        });
    });
    wx.error(function() {
        alert('连接错误');
    });
});
