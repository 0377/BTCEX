<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="renderer" content="webkit">
		<title><?php echo L(C('web_title'));?></title>
		<meta name="Keywords" content="<?php echo L(C('web_keywords'));?>">
		<meta name="Description" content="<?php echo L(C('web_description'));?>">
		<link rel="stylesheet" type="text/css" href="<?php echo stamp('/Public/Home/rh_css/main2.css');?>">
		<script type="text/javascript" src="/Public/Home/rh_js/jquery-1.12.0.min.js"></script>
<!--		<script type="text/javascript" src="<?php echo stamp('/Public/Home/rh_js/jquery.cookies.2.2.0.js');?>"></script>-->
		<script type="text/javascript" src="/Public/layer/layer.js"></script>
	</head>
	<body>
		<div class="menu-tabs">
			<a class="logo" href="/"><img src="/Public/Home/rh_img/logo.png" /></a>
			<div class="tabs">
				<?php if(is_array($daohang)): $i = 0; $__LIST__ = $daohang;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo ($vo['url']); ?>"><?php echo L($vo['title']);?></a><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
			
			<div class="r-box">
				<div class="language">
					<img src="/Public/Home/rh_img/icon_language.png" />
                    <?php if(LANG_SET=='zh-cn'){ $nm = '简体中文';}else{ $nm = 'English';} ?>
                    <label><?php echo $nm;?></label>
					<img class="arrow-down" src="/Public/Home/rh_img/icon_arrow_down.png" />
				</div>
				<?php if(($_SESSION['userId']) > "0"): ?><style>.menu-tabs .language-box {right: 170px !important;}</style>
					<div class="uuser">
						<span><a href="<?php echo U('finance/index');?>"><label><?php echo L(我的资产);?></label></a></span>
					</div>
				<?php else: ?>
					<a class="login" href="<?php echo U('Login/index');?>"><?php echo L(登录);?></a>
					<a class="register" href="<?php echo U('Login/register');?>"><?php echo L(注册);?></a><?php endif; ?>
			</div>
			<div class="language-box">
				<p><a href="<?php echo U('?LANG=en-us');?>">English</a></p>
				<p><a href="<?php echo U('?LANG=zh-cn');?>"><?php echo L(中文简体);?></a></p>
			</div>
			<div class="uuser-box">
				<p><a href="<?php echo U('finance/index');?>"><?php echo L(财务中心);?></a></p>
				<p><a href="<?php echo U('User/index');?>"><?php echo L(安全中心);?></a></p>
				<p><a href="<?php echo U('Login/loginout');?>"><?php echo L(退出);?></a></p>
			</div>
		</div>
<link rel="stylesheet" href="/Public/Home/rh_css/user.css">

<div class="infoBox">
	<div class="marbox">
	<div class="fl">
		<p><h3><?php echo L('账户');?></h3></p>
		<p class="username"><?php echo ($user["username"]); ?><a href="<?php echo U('User/kyc');?>"><b><?php echo L('KYC认证');?> &gt;</b></a></p>
	</div>
	<div class="fr">
		<p style="margin-top:15px;">
			<a class="btns_cz fl" href="<?php echo U('Exchange/index');?>"><?php echo L('C2C充值');?></a>
			<a class="btns_tx fl" href="<?php echo U('Exchange/index');?>"><?php echo L('C2C提现');?></a>
		</p>
	</div>
</div>
</div>

<div class="UserBox" style="margin-top:20px;margin-bottom:40px;">
	<div class="fl SettingLeft">
		 <ul>
	<li><a id="menu_1" href="<?php echo U('User/index');?>"><?php echo L('账户信息');?><i></i></a></li>
	<li><a id="menu_2" href="<?php echo U('User/qianbao');?>"><?php echo L('提币地址管理');?><i></i></a></li>
	<li><a id="menu_3" href="<?php echo U('User/log');?>"><?php echo L('登录日志');?><i></i></a></li>
	<li><a id="menu_0" href="<?php echo U('Finance/index');?>"><?php echo L('我的资产');?><i></i></a></li>
