$(function () {
	var config={};
	var now=Date.now()||new Date().getTime();
	var url='https://7c807484.ngrok.io/JsConfig.php?jsurl='+location.href.split('#')[0];
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
		},
	});
	$('#dialog1').on('touchstart', '.weui_btn_dialog', function () {
		$('#dialog1').hide();
		document.querySelector('.weui_uploader_status_content').style.display="none";
	});
	$('#dialog2').on('touchstart', '.weui_btn_dialog', function () {
		$('#dialog2').hide();
	});
	$('#dialog3').on('touchstart', '.weui_btn_dialog', function () {
		$('#dialog3').hide();
	});
	wx.config({
		debug: false,
		appId: config.appId,
		timestamp: config.timestamp,
		nonceStr: config.nonceStr,
		signature: config.signature,
		jsApiList: [
			'chooseImage',
			'uploadImage'
		]
	});
	wx.ready(function() {
		var images = {
			localId: [],
			serverId: []
		};
		var chooseClass = {
			upFail: function() {
				var ImageParent = document.getElementById('chooseImage').parentNode;
				ImageParent.classList.add('weui_uploader_fail');
				document.querySelector('.weui_uploader_status_content').style.display = "block";
			},
			uplenError: function() {
				var ImageParent = document.getElementById('chooseImage').parentNode;
				if (ImageParent.classList.remove('weui_uploader_success')) {
					ImageParent.classList.remove('weui_uploader_success');
					ImageParent.classList.add('weui_uploader_input_wrp');
					ImageParent.style.backgroundImage = "";
					document.querySelector('.weui_btn.weui_btn_primary.weui_btn_disabled').classList.add('weui_btn_disabled');
				}
			}
		}

		function SetCookie(name, value) {
			var Days = 0.97; //此 cookie 将被保存2小时
			var exp = new Date();
			exp.setTime(exp.getTime() + Days * 2 * 60 * 60 * 1000);
			document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
		}

		function getCookie(name) {
			var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
			if (arr != null) return unescape(arr[2]);
			return null;
		}
		//拍照、本地选图
		document.querySelector('#chooseImage').addEventListener('touchstart', function() {
			var that = this;
			wx.chooseImage({
				count: 1,
				sizeType: ['compressed'],
				success: function(res) {
					images.localId = res.localIds;
					that.parentNode.style.backgroundImage = "url(" + images.localId[0] + ")";
					that.parentNode.style.backgroundSize = "cover";
					that.parentNode.classList.add('weui_uploader_success');
					that.parentNode.classList.remove('weui_uploader_input_wrp');
					document.querySelector('.weui_btn.weui_btn_primary.weui_btn_disabled').classList.remove('weui_btn_disabled');
				}
			});
		});
		// 上传图片
		document.querySelector('#uploadImage').addEventListener('touchstart', function() {
			var i = 0,length = images.localId.length;
            var serverId=sessionStorage.getItem('serverId');
			images.serverId = [];
            if(!serverId){
                serverId=JSON.stringify(images.localId);
                sessionStorage.setItem('serverId',serverId);
            }else {
                serverId=JSON.parse(serverId);
                serverId.indexOf(images.localId[0])==-1&&serverId.push(images.localId[0]);
                sessionStorage.setItem('serverId',JSON.stringify(serverId));
            }
			if (length == 0) {
				chooseClass.uplenError();
				$('#dialog2').show();
				return;
			}
			if (length > 1) {
				$('#dialog3').show();
				chooseClass.uplenError();
				images.localId = [];
				return;
			}
			function upload() {
				wx.uploadImage({
					localId: images.localId[i],
					success: function(res) {
						i++;
						images.serverId.push(res.serverId);
						if (i < length) {
							upload();
						} else {
							var loadObj = {
								'mediaIds': images.serverId
							};
							$.ajax({
								url: 'https://7c807484.ngrok.io/demo/downImg.php',
								type: 'GET',
								data:loadObj,
								timeout: 10000,
								success: function(data) {
									$('#toast').show();
									setTimeout(function() {
										$('#toast').hide();
									}, 1000);
									//alert(JSON.stringify(data));
								},
								error: function() {
									$('#dialog1 .weui_dialog_title').html('上传到第三方服务器失败!');
									$('#dialog1').show();
									chooseClass.upFail();
								}
							});
						}
					},
					fail: function(res) {
						$('#dialog1').show();
						chooseClass.upFail();
					}
				});
			}
			if(JSON.parse(sessionStorage.getItem('serverId')).indexOf(images.localId[0])!=-1){
                alert(sessionStorage.getItem('serverId'));
            }else {
                upload();
            }

		});
	});
	wx.error(function(res) {
		alert('连接错误');
	});
});