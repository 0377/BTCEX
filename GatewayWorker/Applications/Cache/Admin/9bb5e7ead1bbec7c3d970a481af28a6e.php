<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>后台 | 管理中心 - ADMIN EX</title>
	<!-- Loading Bootstrap -->
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/base.css" media="all">
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/common.css" media="all">
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/module.css">
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/default_color.css" media="all">
	<script type="text/javascript" src="/Public/Admin/js/jquery.min.js"></script>
	<script type="text/javascript" src="/Public/layer/layer.js"></script>
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/flat-ui.css">
	<script src="/Public/Admin/js/flat-ui.min.js"></script>
	<script src="/Public/Admin/js/application.js"></script>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="navbar-header">
		<a class="navbar-brand" style="width:200px;text-align:center;background-color:#3c434d;" href="<?php echo U('Index/index');?>">
			<img src="/Public/Admin/rh_img/logo_text.png" />
		</a>
	</div>
	<div class="navbar-collapse collapse">
		<ul class="nav navbar-nav">
			<!-- 主导航 -->
			<?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li <?php if(($menu["class"]) == "current"): ?>class="active"<?php endif; ?> > 
					<a href="<?php echo (U($menu["url"])); ?>">
						<?php if(empty($menu["ico_name"])): ?><span class="glyphicon glyphicon-star" aria-hidden="true"></span>
						<?php else: ?>
							<span class="glyphicon glyphicon-<?php echo ($menu["ico_name"]); ?>" aria-hidden="true"></span><?php endif; ?>
						<?php echo ($menu["title"]); ?> 
					</a>
				</li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
		<ul class="nav navbar-nav navbar-rights" style="margin-right:10px;">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					 <?php echo session('admin_username');?><b class="caret"></b>
				</a>
				<ul class="dropdown-menu">
					<li>
						<a href="<?php echo U('User/setpwd');?>">
							<span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> 修改密码 
						</a>
					</li>
					<li class="center">
						<a href="javascript:void(0);" onclick="lockscreen()">
							<span class="glyphicon glyphicon-lock" aria-hidden="true"></span> 锁屏休息 
						</a>
					</li>
					<li class="dividers"></li>
					<li>
						<a href="<?php echo U('Login/loginout');?>">
							<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> 退出后台 
						</a>
					</li>
				</ul>
			</li>
			<li>
				<a href="<?php echo U('Tools/delcache');?>" class="dropdown-toggle" title="清除缓存">
					<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
				</a>
			</li>
			<li>
				<a class="dropdown-toggle" title="打开前台" href="/" target="_blank">
					<span class="glyphicon glyphicon-share" aria-hidden="true"></span>
				</a>
			</li>
		</ul>
	</div>
</div>
<!-- 边栏 -->
<div class="sidebar">
	<!-- 子导航 -->
	
		<div id="subnav" class="subnav" style="max-height: 94%;overflow-x: hidden;overflow-y: auto;">
			<?php if(!empty($_extra_menu)): ?> <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
			<?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
				<?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
					<ul class="side-sub-menu">
						<?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
								<a class="item" href="<?php echo (U($menu["url"])); ?>">
									<?php if(empty($menu["ico_name"])): ?><span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span>
										<?php else: ?>
										<span class="glyphicon glyphicon-<?php echo ($menu["ico_name"]); ?>" aria-hidden="true"></span><?php endif; ?>
									<?php echo ($menu["title"]); ?>
								</a>
							</li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul><?php endif; ?>
				<!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	
	<!-- /子导航 -->
</div>
<!-- /边栏 -->
<?php if(($versionUp) == "1"): ?><script type="text/javascript" charset="utf-8">
		/**顶部警告栏*/
		var top_alert = $('#top-alerta');
		top_alert.find('.close').on('click', function () {
			top_alert.removeClass('block').slideUp(200);
			// content.animate({paddingTop:'-=55'},200);
		});
	</script><?php endif; ?>
<style>
	.hoh td.item-label,.hoh td.item-note{
		height:80px;line-height:80px;
	}
	.gezibg {
		padding:5px;width:168px;background:url('/Public/Admin/rh_img/imgbg.png');
	}