</ul>
	</div>
	<div class="fr SettingRight">
		<div class="titles"><h3><?php echo L('安全设置');?></h3></div>
		<div class="Column_Security">
			<div class="fl sc_status">
				<img src="/Public/Home/rh_img/user_icon/m-icon0.png" width="45" height="45">
				<h3><?php echo L('实名认证');?></h3>
				
				<?php if($user["kyc_lv"] == 1): if($user["idstate"] == 8): ?><p style="color:#FF0004;"><b><?php echo L('初级认证-未通过');?></b></p>
						<p><a class="btnson" href="<?php echo U('User/kyc');?>"><?php echo L('重新认证');?></a></p>
					<?php elseif($user["idstate"] == 1): ?>
						<p><b><?php echo L('审核中');?></b></p>
						<p><a class="btnjz" href="#"><?php echo L('已提交');?></a></p>
					<?php elseif($user["idstate"] == 2): ?>
						<p><b style="color:#24D328;"><?php echo L('初级认证-通过');?></b></p>
						<p><a class="btnson" href="<?php echo U('User/kyc');?>"><?php echo L('升级认证');?></a></p>
					<?php else: ?>
						<p><?php echo L('未认证');?></p>
						<p><a class="btnson" href="<?php echo U('User/kyc');?>"><?php echo L('立即认证');?></a></p><?php endif; ?>
					
				<?php elseif($user["kyc_lv"] == 2): ?>
					<?php if($user["idstate"] == 8): ?><p style="color:#FF0004;"><b><?php echo L('高级认证-未通过');?></b></p>
						<p><a class="btnson" href="<?php echo U('User/kyc');?>"><?php echo L('重新认证');?></a></p>
					<?php elseif($user["idstate"] == 1): ?>
						<p><b><?php echo L('审核中');?></b></p>
						<p><a class="btnjz" href="#"><?php echo L('已提交');?></a></p>
					<?php elseif($user["idstate"] == 2): ?>
						<p><b style="color:#24D328;"><?php echo L('高级认证-通过');?></b></p>
						<p><a class="btnjz" href="#"><?php echo L('已提交');?></a></p>
					<?php else: ?>
						<p><?php echo L('未认证');?></p>
						<p><a class="btnson" href="<?php echo U('User/kyc');?>"><?php echo L('立即认证');?></a></p><?php endif; ?>
				<?php else: ?>
					<p><?php echo L('未认证');?></p>
					<p><a class="btnson" href="<?php echo U('User/kyc');?>"><?php echo L('立即认证');?></a></p><?php endif; ?>
			</div>
			<div class="fl sc_status">
				<img src="/Public/Home/rh_img/user_icon/m-icon2.png" width="45" height="45">
				<h3><?php echo L('手机认证');?></h3>
				<p>
					<?php if(($user["mobile"]) == ""): echo L('未绑定');?>
					<?php else: ?>
						<?php echo ($mobiles); endif; ?>
				</p>
				<p><a class="btnjz" href="#"><?php echo L('禁止修改');?></a></p>
			</div>
			<div class="fl sc_status">
				<img src="/Public/Home/rh_img/user_icon/m-icon3.png" width="45" height="45">
				<h3><?php echo L('谷歌验证器');?></h3>
				<p><?php echo L('用于提现和安全设置验证');?></p>
				<p>
					<?php if(($user["ga"]) == ""): ?><a class="btnson" onclick="addGoogleSet();"><?php echo L('绑定');?></a>
					<?php else: ?>
						<a class="btns" onclick="addGoogle();"><?php echo L('解绑');?></a><?php endif; ?>
				</p>
			</div>
		</div>

		<div class="Column_Security">
			<div class="fl sc_status">
				<img src="/Public/Home/rh_img/user_icon/m-icon4.png" width="45" height="45">
				<h3><?php echo L('登录密码');?></h3>
				<p><?php echo L('用于登录您的账号，请保存好登录密码');?></p>
				<p><a class="btns" onclick="addPassword();"><?php echo L('修改');?></a></p>
			</div>
			<div class="fl sc_status">
				<img src="/Public/Home/rh_img/user_icon/m-icon5.png" width="45" height="45">
				<h3><?php echo L('交易密码');?></h3>
				<p><?php echo L('账户资金变动时，需先验证交易密码');?></p>
				<p>
					<?php if(($user["paypassword"]) == ""): ?><a class="btnson" onclick="addPaypasswordSet();"><?php echo L('设置');?></a>
					<?php else: ?>
						<a class="btns" onclick="addPaypassword();"><?php echo L('修改');?></a><?php endif; ?>
				</p>
			</div>
		</div>
		
		<div class="titles"><h3><?php echo L('登录历史');?></h3></div>
		<div class="Column_LogonLog">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr class="title">
					<th width="39.5%" align="center"><?php echo L('登录时间');?></th>
					<th width="39.5%" align="center"><?php echo L('IP地址');?></th>
					<th width="20%" align="center"><?php echo L('状态');?></th>
				</tr>
				<?php if(is_array($userlog)): $i = 0; $__LIST__ = $userlog;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						<td><?php echo (addtime($vo["addtime"])); ?></td>
						<td><?php echo ($vo["addip"]); ?></td>
						<td>
							<?php if(($vo["status"]) == "0"): ?><font color="violet"><?php echo L('出错');?></font><?php endif; ?>
							<?php if(($vo["status"]) == "1"): echo L('正常'); endif; ?>
							<?php if(($vo["status"]) == "2"): ?><font color="red"><?php echo L('异常');?></font><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<tr>
					<td align="center" colspan="3">
						<a class="btns" href="<?php echo U('User/log');?>"><?php echo L('加载更多');?></a>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>



