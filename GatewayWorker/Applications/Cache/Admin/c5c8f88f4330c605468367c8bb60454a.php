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
<script type="text/javascript" src="/Public/layer/laydate/laydate.js"></script>
<div id="main-content">
	<div id="top-alert" class="fixed alert alert-error" style="display: none;">
		<button class="close fixed" style="margin-top: 4px;">&times;</button>
		<div class="alert-content">警告内容</div>
	</div>
	<div id="main" class="main">
		<div class="main-title-h">
			<span class="h1-title"><a href="<?php echo U('User/index');?>">用户管理</a> &gt;&gt;</span>
			<span class="h1-title"><?php if(empty($data)): ?>添加用户<?php else: ?>编辑用户<?php endif; ?></span>
		</div>
		<div class="tab-wrap">
			<div class="tab-content">
				<form id="form" action="<?php echo U('User/edit');?>" method="post" class="form-horizontal">
					<div id="tab" class="tab-pane in tab">
						<div class="form-item cf">
							<table>
								<!-- <tr class="controls">
									<td class="item-label">国家区域 :</td>
									<td>
										<select id="area" name="area_id" class="form-control input-10x" placeholder="<?php echo L('select_area');?>">
											<option value="0">选择国家区域</option>

											<?php if(is_array($areas)): $i = 0; $__LIST__ = $areas;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"  <?php if(($data["area_id"]) == $vo['id']): ?>selected<?php endif; ?> >
													<?php echo ($vo['name_zh']); ?> (<?php echo ($vo['name_en']); ?>)
												</option><?php endforeach; endif; else: echo "" ;endif; ?>


										</select>
									</td>
									<td class="item-note"></td>
								</tr> -->
								<tr class="controls">
									<td class="item-label">用户名 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="username" value="<?php echo ($data["username"]); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls">
									<td class="item-label">英文昵称 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="enname" value="<?php echo ($data["enname"]); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls">
									<td class="item-label">用户密码 :</td>
									<td><input type="text" class="form-control input-10x" name="password" value=""></td>
									<td class="item-note">留空不更新</td>
								</tr>
								<tr class="controls">
									<td class="item-label">交易密码 :</td>
									<td><input type="text" class="form-control input-10x" name="paypassword" value="">
									</td>
									<td class="item-note">留空不更新</td>
								</tr>
								<tr class="controls">
									<td class="item-label">手机 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="mobile" value="<?php echo ($data["mobile"]); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls">
									<td class="item-label">手机认证时间 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="mobiletime" value="<?php echo (addtime($data['mobiletime'])); ?>" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})">
									</td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls" >
									<td class="item-label">一代ID:</td>
									<td>
										<input type="text" class="form-control input-10x" name="invit_1" value="<?php echo ($data["invit_1"]); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls" >
									<td class="item-label">二代ID :</td>
									<td>
										<input type="text" class="form-control input-10x" name="invit_2" value="<?php echo ($data["invit_2"]); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls" >
									<td class="item-label">三代ID :</td>
									<td>
										<input type="text" class="form-control input-10x" name="invit_3" value="<?php echo ($data["invit_3"]); ?>">
									</td>
									<td class="item-note"></td>
								</tr>