</style>

<div id="main-content">
	<div id="top-alert" class="fixed alert alert-error" style="display: none;">
		<button class="close fixed" style="margin-top: 4px;">&times;</button>
		<div class="alert-content">警告内容</div>
	</div>
	<div id="main" class="main">
		<div class="main-title-h">
			<span class="h1-title">基本配置</span>
		</div>
		<div class="tab-wrap">
			<div class="tab-content">
				<form id="form" action="<?php echo U('Config/edit');?>" method="post" class="form-horizontal" enctype="multipart/form-data">
					<div id="tab" class="tab-pane in tab">
						<div class="form-item cf">
							<table>
<!--								<tr class="controls">
									<td class="item-label">网站名称 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="web_name" value="<?php echo ($data['web_name']); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
                                <tr class="controls" style="display: none;">
									<td class="item-label">英文名称 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="en_web_name" value="<?php echo ($data['en_web_name']); ?>">
									</td>
									<td class="item-note"></td>
								</tr>-->
								<tr class="controls">
									<td class="item-label">网站标题 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="web_title" value="<?php echo ($data['web_title']); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
<!--                                <tr class="controls" style="display: none;">
									<td class="item-label">英文标题 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="en_web_title" value="<?php echo ($data['en_web_title']); ?>">
									</td>
									<td class="item-note"></td>
								</tr>-->
								
								<tr class="controls">
									<td class="item-label">WAP页面标题 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="wap_title" value="<?php echo ($data['wap_title']); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
								
								<tr class="controls hoh">
									<td class="item-label">网站logo :</td>
									<td>
										<div id="addpicContainer" class="gezibg">
											<?php if(!empty($data["web_logo"])): ?><!--没有图片显示默认图片-->
												<img id="up_img" onclick="getElementById('inputfile').click()" style="cursor:pointer;max-width:400px;" title="点击添加图片" alt="点击添加图片" src="/Public/Home/rh_img/<?php echo ($data["web_logo"]); ?>">
											<?php else: ?>
												<!--没有图片显示默认图片-->
												<img id="up_img" onclick="getElementById('inputfile').click()" style="cursor:pointer;max-height:62px;" title="点击添加图片" alt="点击添加图片" src="/Public/Admin/images/addimg.png"><?php endif; ?>
											<input type="hidden" id="img" name="web_logo" value="<?php echo ($data["web_logo"]); ?>">
											<input type="file" id="inputfile" style="height:0;width:0;z-index: -1; position: absolute;left: 10px;top: 5px;" value=""/>
										</div>
									</td>
									<td class="item-note">168px*62px</td>
								</tr>
								<tr class="controls hoh">
									<td class="item-label">网站副logo :</td>
									<td>
										<div id="addpicContainer" class="gezibg">
											<?php if(!empty($data["web_logo_deputy"])): ?><img id="up_img_deputy" onclick="getElementById('inputfile_deputy').click()" style="cursor:pointer;max-height:62px;" title="点击添加图片" alt="点击添加图片" src="/Public/Home/rh_img/<?php echo ($data["web_logo_deputy"]); ?>">
											<?php else: ?>
												<!--没有图片显示默认图片-->
												<img id="up_img_deputy" onclick="getElementById('inputfile_deputy').click()" style="cursor:pointer;max-height:62px;" title="点击添加图片" alt="点击添加图片" src="/Public/Admin/images/addimg.png"><?php endif; ?>
											<input type="hidden" id="img_deputy" name="web_logo_deputy" value="<?php echo ($data["web_logo_deputy"]); ?>">
											<input type="file" id="inputfile_deputy" style="height:0;width:0;z-index: -1; position: absolute;left: 10px;top: 5px;" value=""/>
										</div>
									</td>
									<td class="item-note">168px*62px</td>
								</tr>
								<tr class="controls hoh">
									<td class="item-label">网站页脚logo :</td>
									<td>
										<div id="addpicContainer" class="gezibg">
											<?php if(!empty($data["footer_logo"])): ?><img id="up_img_footer" onclick="getElementById('inputfile_footer').click()" style="cursor:pointer;max-height:62px;" title="点击添加图片" alt="点击添加图片" src="/Public/Home/rh_img/<?php echo ($data["footer_logo"]); ?>">
											<?php else: ?>
												<!--没有图片显示默认图片-->
												<img id="up_img_footer" onclick="getElementById('inputfile_footer').click()" style="cursor:pointer;max-height:62px;" title="点击添加图片" alt="点击添加图片" src="/Public/Admin/images/addimg.png"><?php endif; ?>
											<input type="hidden" id="img_footer" name="footer_logo" value="<?php echo ($data["footer_logo"]); ?>">
											<input type="file" id="inputfile_footer" style="height:0;width:0;z-index: -1; position: absolute;left: 10px;top: 5px;" value=""/>
										</div>
									</td>
									<td class="item-note">121px*45px</td>
								</tr>
								
								<tr class="controls">
									<td class="item-label">网站关键字 :</td>
									<td>
										<textarea name="web_keywords" class="form-control input-10x" style="margin-bottom:10px;"><?php echo ($data['web_keywords']); ?></textarea>
									</td>
									<td class="item-note"></td>
								</tr>