<div class="float_win_pay" id="addGoogleSet" style="display:none;width:500px;">
    <div class="tan_title">
        <h4><?php echo L('绑定 GOOGLE验证器');?></h4>
        <span class="close-btn" onclick="closeCharge();">x</span>
    </div>
    <div class="payment_content payment_ga" style="min-width:0;">
		<ul>
			<li>
				<p style="margin-bottom:10px;"><b>1. <?php echo L('下载并安装 Google 身份验证器');?></b></p>
				<a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1">
					<img src="/Public/Home/rh_img/user_icon/download-Google.png">
				</a>
				<a style="margin-left:20px;" href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8">
					<img src="/Public/Home/rh_img/user_icon/download-Apple.png">
				</a>
			</li>
			<li>
				<p style="margin-bottom:10px;"><b>2. <?php echo L('使用 Google 身份验证器 扫描二维码或输入序列号');?></b></p>
				<div class="CodeContent">
					<div style="width:50%;" id="qrcode"></div>
					<div style="width:45%;"><?php echo L('双重验证可以更安全的保护您的账户，此密钥可让您在手机丢失的情况下恢复您的 Google 身份验证。');?></div>
				</div>
				<p style="margin-bottom:30px;line-height:45px;background-color:#fff8ea;text-align:center;"><b><?php echo ($Asecret); ?></b></p>
			</li>
			<li>
				<span class="label-1"><?php echo L('谷歌验证码');?></span>
				<input type="text" autocomplete="off" id="ga_verify" class="input-1" />
			</li>
			<li>
				<div class="dv_radio" style="display:none">
				   <!-- <?php echo L('配置验证的模块');?>：-->
					<label for="ga-login">
						<input type="checkbox" id="ga-login" name="login" checked="1"><?php echo L('登录');?></label>
					<label for="ga-transfer">
						<input type="checkbox" id="ga-transfer" checked="1" name="transfer"><?php echo L('资金转出');?>
					</label>
				</div>
			</li>
			<li style="margin-top:30px;">
				<a href="javascript:void(0)" onclick="UpGoogleSet()" class="btns"><?php echo L('确认');?></a>
			</li>
		</ul>
    </div>
</div>
<div class="float_win_pay" id="addGoogle" style="display:none;width:500px;">
    <div class="tan_title">
        <h4><?php echo L('解绑 GOOGLE验证器');?></h4>
        <span class="close-btn" onclick="closeCharge();">x</span>
    </div>
    <div class="payment_content payment_ga" style="min-width:0;">
		<ul>
			<li>
				<span class="label-1"><?php echo L('谷歌验证码');?></span>
				<input type="text" autocomplete="off" id="ga_delete" class="input-1" />
			</li>
			<li style="margin-top:30px;">
				<a href="javascript:void(0)" onclick="UpGoogle()" class="btns"><?php echo L('确认');?></a>
			</li>
		</ul>
    </div>
</div>
<div class="float_win_pay" id="addPassword" style="display:none;">
    <div class="tan_title">
        <h4><?php echo L('修改登录密码');?></h4>
        <span class="close-btn" onclick="closeCharge();">x</span>
    </div>
    <div class="payment_content">
		<ul>
			<li>
				<span class="label-1"><?php echo L('旧登录密码');?></span>
				<input type="password" autocomplete="off" id="oldpassword" class="input-1" />
			</li>
			<li>
				<span class="label-1"><?php echo L('新登录密码');?></span>
				<input type="password" autocomplete="off" id="newpassword" class="input-1" />
			</li>
			<li>
				<span class="label-1"><?php echo L('确认登录密码');?></span>
				<input type="password" autocomplete="off" id="repassword" class="input-1" />
			</li>
			<li>
				<span class="label-1"><?php echo L('动态验证码');?></span>
				<input type="text" autocomplete="off" id="mobile_verify_password" class="vcode-1" />
				<input type="button" id="regBtn_pass" onclick="SendCode_pass()" value="<?php echo L('获取验证码');?>" class="code-num">
			</li>
			<li style="margin-top:30px;">
				<a href="javascript:void(0)" onclick="UpPassword()" class="btns"><?php echo L('确认');?></a>
			</li>
		</ul>
    </div>
