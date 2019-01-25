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
<style>
	body {background:#eceff0;}
	iframe{
		margin: 0 auto;
		width: 100%;
		height: 552px;
	}

</style>
<link rel="stylesheet" href="/Public/Home/rh_css/fb.css">
<link rel="stylesheet" href="/Public/Home/rh_css/exchange.css">

<ul class="main-advert">
	<?php if((LANG_SET == 'en-us')): if(is_array($notice_list)): $i = 0; $__LIST__ = $notice_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vov): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Support/index/articles/cid/'.$vov['pid'].'/id/'.$vov['id']);?>"><?php echo ($vov['title_en']); ?></a></li>
			<span>/</span><?php endforeach; endif; else: echo "" ;endif; ?>
		<?php else: ?>
		<?php if(is_array($notice_list)): $i = 0; $__LIST__ = $notice_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vov): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Support/index/articles/cid/'.$vov['pid'].'/id/'.$vov['id']);?>"><?php echo ($vov['title']); ?></a></li>
			<span>/</span><?php endforeach; endif; else: echo "" ;endif; endif; ?>
	<li><a href="<?php echo U('Support/index');?>"><?php echo L("更多公告");?></a></li>
</ul>
<div class="banner2">
	<div class="coin-btc-details">
		<div class="main-title">
			<img class="icon_coin-btc" src="/Upload/coin/<?php echo ($C['market'][$market]['xnbimg']); ?>?=123456" />
			<span class="coin-name" style="cursor: pointer"><?php echo ($coin_name); ?> / <?php echo ($coin_type); ?></span>
			<a href="#" class="coin-brief"><?php echo L("简介");?></a>
		</div>
		<ul class="total-box">
			<li class="total-price">
				<h3 class="prices" id="market_new_price">--</h3>
				<!--<p>≈ <?php echo ($rmbprice); ?> <?php echo ($market_coin); ?></p>-->
				<p>≈ <?php echo ($rmbprice); ?> CNY</p>
			</li>
			<li class="total-increase">
				<h3 id="market_change">--</h3>
				<p>24h <?php echo L("涨跌幅");?></p>
			</li>
			<li>
				<h3 class="prices" id="market_max_price">--</h3>
				<p>24h <?php echo L("最高价");?> (<?php echo ($coin_type); ?>)</p>
			</li>
			<li>
				<h3 class="prices" id="market_min_price">--</h3>
				<p>24h <?php echo L("最低价");?> (<?php echo ($coin_type); ?>)</p>
			</li>
			<li class="total-amount">
				<h3 id="market_volume">--</h3>
				<p>24h <?php echo L("成交量");?> (<?php echo ($coin_name); ?>)</p>
			</li>
		</ul>
	</div>
	<!--Candlestick chart-->
	<div id="kline">
	<!--	<div id="paint_chart" style="width: 1200px;height:500px;overflow: hidden;margin: 0 auto;">
			&lt;!&ndash;<iframe style="border-style: none;" border="0" width="100%" height="360" id="market_chart" src="/Trade/ordinary?market=<?php echo ($market); ?>"></iframe>&ndash;&gt;
		</div>-->
	</div>
	<!--Candlestick chart end-->
</div>
<div class="tables-content clear">
	<div class="main-table-box1">
		<ul class="table-section-title">
			<?php if(is_array($jiaoyiqu)): $i = 0; $__LIST__ = $jiaoyiqu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if($v == $coin_type): ?><!--     <li class="trade_qu_pai  <?php if(($key) == $coinnav): ?>active<?php endif; ?>" data="<?php echo ($key); ?>" onclick="trade_qu(this)" title="对<?php echo ($v); ?>交易"><?php echo ($v); ?></li>-->
					<li class="trade_qu_pai active " data="<?php echo ($key); ?>" onclick="trade_qu(this)" title="对<?php echo ($v); ?>交易"><?php echo ($v); ?></li>
					<?php else: ?>
					<li class="trade_qu_pai" data="<?php echo ($key); ?>" onclick="trade_qu(this)" title="对<?php echo ($v); ?>交易"  id="abc"><?php echo ($v); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
		</ul>

		<div class="table_coin_box">
			<ul class="table-head" >
				<li style="width: 33%"><i></i><?php echo L("市场");?></li>
				<li style="width: 42%"><i></i><?php echo L("价格");?></li>
				<li class="tl" style="width: 25%"><?php echo L("涨跌幅");?></li>
			</ul>
			<ul class="table-list" id="all_coin"></ul>
		</div>


	</div>
	<div class="trade-box">
		<div class="clear">
			<div class="form-box">
				<p class="form-title"><?php echo L("可用");?> <?php echo ($coin_type); ?><span id="buy_usable">--</span></p>
				<div class="form-text">
					<input type="text" name="price" id="buy_price"  placeholder="<?php echo L('请输入买入价');?>" autocomplete="off" value="<?php echo ($sell); ?>" />
					<span class="chain-name"><?php echo ($coin_type); ?></span>
				</div>
				<div class="form-text">
					<input type="text" name="num" id="buy_num" placeholder="<?php echo L('请输入买入数量');?>" autocomplete="off" />
					<span class="chain-name"><?php echo ($coin_name); ?></span>
				</div>

				<div class="form-text">
					<input type="password" name="pwtrade" id="buy_paypassword" placeholder="<?php echo L('请输入交易密码');?>" />
					<span class="chain-name3" onclick="layertpwd()"></span>
				</div>

				<p class="form-count clear">
					<label><?php echo L("交易额");?> <span id="buy_mum">0.00</span><span><?php echo ($coin_type); ?></span></label>
					<label><?php echo L("手续费");?> <?php echo C('market')[$market]['fee_buy'];?>%</label>
				</p>

				<?php if($_SESSION['userId']== '0'): ?><button type="button" class="btn-buy" onclick="tologin();"><?php echo L("请先登录");?></button>
					<?php else: ?>
					<button type="button" id="buybutton" onclick="ajaxDeal('buybutton');" class="btn-buy"><?php echo L("买入");?> <?php echo ($coin_name); ?></button><?php endif; ?>
			</div>
			<input type="hidden" name="TOKEN" value="<?php echo session('__token__');?>">
			<div class="form-box sell-box">
				<p class="form-title"><?php echo L("可用");?> <?php echo ($coin_name); ?><span id="sell_usable">--</span></p>
				<div class="form-text">
					<input type="text" name="price" id="sell_price" placeholder="<?php echo L('请输入卖出价');?>" autocomplete="off" value="<?php echo ($buy); ?>" />
					<span class="chain-name"><?php echo ($coin_type); ?></span>
				</div>
				<div class="form-text">
					<input type="text" name="num" id="sell_num" placeholder="<?php echo L('请输入卖出数量');?>" autocomplete="off" />
					<span class="chain-name"><?php echo ($coin_name); ?></span>
				</div>

				<div class="form-text">
					<input type="password" name="pwtrade" id="sell_paypassword" placeholder="<?php echo L('请输入交易密码');?>" />
					<span class="chain-name3" onclick="layertpwd()"></span>
				</div>

				<p class="form-count clear">
					<label><?php echo L("交易额");?> <span id="sell_mum">0.00</span><span><?php echo ($coin_type); ?></span></label>
					<label><?php echo L("手续费");?> <?php echo C('market')[$market]['fee_sell'];?>%</label>
				</p>

				<?php if($_SESSION['userId']== '0'): ?><button type="button" class="btn-buy" onclick="tologin();"><?php echo L("请先登录");?></button>
					<?php else: ?>
					<button type="button" id="sellerbutton" onclick="ajaxDeal('sellerbutton');" class="btn-buy"><?php echo L("卖出");?> <?php echo ($coin_name); ?></button><?php endif; ?>
			</div>
		</div>
		<div class="authorize-box">
			<div class="table-section-title2">
				<span class="active"><?php echo L("当前委托");?></span>
				<span><a href="/Finance/mywt/type/undefined/market/<?php echo ($market); ?>"><?php echo L("历史委托");?></a></span>
				<p class="r-btns">
					<a href="/Finance/mywt/type/undefined/status/undefined/market/<?php echo ($market); ?>">[<?php echo L("更多记录");?>]</a>
				</p>
			</div>

			<div class="entrust-box">
				<ul class="table-head">
					<li style="width: 20%"><i></i><?php echo L("委托时间");?></li>
					<li style="width: 25%"><i></i><?php echo L("委托价");?>(<?php echo ($coin_type); ?>)</li>
					<li style="width: 35%"><i></i><?php echo L("委托量");?> / <?php echo L("已成交");?>(<?php echo ($coin_name); ?>)</li>
					<li style="width: 10%" class="tc"><?php echo L("状态");?></li>
					<li style="width: 10%" class="tc"><?php echo L("交易");?></li>
				</ul>
				<ul class="table-list" id="entrustlist"></ul>
			</div>

		</div>
	</div>
	<div class="records-box">
		<div class="main-table-box2">
			<ul class="table-head">
				<li style="width: 20%"><i></i><?php echo L("档位");?></li>
				<li style="width: 40%"><i></i><?php echo L("价格");?>(<?php echo ($coin_type); ?>)</li>
				<li class="tr" style="width: 40%"><?php echo L("数量");?>(<?php echo ($coin_name); ?>)<i></i></li>
			</ul>
			<ul class="table-list green" id="selllist"></ul>
			<ul class="table-list red" id="buylist"></ul>
		</div>

		<div class="table-count-list">
			<ul class="table-head">
				<li style="width: 25%"><i></i><?php echo L("时间");?></li>
				<li style="width: 35%"><i></i><?php echo L("成交价");?>(<?php echo ($coin_type); ?>)</li>
				<li class="tr" style="width: 40%"><?php echo L("成交量");?>(<?php echo ($coin_name); ?>)<i></i></li>
			</ul>
			<ul class="table-list" id="orderlist">
				<?php echo ($orderlist); ?>
			</ul>
		</div>
	</div>
