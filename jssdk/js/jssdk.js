$(function () {
	var config={};
<<<<<<< HEAD:demo/js/demo.js
	var now=Date.now()||new Date().getTime();
	var url='https://7c807484.ngrok.io/JsConfig.php?jsurl='+location.href.split('#')[0];
    sessionStorage.removeItem('serverId');
=======
	var ajaxUrl='http://f942ffed.ngrok.io/';
	var url=ajaxUrl+'JsConfig.php?jsurl='+location.href.split('#')[0];
	sessionStorage.removeItem('serverId');
>>>>>>> origin/master:jssdk/js/jssdk.js
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
		debug:false,
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
		};
		//拍照、本地选图
		document.querySelector('#chooseImage').addEventListener('touchstart', function() {
			var that = this;
			wx.chooseImage({
				count: 9,
				sizeType: ['compressed'],
				success: function(res) {
					var i=0,len=res.localIds.length;
					images.localId = res.localIds;
					$.each(res.localIds,function(index,item) {
						var li=$('<li class="weui-uploader__file"></li>');
						li.css('background-image','url('+item+')');
						$(that).parent().prev('.weui-uploader__files').append(li);
					});
				}
			});
		});
		// 上传图片
		document.querySelector('#uploadImage').addEventListener('touchstart', function() {
<<<<<<< HEAD:demo/js/demo.js
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
=======
			if (images.localId.length == 0) {
>>>>>>> origin/master:jssdk/js/jssdk.js
				chooseClass.uplenError();
				$('#dialog2').show();
				return;
			}
			var i = 0,length = images.localId.length;
			var serverId=JSON.parse(sessionStorage.getItem('serverId'));
			images.serverId = [];
			if(!serverId){
				serverId=JSON.stringify(images.localId);
				sessionStorage.setItem('serverId',serverId);
			}else {
				if(serverId.indexOf(images.localId[i]!=-1)){
					$("#dialog3").find('.weui_dialog_title').html('不能重复上传!');
					$('#dialog3').show();
					return;
				}
				serverId.push(images.localId[i]);
				sessionStorage.setItem('serverId',JSON.stringify(serverId));
			}
			// if (length > 1) {//限制单张图片上传
			// 	$('#dialog3').show();
			// 	chooseClass.uplenError();
			// 	images.localId = [];
			// 	return;
			// }
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
<<<<<<< HEAD:demo/js/demo.js
								url: 'https://7c807484.ngrok.io/demo/downImg.php',
=======
								url: ajaxUrl+'jssdk/downImg.php',
>>>>>>> origin/master:jssdk/js/jssdk.js
								type: 'GET',
								data:loadObj,
								timeout:10000,
								success: function(data) {
									$('#toast').show();
									setTimeout(function() {
										$('#toast').hide();
									}, 1000);
									console.log(JSON.stringify(data));
								},
								error: function() {
									$("#dialog1").find('.weui_dialog_title').html('上传到第三方服务器失败!');
									$('#dialog1').show();
									chooseClass.upFail();
								}
							});
						}
					},
					fail: function() {
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
	wx.error(function() {
		alert('连接错误');
	});
});