<!--                                <tr class="controls" style="display: none;">
									<td class="item-label">英文关键字 :</td>
									<td>
										<textarea name="en_web_keywords" class="form-control  input-10x" style="margin-bottom:10px;"><?php echo ($data['en_web_keywords']); ?></textarea>
									</td>
									<td class="item-note"></td>
								</tr>-->

								<tr class="controls">
									<td class="item-label">网站描述 :</td>
									<td>
										<textarea name="web_description" class="form-control input-10x" style="margin-bottom:10px;"><?php echo ($data['web_description']); ?></textarea>
									</td>
									<td class="item-note"></td>
								</tr>
<!--                                <tr class="controls" style="display: none;">
									<td class="item-label">英文描述 :</td>
									<td>
										<textarea name="en_web_description" class="form-control  input-10x" style="margin-bottom:10px;"><?php echo ($data['en_web_description']); ?></textarea>
									</td>
									<td class="item-note"></td>
								</tr>-->

								<tr class="controls" style="border-bottom:1px dashed #d0d0d0;">
									<td class="item-label">网站页脚版权信息 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="web_footer" value="<?php echo ($data['web_footer']); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
								
								<tr class="controls hoh">
									<td class="item-label">帮助中心logo :</td>
									<td>
										<div id="addpicContainer" class="gezibg">
											<?php if(!empty($data["help_logo"])): ?><img id="up_img_help" onclick="getElementById('inputfile_help').click()" style="cursor:pointer;max-height:62px;" title="点击添加图片" alt="点击添加图片" src="/Public/Home/rh_img/<?php echo ($data["help_logo"]); ?>">
											<?php else: ?>
												<!--没有图片显示默认图片-->
												<img id="up_img_help" onclick="getElementById('inputfile_help').click()" style="cursor:pointer;max-height:62px;" title="点击添加图片" alt="点击添加图片" src="/Public/Admin/images/addimg.png"><?php endif; ?>
											<input type="hidden" id="img_help" name="help_logo" value="<?php echo ($data["help_logo"]); ?>">
											<input type="file" id="inputfile_help" style="height:0;width:0;z-index: -1; position: absolute;left: 10px;top: 5px;" value=""/>
										</div>
									</td>
									<td class="item-note">168px*62px</td>
								</tr>
								
								<tr class="controls">
									<td class="item-label">帮助中心页脚版权信息 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="help_footer" value="<?php echo ($data['help_footer']); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls">
									<td class="item-label">帮助中心文章结尾标识 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="web_identification" value="<?php echo ($data['web_identification']); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
								
								<tr class="controls hoh" style="border-top:1px dashed #d0d0d0;">
									<td class="item-label">统计代码:</td>
									<td>
										<textarea name="web_cnzz" class="form-control input-10x"><?php echo ($data['web_cnzz']); ?></textarea>
									</td>
									<td class="item-note">第三方：<a href="https://www.umeng.com" target="_blank">友盟CNZZ网站统计</a></td>
								</tr>
								<tr class="controls">
									<td class="item-label">禁止访问原因 [中文] :</td>
									<td>
										<textarea name="web_close_cause" class="form-control  input-10x" style="margin-bottom:10px;"><?php echo ($data['web_close_cause']); ?></textarea>
									</td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls">
									<td class="item-label">禁止访问原因 [英文] :</td>
									<td>
										<textarea name="web_close_cause_en" class="form-control input-10x" style="margin-bottom:10px;"><?php echo ($data['web_close_cause_en']); ?></textarea>
									</td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls">
									<td class="item-label">网站状态 :</td>
									<td>
										<select name="web_close" class="form-control  input-10x">
											<option value="1" <?php if(($data['web_close']) == "1"): ?>selected<?php endif; ?>>正常</option>
											<option value="0" <?php if(($data['web_close']) == "0"): ?>selected<?php endif; ?>>禁止访问</option>
										</select>
									</td>
									<td class="item-note"></td>
								</tr>
								
								<!-- <tr class="controls">
									<td class="item-label">备案号 :</td>
									<td>
										<input type="text" class="form-control  input-10x" name="web_icp" value="<?php echo ($data['web_icp']); ?>">
									</td>
									<td class="item-note"></td>
								</tr> -->