</div>


<div class="float_win_pay" id="layertpwd" style="display: none;">
	<div class="tan_title">
		<h4><?php echo L('交易密码输入设置');?></h4>
		<span class="closebut" onclick="closeCharge();"><img src="/Public/Home/news/images/closebut.png"></span>
	</div>
	<div class="payment_content">
		<form id="tpwdsetting" class="set_verify" style="padding-left:0px!important;">
			<ul class="tpwd">
				<li><label for="only"><input type="radio" id="only" value="1" name="aaatpwdsetting"> <?php echo L('每次登录只输入一次交易密码');?> </label></li>
				<li><label for="every"><input type="radio" checked id="every" value="2" name="aaatpwdsetting"> <?php echo L('每笔交易都输入交易密码');?></label></li>
				<li><label for="none"><input type="radio" id="none" name="aaatpwdsetting" value="3"> <?php echo L('每次交易都不需要输入交易密码');?></label></li>
				<li><input type="password" id="aaapaypassword" name="paypassword" placeholder="<?php echo L('请输入交易密码');?>" class="text"/></li>
			</ul>
			<div class="save_verify"><input type="button" value="<?php echo L('保存');?>" onclick="tpwdsettingaa()" /></div>
		</form>
	</div>
</div>
<div id="dialogBoxShadow" style="position: absolute; top: 0px; left: 0px; z-index: 100; background-color: rgb(0, 0, 0); opacity: 0.4; width: 100%; background-position: initial initial; background-repeat: initial initial; display: none;"></div>
<!--<script src="/Public/Home/js/echarts.js"></script>-->
<script>
	<!--点击滚屏-->
	$('.coin-name').click(function(){
        $("html,body").animate({ scrollTop: 832}, 200);
	});

</script>
<script>
    $("title").html("<?php echo C('web_title');?>");

 /*   $(function() {

        /!* getJsonTop();
         allcoin();*!/
    });*/

    /**
     * 	可能是涨跌幅信息
     */
    /*function getJsonTop(){
        $.getJSON("/Ajax/getJsonTop?market=<?php echo ($market); ?>&t=" + Math.random(),function(data){
            if(data){
                if(data['info']['new_price']){
                    $('#market_new_price').removeClass('buy');
                    $('#market_new_price').removeClass('sell');

                    if(data['info']['change'] == 0 || data['info']['change'] > 0){
                        $('#market_new_price').addClass('buy');
                    }else{
                        $('#market_new_price').addClass('sell');
                    }

                    $("#market_new_price").html(data['info']['new_price']);

                    $("title").html(data['info']['new_price'] + " | <?php echo ($coin_name); ?>-<?php echo ($coin_type); ?> | <?php echo C('web_title');?>");
                }
                if(data['info']['buy_price']){
                    $('#market_buy_price').removeClass('buy');
                    $('#market_buy_price').removeClass('sell');

                    if($("#market_buy_price").html()>data['info']['buy_price']){
                        $('#market_buy_price').addClass('sell');
                    }
                    if($("#market_buy_price").html()<data['info']['buy_price']){
                        $('#market_buy_price').addClass('buy');
                    }

                    $("#market_buy_price").html(data['info']['buy_price']);
                    $("#sell_best_price").html('￥'+data['info']['buy_price']);
                }
                if(data['info']['sell_price']){
                    $('#market_sell_price').removeClass('buy');
                    $('#market_sell_price').removeClass('sell');

                    if($("#market_sell_price").html()>data['info']['sell_price']){
                        $('#market_sell_price').addClass('sell');
                    }
                    if($("#market_sell_price").html()<data['info']['sell_price']){
                        $('#market_sell_price').addClass('buy');
                    }

                    $("#market_sell_price").html(data['info']['sell_price']);
                    $("#buy_best_price").html('￥'+data['info']['sell_price']);
                }
                if(data['info']['max_price']){
                    $("#market_max_price").html(data['info']['max_price']);
                }
                if(data['info']['min_price']){
                    $("#market_min_price").html(data['info']['min_price']);
                }
                if(data['info']['volume']){
                    if(data['info']['volume']>10000){
                        data['info']['volume']=(data['info']['volume']/10000).toFixed(2)+"万"
                    }
                    if(data['info']['volume']>100000000){
                        data['info']['volume']=(data['info']['volume']/100000000).toFixed(2)+"亿"
                    }
                    $("#market_volume").html(data['info']['volume']);
                }
                if(data['info']['change'] || data['info']['change'] == 0){
                    $('#market_change').removeClass('buy');
                    $('#market_change').removeClass('sell');

                    if(data['info']['change'] == 0){
                        $('#market_change').addClass('buy');
                        $("#market_change").html("+0.00%");
                    }else if(data['info']['change'] > 0){
                        $('#market_change').addClass('buy');
                        $("#market_change").html('+' + data['info']['change']+"%");
                    }else{
                        $('#market_change').addClass('sell');
                        $("#market_change").html(data['info']['change']+"%");
                    }
                }
            }
        });
        //setTimeout('getJsonTop()',5000);
    }*/

    /**
     * 交易区信息
     */
    function allcoin(){
        var trade_qu_id = $('.table-section-title .active').attr('data');
        $.getJSON("/Ajax/getJsonTop2/id/" + trade_qu_id + "?t=" + Math.random(), function (data) {
            console.log(data);
            if(data){
                var list='';
                for(var i in data['list']){
                    ifcolor = (data['list'][i]['change'] >= 0 ? 'red' : 'green');
                    //console.log(data['list'][i]['coin_name']);
                    list+='<li><dl><a href="/Trade/index/market/'+data['list'][i]['name']+'"><dt class="fl market" style="width: 33%"><i></i><img src="/Upload/coin/'+data['list'][i]['img']+'" width="20" height="20" alt="" /><span class="coin_name">'+data['list'][i]['coin_name']+'</span></dt><dd class="fl ' + ifcolor + '" style="width: 42%"><i></i>'+data['list'][i]['new_price']+'</dd><dd class="fl tl ' + ifcolor + '" style="width: 25%">' + (parseFloat(data['list'][i]['change']) < 0 ? '' : '+') + ((parseFloat(data['list'][i]['change']) < 0.01 && parseFloat(data['list'][i]['change']) > -0.01) ? "0.00" : (parseFloat(data['list'][i]['change'])).toFixed(2)) + '%<i></i></dd></a></dl></li>';
                }
                //console.log(list);

                $("#all_coin").html(list);
            }
        });
        //setTimeout('allcoin()',5000);
    }
      function trade_qu(o){
            $('.trade_qu_pai').removeClass('active');
            $(o).addClass('active');
            allcoin();
        }

   // $("title").html("<?php echo C('web_title');?>");

   /* $(function() {
        /!*	getJsonTop();
            allcoin();*!/
    });*/




