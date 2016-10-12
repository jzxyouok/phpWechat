$(function () {
    var config={};
    var ajaxUrl='http://2eef9fb8.ngrok.io/';
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
            'startRecord',
            'stopRecord',
            'playVoice'
        ]
    });
    wx.ready(function() {
        var voice = {
            localId: '',
            serverId: ''
        };
        document.getElementById('startRecord').addEventListener('touchstart',function () {
            wx.startRecord({
                cancel: function () {
                    alert('用户拒绝授权录音');
                }
            });
        });
        document.getElementById('stopRecord').addEventListener('touchstart',function () {
            wx.stopRecord({
                success: function (res) {
                    voice.localId = res.localId;
                },
                fail: function (res) {
                    alert(JSON.stringify(res));
                }
            });
        });
        wx.onVoiceRecordEnd({
            complete: function (res) {
                voice.localId = res.localId;
                alert('录音时间已超过一分钟');
            }
        });
        document.getElementById('playVoice').addEventListener('touchstart',function () {
            if (voice.localId == '') {
                alert('请先使用 startRecord 接口录制一段声音');
                return;
            }
            wx.playVoice({
                localId: voice.localId
            });
        });
    });
    wx.error(function() {
        alert('连接错误');
    });
});