</div>
<div class="float_win_pay" id="addPaypasswordSet" style="display:none;">
    <div class="tan_title">
        <h4><?php echo L('设置交易密码');?></h4>
        <span class="close-btn" onclick="closeCharge();">x</span>
    </div>
    <div class="payment_content">
		<ul>
			<li>
				<span class="label-1"><?php echo L('交易密码');?></span>
				<input type="password" autocomplete="off" id="set_paypassword" class="input-1" />
			</li>
			<li>
				<span class="label-1"><?php echo L('确认交易密码');?></span>
				<input type="password" autocomplete="off" id="set_repaypassword" class="input-1" />
			</li>
			<li>
				<span class="label-1"><?php echo L('动态验证码');?></span>
				<input type="text" autocomplete="off" id="set_mobile_verify" class="vcode-1" />
				<input type="button" id="setBtn_paypass" onclick="SendCode_setpaypass()" value="<?php echo L('获取验证码');?>" class="code-num">
			</li>
			<li style="margin-top:30px;">
				<a href="javascript:void(0)" onclick="UpPaypasswordSet()" class="btns"><?php echo L('确认');?></a>
			</li>
		</ul>
    </div>
</div>
<div class="float_win_pay" id="addPaypassword" style="display:none;">
    <div class="tan_title">
        <h4><?php echo L('修改交易密码');?></h4>
        <span class="close-btn" onclick="closeCharge();">x</span>
    </div>
    <div class="payment_content">
		<ul>
			<li>
				<span class="label-1"><?php echo L('旧交易密码');?></span>
				<input type="password" autocomplete="off" id="oldpaypassword" class="input-1" />
			</li>
			<li>
				<span class="label-1"><?php echo L('新交易密码');?></span>
				<input type="password" autocomplete="off" id="newpaypassword" class="input-1" />
			</li>
			<li>
				<span class="label-1"><?php echo L('确认交易密码');?></span>
				<input type="password" autocomplete="off" id="repaypassword" class="input-1" />
			</li>
			<li>
				<span class="label-1"><?php echo L('动态验证码');?></span>
				<input type="text" autocomplete="off" id="mobile_verify_paypassword" class="vcode-1" />
				<input type="button" id="regBtn_paypass" onclick="SendCode_paypass()" value="<?php echo L('获取验证码');?>" class="code-num">
			</li>
			<li style="margin-top:30px;">
				<a href="javascript:void(0)" onclick="UpPaypassword()" class="btns"><?php echo L('确认');?></a>
			</li>
			<li><p class="forget tc"><a href="<?php echo U('Login/findpaypwd');?>"><?php echo L('忘记交易密码？');?></a></p></li>
		</ul>
    </div>
</div>
<div id="dialogBoxShadow" style="position: absolute; top: 0px; left: 0px; z-index: 100; background-color: rgb(0, 0, 0); opacity: 0.5; width: 100%; background-position: initial initial; background-repeat: initial initial; display: none;"></div>


