<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="renderer" content="webkit">
		<title><?php echo L(C('web_title'));?></title>
		<meta name="Keywords" content="<?php echo L(C('web_keywords'));?>">
		<meta name="Description" content="<?php echo L(C('web_description'));?>">
		<link rel="stylesheet" type="text/css" href="<?php echo stamp('/Public/Home/rh_css/main.css');?>">
		<script type="text/javascript" src="/Public/Home/rh_js/jquery-1.12.0.min.js"></script>
		<script type="text/javascript" src="<?php echo stamp('/Public/Home/news/js/jquery.SuperSlide.2.1.1.js');?>"></script>
		<script type="text/javascript" src="<?php echo stamp('/Public/Home/rh_js/jquery.cookies.2.2.0.js');?>"></script>
		<script type="text/javascript" src="/Public/layer/layer.js"></script>
	</head>
	<body>
		<div class="menu-tabs">
			<a class="logo" href="/"><img src="/Public/Home/rh_img/logo.png" /></a>
			<!--<a class="logo" href="/"><img src="/Home/rh_img/logo.png" /></a>-->
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
<link rel="stylesheet" href="<?php echo stamp('/Public/Home/rh_css/swiper-4.3.3.min.css');?>">
<style>
	.swiper-container-horizontal>.swiper-pagination-bullets, .swiper-pagination-custom, .swiper-pagination-fraction {
		bottom: 20px;
		left: 0;
		width: 100%;
	}
</style>
<!--<div class="banner">
=======
    bottom: 20px;
    left: 0;
    width: 100%;
}
</style>-->

<div class="banner" style="background-size: 100% 100%">

	<div class="banner-brief">
		<!--<p class="text-1 animated fadeInUp"><?php echo L('数字资产交易平台');?></p>
		<p class="text-2 animated fadeInUp"><?php echo L('让交易更<span>快捷</span>');?> , <?php echo L('让资产更<span>安全</span>');?></p>-->
		<p class="text-1 animated fadeInUp"></p>
		<p class="text-2 animated fadeInUp"></p>
	</div>

	<!--<div class="swiper-container">-->
		<!--<div class="swiper-container" style="margin-top: 250px!important;">-->

	<div class="swiper-container" style="margin-top: 250px!important;">
		<div class="swiper-wrapper">
			<?php if(is_array($banner)): $i = 0; $__LIST__ = $banner;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$b): $mod = ($i % 2 );++$i;?><div class="swiper-slide">
					<div class="icon-items animated zoomIn">
						<span class="img_icon" style="background:url(/Upload/ad/<?php echo ($b['img']); ?>) no-repeat center;background-size: 100%;"></span>
						<div class="text-content">
							<p><?php echo ($b['name']); ?></p>
							<p><?php echo (date('m月d日',$b['onlinetime'])); ?></p>
							<p><?php echo L($b['subhead']);?></p>
						</div>
					</div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
		<div class="swiper-pagination"></div>
	</div>
</div>
<ul class="main-advert">
	<?php if((LANG_SET == 'en-us')): if(is_array($notice_list)): $i = 0; $__LIST__ = $notice_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vov): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Support/index/articles/cid/'.$vov['pid'].'/id/'.$vov['id']);?>"><?php echo ($vov['title_en']); ?></a></li>
			<span>/</span><?php endforeach; endif; else: echo "" ;endif; ?>
		<?php else: ?>
			<?php if(is_array($notice_list)): $i = 0; $__LIST__ = $notice_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vov): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Support/index/articles/cid/'.$vov['pid'].'/id/'.$vov['id']);?>"><?php echo ($vov['title']); ?></a></li>
				<span>/</span><?php endforeach; endif; else: echo "" ;endif; endif; ?>
	<li><a href="<?php echo U('Support/index');?>"><?php echo L("更多公告");?></a></li>
</ul>

<div class="main-table-box5">
	<ul class="table-tab">
		<?php if(is_array($jiaoyiqu)): $i = 0; $__LIST__ = $jiaoyiqu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li class="trade_qu_pai <?php if(($key) == $coinnav): ?>active<?php endif; ?>" data="<?php echo ($key); ?>" onclick="trade_qu(this)" title="对<?php echo ($v); ?>交易"><?php echo ($v); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
	<ul class="table-head" id="market_coin_tabs">
		<li style="width: 15%"><i></i><?php echo L('交易对');?></li>
		<li style="width: 16%"><i></i><?php echo L('最新价格');?></li>
		<li style="width: 10%">24h <?php echo L('涨跌幅');?></li>
		<li style="width: 16%"><i></i>24h <?php echo L('最高价');?></li>
		<li style="width: 16%"><i></i>24h <?php echo L('最低价');?></li>
		<li style="width: 20%"><i></i>24h <?php echo L('成交量');?></li>
		<li style="width: 7%" class="tr"><?php echo L('操作');?><i></i></li>
	</ul>
	<ul class="table-item coins_list" id="coins_list"></ul>
</div>