</script>
<script src="https://cdn.bootcss.com/reconnecting-websocket/1.0.0/reconnecting-websocket.js"></script>
<script type="text/javascript">
    function KlineHistory(data) {
    }
    function KlineUpdata(data) {

    }
    /**
     * 设置cookie
     * */
  /*  function setCookie(name, value, expires, path, domain, secure) {
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
    }*/
    //过滤HTML标签
    function removeHTMLTag(str) {
        str = str.replace(/<\/?[^>]*>/g, ''); //去除HTML tag
        str = str.replace(/[ | ]*\n/g, '\n'); //去除行尾空白
        //str = str.replace(/\n[\s| | ]*\r/g,'\n'); //去除多余空行
        str = str.replace(/ /ig, ''); //去掉
        return str;
    }
    //获取cookie
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
    var trade_qu_id = $('.table-section-title .active').attr('data');
    var trade_moshi = 1;
    if(get_cookie('chart_time')){
        var time=get_cookie('chart_time');
	}else{
        var time='1440';
	}

    //alert(time);
    //   ws = new ReconnectingWebSocket("ws://"+document.domain+":8331");

    //   ws = new WebSocket("ws://"+document.domain+":8331");

    // ws = new ReconnectingWebSocket("ws://"+document.domain+":8331");
    	ws = new WebSocket("wss://www.wsex.cc/wss");
    //console.log(ws);
   // console.log("ws.readyState:"+ws.readyState)
    console.log(ws);
    // 服务端主动推送消息时会触发这里的onmessage
    ws.onmessage = function(e){
       
        // json数据转换成js对象
		var data = JSON.parse(e.data);
		console.log(data);
    /*    if(i==1){
            setCookie('client_id',data.client_id);
        }*/
        var type = data.type || '';

        switch(type){
            // Events.php中返回的init类型的消息，将client_id发给后台进行uid绑定
            case 'init':
            function getParameterByName(name) {
                name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                    results = regex.exec(location.search);
                return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
           	 }
                var config    =   {
                    supported_resolutions: ["5", "15", "30", "60", "360", "1D"],
                };
                var datafeed = {
                    onReady: cb => {
                        console.log("=====onReady running");
                       setTimeout(() => cb(config), 0);
                    },
                    resolveSymbol: (symbolName, onSymbolResolvedCallback, onResolveErrorCallback) => {
                        var symbol_stub = {
                            name: symbolName,
                            description: "",
                            has_intraday: true,
                            has_no_volume: false,
                            minmov: 1,
                            minmov2: 2,
                            pricescale: 100,
                            session: "24x7",
                            supported_resolutions: ["1", "5", "15", "30", "60", "1D", "1W"],
                            ticker: symbolName,
                            timezone: "Asia/Shanghai",
                            type: "stock"
                        }
                        setTimeout(() => onSymbolResolvedCallback(symbol_stub), 2000);
                    },
                    getBars: (symbolInfo, resolution, from, to, onHistoryCallback, onErrorCallback, firstDataRequest) => {
                        timer = setInterval(() => {
                            if(get_cookie('chart_time')){
                                var types = get_cookie('chart_time')
							}else{
                                var types = 1440
							}
                            //   alert(window.parent.socket.readyState);
                            if (window.parent.ws.readyState == 1){
                                var host = window.location.protocol;
                                var url =  window.location.host;
                                var info =  JSON.stringify({
                                    type: 'client',
                                    method: 'KlineHistory',
                                    data: {
                                        host:host,
                                        url:url,
                                        market: symbolInfo.name,
                                        from: from,
                                        to: to,
                                        resolution:types
                                    }
                                })

								//if(get_cookie('sendTime')<=get_cookie('returnTime')){
                               		 var timestamp=new Date().getTime();
                                	setCookie('sendTime',timestamp);
                                    window.parent.ws.send(info);
							//	}
                                clearInterval(timer);
                            }else{

                            }
                        }, 2000);
                        window.parent.KlineHistory = data =>{
                            let bars = [];
                            if (data){
                                data.forEach(e => {
                                    var _bar = {
                                        time: e.t * 1000,
                                        close: e.c,
                                        open: e.o,
                                        high: e.h,
                                        low: e.l,
                                        volume: e.v
                                    }
                                    bars.push(_bar);
                                });
                                meta = {
                                    noData:false
                                };
                                // onHistoryCallback(bars, meta)
                                //console.log(bars);
                                // console.log(meta);
                                //console.log(bars);
                                // console.log(noData);
                            }else{

                                meta = {
                                    noData: true,
                                }
                            }
                            setTimeout(() => onHistoryCallback(bars, meta),2000);
                        }
                        console.log('sss');
                    },

                    subscribeBars: (symbolInfo, resolution, onRealtimeCallback, subscribeUID, onResetCacheNeededCallback) => {
                        window.parent.KlineUpdata = data => {
                            if (!data.s)
                                return;
                            var bar = {
                                time: data.t * 1000,
                                close: data.c,
                                open: data.o,
                                high: data.h,
                                low: data.l,
                                volume: data.v
                            }
                            setTimeout(() => onRealtimeCallback(bar),1000);
                        }
                    },
                }

                const  widgetOptions={
                    // debug: true, // uncomment this line to see Library errors and warnings in the console
                    fullscreen: false,// 是否全屏
                    symbol: "<?php echo ($market); ?>", // 商品标识
                    toolbar_bg: 'red',    //工具栏背景颜色
                    interval: time, // 初始化显示时间范围
                    container_id: "kline", // 容器的id名
                    // BEWARE: no trailing slash is expected in feed URL
                    // 调用接口API hrc_usdt
                    datafeed: datafeed,
                    //  datafeed: new Datafeeds.UDFCompatibleDatafeed(""),
                    library_path: "/Public/Home/K_js/charting_library/",//调用本js图表地库和样式

                    // 语言 en英文 zh 中文

                    locale: getParameterByName('lang') || "zh",
                 //   disabled_features: [],// 禁用功能
                    enabled_features: ["study_templates"],
                    disabled_features:[
                        "header_indicators",
                        "header_compare",
                        "header_settings",
                        "use_localstorage_for_settings",
                        "display_market_status",
                        "volume_force_overlay",
                        "left_toolbar",

						// 'header_indicators',


                        /*  //左侧

                        //底部
                        "timeframes_toolbar",
                        //指标
                        "header_indicators",
                        //相机
                        "header_screenshot",
                        //搜索
                        "header_symbol_search",
                        //保存
                        "header_saveload",
                        //全屏
                        "header_fullscreen_button"*/
                        "header_widget_dom_node",
                        "header_symbol_search",
                        "header_saveload",
                        "header_screenshot",
                        "header_chart_type",
                        "header_compare",
                        "header_undo_redo",
                        "timeframes_toolbar",
                        "volume_force_overlay",
                        "header_resolutions",
                        //"header_widget_dom_node"
                        //"border_around_the_chart"
                        /*"timeframes_toolbar",
                        "header_symbol_search",

                        "header_undo_redo",
                        "header_screenshot",
                        "header_resoluitons",
                        "header_chart_type",
                        "header_interval_dialog_button",

                        "show_interval_dialog_on_key_press"*/


                    ],

                    overrides: {
                        'paneProperties.legendProperties.showLegend': false,

                        "paneProperties.background": "#323947",
                        //Y轴
                        "paneProperties.vertGridProperties.color": "#454545",
                        //X轴
                        "paneProperties.horzGridProperties.color": "#454545",
                        "symbolWatermarkProperties.transparency": 90,
                        "scalesProperties.textColor" : "#AAA"
                    },
                    studies_overrides: {
                        "bollinger bands.median.color": "#33FF88",
                        "bollinger bands.upper.linewidth": 7
                    },


                    charts_storage_url: 'http://saveload.tradingview.com',
                    charts_storage_api_version: "1.1",
                    client_id: 'tradingview.com',
                    user_id: 'public_user_id',
                    theme: getParameterByName('theme')
                }

                var widget = window.tvWidget = new TradingView.widget(widgetOptions);

                var thats = widget;
               var buttons = [
                    //   {title:'Time',resolution:'1',chartType:3},
                    {title:'1m',resolution:'1',chartType:1},
                    {title:'5m',resolution:'5',chartType:1},
                    {title:'15m',resolution:'15',chartType:1},
                    {title:'30m',resolution:'30',chartType:1},
                    {title:'1h',resolution:'60',chartType:1},
                    {title:'6h',resolution:'360',chartType:1},
                    {title:'1d',resolution:'1440',chartType:1},
                     {title:'1w',resolution:'1W',chartType:1}
                ];
                var studies = [];
            function createButton(buttons){
                for(var i = 0; i < buttons.length; i++){
                    (function(button){
                        // console.log(button);
                    /*    if(i==3){
                            thats.createButton().attr('title', button.title).addClass("mydate").text(button.title).on('click', function(e) {
                           // thats.createButton().attr('title', button.title).addClass("mydate").parents('.wrap-18oKCBRc-').css("background","#fffAE3").text(button.title).on('click', function(e) {
                                $(this).parents('.wrap-18oKCBRc-').css("background","#fffAE3").parent().siblings().children(".wrap-18oKCBRc-").css("background","#fff");
                                var type = $(this).text();
                                if(type=="5m"){
                                    setCookie('chart_time',5);
                                }else if(type=="15m"){
                                    setCookie('chart_time',15);
                                }else if(type=="30m"){
                                    setCookie('chart_time',30);
                                }else if(type=="1h"){
                                    setCookie('chart_time',60);
                                }else if(type=='6h'){
                                    setCookie('chart_time',360);
                                }else{
                                    setCookie('chart_time',1440);
                                }
                                /!*   if($(this).parent().hasClass('active')){
                                       return false;
                                   }

                                   localStorage.setItem('tradingview.resolution',button.resolution);
                                   localStorage.setItem('tradingview.chartType',button.chartType);
                                   $(this).parent().addClass('active').siblings('.active').removeClass('active');
                                   thats.chart().setResolution(button.resolution, function onReadyCallback() {});
                                   if(button.chartType != thats.chart().chartType()){
                                       thats.chart().setChartType(button.chartType);
                                       toggleStudy(button.chartType);
                                   }*!/

                            })
                        }else{*/
                            thats.createButton().attr('title', button.title).addClass("mydate").text(button.title).on('click', function(e) {
                                $(this).parents('.wrap-18oKCBRc-').css("background","#fffAE3").parent().siblings().children(".wrap-18oKCBRc-").css("background","#fff");
									var type = $(this).text();
									if(type =="1m"){
                                        setCookie('chart_time',1);
									}else if(type=="5m"){
									    setCookie('chart_time',5);
                                    //    var widgets = window.tvWidget = new TradingView.widget(typeWid(5));
									}else if(type=="15m"){
                                        setCookie('chart_time',15);
									}else if(type=="30m"){
                                        setCookie('chart_time',30);
                                    }else if(type=="1h"){
                                        setCookie('chart_time',60);
                                    }else if(type=='6h'){
                                        setCookie('chart_time',360);
									}else if(type=="1d"){
                                        setCookie('chart_time',1440);
                                    }else if(type=='1w'){
                                        setCookie('chart_time',10080);
									}
                                //toggleStudy(button.chartType);

                                /*   if($(this).parent().hasClass('active')){
                                       return false;
                                   }

                                   localStorage.setItem('tradingview.resolution',button.resolution);
                                   localStorage.setItem('tradingview.chartType',button.chartType);
                                   $(this).parent().addClass('active').siblings('.active').removeClass('active');
                                   thats.chart().setResolution(button.resolution, function onReadyCallback() {});
                                   if(button.chartType != thats.chart().chartType()){
                                       thats.chart().setChartType(button.chartType);
                                       toggleStudy(button.chartType);
                                   }*/
                            })
                       // }

                    })(buttons[i]);
                }
            }

            /**
             * 均线
             * */
            function createStudy(){
                var id = widget.chart().createStudy('Moving Average', false, false, [5], null, {'Plot.color': 'rgb(150, 95, 196)'});
                studies.push(id);
                id = widget.chart().createStudy('Moving Average', false, false, [10], null, {'Plot.color': 'rgb(116,149,187)'});
                studies.push(id);
                id = widget.chart().createStudy('Moving Average', false, false, [20],null,{"plot.color": "rgb(58,113,74)"});
                studies.push(id);
                id = widget.chart().createStudy('Moving Average', false, false, [30],null,{"plot.color": "rgb(118,32,99)"});
                studies.push(id);
            }
            function toggleStudy(chartType){
                var state = chartType == 3 ? 0 : 1;
                for(var i = 0; i < studies.length; i++){
                    thats.chart().getStudyById(studies[i]).setVisible(state);
                }
            }


                widget.onChartReady(function(){
                   //createStudy();
                    createButton(buttons)

                  //  toggleStudy(chartType)
                    //设置均线种类 均线样式
                    /*
                       //生成时间按钮
                       createButton(buttons);
						 thats.chart().setChartType(chartType);
                       toggleStudy(chartType);*/
                });
                //当图表被创建完成时触发


                /*widget.onChartReady(function(){
				})*/

                // 利用jquery发起ajax请求，将client_id发给后端进行uid绑定
                $.post(
                    "<?php echo url('/Wokerman/getInfo');?>",
                    {
                        'client_id': data.client_id,
                        'market':"<?php echo ($market); ?>",
                        'trade_moshi':trade_moshi,
                        'trade_qu_id':trade_qu_id,
                        'time':time,
                    },
                    function(data)
                    {
                        console.log('init:'+data+'111111111111');
                    },
                    'json');
                break;
            case 'lineK':
                var timestamp=new Date().getTime();
                setCookie('returnTime',timestamp);
                window[data.method](data.data)
                break
            case  'say':
                $("[name='TOKEN']").val(data.tokens);
                break;

            case 'getJsonTop':
                //未知
                getJsonTops(data.getJsonTop);
                // 我的财产 我的委托
                myGetzican(data.getEntrustAndUsercoin)
                //交易委托信息
                information(data.getDepth)
                //最新交易信息
                newestOrder(data.getTradelog)
                //币种行情
                currency(data.getJsonTop2);
                break

            // 当mvc框架调用GatewayClient发消息时直接alert出来
            default :
                alert(e.data);
        }
    };

    /**
     * 取消订单
     */
    function allcoin(){
        var trade_qu_id = $('.table-section-title .active').attr('data');
        $.getJSON("/Ajax/getJsonTop2/id/" + trade_qu_id + "?t=" + Math.random(), function (data) {
            if(data){
                var list='';
                for(var i in data['list']){
                    ifcolor = (data['list'][i]['change'] >= 0 ? 'red' : 'green');
                    list+='<li><dl><a href="/Trade/index/market/'+data['list'][i]['name']+'"><dt class="fl market" style="width: 33%"><i></i><img src="/Upload/coin/'+data['list'][i]['img']+'" width="20" height="20" alt="" /><span class="coin_name">'+data['list'][i]['coin_name']+'</span></dt><dd class="fl ' + ifcolor + '" style="width: 42%"><i></i>'+data['list'][i]['new_price']+'</dd><dd class="fl tl ' + ifcolor + '" style="width: 25%">' + (parseFloat(data['list'][i]['change']) < 0 ? '' : '+') + ((parseFloat(data['list'][i]['change']) < 0.01 && parseFloat(data['list'][i]['change']) > -0.01) ? "0.00" : (parseFloat(data['list'][i]['change'])).toFixed(2)) + '%<i></i></dd></a></dl></li>';
                }
                $("#all_coin").html(list);
            }
        });
//	setTimeout('allcoin()',5000);
    }