<!--								<tr class="controls">
									<td class="item-label">客服QQ :</td>
									<td>
										<input name="contact_qq" class="form-control  input-10x" style="margin-bottom:10px;" value="<?php echo ($data['contact_qq']); ?>"></input>
									</td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls">
									<td class="item-label">客服QQ群 :</td>
									<td>
										<input name="contact_qqun" class="form-control  input-10x" style="margin-bottom:10px;" value="<?php echo ($data['contact_qqun']); ?>"></input>
									</td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls">
									<td class="item-label">客服微信 :</td>
									<td>
										<input name="contact_wx" class="form-control  input-10x" style="margin-bottom:10px;" value="<?php echo ($data['contact_wx']); ?>"></input>
									</td>
									<td class="item-note"></td>
								</tr>-->
<!--								<tr class="controls">
									<td class="item-label">微信二维码 :</td>
									<td>
										<div id="addpicContainer">
											 利用multiple="multiple"属性实现添加多图功能 
											 position: absolute;left: 10px;top: 5px;只针对本用例将input隐至图片底下。
											 height:0;width:0;z-index: -1;是为了隐藏input，因为Chrome下不能使用display:none，否则无法添加文件 
											 onclick="getElementById('inputfile').click()" 点击图片时则点击添加文件按钮 
											<?php if(!empty($data["wxcode"])): ?><img id="up_imgwx" onclick="getElementById('inputfilewx').click()" style="cursor:pointer;max-width:400px;" title="点击添加图片" alt="点击添加图片" src="/Upload/public/<?php echo ($data["wxcode"]); ?>">
												<?php else: ?>
												<img id="up_imgwx" onclick="getElementById('inputfilewx').click()" style="cursor:pointer;max-width:400px;" title="点击添加图片" alt="点击添加图片" src="/Public/Admin/images/addimg.png"><?php endif; ?>
											<input type="hidden" id="imgwx" name="wxcode" value="<?php echo ($data["wxcode"]); ?>">
											<input type="file" id="inputfilewx" style="height:0;width:0;z-index: -1; position: absolute;left: 10px;top: 5px;" value=""/>
										</div>
									</td>
									<td class="item-note">微信二维码  400PX*400PX</td>
								</tr>-->
								<tr class="controls">
									<td class="item-label"></td>
									<td>
										<div class="form-item cf">
											<button class="btn submit-btn ajax-post" target-form="form-horizontal" id="submit" type="submit">提交</button>
											<a class="btn btn-return" href="<?php echo ($_SERVER['HTTP_REFERER']); ?>">返 回</a>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</form>
				<script type="text/javascript">
					//提交表单
					$('#submit').click(function () {
						$('#form').submit();
					});
				</script>
			</div>
		</div>
	</div>
</div>

