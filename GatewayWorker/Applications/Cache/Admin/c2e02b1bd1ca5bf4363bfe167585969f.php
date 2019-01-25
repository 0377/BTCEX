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
	.disyc{display:none;}
</style>
<script>
	$(function(){
		//页面加载完毕后开始执行的事件
		$("select[id='api_type']").change(function(){
			if ($(this).val()=="eth") {
				$("#erc_token").show();
				$("#eth_wallet").html('总钱包地址 :');
				$("#eth_account").html('总钱包平台账户 :');
			} else {
				$("#erc_token").hide();
				$("#eth_wallet").html('钱包服务器用户名 :');
				$("#eth_account").html('钱包服务器密码 :');
			}
		});
		$("select[id='token_type']").change(function(){
			if ($(this).val()=="1") {
				$("#erc_token_hy").show();
			} else {
				$("#erc_token_hy").hide();
			}
		});
	});
</script>
<div id="main-content">
	<div id="top-alert" class="fixed alert alert-error" style="display: none;">
		<button class="close fixed" style="margin-top: 4px;">&times;</button>
		<div class="alert-content">警告内容</div>
	</div>
  <div id="main" class="main">
    <div class="main-title-h"> 
    	<span class="h1-title"><a href="<?php echo U('Config/coin');?>">币种配置</a> &gt;&gt;</span> 
    	<span class="h1-title"><?php if(empty($data)): ?>新增币种<?php else: ?>编辑币种<?php endif; ?></span>
	</div>
    <div class="tab-wrap">
      <div class="tab-content">
        <form id="form" action="<?php echo U('Config/coinEdit');?>" method="post" class="form-horizontal" enctype="multipart/form-data">
          <div id="tab" class="tab-pane in tab">
            <div class="form-item cf">
              <table>
                <tr class="controls">
					<td class="item-label">图标 :</td>
					<td>
						<div id="addpicContainer"> 
						<!-- 利用multiple="multiple"属性实现添加多图功能 --> 
						<!-- position: absolute;left: 10px;top: 5px;只针对本用例将input隐至图片底下。--> 
						<!-- height:0;width:0;z-index: -1;是为了隐藏input，因为Chrome下不能使用display:none，否则无法添加文件 --> 
						<!-- onclick="getElementById('inputfile').click()" 点击图片时则点击添加文件按钮 -->
						<?php if(!empty($data["img"])): ?><!--没有图片显示默认图片--> 
							<img id="up_img" onclick="getElementById('inputfile').click()" style="cursor:pointer;max-width:100px;" title="点击添加图片" alt="点击添加图片" src="/Upload/coin/<?php echo ($data["img"]); ?>">
						<?php else: ?>
							<!--没有图片显示默认图片--> 
							<img id="up_img" onclick="getElementById('inputfile').click()" style="cursor:pointer;max-width:100px;" title="点击添加图片" alt="点击添加图片" src="/Public/Admin/images/addimg.png"><?php endif; ?>
						<input type="hidden" id="img" name="img" value="<?php echo ($data["img"]); ?>">
						<input type="file" id="inputfile" style="height:0;width:0;z-index:-1;position:absolute;left:10px;top:5px;" value=""/>
						</div>
					</td>
					<td class="item-note">80px*80px</td>
                </tr>
                
                <tr class="controls">
					<td class="item-label">币种简称 :</td>
					<?php if(empty($data['name'])): ?><td><input type="text" class="form-control input-10x" name="name" value=""></td>
					<?php else: ?>
						<input type="hidden" class="form-control input-10x" name="name" value="<?php echo ($data["name"]); ?>">
						<!--<td><?php echo ($data["name"]); ?></td>-->
						<td><input type="text" class="form-control input-10x" name="name" value="<?php echo ($data["name"]); ?>"></td><?php endif; ?>
					<td class="item-note">* 币种代码，必须填写：只能英文小写，但不能填写关键字比如：asc 、desc</td>
                </tr>
                <tr class="controls">
					<td class="item-label">英文名称 :</td>
					<td><input type="text" class="form-control input-10x" name="js_yw" value="<?php echo ($data['js_yw']); ?>"></td>
					<td class="item-note">* 必须填写</td>
                </tr>
                <tr class="controls">
					<td class="item-label">中文名称 :</td>
					<td><input type="text" class="form-control input-10x" name="title" value="<?php echo ($data['title']); ?>"></td>
					<td class="item-note">* 必须填写</td>
                </tr>
                <tr class="controls">
					<td class="item-label">币种类型:</td>
					<td>
						<select name="type" class="form-control input-10x">
							<option value="qbb" <?php if(($data["type"]) == "qbb"): ?>selected<?php endif; ?>>钱包币</option>
							<option value="ptb" <?php if(($data["type"]) == "ptb"): ?>selected<?php endif; ?>>平台币</option>
							<option value="rgb" <?php if(($data["type"]) == "rgb"): ?>selected<?php endif; ?>>认购币</option>
							<option value="rmb" <?php if(($data["type"]) == "rmb"): ?>selected<?php endif; ?>>法定货币</option>
						</select>
					</td>
					<td class="item-note"></td>
                </tr>
                
                <tr class="controls">
                  <td class="item-label">接口类型 :</td>
					<td>
						<select name="api_type" id="api_type" class="input-small" style="width:120px">
							<option value="" <?php if(($data['api_type']) == ""): ?>selected<?php endif; ?>>空设置</option>
							<option value="btc" <?php if(($data['api_type']) == "btc"): ?>selected<?php endif; ?>>BTC</option>
							<option value="eth" <?php if(($data['api_type']) == "eth"): ?>selected<?php endif; ?>>ETH</option>
						</select>
					</td>
               		<td class="item-note">如果非平台或法定货币，必须选择</td>
                </tr>
                <tr class="controls disyc" id="erc_token" <?php if(($data['api_type']) == "eth"): ?>style="display:table-row;"<?php endif; ?>>
					<td class="item-label">是否ERC20 :</td>
					<td>
						<select name="token_type" id="token_type" class="input-small" style="width: 120px">
							<option value="0" <?php if(($data['token_type']) == "0"): ?>selected<?php endif; ?>>否</option>
							<option value="1" <?php if(($data['token_type']) == "1"): ?>selected<?php endif; ?>>是</option>
						</select>
					</td>
               		<td class="item-note">以太坊发行的代币协议</td>
                </tr>
                <tr class="controls disyc" id="erc_token_hy" <?php if(($data['token_type']) == "1"): ?>style="display:table-row;"<?php endif; ?>>
					<td class="item-label">ERC20合约地址 :</td>
					<td><input type="text" class="form-control input-10x" name="dj_hydz" value="<?php echo ($data['dj_hydz']); ?>" autocomplete="off" aria-autocomplete="none"></td>
					<td class="item-note">非ETH (ERC20) 不用填写</td>
                </tr>
                
                <tr class="controls" style="border-top:1px solid #d0d0d0;">
					<td class="item-label">钱包服务器ip :</td>
					<td><input type="text" class="form-control input-10x" name="dj_zj" value="<?php echo ($data['dj_zj']); ?>"></td>
					<td class="item-note">对接钱包使用 认购币类型的 不用填写</td>
                </tr>
                <tr class="controls">
					<td class="item-label">钱包服务器端口 :</td>
					<td><input type="text" class="form-control input-10x" name="dj_dk" value="<?php echo ($data['dj_dk']); ?>"></td>
					<td class="item-note">对接钱包使用 认购币类型的 不用填写</td>
                </tr>
                <tr class="controls">
					<td class="item-label" id="eth_wallet">
						<?php if(($data['api_type']) == "eth"): ?>总钱包地址 :<?php else: ?>钱包服务器用户名 :<?php endif; ?>
					</td>
					<td><input type="text" class="form-control input-10x" name="dj_yh" value="<?php echo ($data['dj_yh']); ?>" autocomplete="off" aria-autocomplete="none"></td>
					<td class="item-note">对接钱包使用 认购币类型的 不用填写</td>
                </tr>
                <tr class="controls">
					<td class="item-label" id="eth_account">
						<?php if(($data['api_type']) == "eth"): ?>总钱包平台账户 :<?php else: ?>钱包服务器密码 :<?php endif; ?>
					</td>
					<td>
						<input type="text" class="form-control input-10x" name="dj_mm" value="<?php echo ($data['dj_mm']); ?>"  autocomplete="off" aria-autocomplete="none">
					</td>
					<td class="item-note">对接钱包使用 认购币类型的 不用填写</td>
                </tr>

                <tr class="controls" style="border-top:1px solid #d0d0d0;">
					<td class="item-label">兑换方式 :</td>
					<td>
						<select name="change" class="input-small" style="width:120px">
							<option value="0" <?php if(($data['change']) == "0"): ?>selected<?php endif; ?>>禁止兑换</option>
							<option value="1" <?php if(($data['change']) == "1"): ?>selected<?php endif; ?>>固定比例</option>
							<option value="2" <?php if(($data['change']) == "2"): ?>selected<?php endif; ?>>浮动比例</option>
						</select>
					</td>
               		<td class="item-note"></td>
                </tr>
                <tr class="controls">
					<td class="item-label">可兑换币种 :</td>
					<td>
						<select name="changecoin" class="form-control input-10x">
							<?php if(is_array($C['coin'])): $k = 0; $__LIST__ = $C['coin'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($k % 2 );++$k;?><!-- <option value="<?php echo ($v['name']); ?>"><?php echo ($v['title']); ?></option> --> 
								<option value="<?php echo ($v['name']); ?>" <?php if(($data["changecoin"]) == $v['name']): ?>selected<?php endif; ?>>
									<?php echo ($v['title']); ?>
								</option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
                    </td>
                </tr>
                <tr class="controls">
					<td class="item-label">固定比例 :</td>
					<td><input type="text" class="form-control" name="huilv" value="<?php echo ($data['huilv']); ?>" autocomplete="off" aria-autocomplete="none"></td>
					<td class="item-note">如填10,则10<?php echo ($data['title']); ?>兑换1&nbsp;<?php echo ($data['changecoin']); ?>(选择固定比例时有效)</td>
                </tr>
                <tr class="controls">
					<td class="item-label">最小兑换数量 :</td>
					<td><input type="text" class="form-control" name="amount" value="<?php echo ($data['amount']); ?>" autocomplete="off" aria-autocomplete="none"></td>
					<td class="item-note">如填10,则最少用10<?php echo ($data['title']); ?>起兑换<?php echo ($data['changecoin']); ?></td>
                </tr>
                <tr class="controls" style="display:none">
					<td class="item-label">转入赠送 :</td>
					<td><input type="text" class="form-control input-10x" name="zr_zs" value="<?php echo ($data['zr_zs']); ?>"></td>
					<td class="item-note">% (填写0.01-100 任意数字) 转入手续费比例,费用直接从官方手续费地址扣取</td>
                </tr>
                <tr class="controls">
					<td class="item-label">转入状态 :</td>
					<td>
						<select name="zr_jz" class="form-control input-10x" >
							<option value="1" <?php if(($data['zr_jz']) == "1"): ?>selected<?php endif; ?>>正常转入</option>
							<option value="0" <?php if(($data['zr_jz']) == "0"): ?>selected<?php endif; ?>>禁止转入</option>
						</select>
					</td>
					<td class="item-note"></td>
                </tr>
                <tr class="controls">
					<td class="item-label">一代赠送比例 :</td>
					<td><input type="text" class="form-control input-10x" name="type1_give" value="<?php echo ($data['type1_give']); ?>"></td>
					<td class="item-note">% 根据单笔计算</td>
                </tr>
                <tr class="controls">
					<td class="item-label">二代赠送比例 :</td>
					<td><input type="text" class="form-control input-10x" name="type2_give" value="<?php echo ($data['type2_give']); ?>"></td>
					<td class="item-note">% 根据单笔计算</td>
                </tr>
                <tr class="controls">
					<td class="item-label">三代赠送比例 :</td>
					<td><input type="text" class="form-control input-10x" name="type3_give" value="<?php echo ($data['type3_give']); ?>"></td>
					<td class="item-note">% 根据单笔计算</td>
                </tr>
                <tr class="controls">
					<td class="item-label">确认次数 :</td>
					<td>
						<?php if(empty($data['zr_dz'])): ?><input type="text" class="form-control input-10x" name="zr_dz" value="1">
						<?php else: ?>
							<input type="text" class="form-control input-10x" name="zr_dz" value="<?php echo ($data['zr_dz']); ?>"><?php endif; ?>
					</td>
					<td class="item-note">转出确认次数必须填写,且大于0</td>
                </tr>
                
                <tr class="controls" style="border-top:1px solid #d0d0d0;">
					<td class="item-label">转出手续费 :</td>
					<td><input type="text" class="form-control input-10x" name="zc_fee" value="<?php echo ($data['zc_fee']); ?>"></td>
					<td class="item-note">% (填写0.01-100 任意数字) 转出手续费比例,费用直接存入官方手续费地址</td>
                </tr>
                <?php if(empty($data['name'])): ?><tr class="controls">
						<td class="item-label">官方手续费地址 :</td>
						<td><input type="text" class="form-control input-10x" name="zc_user" value="0" readonly></td>
						<td class="item-note"><b>[新增币种不可操作]</b> 如果要收取手续费,添加成功后复制官方某账户对应本币地址,重新编辑本币种,填写到本处即可</td>
					</tr>
				<?php else: ?>
					<tr class="controls">
						<td class="item-label">官方手续费地址 :</td>
						<td><input type="text" class="form-control input-10x" name="zc_user" value="<?php echo ($data['zc_user']); ?>"></td>
						<td class="item-note"><b>[重要]</b> 请填写一个官方前台账户生成的本币地址到本处作为手续费(收取|扣除)账户,否则手续费比例设置无效</td>
					</tr><?php endif; ?>
                <tr class="controls">
                	<td class="item-label">最小转出数量 :</td>
                	<td><input type="text" class="form-control input-10x" name="zc_min" value="<?php echo ($data['zc_min']); ?>"></td>
                	<td class="item-note">推荐:正数且大于0.01</td>
                </tr>
                <tr class="controls">
                	<td class="item-label">最大转出数量 :</td>
                	<td><input type="text" class="form-control input-10x" name="zc_max" value="<?php echo ($data['zc_max']); ?>"></td>
                	<td class="item-note">推荐:正数且大于10000</td>
                </tr>
                <tr class="controls">
                	<td class="item-label">转出状态 :</td>
					<td>
						<select name="zc_jz" class="form-control input-10x">
							<option value="1" <?php if(($data['zc_jz']) == "1"): ?>selected<?php endif; ?>>正常转出</option>
							<option value="0" <?php if(($data['zc_jz']) == "0"): ?>selected<?php endif; ?>>禁止转出</option>
						</select>
					</td>
					<td class="item-note"></td>
                </tr>
                <tr class="controls">
					<td class="item-label">转出自动 :</td>
					<td>
						<?php if(empty($data['zc_zd'])): ?><input type="text" class="form-control input-10x" name="zc_zd" value="0">
						<?php else: ?>
							<input type="text" class="form-control input-10x" name="zc_zd" value="<?php echo ($data['zc_zd']); ?>"><?php endif; ?>
					</td>
					<td class="item-note">推荐:正数且大于10 (小于这个数自动转出,大于这个数后台审核 )为了安全不要设置太大</td>
                </tr>
                
				<tr class="controls">
					<td class="item-label">排序 :</td>
					<td><input type="text" class="form-control input-10x" name="sort" value="<?php echo ($data["sort"]); ?>"></td>
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
<script type="text/javascript">
    $(document).ready(function () {
        //响应文件添加成功事件
        $("#inputfile").change(function () {
            //创建FormData对象
            var data = new FormData();
            //为FormData对象添加数据
            $.each($('#inputfile')[0].files, function (i, file) {
                data.append('upload_file' + i, file);
            });

            //发送数据
            $.ajax({
                url: '/Admin/Config/coinImage',
                type: 'POST',
                data: data,
                cache: false,
                contentType: false, //不可缺参数
                processData: false, //不可缺参数
                success: function (data) {
                    console.log(data);
                    if (data) {
                        $('#up_img').attr("src", '/Upload/coin/'+ $.trim(data));
                        $('#img').val(data);
                        $('#up_img').show();
                    }
                },
                error: function () {
                    alert('上传出错');
                    $(".loading").hide(); //加载失败移除加载图片
                }
            });

        });
    });
</script> 
<script type="text/javascript" src="/Public/kindeditor/kindeditor-min.js"></script> 
<script type="text/javascript">
    var editor;
    KindEditor.ready(function (K) {
        editor = K.create('textarea', {
            width: '500px',
            height: '100px',
            items: ['source', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|', 'emoticons', 'link', 'fullscreen'],
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
 
<script type="text/javascript" charset="utf-8">
	//导航高亮
	highlight_subnav("<?php echo U('Config/coin');?>");
</script>