<script type="text/javascript" src="/Public/Home/js/jquery.qrcode.min.js"></script>
<script>
$('#qrcode').qrcode({
	render: "table",
	size: 200,
	text: "<?php echo ($qrCodeUrl); ?>"
});
</script>
<script>
function UpGoogleSet() {
    var ga_verify = $("#ga_verify").val();
	var ga_login = $('#ga-login').is(':checked') ? 1 : 0;
	var ga_transfer = $('#ga-transfer').is(':checked') ? 1 : 0;

    if (ga_verify == "" || ga_verify == null) {
        layer.tips('<?php echo (L("请输入谷歌验证码")); ?>', '#ga_verify', { tips: 3 });
        return false;
    }

    $.post("<?php echo U('User/gaGoogle');?>", { ga_verify: ga_verify, ga_login: ga_login, ga_transfer: ga_transfer, type: 'add' }, function(data) {
		if (data.status) {
			layer.closeAll();
			layer.msg("<?php echo L('操作成功跳转中...');?>", { icon: 16 });
			window.setTimeout('window.location="<?php echo U('User/index');?>"',1000);
		} else {
			layer.msg(data.info, { icon: 2 });
		}
    }, "json");
}
function UpGoogle() {
    var ga_verify = $("#ga_delete").val();

    if (ga_verify == "" || ga_verify == null) {
        layer.tips('<?php echo (L("请输入谷歌验证码")); ?>', '#ga_delete', { tips: 3 });
        return false;
    }

    $.post("<?php echo U('User/gaGoogle');?>", { ga_verify: ga_verify, type: 'delet' }, function(data) {
		if (data.status) {
			layer.closeAll();
			layer.msg("<?php echo L('操作成功跳转中...');?>", { icon: 16 });
			window.setTimeout('window.location="<?php echo U('User/index');?>"',1000);
		} else {
			layer.msg(data.info, { icon: 2 });
		}
    }, "json");
}
function UpPassword() {
    var oldpassword = $("#oldpassword").val();
    var newpassword = $("#newpassword").val();
    var repassword = $("#repassword").val();
    var mobile_verify = $("#mobile_verify_password").val();

    if (oldpassword == "" || oldpassword == null) {
        layer.tips('<?php echo (L("请输入旧登录密码")); ?>', '#oldpassword', { tips: 3 });
        return false;
    }
    if (newpassword == "" || newpassword == null) {
        layer.tips('<?php echo (L("请输入新登录密码")); ?>', '#newpassword', { tips: 3 });
        return false;
    }
    if (repassword == "" || repassword == null) {
        layer.tips('<?php echo (L("请输入确认新登录密码")); ?>', '#repassword', { tips: 3 });
        return false;
    }
    if (mobile_verify == "" || mobile_verify == null) {
        layer.tips('<?php echo (L("请输入短信验证码")); ?>', '#mobile_verify_password', { tips: 3 });
        return false;
    }

    $.post("<?php echo U('User/uppassword');?>", { mobile_verify: mobile_verify, oldpassword: oldpassword, newpassword: newpassword, repassword: repassword }, function(data) {
        if (data.status == 1) {
            layer.msg(data.info, { icon: 1 });
            window.setTimeout('window.location="<?php echo U('User/index');?>"',1000);
            // window.location = "<?php echo U('User/paypassword');?>";
        } else {
            layer.msg(data.info, { icon: 2 });
            if (data.url) {
                window.location = data.url;
            }
        }
    }, "json");
}
function UpPaypasswordSet() {
    var paypassword = $("#set_paypassword").val();
    var repaypassword = $("#set_repaypassword").val();
    var mobile_verify = $("#set_mobile_verify").val();

    if (paypassword == "" || paypassword == null) {
        layer.tips('<?php echo (L("请输入交易密码")); ?>', '#set_paypassword', { tips: 3 });
        return false;
    }
    if (repaypassword == "" || repaypassword == null) {
        layer.tips('<?php echo (L("请输入确认密码")); ?>', '#set_repaypassword', { tips: 3 });
        return false;
    }
    if (mobile_verify == "" || mobile_verify == null) {
        layer.tips('<?php echo (L("请输入短信验证码")); ?>', '#set_mobile_verify', { tips: 3 });
        return false;
    }

    $.post("<?php echo U('User/uppaypasswordset');?>", { mobile_verify: mobile_verify, paypassword: paypassword, repaypassword: repaypassword }, function(data) {
        if (data.status == 1) {
            layer.msg(data.info, { icon: 1 });
            window.setTimeout('window.location="<?php echo U('User/index');?>"',1000);
            // window.location = "<?php echo U('User/paypassword');?>";
        } else {
            layer.msg(data.info, { icon: 2 });
            if (data.url) {
                window.location = data.url;
            }
        }
    }, "json");
}
function UpPaypassword() {
    var oldpaypassword = $("#oldpaypassword").val();
    var newpaypassword = $("#newpaypassword").val();
    var repaypassword = $("#repaypassword").val();
    var mobile_verify = $("#mobile_verify_paypassword").val();

    if (oldpaypassword == "" || oldpaypassword == null) {
        layer.tips('<?php echo (L("请输入旧交易密码")); ?>', '#oldpaypassword', { tips: 3 });
        return false;
    }
    if (newpaypassword == "" || newpaypassword == null) {
        layer.tips('<?php echo (L("请输入新交易密码")); ?>', '#newpaypassword', { tips: 3 });
        return false;
    }
    if (repaypassword == "" || repaypassword == null) {
        layer.tips('<?php echo (L("请输入确认新密码")); ?>', '#repaypassword', { tips: 3 });
        return false;
    }
    if (mobile_verify == "" || mobile_verify == null) {
        layer.tips('<?php echo (L("请输入短信验证码")); ?>', '#mobile_verify_paypassword', { tips: 3 });
        return false;
    }

    $.post("<?php echo U('User/uppaypassword');?>", { mobile_verify: mobile_verify, oldpaypassword: oldpaypassword, newpaypassword: newpaypassword, repaypassword: repaypassword }, function(data) {
        if (data.status == 1) {
            layer.msg(data.info, { icon: 1 });
            window.setTimeout('window.location="<?php echo U('User/index');?>"',1000);
            // window.location = "<?php echo U('User/paypassword');?>";
        } else {
            layer.msg(data.info, { icon: 2 });
            if (data.url) {
                window.location = data.url;
            }
        }
    }, "json");
}
</script>