<script charset="utf-8" src="/Public/kindeditorv4/kindeditor-all-min.js"></script>
<script charset="utf-8" src="/Public/kindeditorv4//lang/zh-CN.js"></script>
<script type="text/javascript">
	/** 主LOGO上传 **/
	$(document).ready(function () {
		//响应文件添加成功事件
		$("#inputfile").change(function () {
			
			if (<?php echo APP_DEMO;?>) {
				return alert('测试站暂时不能修改！');
			}
			
			//创建FormData对象
			var data = new FormData();
			//为FormData对象添加数据
			$.each($('#inputfile')[0].files, function (i, file) {
				data.append('upload_file' + i, file);
			});

			//发送数据
			$.ajax({
				url: '/Admin/Config/image',
				type: 'POST',
				data: data,
				cache: false,
				contentType: false,		//不可缺参数
				processData: false,		//不可缺参数
				success: function (data) {
					if (data) {
						$('#up_img').attr("src", '/Upload/public/' + $.trim(data));
						$('#img').val($.trim(data));
						$('#up_img').show();
					}
				},
				error: function () {
					alert('上传出错');
					$(".loading").hide();	//加载失败移除加载图片
				}
			});

		});
	});
	
	/** 副LOGO上传 **/
	$(document).ready(function () {
		//响应文件添加成功事件
		$("#inputfile_deputy").change(function () {
			
			if (<?php echo APP_DEMO;?>) {
				return alert('测试站暂时不能修改！');
			}
			
			//创建FormData对象
			var data = new FormData();
			//为FormData对象添加数据
			$.each($('#inputfile_deputy')[0].files, function (i, file) {
				data.append('upload_file' + i, file);
			});

			//发送数据
			$.ajax({
				url: '/Admin/Config/image',
				type: 'POST',
				data: data,
				cache: false,
				contentType: false,		//不可缺参数
				processData: false,		//不可缺参数
				success: function (data) {
					if (data) {
						$('#up_img_deputy').attr("src", '/Upload/public/' + $.trim(data));
						$('#img_deputy').val($.trim(data));
						$('#up_img_deputy').show();
					}
				},
				error: function () {
					alert('上传出错');
					$(".loading").hide();	//加载失败移除加载图片
				}
			});

		});
	});
	
	/** 页脚LOGO上传 **/
	$(document).ready(function () {
		//响应文件添加成功事件
		$("#inputfile_footer").change(function () {
			
			if (<?php echo APP_DEMO;?>) {
				return alert('测试站暂时不能修改！');
			}
			
			//创建FormData对象
			var data = new FormData();
			//为FormData对象添加数据
			$.each($('#inputfile_footer')[0].files, function (i, file) {
				data.append('upload_file' + i, file);
			});

			//发送数据
			$.ajax({
				url: '/Admin/Config/image',
				type: 'POST',
				data: data,
				cache: false,
				contentType: false,		//不可缺参数
				processData: false,		//不可缺参数
				success: function (data) {
					if (data) {
						$('#up_img_footer').attr("src", '/Upload/public/' + $.trim(data));
						$('#img_footer').val($.trim(data));
						$('#up_img_footer').show();
					}
				},
				error: function () {
					alert('上传出错');
					$(".loading").hide();	//加载失败移除加载图片
				}
			});

		});
	});
	
	/** 帮助中心LOGO上传 **/
	$(document).ready(function () {
		//响应文件添加成功事件
		$("#inputfile_help").change(function () {
			
			if (<?php echo APP_DEMO;?>) {
				return alert('测试站暂时不能修改！');
			}
			
			//创建FormData对象
			var data = new FormData();
			//为FormData对象添加数据
			$.each($('#inputfile_help')[0].files, function (i, file) {
				data.append('upload_file' + i, file);
			});

			//发送数据
			$.ajax({
				url: '/Admin/Config/image',
				type: 'POST',
				data: data,
				cache: false,
				contentType: false,		//不可缺参数
				processData: false,		//不可缺参数
				success: function (data) {
					if (data) {
						$('#up_img_help').attr("src", '/Upload/public/' + $.trim(data));
						$('#img_help').val($.trim(data));
						$('#up_img_help').show();
					}
				},
				error: function () {
					alert('上传出错');
					$(".loading").hide();	//加载失败移除加载图片
				}
			});

		});
	});
