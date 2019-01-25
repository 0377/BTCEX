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
	<li><a id="menu_0" href="<?php echo U('Finance/index');?>"><?php echo L('我的资产');?><i></i></a></li>
	<?php if(($backstage) == "1"): ?><li><a id="menu_10" href="<?php echo U('Backstage/exchange');?>"><b><?php echo L('我的后台');?></b><i></i></a></li><?php endif; ?>
	<?php if(($backstage) == "2"): ?><li><a id="menu_10" href="<?php echo U('Backstage/super');?>"><b><?php echo L('我的后台');?></b><i></i></a></li><?php endif; ?>
	<li><a id="menu_1" href="<?php echo U('Finance/myzr');?>"><?php echo L('转入资产');?><i></i></a></li>
	<li><a id="menu_2" href="<?php echo U('Finance/myzc');?>"><?php echo L('转出资产');?><i></i></a></li>
	<li><a id="menu_3" href="<?php echo U('Finance/mywt');?>"><?php echo L('委托管理');?><i></i></a></li>
	<li><a id="menu_4" href="<?php echo U('Finance/mycj');?>"><?php echo L('成交查询');?><i></i></a></li>
<!--	<li><a id="menu_3" href="<?php echo U('');?>">RT <?php echo L('分红');?><i></i></a></li>-->
	<li><a id="menu_5" href="<?php echo U('Finance/invite');?>"><?php echo L('邀请奖励');?><i></i></a></li>
	<li><a id="menu_6" href="<?php echo U('User/index');?>"><?php echo L('安全中心');?><i></i></a></li>
</ul>
	</div>
	<div class="fr SettingRight">
		<div class="titles">
			<h3 class="fl"><?php echo L('委托管理');?></h3>
			
			<div class="select fr">
				<img src="/Upload/coin/<?php echo ($coin_list[$market_list[$market]['xnb']]['img']); ?>">
				<select name="market-selectTest" id="market-selectTest" class="selul">
					<?php if(is_array($market_list)): $i = 0; $__LIST__ = $market_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($market) == $key): ?><option value="<?php echo ($vo['name']); ?>" selected="selected"><?php echo (strtoupper($vo["xnb"])); ?> (<?php echo (strtoupper($vo["rmb"])); ?>)</option>
							<?php else: ?>
							<option value="<?php echo ($vo['name']); ?>"><?php echo (strtoupper($vo["xnb"])); ?> (<?php echo (strtoupper($vo["rmb"])); ?>)</option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
				</select>
			</div>
		</div>
		<div class="Column_LogonLog">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr class="title">
					<th width="150px"><?php echo L('币种');?></th>
					<th width="160px"><?php echo L('委托时间');?></th>
					<th width="100px" class="last">
						<select name="type-selectTest" id="type-selectTest" class="wd70">
							<option value="0"<?php if(($type) == "0"): ?>selected<?php endif; ?>><?php echo L('类型');?></option>
							<option value="1"<?php if(($type) == "1"): ?>selected<?php endif; ?>><?php echo L('买入');?></option>
							<option value="2"<?php if(($type) == "2"): ?>selected<?php endif; ?>><?php echo L('卖出');?></option>
						</select>
					</th>
					<th width="140px"><?php echo L('委托价格');?></th>
					<th width="140px"><?php echo L('委托数量');?></th>
					<th><?php echo L('已成交量');?></th>
					<th width="110px" class="last">
						 <select name="status-selectTest" id="status-selectTest">
							 <option value="0"<?php if(($status) == "0"): ?>selected<?php endif; ?>><?php echo L('委托状态');?></option>
							 <option value="1"<?php if(($status) == "1"): ?>selected<?php endif; ?>><?php echo L('交易中');?></option>
							 <option value="2"<?php if(($status) == "2"): ?>selected<?php endif; ?>><?php echo L('已完成');?></option>
							 <option value="3"<?php if(($status) == "3"): ?>selected<?php endif; ?>><?php echo L('已撤销');?></option>
						 </select>
					</th>
				</tr>
				<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						<td><?php echo ($coin_list[$market_list[$vo['market']]['xnb']]['title']); ?>  (<?php echo (strtoupper($market_list[$vo['market']]['rmb'])); ?>)</td>
						<td><?php echo (date('m-d H:i:s',$vo["addtime"])); ?></td>
						<td>
							<?php if(($vo["type"]) == "1"): ?><font class="buy"><?php echo L('买入');?></font>
								<?php else: ?>
								<font class="sell"><?php echo L('卖出');?></font><?php endif; ?>
						</td>
						<td style="text-transform:uppercase;"><?php echo (NumToStr($vo['price'])); ?> <?php echo Trade;?></td>
						<td><?php echo (NumToStr($vo['num'])); ?></td>
						<td><?php echo (NumToStr($vo['deal'])); ?></td>
						<td>
							<?php if(($vo["status"]) == "0"): echo L('交易中');?> | <a class="cancel" id="<?php echo ($vo["id"]); ?>" href="javascript:void(0);"><?php echo L('撤销');?></a><?php endif; ?>
							<?php if(($vo["status"]) == "1"): echo L('已完成'); endif; ?>
							<?php if(($vo["status"]) == "2"): echo L('已撤销'); endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</table>
			<div class="pages"><?php echo ($page); ?></div>
		</div>
	</div>
</div>

<script>
$("#type-selectTest,#status-selectTest,#market-selectTest").change(function() {
	var type = $("#type-selectTest option:selected").val();
	var status = $("#status-selectTest option:selected").val();
	var market = $("#market-selectTest option:selected").val();
	window.location = '/Finance/mywt/type/' + type + '/status/' + status + '/market/' + market + '.html';
});

$('.cancel').click(function() {

	$.post("<?php echo U('Trade/chexiao');?>", { id: $(this).attr('id'), }, function(data) {
		if (data.status == 1) {
			layer.msg(data.info, { icon: 1 });
			window.setTimeout("window.location='<?php echo U('Finance/mywt');?>'", 1000);
		} else {
			layer.msg(data.info, { icon: 2 });
		}
	});
});
</script>
<script>
$("title").html("<?php echo L('委托管理');?> - <?php echo C('web_title');?>");
$('#menu_3').addClass('on');
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