<script>
// 发送短息验证码 - 修改登录密码
function SendCode_pass() {
    var oldpassword = $("#oldpassword").val();
    var newpassword = $("#newpassword").val();
    var repassword = $("#repassword").val();

    if (oldpassword == "" || oldpassword == null) {
        layer.tips('<?php echo (L("请输入旧登录密码")); ?>', '#oldpassword', { tips: 3 });
        return false;
    }
    if (oldpassword == newpassword) {
        layer.tips('<?php echo (L("新登录密码跟原密码相同")); ?>', '#newpassword', { tips: 3 });
        return false;
    }
    if (newpassword == "" || newpassword == null) {
        layer.tips('<?php echo (L("请输入新登录密码")); ?>', '#newpassword', { tips: 3 });
        return false;
    }
    if (repassword == "" || repassword == null) {
        layer.tips('<?php echo (L("请输入确认新登录密码")); ?>', '#repassword', { tips: 3 });
        return false;
    }
    if (repassword != newpassword) {
        layer.tips('<?php echo (L("输入的密码不一致")); ?>', '#repassword', { tips: 3 });
        return false;
    }
    //layer.load(0, { shade: [0.5, '#6b6b6b'] });
    $('#regBtn_pass').attr("disabled", "disabled");
    $.post("<?php echo U('Verify/pass');?>", {}, function(data) {
        //layer.closeAll();
        if (data.status == 1) {
            layer.msg(data.info, {
                icon: 1
            });
			//parent.layer.msg(data.info, {shade: 0.3})
            var obj = $('#regBtn_pass');
            var wait = 120;
            var interval = setInterval(function() {
                obj.css('backgroundColor', '#8d9eff');
				obj.css('cursor', 'auto');
                obj.val(wait + '<?php echo (L("秒")); ?>');
                wait--;
                if (wait < 0) {
                    $('#regBtn_pass').removeAttr("disabled");
                    clearInterval(interval);
                    obj.val('<?php echo (L("获取验证码")); ?>');
                    obj.css('backgroundColor', '#4f64dc');
					obj.css('cursor', 'pointer');
                };
            }, 1000);
        } else {
            $('#regBtn_pass').removeAttr("disabled");
            /*layer.msg(data.info, {
                icon: 2
            });*/
			parent.layer.msg(data.info, {shade: 0.3})
        }
    }, "json");
}