</script>

<script type="text/javascript">
    // KindEditor.ready(function(K) {
    //     window.editor = K.create('#web_reg');
    // });
	var editor;
	KindEditor.ready(function (K) {
		editor = K.create('textarea[name="web_reg"]', {
			width: '500px',
			height: '100px',
			allowImageUpload: true,
			items: [
				'source', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
				'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
				'insertunorderedlist', '|', 'emoticons', 'link', 'fullscreen'],
			afterBlur: function () {

                editor.sync();
			}
		});
		editors = K.create('textarea[name="en_web_reg"]', {
			width: '500px',
			height: '100px',
			allowPreviewEmoticons: false,
			allowImageUpload: true,
			items: [
				'source', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
				'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
				'insertunorderedlist', '|', 'emoticons', 'link', 'fullscreen'],
			afterBlur: function () {
				this.sync();
			}
		});
	});
</script>
<script type="text/javascript" src="/Public/Admin/js/common.js"></script>
<script type="text/javascript">
	+function(){
		//$("select").select2({dropdownCssClass: 'dropdown-inverse'});//下拉条样式
		layer.config({
			extend: 'extend/layer.ext.js'
		});

		var $window = $(window), $subnav = $("#subnav"), url;
		$window.resize(function(){
			//$("#main").css("min-height", $window.height() - 90);
		}).resize();

		/* 左边菜单高亮 */
		url = window.location.pathname + window.location.search;

		url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
		$subnav.find("a[href='" + url + "']").parent().addClass("current");

		/* 左边菜单显示收起 */
		$("#subnav").on("click", "h3", function(){
			var $this = $(this);
			$this.find(".icon").toggleClass("icon-fold");
			$this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
			prev("h3").find("i").addClass("icon-fold").end().end().hide();
		});

		$("#subnav h3 a").click(function(e){e.stopPropagation()});

		/* 头部管理员菜单 */
		$(".user-bar").mouseenter(function(){
			var userMenu = $(this).children(".user-menu ");
			userMenu.removeClass("hidden");
			clearTimeout(userMenu.data("timeout"));
		}).mouseleave(function(){
			var userMenu = $(this).children(".user-menu");
			userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
			userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
		});

		/* 表单获取焦点变色 */
		$("form").on("focus", "input", function(){
			$(this).addClass('focus');
		}).on("blur","input",function(){
			$(this).removeClass('focus');
		});
		$("form").on("focus", "textarea", function(){
			$(this).closest('label').addClass('focus');
		}).on("blur","textarea",function(){
			$(this).closest('label').removeClass('focus');
		});

		// 导航栏超出窗口高度后的模拟滚动条
		var sHeight = $(".sidebar").height();
		var subHeight  = $(".subnav").height();
		var diff = subHeight - sHeight; //250
		var sub = $(".subnav");
		if(diff > 0){
//			$(window).mousewheel(function(event, delta){
//				if(delta>0){
//					if(parseInt(sub.css('marginTop'))>-10){
//						sub.css('marginTop','0px');
//					}else{
//						sub.css('marginTop','+='+10);
//					}
//				}else{
//					if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
//						sub.css('marginTop','-'+(diff-10));
//					}else{
//						sub.css('marginTop','-='+10);
//					}
//				}
//			});
		}
	}();

	//导航高亮
	function highlight_subnav(url){
		$('.side-sub-menu').find('a[href="'+url+'"]').closest('li').addClass('current');
	}

	function lockscreen(){
		layer.prompt({
			title: '输入一个锁屏密码',
			formType: 1,
			btn: ['锁屏','取消'] //按钮
		}, function(pass){
			if(!pass){
				layer.msg('需要输入一个密码!');
			}else{
				$.post("<?php echo U('Login/lockScreen');?>",{pass:pass},function(data){
					layer.msg(data.info);
					layer.close();
					if(data.status){
						window.location.href = "<?php echo U('Login/lockScreen');?>";
					}
				},'json');
			}
		});
	}
</script>
<div style="display:none;">

</div>
</body>
</html>