</script>

<!--<script src='http://cdn.bootcss.com/socket.io/1.3.7/socket.io.js'></script>-->
<script type="text/javascript" src="/Public/Home/K_js/charting_library/charting_library.min.js"></script>
<script type="text/javascript" src="/Public/Home/K_js/datafeeds/udf/dist/polyfills.js"></script>
<script type="text/javascript" src="/Public/Home/K_js/datafeeds/udf/dist/bundle.js"></script>

<script type="text/javascript">
    function tologin() { window.location.href="<?php echo U('Login/index');?>"; }
    var market = "<?php echo ($market); ?>";
    var market_round = "<?php echo ($C['market'][$market]['round']); ?>";
    var market_round_num = "<?php echo ($C['market'][$market]['round']); ?>";
    var market_round_total = parseInt(market_round)+parseInt(market_round_num);
    var heh = "<?php echo $C['market'][$market]['hou_price'];?>";

    //console.log("<?php echo $C['market'][$market]['hou_price'];?>");

    var userid = "<?php echo (session('userId')); ?>";
    var trade_moshi = 1;
    var getDepth_tlme = null;
    var trans_lock = 0;

    // Maximum buy
    $("#buy_usable").on("click",function() {
        $("#buy_num").val('0');
        $("#buy_mum").val('0.00');
        if($("#buy_num").val().trim() != ''){
            var buyusable = parseFloat( $('#buy_usable').html() ) || 0;
            var buyprice = parseFloat( $('#buy_price').val() ) || 0;
            if(buyusable && buyprice) {
                $("#buy_num").val((buyusable / buyprice).toFixed(market_round_num) * 1);
                $("#buy_mum").html(($('#buy_num').val() * $('#buy_price').val()).toFixed(8) * 1);
            }
        }
    })

    // Maximum sale
    $("#sell_usable").on("click",function() {
        $("#sell_num").val('0');
        $("#sell_mum").val('0.00');
        if($("#sell_price").val().trim() != 0){
            $("#sell_num").val($(this).html().replace(/[-\s]/g,""));
            $("#sell_mum").html(($('#sell_num').val() * $('#sell_price').val()).toFixed(8) * 1);
        }
    })

    // Transaction password set
    function layertpwd(){
       layer.open({
            type: 1,
            title: false,
            closeBtn: 0,
            area: 'autos',
            skin: 'layui-layer-nobg',
            shadeClose: true,
            content: $('#layertpwd')
        });

        $.get('/user/tpwdsetting', function(d){
            if (d==1) { $('#only').prop('checked', true); }
            if (d==2) { $('#every').prop('checked', true); }
            if (d==3) { $('#none').prop('checked', true); }
        })
    }

    function closeCharge() {
        layer.closeAll();
    }

    // Save the transaction password set
    function tpwdsettingaa() {
        var paypassword = $("#aaapaypassword").val();
        var tpwdsetting = $("input[name='aaatpwdsetting']:checked").val();
        if (paypassword == "" || paypassword == null) {
            layer.tips('请输入交易密码', '#aaapaypassword', {tips: 3});
            return false;
        }
        if (tpwdsetting == "" || tpwdsetting == null) {
            layer.tips('请输入选择一个', '#aaatpwdsetting', {tips: 3});
            return false;
        }
        $.post('/user/uptpwdsetting', {paypassword: paypassword, tpwdsetting: tpwdsetting}, function (d) {
            if (d.status) {
                layer.msg('设置成功', {icon: 1});
                window.location.reload();
            } else {
                layer.msg(d.info, {icon: 2});
            }
        }, 'json');
    }

    /*    $('#buy_price').blur(function () {
            var price = parseFloat($('#buy_price').val());
            var num = parseFloat($('#buy_num').val());
            var paypassword = $('#buy_paypassword').val();
            //var reg = /^(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*))$/;
            //var reg =   /^\d{1,6}$|^\d{1,11}[.]\d{1,8}$/;
            //var reg = /^\d{1,14}$|^\d{1,12}[.]\d{2}$/;
            var regNum = /^\d{1,14}$|^\d{1,10}[.]\d{1,4}$/;
            var reg = /^\d{1,14}$|^\d{1,12}[.]\d{1}$/;

            if(price==""||price==null||!reg.test(price)){
              //  $('#buy_price').attr('disabled',"disabled");

                layer.tips("<?php echo L('交易价格输入错误');?>",'#buy_price',{tips : 3 });
                trans_lock = 0;
                return false;
            }
            if(num==""||num==null||!regNum.test(num)){
                //$('#buy_nume').attr('disabled',"disabled");
                layer.tips("<?php echo L('交易数量输入错误');?>",'#buy_num',{tips : 3 });
                trans_lock = 0;
                return false;
            }
        });*/
    // Buy operation
    //买入
   /* function tradeadd_buy(){
        if(trans_lock){
            layer.msg("<?php echo L('不要重复提交');?>",{icon : 2 });
            return;
        }
        trans_lock = 1;
        var num = $('#buy_num').val()
        //加载层
        layer.load(2);
        //此处演示关闭
        /!*	setTimeout(function(){
                layer.closeAll('loading');
                trans_lock = 0;
            }, 10000);*!/
        $.ajax({
            url:"<?php echo U('Trade/upTrade');?>",
            type:"POST",
            data:{
                'price' : $('#buy_price').val(),
                'num' :num,
                'paypassword' : $('#buy_paypassword').val(),
                'market' : market,
                'type' : 1 ,
                'trade_qu_id': $('.table-section-title .active').attr('data'),


// <<<<<<< HEAD
//             },
//             success:function (e) {
//                 // var reg=/<[^<>]+>/g ;
//                 console.log(e);
//                 // var mes = e.replace(reg,"");
//                 // var arr =  mes.replace(/float\(\d\)+/g,'');
//                 // var data = JSON.parse(e);
//                 var data = e;
// =======

			},
			success:function (e) {

                if(e instanceof Object){
                    layer.closeAll('loading');
                    trans_lock = 0;
                    var data=e;
                    layer.msg(data.info,{icon : 2 });
                    return;
				}
                var reg=/<[^<>]+>/g ;
				var mes = e.replace(reg,"");
                var arr =  mes.replace(/float\(\d\)+/g,'');
				var data = JSON.parse(arr);

                if(data.status==1){
                    layer.closeAll('loading');
                    trans_lock = 0;
                    $("#buy_price").val('');
                    $("#buy_num").val('');
                    $('#buy_paypassword').val('');
                    layer.msg(data.info,{icon : 1 });

                }else{
                    layer.closeAll('loading');
                    trans_lock = 0;
                    layer.msg(data.info,{icon : 2 });
                }

            }
        });
        /!*   $.post("<?php echo U('Trade/upTrade');?>",{
               'price' : $('#buy_price').val(),
               'num' : $('#buy_num').val(),
               'paypassword' : $('#buy_paypassword').val(),
               'market' : market,
               'type' : 1 ,
               'trade_qu_id': $('.table-section-title .active').attr('data'),
           },function(data){
               console.log(123);
               //data = removeHTMLTag(data);
              /!* layer.closeAll('loading');
               trans_lock = 0;*!/
               console.log(data);
            /!*   if(data.status==1){

                   $("#buy_price").val('');
                   $("#buy_num").val('');
                   $("#sell_price").val('');
                   $("#sell_num").val('');
                   $('#buy_paypassword').val('');

                   layer.msg(data.info,{icon : 1 });
               }else{
                   layer.msg(data.info,{icon : 2 });
               }*!/
           },'json');*!/
    }*/
    function ajaxDeal(type) {
		var tokens = $("[name='TOKEN']").val();

        if(type=='buybutton' ){
           var types=1;
           var num =$('#buy_num').val();
           var password =  $('#buy_paypassword').val();
           var price = $('#buy_price').val();
		}else if(type=='sellerbutton' ){
            var types=2;
            var num =$('#sell_num').val();
            var password =  $('#sell_paypassword').val();
            var price = $('#sell_price').val();
		}else{
            return
		}
        num = removeHTMLTag(num);
        password =removeHTMLTag(password);
        price = removeHTMLTag(price);

        if(trans_lock){
            layer.msg("<?php echo L('不要重复提交');?>",{icon : 2 }); return;
        }
        trans_lock = 1;
        layer.load(2);
        $.ajax({
            url: "<?php echo U('Trade/upTrade');?>",
            type: "POST",
            data: {
                'price': price,
                'num': num,
                'paypassword': password,
                'type': types,
                'market': market,
                'trade_qu_id': $('.table-section-title .active').attr('data'),
				'tokens':tokens,
            },
            success: function (e) {
                layer.closeAll('loading');
                trans_lock = 0;


            //    $("[name='TOKEN']").val("<?php echo session('__token__');?>");
                $('#buy_paypassword').val('');
                $('#sell_paypassword').val('');
                if (e instanceof Object) {

                    layer.msg(e.info, {icon: 2});
                    return;
                }
                var reg = /<[^<>]+>/g;
                var mes = e.replace(reg, "");
                var arr = mes.replace(/float\(\d\)+/g, '');
                var data = JSON.parse(arr);
                if (data.status == 1) {
                    $("#buy_price").val('');
                    $("#buy_num").val('');
                    $('#buy_paypassword').val('');
                    $("#sell_price").val('');
                    $("#sell_num").val('');
                    $('#sell_paypassword').val('');
                    layer.msg(data.info, {icon: 1});
                } else {
                    layer.msg(data.info, {icon: 2});
                }
            }
        })
    }

    // Selling operation
    //卖出
   /* function tradeadd_sell(){
        if(trans_lock){
            layer.msg("<?php echo L('不要重复提交');?>",{icon : 2 }); return;
        }
        trans_lock = 1;
        var price=parseFloat($('#sell_price').val());
        var num=parseFloat($('#sell_num').val());
        var paypassword=$('#sell_paypassword').val();
        var reg = /^(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*))$/;

        if(price==""||price==null||!reg.test(price)){
            layer.tips("<?php echo L('交易价格必须大于');?>0",'#sell_price',{tips : 3 });
            trans_lock = 0;
            return false;
        }
        if(num==""||num==null||!reg.test(num)){
            layer.tips("<?php echo L('交易数量必须大于');?>0",'#sell_num',{tips : 3 });
            trans_lock = 0;
            return false;
        }
        if(paypassword==""||paypassword==null){
            layer.tips("<?php echo L('请输入交易密码');?>",'#sell_paypassword',{tips : 3 });
            trans_lock = 0;
            return false;
        }

        //加载层
        layer.load(2);
        //此处演示关闭
        /!*	setTimeout(function(){
                layer.closeAll('loading');
                trans_lock = 0;
            }, 10000);*!/

        $.post("<?php echo U('Trade/upTrade');?>",{
            "price" : $('#sell_price').val(),
            "num" : $('#sell_num').val(),
            "paypassword" : $('#sell_paypassword').val(),

            'type' : 2,
            "market" : market,
            'trade_qu_id': $('.table-section-title .active').attr('data'),

        },function(data){
            layer.closeAll('loading');
            trans_lock = 0;
            $('#sell_paypassword').val('');
            if(data instanceof Object){

                layer.msg(data.info,{icon : 2 });
                return;
            }
            if(data.status==1){
                $("#sell_price").val('');
                $("#sell_num").val('');
                $('#sell_paypassword').val('');
                layer.msg(data.info,{icon : 1 });
            }else{

                layer.msg(data.info,{icon : 2 });
            }
        },'json');
    }*/

    // 我的财产 我的委托
    function getEntrustAndUsercoin(){
        $.getJSON("/Ajax/getEntrustAndUsercoin?market="+market+"&t="+Math.random(),function(data){
            if(data){
                if(data['entrust']){
                    $('#entrust_over').show();
                    var list='';
                    var cont=data['entrust'].length;
                    var status_name = '--';

                    // 委托信息
                    for(i=0; i<data['entrust'].length; i++){
                        if(data['entrust'][i]['type']==1){
                            if(data['entrust'][i]['status'] == 0){status_name = "<?php echo L('交易中');?>";}
                            else if(data['entrust'][i]['status'] == 1){status_name = "<?php echo L('已完成');?>";}
                            else if(data['entrust'][i]['status'] == 2){status_name = "<?php echo L('已撤销');?>";}

                            list+='<li><dl><dd class="fl" style="width: 20%"><i></i>'+data['entrust'][i]['addtime']+'</dd><dd class="fl red" style="width: 25%"><i></i><?php echo L('买');?> '+(data['entrust'][i]['price']).toFixed(market_round)+'</dd><dd class="fl red" style="width: 35%"><i></i>'+(data['entrust'][i]['num']).toFixed(market_round_num)+' / '+(data['entrust'][i]['deal']).toFixed(market_round_num)+'</dd><dd class="fl tc"  style="width: 10%">'+status_name+'</dd><dd class="fl tc" style="width: 10%"><a class="green" id="'+data['entrust'][i]['id']+'" onclick="cancelaa(\''+data['entrust'][i]['id']+'\')" href="javascript:void(0);"><?php echo L('取消');?></a></dd></dl></li>';
                        }else{
                            if(data['entrust'][i]['status'] == 0){status_name = "<?php echo L('交易中');?>";}
                            else if(data['entrust'][i]['status'] == 1){status_name = "<?php echo L('已完成');?>";}
                            else if(data['entrust'][i]['status'] == 2){status_name = "<?php echo L('已撤销');?>";}

                            list+='<li><dl><dd class="fl" style="width: 20%"><i></i>'+data['entrust'][i]['addtime']+'</dd><dd class="fl green" style="width: 25%"><i></i><?php echo L('卖');?> '+(data['entrust'][i]['price']).toFixed(market_round)+'</dd><dd class="fl green" style="width: 35%"><i></i>'+(data['entrust'][i]['num']).toFixed(market_round_num)+' / '+(data['entrust'][i]['deal']).toFixed(market_round_num)+'</dd><dd class="fl tc"  style="width: 10%">'+status_name+'</dd><dd class="fl tc" style="width: 10%"><a class="green" id="'+data['entrust'][i]['id']+'" onclick="cancelaa(\''+data['entrust'][i]['id']+'\')" href="javascript:void(0);"><?php echo L('取消');?></a></dd></dl></li>';
                        }
                    }
                    $('#entrustlist').html(list);
                }else{
                    $('#entrust_over').hide();
                }

                // 我的财产信息
                if(data['usercoin']){
                    if(data['usercoin']['rmb']){
                        $("#buy_usable").html(data['usercoin']['rmb'].toFixed(5) * 1);
                        if($('#buy_price').val()>0){
                            $("#buy_max").html(((data['usercoin']['rmb']/$('#buy_price').val()).toFixed(market_round_num) * 1));
                        }else{
                            $("#buy_max").html(0);
                        }
                    }else{
                        $("#buy_usable").html('0.00');
                    }

                    if(data['usercoin']['rmbd']){
                        $("#buy_usabled").html(data['usercoin']['rmbd'].toFixed(market_round_num) * 1);
                    }else{
                        $("#buy_usabled").html('0.00');
                    }

                    if(data['usercoin']['xnb']){
                        $("#sell_usable").html(data['usercoin']['xnb'].toFixed(market_round_num) * 1);
                        $("#sell_max").html(data['usercoin']['xnb'].toFixed(market_round_num) * 1);
                    }else{
                        $("#sell_usable").html('0.00');
                    }

                    if(data['usercoin']['xnbd']){
                        $("#sell_usabled").html(data['usercoin']['xnbd'].toFixed(market_round_num) * 1);
                    }else{
                        $("#sell_usabled").html('0.00');
                    }

                    if(data['time_state']==0){
                        $("#buybutton").show();
                        $("#buybutton").attr("onclick","ajaxDeal('buybutton');");
                        $("#sellerbutton").show();
                        $("#sellerbutton").attr("onclick","ajaxDeal('sellerbutton');");
                    }else{
                        $("#buybutton").show();
                        $("#buybutton").val("<?php echo L('闭盘期间');?>");
                        $("#buybutton").css("background","#ccc");
                        $("#buybutton").attr("onclick","ajaxDeal('buybutton');");
                        $("#sellerbutton").show();
                        $("#sellerbutton").val("<?php echo L('闭盘期间');?>");
                        $("#sellerbutton").css("background","#ccc");
                        $("#sellerbutton").attr("onclick","ajaxDeal('sellerbutton');");
                    }
                }
            }
        });

        // 账户总资产
        /*$.get("/Ajax/allfinance?t="+Math.random()+'&ajax=json',function(data){
             $('#user_finance').html(toNum(data,2));//原显示
            var data = JSON.parse(data);
            $('#user_finance').html(data);//千分位显示
        });*/
        //      setTimeout('getEntrustAndUsercoin()',1000);
    }

    // 最新交易记录
    function getTradelog(){
        $.get("/Ajax/getTradelog?market="+market+"&t="+Math.random()+'&ajax=json',function(data){

            if(data){
                data = JSON.parse(data);
                if(data['tradelog']){

                    var list='';
                    var type='';
                    var typename='';

                    for( var i in data['tradelog']){
                        if(data['tradelog'][i]['type']==1){
                            list+='<li class="red"><dl onclick="autotrust(this,\'buy\',1)"><dd class="fl" style="width:25%"><i></i>'+data['tradelog'][i]['addtime']+'</dd><dd class="fl" style="width:35%"><i></i>'+(data['tradelog'][i]['price']).toFixed(market_round_num)+'</dd><dd class="fl tr" style="width:40%">'+(data['tradelog'][i]['num']).toFixed(4)+'<i></i></dd></dl></li>';
                        }else{
                            list+='<li class="green"><dl onclick="autotrust(this,\'sell\',1)"><dd class="fl" style="width:25%"><i></i>'+data['tradelog'][i]['addtime']+'</dd><dd class="fl" style="width:35%"><i></i>'+(data['tradelog'][i]['price']).toFixed(market_round_num)+'</dd><dd class="fl tr" style="width:40%">'+(data['tradelog'][i]['num']).toFixed(4)+'<i></i></dd></dl></li>';
                        }
                    }
                    $("#orderlist").html(list);
                }
            }
        });
        // setTimeout('getTradelog()',5000);
    }

    // 交易买卖委托信息
    function getDepth(){
        $.get("/Ajax/getDepth?market="+market+"&trade_moshi="+trade_moshi+"&t="+Math.random()+'&ajax=json',function(data){
            var data = JSON.parse(data);
            if(data){
                if(data['depth']){
                    var list='';
                    var sellk=data['depth']['sell'].length;
                    if(data['depth']['sell']){
                        for(i=0; i<data['depth']['sell'].length; i++){
                            list += '<li><dl onclick="autotrust(this,\'sell\',1,2)"><dd class="fl" style="width:20%"><i></i><?php echo L('卖');?>'+(sellk-i)+'</dd><dd class="fl" style="width:40%"><i></i>'+(data['depth']['sell'][i][0]).toFixed(market_round_num)+'</dd><dd class="fl tr" style="width:40%">'+(data['depth']['sell'][i][1]).toFixed(4)+'<i></i><i class="turntable_bg_green" style="width: '+(data['depth']['sellpbar'][i])+'%"></i></dd></dl></li>';
                        }
                    }
                    $("#selllist").html(list);
                    list='';
                    if(data['depth']['buy']){
                        for(i=0; i<data['depth']['buy'].length; i++){
                            list += '<li><dl onclick="autotrust(this,\'buy\',1,2)"><dd class="fl" style="width:20%"><i></i><?php echo L('买');?>'+(i+1)+'</dd><dd class="fl" style="width:40%"><i></i>'+(data['depth']['buy'][i][0]).toFixed(market_round_num)+'</dd><dd class="fl tr" style="width:40%">'+(data['depth']['buy'][i][1]).toFixed(4)+'<i></i><i class="turntable_bg_red" style="width: '+(data['depth']['buypbar'][i])+'%"></i></dd></dl></li>';
                        }
                    }
                    $("#buylist").html(list);
                }
            }
        });
        clearInterval(getDepth_tlme);

        var wait=second=1;
        /*getDepth_tlme=setInterval(function(){
            wait--;
            if(wait<0){
                clearInterval(getDepth_tlme);
                getDepth();
                wait=second;
            }
        },100000);*/
    }

  /*  function closetanchu(){
        layer.closeAll('loading');
    }*/

    //撤销
    function cancelaa(id){
        layer.load(0, {shade: [0.5,'#8F8F8F']});
        $.post("<?php echo U('Trade/chexiao');?>",{
            'id' : id ,
            'trade_qu_id': $('.table-section-title .active').attr('data'),
            "market" : market,
        },function(data){
            //   setTimeout("closetanchu()",4000);
            if(data.status==1){
                layer.closeAll('loading');
                trans_lock = 0;
                // getEntrustAndUsercoin();
                layer.msg(data.info,{icon : 1 });
            }else{
                layer.closeAll('loading');
                trans_lock = 0;
                layer.msg(data.info,{icon : 2 });
            }
        });
    }

    // 执行函数
  /*  $(function(){
        //    getTradelog()
        //  getDepth();
        /!* getJsonTop();
        ;*!/
        /!*  if(userid>0){
              getEntrustAndUsercoin();
          }else{
              $('#entrust_over').hide();
          }*!/
        // xianshi();
    });*/


   /* function xianshi(){
        $('#market_change1').html(parseInt(heh).toFixed(market_round_num))
    }*/

    function toNum(num,round){
        var mum=Math.round(num*Math.pow(10,round))/Math.pow(10,round);
        if(mum<0){var mum=0;}
        return mum;
    }

    // Price check价格检查
    function autotrust(_this, type, cq, nums) {
        $('#buy_price,#sell_price').val( parseFloat(removeHTMLTag($(_this).children().eq(cq).html())) );

        if(nums > 0){
            var bs_num = parseFloat(removeHTMLTag($(_this).children().eq(nums).html()));
        }

        if(type == 'sell'){
            /*		if ($("#buy_usable").html() > 0 && nums > 0) {
                        //$('#buy_num').val(bs_num);
                        if (bs_num >= $("#buy_usable").html()) {
                            $("#buy_num").val(toNum(($("#buy_usable").html() / $('#buy_price').val()), market_round_num));
                        } else {
                            $('#buy_num').val(bs_num);
                        }
                    }*/

            if ($('#buy_num').val()) {
                $('#buy_num').val();
                $("#buy_mum").html(($('#buy_num').val() * $('#buy_price').val()).toFixed(8) * 1);
            } else {
                $('#buy_num').val('');
                $('#buy_mum').html('0.00');
            }

            if ($('#sell_num').val()) {
                $('#sell_num').val()
                $("#sell_mum").html(($('#sell_num').val() * $('#sell_price').val()).toFixed(8) * 1);
            } else {
                $('#sell_num').val('');
                $('#sell_mum').html('0.00');
            }
        }

        if(type == 'buy'){
            /*		if ($("#sell_usable").html() > 0 && nums > 0) {
                        if (bs_num >= $("#sell_usable").html()) {
                            $("#sell_num").val($("#sell_usable").html());
                        } else {
                            $('#sell_num').val(bs_num);
                        }
                    }*/

            if ($('#buy_num').val()) {
                $('#buy_num').val();
                $("#buy_mum").html(($('#buy_num').val() * $('#buy_price').val()).toFixed(8) * 1);
            } else {
                $('#buy_num').val('');
                $('#buy_mum').html('0.00');
            }

            if ($('#sell_num').val()) {
                $('#sell_num').val()
                $("#sell_mum").html(($('#sell_num').val() * $('#sell_price').val()).toFixed(8) * 1);
            } else {
                $('#sell_num').val('');
                $('#sell_mum').html('0.00');
            }
        }
    }



    // 买卖价格与数量 绑定键盘与选择事件
    $('#buy_price,#buy_num,#sell_price,#sell_num').css("ime-mode","disabled").bind('keyup change',function(){
        var buyprice=parseFloat($('#buy_price').val());
        var buynum=parseFloat($('#buy_num').val());
        var sellprice=parseFloat($('#sell_price').val());
        var sellnum=parseFloat($('#sell_num').val());
        var buymum=buyprice*buynum;
        var sellmum=sellprice*sellnum;
        var myrmb=$("#buy_usable").html();
        var myxnb=$("#sell_usable").html();
        var buykenum=0;
        var sellkenum=0;
        if(myrmb>0){
            buykenum=myrmb/buyprice;
        }
        if(myxnb>0){
            sellkenum=myxnb;
        }
        if(buyprice!=null&&buyprice.toString().split(".")!=null&&buyprice.toString().split(".")[1]!=null){
            if(buyprice.toString().split('.')[1].length>market_round){
                $('#buy_price').val(toNum(buyprice,market_round));
            }
        }
        if(buynum!=null&&buynum.toString().split(".")!=null&&buynum.toString().split(".")[1]!=null){
            if(buynum.toString().split('.')[1].length>market_round_num){
                $('#buy_num').val(toNum(buynum,market_round_num));
            }
        }
        if(sellprice!=null&&sellprice.toString().split(".")!=null&&sellprice.toString().split(".")[1]!=null){
            if(sellprice.toString().split('.')[1].length>market_round){
                $('#sell_price').val(toNum(sellprice,market_round));
            }
        }
        if(sellnum!=null&&sellnum.toString().split(".")!=null&&sellnum.toString().split(".")[1]!=null){
            if(sellnum.toString().split('.')[1].length>market_round_num){
                $('#sell_num').val(toNum(sellnum,market_round_num));
            }
        }
        if(buymum!=null&&buymum>0){
            $('#buy_mum').html(toNum(buymum,market_round_num)*1);
        }
        if(sellmum!=null&&sellmum>0){
            $('#sell_mum').html(toNum(sellmum,market_round_num)*1);
        }
        if(buykenum!=null&&buykenum>0&&buykenum!='Infinity'){
            $('#buy_max').html(toNum(buykenum,market_round_num));
            //$('#buy_max').html(toNum(buykenum,market_round_num));
        }
        if(sellkenum!=null&&sellkenum>0&&sellkenum!='Infinity'){
            $('#sell_max').html(toNum(sellkenum,market_round_num));
        }
    }).bind("paste",function(){
        return false;
    }).bind("blur",function(){
        if(this.value.slice(-1)=="."){
            this.value=this.value.slice(0,this.value.length-1);
        }
    }).bind("keypress",function(e){
        var code=(e.keyCode ? e.keyCode : e.which); //compatible:Firefox,IE
        if(this.value.indexOf(".")==-1){
            return (code>=48&&code<=57)||(code==46);
        }else{
            return code>=48&&code<=57
        }
    });