// 发送短息验证码 - 设置交易密码
function SendCode_setpaypass() {
    var paypassword = $("#set_paypassword").val();
    var repaypassword = $("#set_repaypassword").val();
    var mobile_verify = $("#set_mobile_verify").val();

    if (paypassword == "" || paypassword == null) {
        layer.tips('<?php echo (L("请输入交易密码")); ?>', '#set_paypassword', { tips: 3 });
        return false;
    }
    if (repaypassword == "" || repaypassword == null) {
        layer.tips('<?php echo (L("请输入确认密码")); ?>', '#set_repaypassword', { tips: 3 });
        return false;
    }
    if (paypassword !== repaypassword) {
        layer.tips('<?php echo (L("输入密码不一致")); ?>', '#set_paypassword', { tips: 3 });
        return false;
    }
    //layer.load(0, { shade: [0.5, '#6b6b6b'] });
    $('#setBtn_paypass').attr("disabled", "disabled");
    $.post("<?php echo U('Verify/paypass');?>", {}, function(data) {
        //layer.closeAll();
        if (data.status == 1) {
            layer.msg(data.info, {
                icon: 1
            });
			//parent.layer.msg(data.info, {shade: 0.3})
            var obj = $('#setBtn_paypass');
            var wait = 120;
            var interval = setInterval(function() {
                obj.css('backgroundColor', '#8d9eff');
				obj.css('cursor', 'auto');
                obj.val(wait + '<?php echo (L("秒")); ?>');
                wait--;
                if (wait < 0) {
                    $('#setBtn_paypass').removeAttr("disabled");
                    clearInterval(interval);
                    obj.val('<?php echo (L("获取验证码")); ?>');
                    obj.css('backgroundColor', '#4f64dc');
					obj.css('cursor', 'pointer');
                };
            }, 1000);
        } else {
            $('#setBtn_paypass').removeAttr("disabled");
            /*layer.msg(data.info, {
                icon: 2
            });*/
			parent.layer.msg(data.info, {shade: 0.3})
        }
    }, "json");
}


// 发送短息验证码 - 修改交易密码
function SendCode_paypass() {
   var oldpaypassword = $("#oldpaypassword").val();
    var newpaypassword = $("#newpaypassword").val();
    var repaypassword = $("#repaypassword").val();

    if (oldpaypassword == "" || oldpaypassword == null) {
        layer.tips('<?php echo (L("请输入旧交易密码")); ?>', '#oldpaypassword', { tips: 3 });
        return false;
    }
    if (newpaypassword == "" || newpaypassword == null) {
        layer.tips('<?php echo (L("请输入新交易密码")); ?>', '#newpaypassword', { tips: 3 });
        return false;
    }
    if (repaypassword == "" || repaypassword == null) {
        layer.tips('<?php echo (L("请输入确认新密码")); ?>', '#repaypassword', { tips: 3 });
        return false;
    }
    if (newpaypassword != repaypassword) {
        layer.tips('<?php echo (L("设置的新密码输入不一致")); ?>', '#repaypassword', { tips: 3 });
        return false;
    }

    //layer.load(0, { shade: [0.5, '#8F8F8F'] });
    $('#regBtn_paypass').attr("disabled", "disabled");
    $.post("<?php echo U('Verify/paypass');?>", {}, function(data) {
        //layer.closeAll();
        if (data.status == 1) {
            layer.msg(data.info, {
                icon: 1
            });

            var obj = $('#regBtn_paypass');
            var wait = 120;
            var interval = setInterval(function() {
                obj.css('backgroundColor', '#8d9eff');
				obj.css('cursor', 'auto');
                obj.val(wait + '<?php echo (L("秒")); ?>');
                wait--;
                if (wait < 0) {
                    $('#regBtn_paypass').removeAttr("disabled");
                    clearInterval(interval);
                    obj.val('<?php echo (L("获取验证码")); ?>');
                    obj.css('backgroundColor', '#4f64dc');
					obj.css('cursor', 'pointer');
                };
            }, 1000);
        } else {
            $('#regBtn_paypass').removeAttr("disabled");
            /*layer.msg(data.info, {
                icon: 2
            });*/
			parent.layer.msg(data.info, {shade: 0.3})
        }
    }, "json");
}

function addGoogleSet() {
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        area: 'autos',
        skin: 'layui-layer-nobg',
        shadeClose: true,
        content: $('#addGoogleSet')
    });
}
function addGoogle() {
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        area: 'autos',
        skin: 'layui-layer-nobg',
        shadeClose: true,
        content: $('#addGoogle')
    });
}
function addPassword() {
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        area: 'autos',
        skin: 'layui-layer-nobg',
        shadeClose: true,
        content: $('#addPassword')
    });
}
function addPaypasswordSet() {
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        area: 'autos',
        skin: 'layui-layer-nobg',
        shadeClose: true,
        content: $('#addPaypasswordSet')
    });
}
function addPaypassword() {
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        area: 'autos',
        skin: 'layui-layer-nobg',
        shadeClose: true,
        content: $('#addPaypassword')
    });
}
function closeCharge() {
    layer.closeAll();
	window.location.reload();
}
</script>
<script>
$("title").html("<?php echo L('账户信息');?> - <?php echo C('web_title');?>");
$('#menu_1').addClass('on');
</script>
<div class="footer">
	<div class="footer-content">
		<div class="copyright-box">
			<a href="/"><img src="/Public/Home/rh_img/logo.png" width="121" height="45" alt="" /></a>
			<p><?php echo C('web_footer');?></p>
		</div>

		<div class="fc-box">
			<div class="footer-menus">
				<?php if(is_array($footer)): $i = 0; $__LIST__ = $footer;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo ($vo['url']); ?>"><?php echo L($vo['title']);?></a>
					<span class="line">/</span><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
			<div class="country-list">
				<a href="<?php echo U('?LANG=en-us');?>" title="English"><img src="/Public/Home/rh_img/icon_country1.jpg" /></a>
				<a href="<?php echo U('?LANG=zh-cn');?>" title="<?php echo L(中文简体);?>"><img src="/Public/Home/rh_img/icon_country2.jpg" /></a>
			</div>
		</div>
	</div>
