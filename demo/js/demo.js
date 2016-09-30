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
					images.serverId = [];
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
										'mediaIds': images.serverId,
										'token': JSON.parse(getCookie('config')).token
									};
									$.ajax({
										url: 'http://103.195.187.108:80/WechatApp/image/downloadImageFromWechat?param='+JSON.stringify(loadObj),
										type: 'GET',
										dataType: 'json',
										timeout: 1000,
										success: function(data) {
											$('#toast').show();
											setTimeout(function() {
												$('#toast').hide();
											}, 1000);
											//alert(JSON.stringify(data));
										},
										error: function() {
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
					upload();
				});
			});
			wx.error(function(res) {
				alert('连接错误');
			});