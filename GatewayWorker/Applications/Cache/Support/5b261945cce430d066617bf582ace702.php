<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="renderer" content="webkit">
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
	<title><?php echo L('帮助中心');?></title>
	<meta name="Keywords" content="<?php echo L(C('web_keywords'));?>">
	<meta name="Description" content="<?php echo L(C('web_description'));?>">
</head>
<link rel="stylesheet" href="/Public/Home/rh_css/support.css">
<script type="text/javascript" src="/Public/Home/rh_js/swiper-4.3.3.min.js"></script>
<script type="text/javascript" src="/Public/Home/rh_js/jquery-1.12.0.min.js" ></script>
<script type="text/javascript" src="/Public/layer/layer.js"></script>
<body>
	<header class="header">
		<div class="logo">
			<a href="/"><img src="/Public/Home/rh_img/logo-support.png" /></a>
		</div>
		<div class="language">
			<?php if(LANG_SET=='zh-cn'){ $nm = '简体中文';}else{ $nm = 'English';}$cid = $_GET['cid'];$id = $_GET['id']; ?>
			<label><?php echo $nm;?></label>
			<img class="arrow-down" src="/Public/Home/rh_img/icon_arrow_support.png" />
		</div>
		<div class="language-box">
			<p><a href="<?php echo U('?LANG=en-us'.'&cid='.$cid.'&id='.$id);?>">English</a></p>
			<p><a href="<?php echo U('?LANG=zh-cn'.'&cid='.$cid.'&id='.$id);?>">简体中文</a></p>
		</div>
	</header>

<div class="main">
	<div class="container-divider"></div>
</div>

<div class="container">
	<nav class="sub-nav">
		<ol><?php echo L(GetPosStr($info['cid']));?></ol>

		<form role="search" class="search" method="get" action="<?php echo U('Support/index/search/');?>">
			<img class="icon" src="/Public/Home/rh_img/soso.png" />
			<input type="search" name="so" placeholder="<?php echo L('搜索');?>" autocomplete="off" value="<?php echo ($keyword); ?>">
		</form>
	</nav>

	<div class="category-content">
		<header class="page-header">
        	<h1><?php echo L($classinfo['title']);?></h1>
		</header>
		
		<div class="section-tree">
			<?php if(is_array($classify)): $i = 0; $__LIST__ = $classify;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc): $mod = ($i % 2 );++$i;?><section class="section">
					<h3><a href="<?php echo U('Support/index/sections/cid/'.$voc['pid'].'/id/'.$voc['id']);?>"><?php echo L($voc['title']);?></a></h3>
					<?php if((LANG == zh-cn)): ?><ul>
							<?php if(is_array($voc['voo'])): $i = 0; $__LIST__ = $voc['voo'];if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
									<?php if(($vo["recommend"]) == "1"): ?><img src="/Public/Home/rh_img/recommend.png" /><?php endif; ?>
									<a href="<?php echo U('Support/index/articles/cid/'.$voc['id'].'/id/'.$vo['id']);?>"><?php echo L($vo['title']);?></a>
								</li><?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
						<?php else: ?>
							<ul>
								<?php if(is_array($voc['voo'])): $i = 0; $__LIST__ = $voc['voo'];if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
										<?php if(($vo["recommend"]) == "1"): ?><img src="/Public/Home/rh_img/recommend.png" /><?php endif; ?>
										<a href="<?php echo U('Support/index/articles/cid/'.$voc['id'].'/id/'.$vo['id']);?>"><?php echo L($vo['title_en']);?></a>
									</li><?php endforeach; endif; else: echo "$empty" ;endif; ?>
							</ul><?php endif; ?>
					<!--<ul>
						<?php if(is_array($voc['voo'])): $i = 0; $__LIST__ = $voc['voo'];if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
								<?php if(($vo["recommend"]) == "1"): ?><img src="/Public/Home/rh_img/recommend.png" /><?php endif; ?>
								<a href="<?php echo U('Support/index/articles/cid/'.$voc['id'].'/id/'.$vo['id']);?>"><?php echo ($vo['title']); ?></a>
							</li><?php endforeach; endif; else: echo "$empty" ;endif; ?>
					</ul>-->
				</section><?php endforeach; endif; else: echo "$empty" ;endif; ?>
		</div>
		
	</div>
</div>

<footer class="footer">
	<div class="footer-inner"><a href="#"><?php echo L(C('help_footer'));?></a></div>
</footer>
		
<script>
	$(function() {
		var isShowLanguage= false;

		$(".language").click(function() {
			if(isShowLanguage) {
				$(".language-box").hide();
				isShowLanguage = false;
				return false;
			}
			$(".language-box").show();
			isShowLanguage = true;
		})

		$(".header .language-box p").click(function() {
			$(".language-box").hide();
			isShowLanguage = false;
		})

		document.getElementsByTagName("body")[0].addEventListener("click", function() {
			if($(".header .language-box").css("display")=="block") {
				$(".language-box").hide();
				setTimeout(function() {
					isShowLanguage = false;
				},300)
			}
		}, true);

	})
</script>
</body>
</html>