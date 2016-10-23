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
            'startRecord',
            'stopRecord',
            'playVoice',
            'translateVoice'
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
        document.getElementById('translateVoice').addEventListener('touchstart',function () {
            wx.translateVoice({
                localId: voice.localId, // 需要识别的音频的本地Id，由录音相关接口获得
                isShowProgressTips: 1, // 默认为1，显示进度提示
                complete: function (res) {
                    if (res.hasOwnProperty('translateResult')) {
                        alert('识别结果：' + res.translateResult);
                    } else {
                        alert('无法识别');
                    }
                }
            });
        });
    });
    wx.error(function() {
        alert('连接错误');
    });
});