<!--								<tr class="controls">
									<td class="item-label">密保问题 :</td>
									<td>
										<select id="mibao_question" name="mibao_question" class="form-control input-10x" style="">
		                                <option value="">请选择密保问题</option>
		                                <option value="你父亲的姓名"
		                                	<?php if(($data["mibao_question"]) == "你父亲的姓名"): ?>selected<?php endif; ?>
		                                >你父亲的姓名</option>
		                                <option value="你母亲的姓名"
											<?php if(($data["mibao_question"]) == "你母亲的姓名"): ?>selected<?php endif; ?>
		                                >你母亲的姓名</option>
		                                <option value="你爱人的姓名"
											<?php if(($data["mibao_question"]) == "你爱人的姓名"): ?>selected<?php endif; ?>
		                                >你爱人的姓名</option>
		                                <option value="你的出生日期"
		                                	<?php if(($data["mibao_question"]) == "你的出生日期"): ?>selected<?php endif; ?>

		                                >你的出生日期</option>
		                                <option value="你父亲的出生日期"
											<?php if(($data["mibao_question"]) == "你父亲的出生日期"): ?>selected<?php endif; ?>
		                                >你父亲的出生日期</option>
		                                <option value="你母亲的出生日期"
		                                	<?php if(($data["mibao_question"]) == "你母亲的出生日期"): ?>selected<?php endif; ?>

		                                >你母亲的出生日期</option>
		                                <option value="你爱人的出生日期"
											<?php if(($data["mibao_question"]) == "你爱人的出生日期"): ?>selected<?php endif; ?>
		                                >你爱人的出生日期</option>
		                            </select>
									</td>
									<td class="item-note">不选不更新</td>
								</tr>
								<tr class="controls">
									<td class="item-label">密保问题答案 :</td>
									<td>
										<input type="text" class="form-control input-10x" name="mibao_answer" value="<?php echo ($data["mibao_answer"]); ?>">
									</td>
									<td class="item-note">留空不更新</td>
								</tr>-->

								<tr class="controls">
									<td class="item-label">推广 :</td>
									<td><select name="is_generalize" class="form-control input-10x">
										<option value="1"
										<?php if(($data["is_generalize"]) == "1"): ?>selected<?php endif; ?>
										>一代</option>
										<option value="1"
										<?php if(($data["is_generalize"]) == "3"): ?>selected<?php endif; ?>
										>三代</option>
										<option value="0"
										<?php if(($data["is_generalize"]) == "0"): ?>selected<?php endif; ?>
										>禁用</option>
									</select></td>
									<td class="item-note"></td>
								</tr>
								<tr class="controls">
									<td class="item-label">手续费分组 :</td>
									<td>
										<select name="lv" class="form-control input-10x">
											<option value="0"
											<?php if(($data["lv"]) == "0"): ?>selected<?php endif; ?>
											>普通用户</option>
											<option value="1"
											<?php if(($data["lv"]) == "1"): ?>selected<?php endif; ?>
											>做市商</option>
										</select>
									</td>
									<td class="item-note">普通用户按照市场设置手续费收取,做市商手续费为0</td>
								</tr>
								
								<tr class="controls">
									<td class="item-label">做市商后台 :</td>
									<td>
										<select name="backstage" class="form-control input-10x">
											<option value="0"
											<?php if(($data["backstage"]) == "0"): ?>selected<?php endif; ?>
											>不开放</option>
											<option value="1"
											<?php if(($data["backstage"]) == "1"): ?>selected<?php endif; ?>
											>开放C2C后台</option>
											<option value="2"
											<?php if(($data["backstage"]) == "2"): ?>selected<?php endif; ?>
											>开放超级后台</option>
										</select>
									</td>
									<td class="item-note">访问入口在前台个人中心</td>
								</tr>
								
								<tr class="controls">
									<td class="item-label">状态 :</td>
									<td><select name="status" class="form-control input-10x">
										<option value="1"
										<?php if(($data["status"]) == "1"): ?>selected<?php endif; ?>
										>正常</option>
										<option value="0"
										<?php if(($data["status"]) == "0"): ?>selected<?php endif; ?>
										>冻结</option>
									</select></td>
									<td class="item-note"></td>
								</tr>

								<tr class="controls">
									<td class="item-label"></td>
									<td>
										<div class="form-item cf">
											<button class="btn submit-btn ajax-post" id="submit" type="submit" target-form="form-horizontal">提交</button>
											<a class="btn btn-return" href="<?php echo ($_SERVER['HTTP_REFERER']); ?>">返 回</a>
											<?php if(!empty($data["id"])): ?><input type="hidden" name="id" value="<?php echo ($data["id"]); ?>"/><?php endif; ?>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	//提交表单
	$('#submit').click(function () {
		$('#form').submit();
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

	<script type="text/javascript" charset="utf-8">
		//导航高亮
		highlight_subnav("<?php echo U('User/index');?>");
	</script>