<div class="main-summary">
	<h2 class="title">Global Blockchain Assets Trading Platform</h2>
	<div class="summary-content">
		<div class="item">
			<img src="/Public/Home/rh_img/img_icon/trait_001.gif"/>
			<h3><?php echo L('系统可靠');?></h3>
			<p><?php echo L('保障交易的安全、稳定、高效运行');?></p>
			<p><?php echo L('全球顶级人才构成的精英团队');?></p>
		</div>
		<div class="item">
			<img src="/Public/Home/rh_img/img_icon/trait_002.gif" />
			<h3><?php echo L('资金保障');?></h3>
			<p><?php echo L('银行级数据加密，动态身份验证');?></p>
			<p><?php echo L('冷钱包存储，金融级架构，多重安全防护');?></p>
		</div>
		<div class="item">
			<img src="/Public/Home/rh_img/img_icon/trait_003.gif" />
			<h3><?php echo L('极致体验');?></h3>
			<p><?php echo L('充提迅速、多国语言');?></p>
			<p><?php echo L('高性能撮合交易引擎，快速交易体验');?></p>
		</div>
		<div class="item">
			<img src="/Public/Home/rh_img/img_icon/trait_004.gif" />
			<h3><?php echo L('专业服务');?></h3>
			<p><?php echo L('专业的国际服务团队');?></p>
			<p><?php echo L('及时响应用户问题，为您保驾护航');?></p>
		</div>
	</div>
</div>

<div class="box-main-warings">
	<div class="main-warings">
		<img src="/Public/Home/rh_img/icon_waring.png" />
		<span><?php echo L('数字资产是创新的投资产品，价格波动较大，具有较高的投资风险，请您投资前对数字资产充分认知，理性判断自己的投资能力，审慎做出投资决策。');?></span>
	</div>
</div>

<div class="main-download">
	<div class="title">
		<h2><?php echo L("多终端覆盖");?></h2>
		<h3><?php echo L("支持iOS、Android、Windows、Mac等客户端支持全业务功能");?></h3>
	</div>
	<div class="listshow">
		<div class="list-ld">
			<img src="/Public/Home/rh_img/download-1.png" height="120" />
			<ul>
				<li><a onclick="Downloads()">MAC <?php echo L("下载");?></a></li>
				<li><a onclick="Downloads()"><?php echo L("Win32位");?> <?php echo L("下载");?></a></li>
				<li><a onclick="Downloads()"><?php echo L("Win64位");?> <?php echo L("下载");?></a></li>
			</ul>
		</div>
		<div class="list-rd">
			<img src="/Public/Home/rh_img/download-2.png" height="120" />
			<ul>
				<li><a onclick="Downloads()">IOS <?php echo L("下载");?></a></li>
				<li><a onclick="Download_Android()">Android <?php echo L("下载");?></a></li>
			</ul>
		</div>
	</div>
</div>

<script>
function trends() {
	$.getJSON('/ajax/index_b_trends?t=' + Math.random(), function (d) {
		trends = d;
		allcoin();

	});
}

function allcoin(cb) {
    var htmlHref = window.location.href;
    htmlHref = htmlHref.replace(/^http:\/\/[^/]+/, "");
    var addr = htmlHref.substr(htmlHref.lastIndexOf('/', htmlHref.lastIndexOf('/') - 1) + 1);
    var index = addr.lastIndexOf("\/");
    //js 获取字符串中最后一个斜杠后面的内容
    var addrLast = decodeURI(addr.substring(index + 1, addr.length));
    //js 获取字符串中最后一个斜杠前面的内容
    var str = decodeURI(addr.substring(0, index));
    var string = addrLast.substring(0,5);
    //console.log(addrLast+ "  " +string);

	var trade_qu_id = $('.table-tab .active').attr('data');
	$.get('/ajax/allcoin_a/id/' + trade_qu_id + '?t=' + Math.random()+'&lang='+string, cb ? cb : function (data) {
	   console.log(data);
		var datas;
		if (data.status == 1) { datas = data.url; }
		renderPage(datas);
		t = setTimeout('allcoin()', 5000);
	}, 'json');
}
function renderPage(ary) {
	var html = '';
	for (var i in ary) {
		ifcolor_1 = (ary[i][8] >= 0 ? 'btn-up' : 'btn-down');
		ifcolor_2 = (ary[i][8] >= 0 ? 'icon-up' : 'icon-down');
		html += '<li><dl><dt class="fl market" style="width: 15%"><i></i><a href="/trade/index/market/' + ary[i][9] + '/"><img src="/Upload/coin/' + ary[i][10] + '" width="22" height="22" /><span class="coin_name">' + ary[i][0] + '</span><span> / ' + ary[i][1] + '</span></a></dt><dd class="fl" style="width: 16%"><i></i>' + ary[i][2] + '</dd><dd class="fl float" style="width: 10%"><span class="' + ifcolor_1 + '"><i class="' + ifcolor_2 + '"></i>' + (parseFloat(ary[i][8]) < 0 ? '' : '+') + ((parseFloat(ary[i][8]) < 0.01 && parseFloat(ary[i][8]) > -0.01) ? "0.00" : (parseFloat(ary[i][8])).toFixed(2)) + '%</span></dd><dd class="fl" style="width: 16%"><i></i>' + ary[i][11] + '</dd><dd class="fl" style="width: 16%"><i></i>' + ary[i][12] + '</dd><dt class="fl deal" style="width: 20%"><div><p>' + ary[i][7] + ' ' + ary[i][0] + '</p><p>≈ ' + ary[i][5] + ' ' + ary[i][14] + '</p></div></dt><dd class="fl tr" style="width: 7%"><a href="/trade/index/market/' + ary[i][9] + '/"><img src="/Public/Home/rh_img/icon_operation.png" /></a><i></i></dd></dl></li>';
		
	}
	$('#coins_list').html(html);
}
function trade_qu(o){
	$('.trade_qu_pai').removeClass('active');
	$(o).addClass('active');
	allcoin();
}
trends();
</script>
<script>
function Downloads() {
	layer.msg("<?php echo L('敬请期待');?>");
	return false;
}
function Download_Android(){
	window.location.href='https://copy.im/a/R3b6mC';
}
</script>
<script type="text/javascript" src="/Public/Home/rh_js/swiper.animate1.0.3.min.js"></script>
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