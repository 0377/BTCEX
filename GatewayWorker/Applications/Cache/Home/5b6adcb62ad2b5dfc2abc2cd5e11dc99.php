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
<link rel="stylesheet" href="/Public/Home/rh_css/pass.css">
<div class="logsbox" style="margin-top:100px;margin-bottom:50px;" onkeydown="keyLogin();">
	<form id="form-login">
		<h2><?php echo L('登录');?></h2>
		<div class="form-tips"><?php echo L('还没账号？');?><a href="<?php echo U('Login/register');?>"><?php echo L('立即注册');?></a></div>
		<div class="form-group">
			<input type="text" autocomplete="off" name="username" id="login_username" placeholder="<?php echo L('手机号码');?>" onblur="if(this.value == '')this.placeholder='<?php echo L('手机号码');?>';" onclick="if(this.placeholder == '<?php echo L('手机号码');?>')this.placeholder='';" />
		</div>
		<div class="form-group">
			<input type="password" autocomplete="off" name="password" id="login_password" placeholder="<?php echo L('登录密码');?>" onblur="if(this.value == '')this.placeholder='<?php echo L('登录密码');?>';" onclick="if(this.placeholder == '<?php echo L('登录密码');?>')this.placeholder='';" />
		</div>
		<div class="form-group">
			<input type="text" autocomplete="off" name="code" id="login_verify" placeholder="<?php echo L('图形验证码');?>" onblur="if(this.value == '')this.placeholder='<?php echo L('图形验证码');?>';" onclick="if(this.placeholder == '<?php echo L('图形验证码');?>')this.placeholder='';" />
			<div class="imgcode">
				<img src="<?php echo U('Verify/code');?>" onclick="this.src=this.src+'?t='+Math.random()" title="<?php echo L('换一张');?>" id="verifycode">
			</div>
		</div>
		<div class="form-group">
			<input type="text" id="foot_ga" autocomplete="off" placeholder="<?php echo L('谷歌验证码（未绑定无需填写）');?>" onblur="if(this.value == '')this.placeholder='<?php echo L('谷歌验证码（未绑定无需填写）');?>';" onclick="if(this.placeholder == '<?php echo L('谷歌验证码（未绑定无需填写）');?>')this.placeholder='';" />
		</div>
		<div class="form-button">
			<input type="button" name="index_submit" id="loginSubmin" onclick="upLogin();" class="btn btn-primary" value="<?php echo L('登录');?>">
		</div>
	</form>
	<div class="form-other"><a href="<?php echo U('Login/findpwd');?>"><?php echo L('忘记密码');?>?</a></div>
</div>

<script type="text/javascript">
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
	// Remember the account number
	var cookieValue =get_cookie('cookie_username');
	if (cookieValue != '' && cookieValue != null) {
		$("#username").val(cookieValue);
		$("#autoLogin").attr("checked", true);
	}
    //创建cookie
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

	function upLogin() {
		var username = $("#login_username").val();
		var password = $("#login_password").val();
		var verify = $("#login_verify").val();
		var foot_ga = $("#foot_ga").val();

		if (username == "" || username == null) {
			layer.tips('<?php echo L('请输入手机号 ');?>','#login_username', { tips: 3 });
			return false;
		}
		if (password == "" || password == null) {
			layer.tips('<?php echo L('请输入登录密码 ');?>', '#login_password', { tips: 3 });
			return false;
		}
		if (verify == "" || verify == null) {
			layer.tips('<?php echo L('请输入验证码 ');?>', '#login_verify', { tips: 3 });
			return false;
		}

		$.post("<?php echo U('Login/submit');?>",{username:username,password:password,verify:verify,ga:foot_ga},function(data){
			if(data.status==1){
				if($("#autologin").attr("checked")=='checked'){
                    setCookie('cookie_username',username);
				}else{
                    setCookie('cookie_username',null);
				}
				$("#login_verify").val('');
				layer.msg(data.info,{icon:1});
                window.setTimeout("window.location='/'",1000);
			}else{
				$("#login_verify").val('');
				layer.msg(data.info,{icon:2});
                $('#verifycode').click();
				if(data.url){
                    window.setTimeout("window.location='/'",1000);
                }
			}
		},"json");
	}

	$('#menu_top_index').addClass('current');
	$('title').html('<?php echo L('用户登录');?> - '+'<?php echo L(C('web_title'));?>');
</script>
<script language="JavaScript">
function keyLogin() {
if (event.keyCode == 13) // The key value of the return key is 13
	upLogin();
}
</script>
<script type="text/javascript" src="/Public/Home/rh_js/swiper-4.3.3.min.js"></script>
<script type="text/javascript" src="/Public/Home/rh_js/swiper.animate1.0.3.min.js"></script>
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