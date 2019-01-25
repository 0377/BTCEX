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
	<div class="container-divider" style="display: none"></div>
	<section class="hero">
		<div class="hero-inner">
			<form role="search" class="search-full" method="get" action="<?php echo U('Support/index/search/');?>">
				<img class="icon" src="/Public/Home/rh_img/soso.png" />
				<input type="search" name="so" id="so" placeholder="<?php echo L('搜索');?>" autocomplete="off">
			</form>
		</div>
	</section>
</div>

<div class="container">
	<ul class="list">
		<?php if(is_array($classify)): $i = 0; $__LIST__ = $classify;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Support/index/categories/cid/'.$voc['id']);?>"><?php echo L($voc['title']);?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
	
	<div class="recent-activity">
		<h2 class="recent-activity-header"><?php echo L('最近的活动');?></h2>
		<ul class="recent-activity-list">
			<?php if((LANG == zh-cn)): if(is_array($classlist)): $i = 0; $__LIST__ = $classlist;if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
						<a href="<?php echo U('Support/index/articles/cid/'.$vo['pid'].'/id/'.$vo['id']);?>">
							<h3><?php echo L($vo['classname']);?></h3>
							<p><?php echo L($vo['title']);?></p>
						</a>
						<span><?php echo L('文章创建时间：'); echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></span>
					</li><?php endforeach; endif; else: echo "$empty" ;endif; ?>
				<?php else: ?>
				<?php if(is_array($classlist)): $i = 0; $__LIST__ = $classlist;if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
						<a href="<?php echo U('Support/index/articles/cid/'.$vo['pid'].'/id/'.$vo['id']);?>">
							<h3><?php echo L($vo['classname']);?></h3>
							<p><?php echo L($vo['title_en']);?></p>
						</a>
						<span><?php echo L('文章创建时间：'); echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></span>
					</li><?php endforeach; endif; else: echo "$empty" ;endif; endif; ?>
		</ul>
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