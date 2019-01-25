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
		<ol><?php echo GetPosStr($info['cid'],$info['id']);?></ol>
		
		<form role="search" class="search" method="get" action="<?php echo U('Support/index/search/');?>">
			<img class="icon" src="/Public/Home/rh_img/soso.png" />
			<input type="search" name="so" placeholder="<?php echo L('搜索');?>" autocomplete="off" value="<?php echo ($keyword); ?>">
		</form>
	</nav>

	<div class="article-container">
		<section class="article-sidebar">
			<h3 class="sidenav-title"><?php echo L('此组别内的文章');?></h3>
			<ul>
				<?php if((LANG == zh-cn)): ?><li><a class="on"><?php echo ($data["title"]); ?></a></li>
					<?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Support/index/articles/cid/'.$vo['pid'].'/id/'.$vo['id']);?>"><?php echo ($vo['title']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php else: ?>
					<li><a class="on"><?php echo ($data["title_en"]); ?></a></li>
					<?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Support/index/articles/cid/'.$vo['pid'].'/id/'.$vo['id']);?>"><?php echo ($vo['title_en']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
				<!--<li><a class="on"><?php echo ($data["title_en"]); ?></a></li>
				<?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Support/index/articles/cid/'.$vo['pid'].'/id/'.$vo['id']);?>"><?php echo ($vo['title_en']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>-->
			</ul>
		</section>
		<article class="article">
			<header class="article-header">
				<?php if((LANG == zh-cn)): ?><h1 class="article-title"><?php echo L($data['title']);?></h1>
					<?php else: ?>
					<h1 class="article-title"><?php echo L($data['title_en']);?></h1><?php endif; ?>
				<!--<h1 class="article-title"><?php echo L($data.title);?></h1>-->
				<p class="times"><?php echo L('文章更新时间：'); echo (date("Y-m-d H:i:s",$data["endtime"])); ?></p>
			</header>
			<div class="article-content">
				<?php if((LANG == zh-cn)): echo ($data['content']); ?>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p><?php echo ($info['jieweibs']); ?></p>
					<p><?php echo (date("Y年m月d日",$data["endtime"])); ?></p>
					<?php else: ?>
					<?php echo ($data['content_en']); ?>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p><?php echo ($info['jieweibs']); ?></p>
					<p><?php echo (date("Y年m月d日",$data["endtime"])); ?></p><?php endif; ?>

			</div>
		</article>
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