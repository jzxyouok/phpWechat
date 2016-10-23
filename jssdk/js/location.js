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
            'openLocation',
            'getLocation'
        ]
    });
    wx.ready(function() {
        var map={};
        document.getElementById('openLocation').addEventListener('touchstart',function () {
            wx.openLocation({
                latitude: map.latitude,
                longitude: map.longitude,
                name: '安居宝科技园',
                address: '安居宝科技园南门',
                scale: 14,
                infoUrl: 'http://weixin.qq.com'
            });
        });
        document.getElementById('getLocation').addEventListener('touchstart',function () {
            wx.getLocation({
                type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                success: function (res) {
                    var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                    var speed = res.speed; // 速度，以米/每秒计
                    var accuracy = res.accuracy; // 位置精度
                    map.latitude=latitude;
                    map.longitude=longitude;
                    map.speed=speed;
                    map.accuracy=accuracy;
                    alert(JSON.stringify(map));
                },
                cancel: function () {
                    alert('用户拒绝授权获取地理位置');
                }
            });
        });
    });
    wx.error(function() {
        alert('连接错误');
    });
});