</script>

<script>
    $('#trade_box').addClass('on');
    /**
	 * 暂时未知
	 * */
    function getJsonTops(data){
        if(data){
            if(data['info']['new_price']){
                $('#market_new_price').removeClass('buy');
                $('#market_new_price').removeClass('sell');

                if(data['info']['change'] == 0 || data['info']['change'] > 0){
                    $('#market_new_price').addClass('buy');
                }else{
                    $('#market_new_price').addClass('sell');
                }

                $("#market_new_price").html(data['info']['new_price']);

                $("title").html(data['info']['new_price'] + " | <?php echo ($coin_name); ?>-<?php echo ($coin_type); ?> | <?php echo C('web_title');?>");
            }
            if(data['info']['buy_price']){
                $('#market_buy_price').removeClass('buy');
                $('#market_buy_price').removeClass('sell');

                if($("#market_buy_price").html()>data['info']['buy_price']){
                    $('#market_buy_price').addClass('sell');
                }
                if($("#market_buy_price").html()<data['info']['buy_price']){
                    $('#market_buy_price').addClass('buy');
                }

                $("#market_buy_price").html(data['info']['buy_price']);
                $("#sell_best_price").html('￥'+data['info']['buy_price']);
            }
            if(data['info']['sell_price']){
                $('#market_sell_price').removeClass('buy');
                $('#market_sell_price').removeClass('sell');

                if($("#market_sell_price").html()>data['info']['sell_price']){
                    $('#market_sell_price').addClass('sell');
                }
                if($("#market_sell_price").html()<data['info']['sell_price']){
                    $('#market_sell_price').addClass('buy');
                }

                $("#market_sell_price").html(data['info']['sell_price']);
                $("#buy_best_price").html('￥'+data['info']['sell_price']);
            }
            if(data['info']['max_price']){
                $("#market_max_price").html(data['info']['max_price']);
            }
            if(data['info']['min_price']){
                $("#market_min_price").html(data['info']['min_price']);
            }
            if(data['info']['volume']){
                if(data['info']['volume']>10000){
                    data['info']['volume']=(data['info']['volume']/10000).toFixed(2)+"万"
                }
                if(data['info']['volume']>100000000){
                    data['info']['volume']=(data['info']['volume']/100000000).toFixed(2)+"亿"
                }
                $("#market_volume").html(data['info']['volume']);
            }
            if(data['info']['change'] || data['info']['change'] == 0){
                $('#market_change').removeClass('buy');
                $('#market_change').removeClass('sell');

                if(data['info']['change'] == 0){
                    $('#market_change').addClass('buy');
                    $("#market_change").html("+0.00%");
                }else if(data['info']['change'] > 0){
                    $('#market_change').addClass('buy');
                    $("#market_change").html('+' + data['info']['change']+"%");
                }else{
                    $('#market_change').addClass('sell');
                    $("#market_change").html(data['info']['change']+"%");
                }
            }
        }
    }
    /**
     * 币种
     * */
    function currency(data) {

        if(data){
            var list='';
            for(var i in data['list']){
                ifcolor = (data['list'][i]['change'] >= 0 ? 'red' : 'green');

                list+='<li><dl><a href="/Trade/index/market/'+data['list'][i]['name']+'"><dt class="fl market" style="width: 33%"><i></i><img src="/Upload/coin/'+data['list'][i]['img']+'?v=1237777" width="20" height="20" alt="" /><span class="coin_name">'+data['list'][i]['coin_name']+'</span></dt><dd class="fl ' + ifcolor + '" style="width: 42%"><i></i>'+data['list'][i]['new_price']+'</dd><dd class="fl tl ' + ifcolor + '" style="width: 25%">' + (parseFloat(data['list'][i]['change']) < 0 ? '' : '+') + ((parseFloat(data['list'][i]['change']) < 0.01 && parseFloat(data['list'][i]['change']) > -0.01) ? "0.00" : (parseFloat(data['list'][i]['change'])).toFixed(2)) + '%<i></i></dd></a></dl></li>';
            }
            $("#all_coin").html(list);
        }
    }
    /**
     * 最新交易信息
     * */
    function newestOrder(data) {
        if(data){
            if(data['tradelog']){

                var list='';
                var type='';
                var typename='';

                for( var i in data['tradelog']){
                    if(data['tradelog'][i]['type']==1){
                        list+='<li class="red"><dl onclick="autotrust(this,\'buy\',1)"><dd class="fl" style="width:25%"><i></i>'+data['tradelog'][i]['addtime']+'</dd><dd class="fl" style="width:35%"><i></i>'+(data['tradelog'][i]['price']).toFixed(market_round_num)+'</dd><dd class="fl tr" style="width:40%">'+(data['tradelog'][i]['num']).toFixed(4)+'<i></i></dd></dl></li>';
                    }else{
                        list+='<li class="green"><dl onclick="autotrust(this,\'sell\',1)"><dd class="fl" style="width:25%"><i></i>'+data['tradelog'][i]['addtime']+'</dd><dd class="fl" style="width:35%"><i></i>'+(data['tradelog'][i]['price']).toFixed(market_round_num)+'</dd><dd class="fl tr" style="width:40%">'+(data['tradelog'][i]['num']).toFixed(4)+'<i></i></dd></dl></li>';
                    }
                }
                $("#orderlist").html(list);
            }
        }
    }
    /**
     * 交易买卖委托信息
     * */
    function information(data) {
        if(data){
            if(data['depth']){
                var list='';
                var sellk=data['depth']['sell'].length;
                if(data['depth']['sell']){
                    for(i=0; i<data['depth']['sell'].length; i++){
                        list += '<li><dl onclick="autotrust(this,\'sell\',1,2)"><dd class="fl" style="width:20%"><i></i><?php echo L('卖');?>'+(sellk-i)+'</dd><dd class="fl" style="width:40%"><i></i>'+(data['depth']['sell'][i][0]).toFixed(market_round_num)+'</dd><dd class="fl tr" style="width:40%">'+(data['depth']['sell'][i][1]).toFixed(4)+'<i></i><i class="turntable_bg_green" style="width: '+(data['depth']['sellpbar'][i])+'%"></i></dd></dl></li>';
                    }
                }
                $("#selllist").html(list);
                list='';
                if(data['depth']['buy']){
                    for(i=0; i<data['depth']['buy'].length; i++){
                        list += '<li><dl onclick="autotrust(this,\'buy\',1,2)"><dd class="fl" style="width:20%"><i></i><?php echo L('买');?>'+(i+1)+'</dd><dd class="fl" style="width:40%"><i></i>'+(data['depth']['buy'][i][0]).toFixed(market_round_num)+'</dd><dd class="fl tr" style="width:40%">'+(data['depth']['buy'][i][1]).toFixed(4)+'<i></i><i class="turntable_bg_red" style="width: '+(data['depth']['buypbar'][i])+'%"></i></dd></dl></li>';
                    }
                }
                $("#buylist").html(list);
            }
        }
    }
    /**
     * 我的财产 我的委托
     * @param data
     */
    function myGetzican(data) {
        if(data){
            if(data['entrust']){
                $('#entrust_over').show();
                var list='';
                var cont=data['entrust'].length;
                var status_name = '--';
                console.log(cont);
                // 委托信息
                for(i=0; i<=data['entrust'].length-1; i++){
                    if(data['entrust'][i]['type']==1){
                        if(data['entrust'][i]['status'] == 0){status_name = "<?php echo L('交易中');?>";}
                        else if(data['entrust'][i]['status'] == 1){status_name = "<?php echo L('已完成');?>";}
                        else if(data['entrust'][i]['status'] == 2){status_name = "<?php echo L('已撤销');?>";}

                        list+='<li><dl><dd class="fl" style="width: 20%"><i></i>'+data['entrust'][i]['addtime']+'</dd><dd class="fl red" style="width: 25%"><i></i><?php echo L('买');?> '+(data['entrust'][i]['price']).toFixed(market_round)+'</dd><dd class="fl red" style="width: 35%"><i></i>'+(data['entrust'][i]['num']).toFixed(market_round_num)+' / '+(data['entrust'][i]['deal']).toFixed(market_round_num)+'</dd><dd class="fl tc"  style="width: 10%">'+status_name+'</dd><dd class="fl tc" style="width: 10%"><a class="green" id="'+data['entrust'][i]['id']+'" onclick="cancelaa(\''+data['entrust'][i]['id']+'\')" href="javascript:void(0);"><?php echo L('取消');?></a></dd></dl></li>';
                    }else{
                        if(data['entrust'][i]['status'] == 0){status_name = "<?php echo L('交易中');?>";}
                        else if(data['entrust'][i]['status'] == 1){status_name = "<?php echo L('已完成');?>";}
                        else if(data['entrust'][i]['status'] == 2){status_name = "<?php echo L('已撤销');?>";}

                        list+='<li><dl><dd class="fl" style="width: 20%"><i></i>'+data['entrust'][i]['addtime']+'</dd><dd class="fl green" style="width: 25%"><i></i><?php echo L('卖');?> '+(data['entrust'][i]['price']).toFixed(market_round)+'</dd><dd class="fl green" style="width: 35%"><i></i>'+(data['entrust'][i]['num']).toFixed(market_round_num)+' / '+(data['entrust'][i]['deal']).toFixed(market_round_num)+'</dd><dd class="fl tc"  style="width: 10%">'+status_name+'</dd><dd class="fl tc" style="width: 10%"><a class="green" id="'+data['entrust'][i]['id']+'" onclick="cancelaa(\''+data['entrust'][i]['id']+'\')" href="javascript:void(0);"><?php echo L('取消');?></a></dd></dl></li>';
                    }
                }
                $('#entrustlist').html(list);
            }else{
                $('#entrust_over').hide();
                $("#entrustlist li").remove();
            }

            // 我的财产信息
            if(data['usercoin']){
                if(data['usercoin']['rmb']){
                    $("#buy_usable").html(data['usercoin']['rmb'].toFixed(5) * 1);
                    if($('#buy_price').val()>0){
                        $("#buy_max").html(((data['usercoin']['rmb']/$('#buy_price').val()).toFixed(market_round_num) * 1));
                    }else{
                        $("#buy_max").html(0);
                    }
                }else{
                    $("#buy_usable").html('0.00');
                }

                if(data['usercoin']['rmbd']){
                    $("#buy_usabled").html(data['usercoin']['rmbd'].toFixed(market_round_num) * 1);
                }else{
                    $("#buy_usabled").html('0.00');
                }

                if(data['usercoin']['xnb']){
                    $("#sell_usable").html(data['usercoin']['xnb'].toFixed(market_round_num) * 1);
                    $("#sell_max").html(data['usercoin']['xnb'].toFixed(market_round_num) * 1);
                }else{
                    $("#sell_usable").html('0.00');
                }

                if(data['usercoin']['xnbd']){
                    $("#sell_usabled").html(data['usercoin']['xnbd'].toFixed(market_round_num) * 1);
                }else{
                    $("#sell_usabled").html('0.00');
                }

              if(data['time_state']==0){
                    $("#buybutton").show();
                    $("#buybutton").attr("onclick","ajaxDeal('buybutton')");
                    $("#sellerbutton").show();
                    $("#sellerbutton").attr("onclick","ajaxDeal('sellerbutton');");
                }else{
                    $("#buybutton").show();
                    $("#buybutton").val("<?php echo L('闭盘期间');?>");
                    $("#buybutton").css("background","#ccc");
                    $("#buybutton").attr("onclick","ajaxDeal('buybutton')");
                    $("#sellerbutton").show();
                    $("#sellerbutton").val("<?php echo L('闭盘期间');?>");
                    $("#sellerbutton").css("background","#ccc");
                    $("#sellerbutton").attr("onclick","ajaxDeal('sellerbutton');");
                }
            }
        }
    }
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