$(function () {
    var config={};
    var ajaxUrl='http://f942ffed.ngrok.io/';
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
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'onMenuShareQZone'
        ]
    });
    wx.ready(function() {
        document.getElementById('ShareTimeline').addEventListener('touchstart',function () {
            wx.onMenuShareTimeline({
                title: '互联网之子',
                desc: '在长大的过程中，我才慢慢发现，我身边的所有事，别人跟我说的所有事，那些所谓本来如此，注定如此的事，它们其实没有非得如此，事情是可以改变的。更重要的是，有些事既然错了，那就该做出改变。',
                link: 'http://movie.douban.com/subject/25785114/',
                imgUrl: 'http://demo.open.weixin.qq.com/jssdk/images/p2166127561.jpg',
                trigger: function (res) {
                    alert('用户点击发送给朋友');
                },
                success: function (res) {
                    alert('已分享');
                },
                cancel: function (res) {
                    alert('已取消');
                },
                fail: function (res) {
                    alert(JSON.stringify(res));
                }
            });
        })
    });
    wx.error(function() {
        alert('连接错误');
    });
});