</div>
		
<script type="text/javascript" src="/Public/Home/rh_js/swiper-4.3.3.min.js"></script>
<script>
	$(function() {
		var isShowLanguage= false;
		var mySwiper = new Swiper('.swiper-container', {
			loop: true,
			observer: true,
			grabCursor: true,
			autoHeight: true,
			preventClicks: false,
			preventLinksPropagation: true,
			paginationClickable: true,
			slidesPerView : 4,
			slidesPerGroup : 4,
			spaceBetween : 30,
			autoplay: {
				delay: 8000,
				disableOnInteraction: false,
			},
			pagination: {
				el: '.swiper-pagination',
				type: 'bullets',
				observer: true,
				clickable :true,
			},
			on: {
				init: function() {
					swiperAnimateCache(this); //隐藏动画元素 
					swiperAnimate(this); //初始化完成开始动画
				},
				slideChangeTransitionEnd: function() {
					swiperAnimate(this); //每个slide切换结束时也运行当前slide动画
				}
			}
		})

		$(".language").click(function() {
			if(isShowLanguage) {
				$(".language-box").hide();
				isShowLanguage = false;
				return false;
			}
			$(".language-box").show();
			isShowLanguage = true;
		})
		
		$(".uuser").mouseover(function (){  
            $(".uuser-box").show();
			isShowLanguage = true;
        }).mouseout(function (){  
            $(".uuser-box").hide();
			isShowLanguage = false;
        });  
			
		$(".menu-tabs .language-box p").click(function() {
			$(".language-box").hide();
			isShowLanguage = false;
		})
		
/*		$(".menu-tabs .uuser-box p").click(function() {
			$(".uuser-box").hide();
			isShowLanguage = false;
		})*/
		
		$(".uuser-box").hover(function() {
			$(".uuser-box").show();
			isShowLanguage = true;
		}, function () {
        	$(".uuser-box").hide();
			isShowLanguage = false;
        })
		
		$(".swiper-container").mouseover(function() {
			mySwiper.autoplay.stop();
		}).mouseleave(function() {
			mySwiper.autoplay.start();
		})

		document.getElementsByTagName("body")[0].addEventListener("click", function() {
			if($(".menu-tabs .language-box").css("display")=="block") {
				$(".language-box").hide();
				setTimeout(function() {
					isShowLanguage = false;
				},300)
			}
			if($(".menu-tabs .uuser-box").css("display")=="block") {
				$(".uuser-box").hide();
				setTimeout(function() {
					isShowLanguage = false;
				},300)
			}
		}, true);
	})
</script>
</body>
</html>
<script>

    function setCookie(name, value, expires, path, domain, secure) {
        var cookieText = encodeURIComponent(name) + '=' + encodeURIComponent(value);
        if (expires instanceof Date) {
            cookieText += '; expires=' + expires;
        }
        if (path) {
            cookieText += '; expires=' + expires;
        }
        if (domain) {
            cookieText += '; domain=' + domain;
        }
        if (secure) {
            cookieText += '; secure';
        }
        document.cookie = cookieText;
    }
    //js获取coookie方法
    function get_cookie(Name) {
        var search = Name + "=" //查询检索的值
        var returnvalue = ""; //返回值
        if (document.cookie.length > 0) {
            sd = document.cookie.indexOf(search);
            if (sd != -1) {
                sd += search.length;
                end = document.cookie.indexOf(";", sd);
                if (end == -1)
                    end = document.cookie.length;
                //unescape() 函数可对通过 escape() 编码的字符串进行解码。
                returnvalue = unescape(document.cookie.substring(sd, end))
            }
        }
        return returnvalue;
    }

</script>