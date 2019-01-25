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
<div id="main-content">
	<div id="top-alert" class="fixed alert alert-error" style="display: none;">
		<button class="close fixed" style="margin-top: 4px;">&times;</button>
		<div class="alert-content">警告内容</div>
	</div>
	<div id="main" class="main">
		<div class="main-title-h">
			<span class="h1-title">用户管理</span>
		</div>
		<div class="cf">
			<div class="fl">
				<button class="btn ajax-post confirm btn-primary" url="<?php echo U('User/status',array('type'=>'idauth'));?>" target-form="ids" style="background-color: #1eb544">通过认证</button>
				<button class="btn ajax-post confirm btn-danger" url="<?php echo U('User/status',array('type'=>'notidauth'));?>" target-form="ids">取消认证</button>
				<span style="margin: 0 10px;"></span>
				<a class="btn btn-success  " href="<?php echo U('User/createUser');?>">新 增</a>
				<button class="ajax-post btn  btn-info " url="<?php echo U('User/status',array('type'=>'resume'));?>" target-form="ids">
				启 用</button>
				<button class="ajax-post btn  btn-warning  ajax-post" url="<?php echo U('User/status',array('type'=>'forbid'));?>" target-form="ids">
				禁 用</button>
				<button class="btn ajax-post confirm btn-danger " url="<?php echo U('User/status',array('type'=>'del'));?>" target-form="ids">
				删 除</button>
				<button class="btn btn-success" url="<?php echo U('Finance/userExcel');?>" target-form="ids" id="submit" type="submit">导出选中</button>
				
				<span style="margin-left:10px;">待审核：<b style="color:#4787ff;"><?php echo ($tongji['dsh']*1); ?></b>人</span>
			</div>
			<div class="search-form fr cf">
				<div class="sleft">
					<form name="formSearch" id="formSearch" method="get" name="form1">
						<select style="width:135px;float:left;margin-right:10px;" name="idstate" class="form-control">
							<option value="" <?php if(empty($_GET['idstate'])): ?>selected<?php endif; ?> >全部认证状态</option>
							<option value="2" <?php if(($_GET['idstate']) == "2"): ?>selected<?php endif; ?> >认证待审核</option>
							<option value="3" <?php if(($_GET['idstate']) == "3"): ?>selected<?php endif; ?> >认证已通过</option>
							<option value="9" <?php if(($_GET['idstate']) == "9"): ?>selected<?php endif; ?> >认证未通过</option>
							<option value="1" <?php if(($_GET['idstate']) == "1"): ?>selected<?php endif; ?> >未提交</option>
						</select>
						<select style="width:120px;float:left;margin-right:10px;" name="status" class="form-control">
							<option value="" <?php if(empty($_GET['status'])): ?>selected<?php endif; ?> >全部状态</option>
							<option value="1" <?php if(($_GET['status']) == "1"): ?>selected<?php endif; ?> >冻结状态</option>
							<option value="2" <?php if(($_GET['status']) == "2"): ?>selected<?php endif; ?> >正常状态</option>
						</select>
						<select style="width:120px;float:left;margin-right:10px;" name="field" class="form-control">
							<option value="username"
							<?php if(empty($_GET['field'])): ?>selected<?php endif; ?>
							>用户名</option>
							<option value="mobile"
							<?php if(($_GET['field']) == "mobile"): ?>selected<?php endif; ?>
							>手机号码</option>
							<option value="truename"
							<?php if(($_GET['field']) == "truename"): ?>selected<?php endif; ?>
							>真实姓名</option>
							<option value="idcard"
							<?php if(($_GET['field']) == "idcard"): ?>selected<?php endif; ?>
							>身份证号</option>
						</select>

						<script type="text/javascript" src="/Public/layer/laydate/laydate.js"></script>

						<input type="text" name="name" class="search-input form-control" value="<?php echo ($_GET['name']); ?>" placeholder="请输入查询内容" style="">
						<a class="sch-btn" href="javascript:;" id="search"> <i class="btn-search"></i> </a>
					</form>
					<script>
						//搜索功能
						$(function () {
							$('#search').click(function () {
								$('#formSearch').submit();
							});
						});
						//回车搜索
						$(".search-input").keyup(function (e) {
							if (e.keyCode === 13) {
								$("#search").click();
								return false;
							}
						});
					</script>
				</div>
			</div>
		</div>
		<div class="data-table table-striped">
			<form id="form" action="<?php echo U('User/userExcel');?>" method="post" class="form-horizontal">
				<table class="">
					<thead>
					<tr>
						<th class="row-selected row-selected">
							<input class="check-all" type="checkbox"/>
						</th>
						<th class="">ID</th>
						<th class="">用户名</th>
						<th class="">手机号</th>
						<th class="">认证状态</th>
						<th class="">实名接口</th>
						<th class="">实名信息</th>
						<th class="">注册时间</th>
						<th class="">推荐人</th>
						<!-- <th class="">推广总收益</th> -->
						<!-- <th class="">推广人数</th> -->
						<!-- <th class="">推广总业绩</th> -->
						<th class="">状态</th>
						<th class="">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
								<td>
									<input class="ids" type="checkbox" name="id[]" value="<?php echo ($vo["id"]); ?>"/>
								</td>
								<td><?php echo ($vo["id"]); ?></td>
								<td title="登录该用户"><a href=" <?php echo U('User/loginadmin?id='.$vo['id'].'&pass='.$vo['password']);?>" target="_blank"><?php echo ($vo["username"]); ?></a></td>
								<td><?php echo ($vo["mobile"]); ?></td>
								<td>
									认证级别：<b><?php echo ($vo["kyc_lv"]); ?></b><br>
									<?php if(($vo["idstate"]) == "0"): ?><b>未提交</b><?php endif; ?>
									<?php if(($vo["idstate"]) == "1"): ?><b style="color:#F39309">待审核</b><?php endif; ?>
									<?php if(($vo["idstate"]) == "2"): ?><b style="color:#11B419">已通过</b><?php endif; ?>
									<?php if(($vo["idstate"]) == "8"): ?><b style="color:#FC383C">未通过</b><?php endif; ?>
								</td>
								<td>
									<?php if(($vo["idapi"]) == ""): ?><b>人工审核</b><?php endif; ?>
									<?php if(($vo["idapi"]) == "0"): ?><b style="color:#11B419">匹配</b><?php endif; ?>
									<?php if(($vo["idapi"]) == "5"): ?><b style="color:#FC383C">不匹配</b><?php endif; ?>
									<?php if(($vo["idapi"]) == "14"): ?><b style="color:#FC383C">无效证件</b><?php endif; ?>
									<?php if(($vo["idapi"]) == "96"): ?><b>调用失败</b><?php endif; ?>
								</td>
								<td>
									姓名：<?php echo ($vo["truename"]); ?><br>
									国籍：<?php echo ($vo["idnationality"]); ?><br>
									证件类型：
									<?php if(($vo["idtype"]) == "0"): ?>未知<?php endif; ?>
									<?php if(($vo["idtype"]) == "1"): ?>身份证<?php endif; ?>
									<?php if(($vo["idtype"]) == "2"): ?>护照<?php endif; ?>
									<?php if(($vo["idtype"]) == "3"): ?>驾驶证<?php endif; ?>
									<br>证件号码：<b style="color: #0383FB"><?php echo ($vo["idcard"]); ?></b><br>
								</td>
								<td><?php echo (addtime($vo["addtime"])); ?></td>
								<td>
									<?php if(($vo["invit_1"]) != ""): ?><a href="<?php echo U('User/index?name='.$vo['invit_1'].'&field=username');?>">1代：<?php echo ($vo['invit_1']); ?></a><br><?php endif; ?>
									<?php if(($vo["invit_2"]) != ""): ?><a href="<?php echo U('User/index?name='.$vo['invit_2'].'&field=username');?>">2代：<?php echo ($vo['invit_2']); ?></a><br><?php endif; ?>
									<?php if(($vo["invit_3"]) != ""): ?><a href="<?php echo U('User/index?name='.$vo['invit_3'].'&field=username');?>">3代：<?php echo ($vo['invit_3']); ?></a><br><?php endif; ?>
								</td>
								<!-- <td><?php echo ($vo["tmoney"]); ?></td> -->
								<!-- <td><?php echo ($vo["tmember"]); ?></td> -->
								<!-- <td><?php echo ($vo["ttotal"]); ?></td> -->
								<td>
									<?php if(($vo["status"]) == "0"): ?>冻结<?php endif; ?>
									<?php if(($vo["status"]) == "1"): ?>正常<?php endif; ?>
								</td>
								<td>
									<a href="<?php echo U('User/edit?id='.$vo['id']);?>" class="btn btn-primary btn-xs" >编辑</a>
									
									<?php if(($vo["idstate"]) == "1"): ?><a href="<?php echo U('User/edit2?id='.$vo['id']);?>" class="btn btn-primary btn-xs" style="background-color:#F39309">实名信息</a>
										<?php else: ?>
										<a href="<?php echo U('User/edit2?id='.$vo['id']);?>" class="btn btn-primary btn-xs" >实名信息</a><?php endif; ?>
								</td>
							</tr><?php endforeach; endif; else: echo "" ;endif; ?>
						<?php else: ?>
						<td colspan="12" class="text-center empty-info"><i class="glyphicon glyphicon-exclamation-sign"></i>暂无数据</td><?php endif; ?>
					</tbody>
				</table>
			</form>
			<div class="page">
				<div><?php echo ($page); ?></div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	//提交表单
	$('#submit').click(function () {
		$('#form').submit();
	});
	$(".page > div").children("a").each(function(){
		var ahref = $(this).attr('href');
		var ahrefarr = ahref.split("/");
		var ahlength = ahrefarr.length;
		var newhref = '';
		for(var i=0;i<ahlength;i++){
			if(i<3 && i>0){
				newhref += "/"+ahrefarr[i];
			}
			if(i==3){
				newhref += "/"+ahrefarr[i]+".html?";
			}
			if(i>=4 && i%2==0){
				newhref += "&"+ahrefarr[i]+"="+ahrefarr[i+1];
			}
		}
		$(this).attr("href",newhref);
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