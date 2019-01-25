<?php
/* 交易中心 */
namespace Home\Controller;




use Home\Controller\CeshiController;
use Think\Cache\Driver\Redis;

class TradeController extends HomeController
{
  //  use Workermans;
	public function index($market = NULL)
	{
	    //创建token；
        creatToken();
        // 用户后台被禁用
        $result = M('User')->where(array('status'=>0,'id'=>userid()))->find();
        if($result){
            $this->error("账号异常请联系管理员",U('Login/index'));
        }
		// 过滤非法字符----------------S
		if (checkstr($market)) {
			$this->error(L('您输入的信息有误！'));
		}
		// 过滤非法字符----------------E
		//check_server();
        /*$Coins = M('market')->select();
        foreach ($Coins as $k => $v) {
            $market = $Coins[0]['name'];
        }*/

		if (!$market) {
		    $market = C("market_mr");
		}
		$daohangs = M('daohang')->where(['id'=>(int)1])->find();

		if ($daohangs['access']) {
			$this->error(L('暂未对外开放'), U('Index/index'));
		}
		
		// 分类名称
		$getCoreConfig = getCoreConfig();
		if(!$getCoreConfig){
			$this->error('核心配置有误');
		}
        $getCoreConfig['indexcat'] = [
            0 => 'USDT',
            1 => 'BTC',
            2 => 'ETH'
        ];
		$this->assign('jiaoyiqu', $getCoreConfig['indexcat']);
	//	var_dump($getCoreConfig['indexcat']);
		// 顶部价格信息
		$topdata = (APP_DEBUG ? null : S('getJsonTop' . $market));

		if (!$topdata) {
			if ($market) {
				$xnb = explode('_', $market)[0];
				$rmb = explode('_', $market)[1];

				foreach (C('market') as $k => $v) {
					$v['xnb'] = explode('_', $v['name'])[0];
					$v['rmb'] = explode('_', $v['name'])[1];
					$topdata['list'][$k]['name'] = $v['name'];
					$topdata['list'][$k]['img'] = $v['xnbimg'];
					$topdata['list'][$k]['title'] = $v['title'];
					$topdata['list'][$k]['new_price'] = $v['new_price'];
				}

				$topdata['info']['new_price'] = C('market')[$market]['new_price'];
				S('getJsonTop' . $market, $topdata);
			}
		}


		$this->assign("market", $market);
		$this->assign("coin_name", strtoupper(explode("_", $market)[0])); //币种简称
		$this->assign("coin_type", strtoupper(explode("_", $market)[1])); //币种市场

		$sclj = explode('_', $market)[1];

		$coinnav = 0;
/*		if($sclj=='bwb'){
			$rmb= round($topdata['info']['new_price'] *C('MYCOIN'),2);
		}*/
		if ($sclj==Anchor_CNY) { //锚定法币
			$rmb = bcdiv($topdata['info']['new_price'] * C('MYCOIN'),1,2) * 1;
			$coinnav = 0;
		}
		if ($sclj=='btc') {
			//$rmb = round($topdata['info']['new_price'] * C('BTC'),2);
			$rmb = bcdiv($topdata['info']['new_price'] * C('market')['btc_c'.Anchor_CNY]['new_price'],1,2) * 1;
			$coinnav = 1;
		}
		if ($sclj=='eth') {
			//$rmb = NumToStr(round($topdata['info']['new_price'] * C('market')['eth_'.Anchor_CNY]['new_price']),6);
			$rmb = bcdiv($topdata['info']['new_price'] * C('market')['eth_'.Anchor_CNY]['new_price'],1,2) * 1;
			$coinnav = 2;
		}
		if ($sclj=='usdt') {
			$rmb = bcdiv($topdata['info']['new_price'] * C('market')['usdt_'.Anchor_CNY]['new_price'],1,2) * 1;
			$coinnav = 3;
		}
		if ($sclj=='mob') {
			$rmb = bcdiv($topdata['info']['new_price'] * C('market')['mob_'.Anchor_CNY]['new_price'],1,2) * 1;
			$coinnav = 3;
		}

        $Config = M('Config')->where(array('id' => 1))->find();
		//$this->assign('market_coin', Anchor_CNY);
        $this->assign('market_coin', Anchor_USDT);
		$this->assign('rmbprice', $rmb);
		$this->assign('coinnav', $coinnav);
        $this->assign('config', $Config);
		session('C_market',C("market"));
      //  $this->assign('C_market',);
		
		$this->display();
	}
	
	/**
	 * 普通K线图
	 * @param  [type] $market [description]
	 * @return [type]         [description]
	 */
	public function ordinary($market = NULL)
	{
		// 过滤非法字符----------------S
		if (checkstr($market)) {
			$this->error(L('您输入的信息有误！'));
		}
		// 过滤非法字符----------------E
		if (!$market) {
			$market = C('market_mr');
		}
		$this->assign('market', $market);
		$this->display();
	}

	/**
	 * 专业K线图
	 * @param  [type] $market [description]
	 * @return [type]         [description]
	 */
	public function specialty($market = NULL)
	{
		// 过滤非法字符----------------S
		if (checkstr($market)) {
			$this->error(L('您输入的信息有误！'));
		}
		// 过滤非法字符----------------E
		if (!$market) {
			$market = C('market_mr');
		}
		$this->assign('market', $market);
		$this->display();
	}
    public function ttt()
    {

        $Redis=  new \Redis();
        $Redis->connect('47.244.18.129','18642');
        $Redis->auth('QxTA29BaJN'); //密码验证

        $this->display();
    }
    /**
     * K线图
     */
    public function KMessageDiagram()
    {
        if(isset($_GET['market'])){
            $market = $_GET['market'];
            $this->assign("market", $market);
            $this->display();
        }
    }
	// 买卖
	public function upTrade($paypassword = NULL, $market = NULL, $price, $num, $type)
	{

	    $startTime = microtime(true);
        if (!userid()) {
            $this->error(L('请先登录！'));
        }
	  if(empty($_POST['tokens'])){
	        $this->error(L('参数非法，请联系管理员'));
        }
        if(!checkTokens($_POST['tokens'])){
            $this->error(L('参数非法，请联系管理员'));
        }

        $workerman = A('Wokerman');
        $workerman->getNewToken();
        $userid =userid();
        $trade_qu_id = $_POST['trade_qu_id'];
        $paypassword=$_POST['paypassword'];
        $market = $_POST['market'];
        /*if($_POST['market']!='hrc_usdt'){
            $this->error(L('暂未开放交易！11111'));
        }*/
        $price =$_POST['price'];
        $num = $_POST['num'];
        $type = $_POST['type'];
        if(empty($price)){
            $this->error(L('交易金额不能为空！'));
        }else if(empty($num)){
            $this->error(L('交易数量不能为空！'));
        }else if(empty($type)){
            $this->error(L('系统错误，请联系管理员！'));
        }else if(empty($market)){
            $this->error(L('系统错误，请联系管理员！'));
        }
		// 过滤非法字符----------------S
		if (checkstr($paypassword) || checkstr($market) || checkstr($price) || checkstr($num) || checkstr($type)) {
			$this->error(L('您输入的信息有误！'));
		}
		// 过滤非法字符----------------E

		$xnb = explode('_', $market)[0];
		$rmb = explode('_', $market)[1];

		// 处理开盘闭盘交易时间===开始
		$times = date('G',time());
		$minute = date('i',time());
		$minute = intval($minute);
		if (($times <= C('market')[$market]['start_time'] && $minute < intval(C('market')[$market]['start_minute'])) || ( $times > C('market')[$market]['stop_time'] && $minute>= intval(C('market')[$market]['stop_minute']))) {
			$this->error(L('该时间为闭盘时间！'));
		}
		if (($times <C('market')[$market]['start_time'] )|| $times > C('market')[$market]['stop_time']) {
			$this->error(L('该时间为闭盘时间！'));
		} else {
			if ($times == C('market')[$market]['start_time']) {
				if ($minute < intval(C('market')[$market]['start_minute'])) {
					$this->error(L('该时间为闭盘时间！'));
				}
			} elseif ($times == C('market')[$market]['stop_time']) {
				if (($minute > C('market')[$market]['stop_minute'])) {
					$this->error(L('该时间为闭盘时间！'));
				}
			}
		}
		// 处理周六周日是否可交易===开始
		$weeks = date('N',time());
		if (!C('market')[$market]['agree6']) {
			if ($weeks == 6) {
				$this->error(L('您好，周六为闭盘时间！'));
			}
		}
		if (!C('market')[$market]['agree7']) {
			if ($weeks == 7) {
				$this->error(L('您好，周日为闭盘时间！'));
			}
		}
		//处理周六周日是否可交易===结束
		if (!check($price, 'double')) {
			$this->error(L('交易价格格式错误'));
		}
		if (!check($num, 'double')) {
			$this->error(L('交易数量格式错误'));
		}
		if (($type != 1) && ($type != 2)) {
			$this->error(L('交易类型格式错误'));
		}
		if ($type == 1) {
			if (!$num) {
				$nnn_coin = explode('_', $market);
				$nnn_coin = strtoupper($nnn_coin[0]);

				$this->error(L('单笔买入最小交易数量为：').C('market')[$market]['trade_buy_num_min'].' '.$nnn_coin.'!');
			}
			if ($num<C('market')[$market]['trade_buy_num_min']) {
				$nnn_coin = explode('_', $market);
				$nnn_coin = strtoupper($nnn_coin[0]);
				$this->error(L('单笔买入最小交易数量为：').C('market')[$market]['trade_buy_num_min'].' '.$nnn_coin.'!');
			}
			if ($num>C('market')[$market]['trade_buy_num_max']) {
				$nnn_coin = explode('_', $market);
				$nnn_coin = strtoupper($nnn_coin[0]);
				$this->error(L('单笔买入最大交易数量为：').C('market')[$market]['trade_buy_num_max'].' '.$nnn_coin.'!');
			}
		}
		if ($type == 2) {
			if (!$num) {
				$nnn_coin = explode('_', $market);
				$nnn_coin = strtoupper($nnn_coin[0]);

				$this->error(L('单笔卖出最小交易数量为：').C('market')[$market]['trade_sell_num_min'].' '.$nnn_coin.'!');
			}
			if ($num<C('market')[$market]['trade_sell_num_min']) {
				$nnn_coin = explode('_', $market);
				$nnn_coin = strtoupper($nnn_coin[0]);
				$this->error(L('单笔卖出最小交易数量为：').C('market')[$market]['trade_sell_num_min'].' '.$nnn_coin.'!');
			}
			if ($num>C('market')[$market]['trade_sell_num_max']) {
				$nnn_coin = explode('_', $market);
				$nnn_coin = strtoupper($nnn_coin[0]);
				$this->error(L('单笔卖出最大交易数量为：').C('market')[$market]['trade_sell_num_max'].' '.$nnn_coin.'!');
			}
		}
		$userid = userid();
		$session = session($userid. 'tpwdsetting');
		$user = M('User')->where(array('id' =>$userid))->find();
		if($user['tpwdsetting']=='1'){
            if (!$session) {
                if (md5($paypassword) != $user['paypassword']) {
                    $this->error(L('交易密码错误！'));
                }else{
                    session($userid. 'tpwdsetting', 1);
                }
            }
        }else if($user['tpwdsetting']=='2'){
            if (md5($paypassword) != $user['paypassword']) {
                $this->error(L('交易密码错误！'));
            }
        }

/*		if ($user['tpwdsetting'] == 3) {}
		if ($user['tpwdsetting'] == 2) {
			if (md5($paypassword) != $user['paypassword']) {
				$this->error(L('交易密码错误！'));
			}
		}
		if ($user['tpwdsetting'] == 1) {
			if (!session(userid() . 'tpwdsetting')) {
				if (md5($paypassword) != $user['paypassword']) {
					$this->error(L('交易密码错误！'));
				} else {
					session(userid() . 'tpwdsetting', 1);
				}
			}
		}*/
		if (!C('market')[$market]) {
			$this->error(L('交易市场错误'));
		} else {
			$xnb = explode('_', $market)[0];
			$rmb = explode('_', $market)[1];
		}

		if (!C('market')[$market]['trade']) {
			$this->error(L('当前市场禁止交易'));
		}

		$price = round(floatval($price), C('market')[$market]['round']);
		if (!$price) {
			$this->error(L('交易价格错误') . $price);
		}
		$num = round($num,  C('market')[$market]['round']);;
		if (!check($num, 'double')) {
			$this->error(L('交易数量错误'));
		}

		if ($type == 1) {
			$min_price = (C('market')[$market]['buy_min'] ? C('market')[$market]['buy_min'] : 1.0E-8);
			$max_price = (C('market')[$market]['buy_max'] ? C('market')[$market]['buy_max'] : 10000000);
		} else if ($type == 2) {
			$min_price = (C('market')[$market]['sell_min'] ? C('market')[$market]['sell_min'] : 1.0E-8);
			$max_price = (C('market')[$market]['sell_max'] ? C('market')[$market]['sell_max'] : 10000000);
		} else {
			$this->error(L('交易类型错误'));
		}

		if ($max_price < $price) {
			$this->error(L('交易价格超过今日涨幅限制！'));
		}
		if ($price < $min_price) {
			$this->error(L('交易价格超过今日跌幅限制！'));
		}

		$hou_price = C('market')[$market]['hou_price'];
		if ($hou_price) {
			if (C('market')[$market]['zhang']) {
				$zhang_price = round(($hou_price / 100) * (100 + C('market')[$market]['zhang']), C('market')[$market]['round']);
				if ($zhang_price < $price) {
					$this->error(L('交易价格超过今日涨幅限制！'));
				}
			}
			if (C('market')[$market]['die']) {
				$die_price = round(($hou_price / 100) * (100 - C('market')[$market]['die']), C('market')[$market]['round']);
				if ($price < $die_price) {
					$this->error(L('交易价格超过今日跌幅限制！'));
				}
			}
		}

		$user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
		if ($type == 1) {
			//增加手续费分级,做市商0手续费20180128
 			if($user['lv']==0){
 				$trade_fee = C('market')[$market]['fee_buy'];
 			} else {
 				$trade_fee = 0;
 			}
			if ($trade_fee) {
			    //数量乘以价格 除以100 乘以手续费
				$fee = round((($num * $price) / 100) * $trade_fee, 8);
				$mum = round((($num * $price) / 100) * (100 + $trade_fee), 8);
			} else {
				$fee = 0;
				$mum = round($num * $price, 8);//dump($mum);
			}
			if ($user_coin[$rmb] < $mum) {
				$this->error(C('coin')[$rmb]['title'] . L('余额不足！'));
			}
		} else if ($type == 2) {
			//增加手续费分级,做市商0手续费20180128
 			if($user['lv']==0){
 				$trade_fee = C('market')[$market]['fee_sell'];
 			} else {
 				$trade_fee = 0;
 			}
			if ($trade_fee) {

				$fee = round((($num * $price) / 100) * $trade_fee, 8);
				$mum = round((($num * $price) / 100) * (100 - $trade_fee), 8);
			} else {
				$fee = 0;
				$mum = round($num * $price, 8);
			}
			if ($user_coin[$xnb] < $mum) {
				$this->error(C('coin')[$xnb]['title'] . L('余额不足！'));
			}
		} else {
			$this->error(L('交易类型错误'));
		}
		if (C('market')[$market]['trade_min']) {
			if ($mum < C('market')[$market]['trade_min']) {
				$this->error(L('交易总额不能小于') . C('market')[$market]['trade_min']);
			}
		}
		if (C('market')[$market]['trade_max']) {
			if (C('market')[$market]['trade_max'] < $mum) {
				$this->error(L('交易总额不能大于') . C('market')[$market]['trade_max']);
			}
		}
		if (!$rmb) {
			$this->error(L('数据错误101'));
		}
		if (!$xnb) {
			$this->error(L('数据错误102'));
		}
		if (!$market) {
			$this->error(L('数据错误103'));
		}
		if (!$price) {
			$this->error(L('数据错误104'));
		}
		if (!$num) {
			$this->error(L('数据错误105'));
		}
		if (!$mum) {
			$this->error(L('数据错误106'));
		}
		if (!$type) {
			$this->error(L('数据错误107'));
		}
		try{
			$mo = M();
			$mo->execute('set autocommit=0');
		//	$mo->execute('lock tables tw_trade write ,tw_user_coin write ,tw_finance write,tw_finance_log write,tw_user write');//处理资金变更日志

			$rs = array();
			$user_coin = $mo->table('tw_user_coin')->where(array('userid' => userid()))->find();

			if ($type == 1) {
				if ($user_coin[$rmb] < $mum) {
					throw new \Think\Exception(C('coin')[$rmb]['title'] . L('余额不足！'));
				}

				$finance = $mo->table('tw_finance')->where(array('userid' => userid()))->order('id desc')->find();
				$finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => userid()))->find();

                $rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setDec($rmb, $mum);
				$rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setInc($rmb . 'd', $mum);
				$rs[] = $finance_nameid = $mo->table('tw_trade')->add(array('userid' => userid(), 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 1, 'addtime' => time(), 'status' => 0));

				$finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => userid()))->find();
				$finance_hash = md5(userid() . $finance_num_user_coin[$rmb] . $finance_num_user_coin[$rmb.'d'] . $mum . $finance_mum_user_coin[$rmb] . $finance_mum_user_coin[$rmb.'d'] . MSCODE . 'tp3.net.cn');

				$finance_num = $finance_num_user_coin[$rmb] + $finance_num_user_coin[$rmb.'d'];

				// 处理资金变更日志-----------------S

				$user_n_info = $mo->table('tw_user')->where(array('id' => userid()))->find();

				$rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 0, 'amount' => $mum, 'optype' => 18, 'cointype' => 1, 'old_amount' => $finance_num_user_coin[$rmb], 'new_amount' => $finance_mum_user_coin[$rmb], 'userid' => userid(), 'adminid' => userid(),'addip'=>get_client_ip(),'position'=>1));

				$rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 1, 'amount' => $mum, 'optype' => 20, 'cointype' => 1, 'old_amount' => $finance_num_user_coin[$rmb. 'd'], 'new_amount' => $finance_mum_user_coin[$rmb. 'd'], 'userid' => userid(), 'adminid' => userid(),'addip'=>get_client_ip(),'position'=>1));

				// 处理资金变更日志-----------------E

				if ($finance['mum'] < $finance_num) {
					$finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
				} else {
					$finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
				}

				$rs[] = $mo->table('tw_finance')->add(array('userid' => userid(), 'coinname' => $rmb, 'num_a' => $finance_num_user_coin[$rmb], 'num_b' => $finance_num_user_coin[$rmb.'d'], 'num' => $finance_num_user_coin[$rmb] + $finance_num_user_coin[$rmb.'d'], 'fee' => $mum, 'type' => 2, 'name' => 'trade', 'nameid' => $finance_nameid, 'remark' => L('交易中心-委托买入-市场') . $market, 'mum_a' => $finance_mum_user_coin[$rmb], 'mum_b' => $finance_mum_user_coin[$rmb.'d'], 'mum' => $finance_mum_user_coin[$rmb] + $finance_mum_user_coin[$rmb.'d'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));

			} else if ($type == 2) {
				if ($user_coin[$xnb] < $num) {
					throw new \Think\Exception(C('coin')[$xnb]['title'] . L('余额不足！'));
				}

				$fin_user_coin = $mo->table('tw_user_coin')->where(array('userid' => userid()))->find();//处理资金变更日志

				$rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setDec($xnb, $num);
				$rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setInc($xnb . 'd', $num);
				$rs[] = $mo->table('tw_trade')->add(array('userid' => userid(), 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 2, 'addtime' => time(), 'status' => 0));
				$fin_user_coin_new = $mo->table('tw_user_coin')->where(array('userid' => userid()))->find();//处理资金变更日志

				// 处理资金变更日志-----------------S

				switch ($xnb) {
					case 'hyjf':
						$cointype = 2;//汇云品种类型2
						break;
					default:
						$cointype = 3;//其他币种类型3
						break;
				}

				$user_n_info = $mo->table('tw_user')->where(array('id' => userid()))->find();

				$rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 0, 'amount' => $num, 'optype' => 19, 'cointype' => $cointype, 'old_amount' => $fin_user_coin[$xnb], 'new_amount' => $fin_user_coin_new[$xnb], 'userid' => userid(), 'adminid' => userid(),'addip'=>get_client_ip(),'position'=>1));

				$rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 1, 'amount' => $num, 'optype' => 21, 'cointype' => $cointype, 'old_amount' => $fin_user_coin[$xnb. 'd'], 'new_amount' => $fin_user_coin_new[$xnb. 'd'], 'userid' => userid(), 'adminid' => userid(),'addip'=>get_client_ip(),'position'=>1));

				// 处理资金变更日志-----------------E
			} else {
				throw new \Think\Exception(L('交易类型错误'));
			}

			if (check_arr($rs)) {
				$mo->execute('commit');
			//	$mo->execute('unlock tables');
			} else {
				throw new \Think\Exception(L('交易失败！'));
			}
		} catch(\Think\Exception $e) {
			$mo->execute('rollback');
			//$mo->execute('unlock tables');
			$this->error(L('交易失败！'));
		}

		S('getDepth', null);
		// $this->matchingTradeall($market);//匹配玩家和虚拟交易
		// $this->matchingTrade($market);//只匹配玩家之间
		// $this->success(L('交易成功！'));
		//jhsoft即时处理交易状态和异常处理
        $orderEndTime = microtime(true);

        A('Queue')->checkDapan(); //匹配所有订单交易

        $autoEndTime = microtime(true);
        //机器人交易刷单
       // A('Queue')->autojy2($market);
		//jhsoft对当前交易订单处理开始
		$corderid=$rs[2];

		$mo = M();
//		$mo->execute('set autocommit=0');
		//$mo->execute('lock tables tw_trade write');

		$cTrade = M('Trade')->where('id ='.$corderid)->find();
       // $worker =new WokermanController();

       // $arr = New CeshiController();
      //  $arr = new CeshiController;

    //    $workerman->getNewMarkets($userid,$market,$trade_qu_id);
  //      M()->query("unlock tables");
      //  $url = $_SERVER['SERVER_NAME'].":8082?type=publish&content=".json_encode($arr)."&to=".$userid;

		if ($cTrade) {

		    $cstatus=$cTrade['status'];
		    $cdeal=$cTrade['deal'];

		    $cnum=$cTrade['num'];

		    if ($cdeal>$cnum) {
		        $mo->table('tw_trade')->where(array('id' => $corderid))->save(array('deal' => Num($cnum),'status' => 1));
		        $mo->execute('commit');
		    //    $mo->execute('unlock tables');
		        $cstatus=1;
		        $cdeal=$cnum;
		    }
		    if ($cstatus==1) {
            //    $this->carriedApi($url);

                $workerman->getNewMarkets($userid,$market,$trade_qu_id);

                $EndTime = microtime(true);
                // 下单时间
                $orderTime = round($orderEndTime-$startTime,3);
                // 撮合时间
                $autoTime = round($autoEndTime-$orderEndTime,3);
                // 总流程时间
                $alltime = round($EndTime-$orderEndTime,3);
                $log = '下单时间：'.$orderTime.';撮合时间：'.$autoTime.';总流程时间：'.$alltime.';';
                //$this->upTradeTimeLog($log);

                $this->ajaxBack('交易成功！',1);
                //$this->success("xxxxxxxxxx");
		       // echo L('交易成功！交易成功！！！！！！');

		    }
		    if ($cstatus==0){
		        if ($cdeal>0) {
                //    $this->carriedApi($url);
                    $workerman->getNewMarkets($userid,$market,$trade_qu_id);
		            // $this->success(L('已成功交易'.$cdeal.',余下'.($cnum-$cdeal).'自动转为委托交易中...！'));

                    $EndTime = microtime(true);
                    // 下单时间
                    $orderTime = round($orderEndTime-$startTime,3);
                    // 撮合时间
                    $autoTime = round($autoEndTime-$orderEndTime,3);
                    // 总流程时间
                    $alltime = round($EndTime-$orderEndTime,3);
                    $log = '下单时间：'.$orderTime.';撮合时间：'.$autoTime.';总流程时间：'.$alltime.';';
                 //   $this->upTradeTimeLog($log);

                    $this->ajaxBack('交易成功！',1);
		             //$this->success(L());
                    //return;
		        } else {
             //       $this->carriedApi($url);
                    //$worker->getNewMarket($userid,$market,$trade_qu_id);
                    $workerman->getNewMarkets($userid,$market,$trade_qu_id);
		            // $this->success('已自动委托交易中...！');
                  //  $this->success(L('交易成功！333')); // 卖出
                    $EndTime = microtime(true);
                    // 下单时间
                    $orderTime = round($orderEndTime-$startTime,3);
                    // 撮合时间
                    $autoTime = round($autoEndTime-$orderEndTime,3);
                    // 总流程时间
                    $alltime = round($EndTime-$orderEndTime,3);
                    $log = '下单时间：'.$orderTime.';撮合时间：'.$autoTime.';总流程时间：'.$alltime.';';
                   // $this->upTradeTimeLog($log);

                    $this->ajaxBack('交易成功！',1);
                    // $this->success(L('交易成功！1111'));
                   // return;
                    //return;
                 //   $workerman->getNewMarkets($userid,$market,$trade_qu_id);
		        }
		    }
		} else {
            $workerman->getNewMarkets($userid,$market,$trade_qu_id);
           // $this->carriedApi($url);
            $EndTime = microtime(true);
            // 下单时间
            $orderTime = round($orderEndTime-$startTime,3);
            // 撮合时间
            $autoTime = round($autoEndTime-$orderEndTime,3);
            // 总流程时间
            $alltime = round($EndTime-$orderEndTime,3);
            $log = '下单时间：'.$orderTime.';撮合时间：'.$autoTime.';总流程时间：'.$alltime.';';
            //$this->upTradeTimeLog($log);

            $this->ajaxBack('交易成功！',1);
          //  return;
		}

		//jhsoft对当前交易订单处理结束
	}
    // 买卖
    public function upTrade43ASD4F65A4SDF6($userid,$paypassword = NULL, $market = NULL, $price, $num, $type)
    {
//        var_dump($_POST);exit;
//        echo 12121;exit;
//        if (!userid()) {
//            $this->error(L('请先登录！'));
//        }
//        echo 3343434;exit;
//        $data = @file_get_contents("php://input");
//        $data = json_decode($data,true);
//        var_dump($data);exit;
        $data = $_POST;
        $userid = $_POST['userid'];
        $data = $_POST;
        $userid = $data['userid'];
//        var_dump($userid);exit;
        if (!$userid) {
            $this->error(L('请先登录！'));
        }
//        if(empty($_POST['tokens'])){
//            $this->error(L('参数非法，请联系管理员'));
//        }
//        if(!checkTokens($_POST['tokens'])){
//            $this->error(L('参数非法，请联系管理员'));
//        }
//        $workerman = A('Wokerman');
//        $workerman->getNewToken();
//        $userid =userid();
        $trade_qu_id = $_POST['trade_qu_id'];
        $paypassword=$_POST['paypassword'];
        $market = $_POST['market'];
        $price =$_POST['price'];
        $num = $_POST['num'];
        $type = $_POST['type'];
        $trade_qu_id = $data['trade_qu_id'];
        $paypassword = $data['paypassword'];
        $market = $data['market'];
        $price = $data['price'];
        $num = $data['num'];
        $type = $data['type'];
        if(empty($price)){
            $this->error(L('交易金额不能为空！'));
        }else if(empty($num)){
            $this->error(L('交易数量不能为空！'));
        }else if(empty($type)){
            var_dump($type);
            $this->error(L('系统错误，请联系管理员！'));
        }else if(empty($market)){
            var_dump($market);
            $this->error(L('系统错误，请联系管理员！'));
        }
        // 过滤非法字符----------------S
        if (checkstr($paypassword) || checkstr($market) || checkstr($price) || checkstr($num) || checkstr($type)) {
            $this->error(L('您输入的信息有误！'));
        }
        // 过滤非法字符----------------E

        $xnb = explode('_', $market)[0];
        $rmb = explode('_', $market)[1];

        // 处理开盘闭盘交易时间===开始
        $times = date('G',time());
        $minute = date('i',time());
        $minute = intval($minute);
        if (($times <= C('market')[$market]['start_time'] && $minute < intval(C('market')[$market]['start_minute'])) || ( $times > C('market')[$market]['stop_time'] && $minute>= intval(C('market')[$market]['stop_minute']))) {
            $this->error(L('该时间为闭盘时间！'));
        }
        if (($times <C('market')[$market]['start_time'] )|| $times > C('market')[$market]['stop_time']) {
            $this->error(L('该时间为闭盘时间！'));
        } else {
            if ($times == C('market')[$market]['start_time']) {
                if ($minute < intval(C('market')[$market]['start_minute'])) {
                    $this->error(L('该时间为闭盘时间！'));
                }
            } elseif ($times == C('market')[$market]['stop_time']) {
                if (($minute > C('market')[$market]['stop_minute'])) {
                    $this->error(L('该时间为闭盘时间！'));
                }
            }
        }
        // 处理周六周日是否可交易===开始
        $weeks = date('N',time());
        if (!C('market')[$market]['agree6']) {
            if ($weeks == 6) {
                $this->error(L('您好，周六为闭盘时间！'));
            }
        }
        if (!C('market')[$market]['agree7']) {
            if ($weeks == 7) {
                $this->error(L('您好，周日为闭盘时间！'));
            }
        }
        echo 1111111;
        //处理周六周日是否可交易===结束
        if (!check($price, 'double')) {
            $this->error(L('交易价格格式错误'));
        }
        if (!check($num, 'double')) {
            $this->error(L('交易数量格式错误'));
        }
        if (($type != 1) && ($type != 2)) {
            $this->error(L('交易类型格式错误'));
        }
        if ($type == 1) {
            if (!$num) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);

                $this->error(L('单笔买入最小交易数量为：').C('market')[$market]['trade_buy_num_min'].' '.$nnn_coin.'!');
            }
            if ($num<C('market')[$market]['trade_buy_num_min']) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);
                $this->error(L('单笔买入最小交易数量为：').C('market')[$market]['trade_buy_num_min'].' '.$nnn_coin.'!');
            }
            if ($num>C('market')[$market]['trade_buy_num_max']) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);
                $this->error(L('单笔买入最大交易数量为：').C('market')[$market]['trade_buy_num_max'].' '.$nnn_coin.'!');
            }
        }
        echo 2222222;
        if ($type == 2) {
            if (!$num) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);

                $this->error(L('单笔卖出最小交易数量为：').C('market')[$market]['trade_sell_num_min'].' '.$nnn_coin.'!');
            }
            if ($num<C('market')[$market]['trade_sell_num_min']) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);
                $this->error(L('单笔卖出最小交易数量为：').C('market')[$market]['trade_sell_num_min'].' '.$nnn_coin.'!');
            }
            if ($num>C('market')[$market]['trade_sell_num_max']) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);
                $this->error(L('单笔卖出最大交易数量为：').C('market')[$market]['trade_sell_num_max'].' '.$nnn_coin.'!');
            }
        }
        echo 3333333333333;
//        $userid = userid();
        $session = session($userid. 'tpwdsetting');
        $user = M('User')->where(array('id' =>$userid))->find();
        if($user['tpwdsetting']=='1'){
            if (!$session) {
                if (md5($paypassword) != $user['paypassword']) {
                    $this->error(L('交易密码错误！'));
                }else{
                    session($userid. 'tpwdsetting', 1);
                }
            }
        }else if($user['tpwdsetting']=='2'){
            if (md5($paypassword) != $user['paypassword']) {
                $this->error(L('交易密码错误！'));
            }
        }
        echo 4444444444444;
        /*		if ($user['tpwdsetting'] == 3) {}
                if ($user['tpwdsetting'] == 2) {
                    if (md5($paypassword) != $user['paypassword']) {
                        $this->error(L('交易密码错误！'));
                    }
                }
                if ($user['tpwdsetting'] == 1) {
                    if (!session(userid() . 'tpwdsetting')) {
                        if (md5($paypassword) != $user['paypassword']) {
                            $this->error(L('交易密码错误！'));
                        } else {
                            session(userid() . 'tpwdsetting', 1);
                        }
                    }
                }*/
        if (!C('market')[$market]) {
            $this->error(L('交易市场错误'));
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }
        echo 555555;
        if (!C('market')[$market]['trade']) {
            $this->error(L('当前市场禁止交易'));
        }

        $price = round(floatval($price), C('market')[$market]['round']);
        if (!$price) {
            $this->error(L('交易价格错误') . $price);
        }
        $num = round($num,  C('market')[$market]['round']);;
        if (!check($num, 'double')) {
            $this->error(L('交易数量错误'));
        }

        if ($type == 1) {
            $min_price = (C('market')[$market]['buy_min'] ? C('market')[$market]['buy_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['buy_max'] ? C('market')[$market]['buy_max'] : 10000000);
        } else if ($type == 2) {
            $min_price = (C('market')[$market]['sell_min'] ? C('market')[$market]['sell_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['sell_max'] ? C('market')[$market]['sell_max'] : 10000000);
        } else {
            $this->error(L('交易类型错误'));
        }

        if ($max_price < $price) {
            $this->error(L('交易价格超过今日涨幅限制！'));
        }
        if ($price < $min_price) {
            $this->error(L('交易价格超过今日跌幅限制！'));
        }

        $hou_price = C('market')[$market]['hou_price'];
        if ($hou_price) {
            if (C('market')[$market]['zhang']) {
                $zhang_price = round(($hou_price / 100) * (100 + C('market')[$market]['zhang']), C('market')[$market]['round']);
                if ($zhang_price < $price) {
                    $this->error(L('交易价格超过今日涨幅限制！'));
                }
            }
            if (C('market')[$market]['die']) {
                $die_price = round(($hou_price / 100) * (100 - C('market')[$market]['die']), C('market')[$market]['round']);
                if ($price < $die_price) {
                    $this->error(L('交易价格超过今日跌幅限制！'));
                }
            }
        }
        echo 56666666;
        $user_coin = M('UserCoin')->where(array('userid' => $userid))->find();
        if ($type == 1) {
            //增加手续费分级,做市商0手续费20180128
            if($user['lv']==0){
                $trade_fee = C('market')[$market]['fee_buy'];
            } else {
                $trade_fee = 0;
            }
            if ($trade_fee) {
                //数量乘以价格 除以100 乘以手续费
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 + $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);//dump($mum);
            }
//            var_dump($user_coin);
//            var_dump($rmb);
//            var_dump($user_coin[$rmb]);
//            var_dump($mum);exit;
            if ($user_coin[$rmb] < $mum) {
                $this->error(C('coin')[$rmb]['title'] . L('1余额不足！'));
            }
        } else if ($type == 2) {
            //增加手续费分级,做市商0手续费20180128
            if($user['lv']==0){
                $trade_fee = C('market')[$market]['fee_sell'];
            } else {
                $trade_fee = 0;
            }
            if ($trade_fee) {

                $fee = round((($num * $price) / 100) * $trade_fee, 8);

                $mum = round((($num * $price) / 100) * (100 - $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }
//            var_dump($user_coin[$xnb]);
//            var_dump($mum);exit;
            if ($user_coin[$xnb] < $mum) {
                $this->error(C('coin')[$xnb]['title'] . L('2余额不足！'));
            }
        } else {
            $this->error(L('交易类型错误'));
        }
        if (C('market')[$market]['trade_min']) {
            if ($mum < C('market')[$market]['trade_min']) {
                $this->error(L('交易总额不能小于') . C('market')[$market]['trade_min']);
            }
        }
        if (C('market')[$market]['trade_max']) {
            if (C('market')[$market]['trade_max'] < $mum) {
                $this->error(L('交易总额不能大于') . C('market')[$market]['trade_max']);
            }
        }
        echo 666666;
        if (!$rmb) {
            $this->error(L('数据错误101'));
        }
        if (!$xnb) {
            $this->error(L('数据错误102'));
        }
        if (!$market) {
            $this->error(L('数据错误103'));
        }
        if (!$price) {
            $this->error(L('数据错误104'));
        }
        if (!$num) {
            $this->error(L('数据错误105'));
        }
        if (!$mum) {
            $this->error(L('数据错误106'));
        }
        if (!$type) {
            $this->error(L('数据错误107'));
        }
        echo 777777777777;
        try{
            echo 88888;
            $mo = M();
//            $mo->execute('unlock tables');
//            $mo->execute('set autocommit=0');
            //	$mo->execute('lock tables tw_trade write ,tw_user_coin write ,tw_finance write,tw_finance_log write,tw_user write');//处理资金变更日志

            $rs = array();
            $user_coin = $mo->table('tw_user_coin')->where(array('userid' => $userid))->find();

            if ($type == 1) {
                echo 'aaaaaaaaa';
                if ($user_coin[$rmb] < $mum) {
                    throw new \Think\Exception(C('coin')[$rmb]['title'] . L('4余额不足！'));
                }

                $finance = $mo->table('tw_finance')->where(array('userid' => $userid))->order('id desc')->find();
                $finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $userid))->find();
echo 'fffffffffff';
                $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $userid))->setDec($rmb, $mum);
                echo 'ttttttt';
                $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $userid))->setInc($rmb . 'd', $mum);
                echo 'yyyyyyyyyyyyyyyyyy';
                $rs[] = $finance_nameid = $mo->table('tw_trade')->add(array('userid' => $userid, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 1, 'addtime' => time(), 'status' => 0));

                $finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $userid))->find();
                $finance_hash = md5($userid . $finance_num_user_coin[$rmb] . $finance_num_user_coin[$rmb.'d'] . $mum . $finance_mum_user_coin[$rmb] . $finance_mum_user_coin[$rmb.'d'] . MSCODE . 'tp3.net.cn');

                $finance_num = $finance_num_user_coin[$rmb] + $finance_num_user_coin[$rmb.'d'];
echo 'eeeeeeeeeeeeeee';
                // 处理资金变更日志-----------------S

                $user_n_info = $mo->table('tw_user')->where(array('id' => $userid))->find();

                $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 0, 'amount' => $mum, 'optype' => 18, 'cointype' => 1, 'old_amount' => $finance_num_user_coin[$rmb], 'new_amount' => $finance_mum_user_coin[$rmb], 'userid' => $userid, 'adminid' => $userid,'addip'=>get_client_ip(),'position'=>1));

                $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 1, 'amount' => $mum, 'optype' => 20, 'cointype' => 1, 'old_amount' => $finance_num_user_coin[$rmb. 'd'], 'new_amount' => $finance_mum_user_coin[$rmb. 'd'], 'userid' => $userid, 'adminid' => $userid,'addip'=>get_client_ip(),'position'=>1));

                // 处理资金变更日志-----------------E

                if ($finance['mum'] < $finance_num) {
                    $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                } else {
                    $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                }
                echo 'bbbbbbbbbbbbbbbbb';
                $rs[] = $mo->table('tw_finance')->add(array('userid' => $userid, 'coinname' => $rmb, 'num_a' => $finance_num_user_coin[$rmb], 'num_b' => $finance_num_user_coin[$rmb.'d'], 'num' => $finance_num_user_coin[$rmb] + $finance_num_user_coin[$rmb.'d'], 'fee' => $mum, 'type' => 2, 'name' => 'trade', 'nameid' => $finance_nameid, 'remark' => L('交易中心-委托买入-市场') . $market, 'mum_a' => $finance_mum_user_coin[$rmb], 'mum_b' => $finance_mum_user_coin[$rmb.'d'], 'mum' => $finance_mum_user_coin[$rmb] + $finance_mum_user_coin[$rmb.'d'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));

            } else if ($type == 2) {echo 9999999;
                if ($user_coin[$xnb] < $num) {
                    throw new \Think\Exception(C('coin')[$xnb]['title'] . L('5余额不足！'));
                }
                echo 1010;
                $fin_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $userid))->find();//处理资金变更日志
                $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $userid))->setDec($xnb, $num);
                $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $userid))->setInc($xnb . 'd', $num);
                $rs[] = $mo->table('tw_trade')->add(array('userid' => $userid, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 2, 'addtime' => time(), 'status' => 0));
                $fin_user_coin_new = $mo->table('tw_user_coin')->where(array('userid' => $userid))->find();//处理资金变更日志

                // 处理资金变更日志-----------------S

                switch ($xnb) {
                    case 'hyjf':
                        $cointype = 2;//汇云品种类型2
                        break;
                    default:
                        $cointype = 3;//其他币种类型3
                        break;
                }

                $user_n_info = $mo->table('tw_user')->where(array('id' => $userid))->find();

                $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 0, 'amount' => $num, 'optype' => 19, 'cointype' => $cointype, 'old_amount' => $fin_user_coin[$xnb], 'new_amount' => $fin_user_coin_new[$xnb], 'userid' => $userid, 'adminid' => $userid,'addip'=>get_client_ip(),'position'=>1));

                $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 1, 'amount' => $num, 'optype' => 21, 'cointype' => $cointype, 'old_amount' => $fin_user_coin[$xnb. 'd'], 'new_amount' => $fin_user_coin_new[$xnb. 'd'], 'userid' => $userid, 'adminid' => $userid,'addip'=>get_client_ip(),'position'=>1));

                // 处理资金变更日志-----------------E
            } else {
                throw new \Think\Exception(L('交易类型错误'));
            }
            echo 'ccccccccccccccccccc';
            if (check_arr($rs)) {
//                $mo->execute('commit');
                //	$mo->execute('unlock tables');
            } else {
                echo 343434;exit;
                throw new \Think\Exception(L('交易失败11！'));
            }
        } catch(\Think\Exception $e) {
//            $mo->execute('rollback');
            //$mo->execute('unlock tables');
            echo 343434347777;exit;
            $this->error(L('交易失败22！'));
        }

        S('getDepth', null);
        // $this->matchingTradeall($market);//匹配玩家和虚拟交易
        // $this->matchingTrade($market);//只匹配玩家之间
        // $this->success(L('交易成功！'));
        //jhsoft即时处理交易状态和异常处理

        A('Queue')->checkDapan(); //匹配所有订单交易


        //机器人交易刷单
         A('Queue')->autojy2($market);
        //jhsoft对当前交易订单处理开始
        $corderid=$rs[2];

//        $mo = M();
//        $mo->execute('set autocommit=0');
        //$mo->execute('lock tables tw_trade write');

        $cTrade = M('Trade')->where('id ='.$corderid)->find();
        // $worker =new WokermanController();

        // $arr = New CeshiController();
        //  $arr = new CeshiController;

        //    $workerman->getNewMarkets($userid,$market,$trade_qu_id);
        //      M()->query("unlock tables");
        //  $url = $_SERVER['SERVER_NAME'].":8082?type=publish&content=".json_encode($arr)."&to=".$userid;
        if ($cTrade) {
            $cstatus=$cTrade['status'];
            $cdeal=$cTrade['deal'];

            $cnum=$cTrade['num'];

            if ($cdeal>$cnum) {
                $mo->table('tw_trade')->where(array('id' => $corderid))->save(array('deal' => Num($cnum),'status' => 1));
//                $mo->execute('commit');
                //    $mo->execute('unlock tables');
                $cstatus=1;
                $cdeal=$cnum;
            }
            if ($cstatus==1) {
                //    $this->carriedApi($url);

//                $workerman->getNewMarkets($userid,$market,$trade_qu_id);
                $this->ajaxBack('交易成功！',1);
                //$this->success("xxxxxxxxxx");
                // echo L('交易成功！交易成功！！！！！！');

            }
            if ($cstatus==0){
                if ($cdeal>0) {
                    //    $this->carriedApi($url);
//                    $workerman->getNewMarkets($userid,$market,$trade_qu_id);
                    // $this->success(L('已成功交易'.$cdeal.',余下'.($cnum-$cdeal).'自动转为委托交易中...！'));
                    $this->ajaxBack('交易成功！',1);
                    //$this->success(L());
                    //return;
                } else {
                    //       $this->carriedApi($url);
                    //$worker->getNewMarket($userid,$market,$trade_qu_id);
//                    $workerman->getNewMarkets($userid,$market,$trade_qu_id);
                    // $this->success('已自动委托交易中...！');
                    //  $this->success(L('交易成功！333')); // 卖出
                    $this->ajaxBack('交易成功！',1);
                    // $this->success(L('交易成功！1111'));
                    // return;
                    //return;
                    //   $workerman->getNewMarkets($userid,$market,$trade_qu_id);
                }
            }
        } else {
//            $workerman->getNewMarkets($userid,$market,$trade_qu_id);
            // $this->carriedApi($url);
            $this->ajaxBack('交易成功！',1);
            //  return;
        }

        //jhsoft对当前交易订单处理结束
    }
    public function ajaxError($messsage,$status,$type='json')
    {
        if(empty($messsage)){
            $this->error('messsage参数不能为空');
        }

        $arr = [
            'info'=>$messsage,
            'status'=>$status,
            'type'=>$type,
        ];
        if($type='json'){
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }
    }
    public function ceshi()
    {
        $this->display();
    }

    /**
     * @param $messsage
     * @param $status
     * @param string $type
     * ajax返回
     */
	public function ajaxBack($messsage,$status,$type='json')
    {
        if(empty($messsage)){
            $this->error($messsage.'参数不能为空');
        }
        $arr =[
            'info'=>$messsage,
            'status'=>$status,
            'type'=>$type,
        ];
        if($type='json'){
            exit(json_encode($arr,JSON_UNESCAPED_UNICODE));
            return;
        }
    }
    public function carriedApi($url,$data=null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        $output = json_decode($output,true);
        return $output;
    }
	public function chexiao($id)
	{
		// 过滤非法字符----------------S
		if (checkstr($id)) {
			$this->error(L('您输入的信息有误！'));
		}
		// 过滤非法字符----------------E

		if (!userid()) {
			$this->error(L('请先登录！'));
		}

		if (!check($id, 'd')) {
			$this->error(L('请选择要撤销的委托！'));
		}

		$trade = M('Trade')->where(array('id' => $id))->find();
		if (!$trade) {
			$this->error(L('撤销委托参数错误！'));
		}
		if ($trade['userid'] != userid()) {
			$this->error(L('参数非法！'));
		}
        $market=$_POST['market'];
        $trade_qu_id = $_POST['trade_qu_id'];
        if(empty($market)){
            $this->error(L('系统错误,参数非法！'));
        }
//        $from = $_GET['from']?$_GET['from']:1;//0为后台1为前台
		 $this->show(D('Trade')->chexiao($id),$trade['userid'],$market,$trade_qu_id);


	}

	public function show($rs = array(),$userid,$market,$trade_qu_id)
	{
		foreach ($rs as $k => $v) {
			// 过滤非法字符----------------S
			if (checkstr($v)) {
				$this->error(L('您输入的信息有误！'));
			}
			// 过滤非法字符----------------E
		}
        $workerman = A('Wokerman');
        $workerman->getNewMarkets($userid,$market,$trade_qu_id);
		if ($rs[0]) {
			$this->success($rs[1]);
		} else {
		    var_dump($rs);
		    //var_dump($rs[1]);
			$this->error('123456789');
		}
	}

    public function matchingTradeall($market = NULL)
    {
        if (!$market) {
            return false;
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }

        $fee_buy = C('market')[$market]['fee_buy'];
        $fee_sell = C('market')[$market]['fee_sell'];
        $invit_buy = C('market')[$market]['invit_buy'];
        $invit_sell = C('market')[$market]['invit_sell'];
        $invit_coin = C('market')[$market]['invit_coin'];
        $invit_1 = C('market')[$market]['invit_1'];
        $invit_2 = C('market')[$market]['invit_2'];
        $invit_3 = C('market')[$market]['invit_3'];

        $new_price = C('market')[$market]['new_price'];

        $mo = M();
        $mo->startTrans();
//        $mo->execute('set autocommit=0');
        $new_trade_btchanges = 0;

//        for (; true; ) {//先查找会员订单,如果找不到会员订单,再成交虚拟订单20170919
        for ($i = 1; $i<10; $i++) {//先查找会员订单,如果找不到会员订单,再成交虚拟订单20170919
            // 匹配非0会员↓
            $buy = $mo->table('tw_trade')->where(array('market' => $market,'userid' => array('gt',0), 'type' => 1, 'status' => 0))->order('price desc,id asc')->find();
            // if(!$buy){
            // 匹配所有会员↓
//            $buy = $mo->table('tw_trade')->where(array('market' => $market,'type' => 1, 'status' => 0))->order('price desc,id asc')->find();
            $a = $mo->table('tw_trade')->getLastSql();
            // }
            $sell = $mo->table('tw_trade')->where(array('market' => $market,'userid' => array('gt',0), 'type' => 2, 'status' => 0))->order('price asc,id asc')->find();
            // if(!$sell){
//            $map['userid'] = array('neq',$buy['userid']);
//            $sell = $mo->table('tw_trade')->where(array('market' => $market, 'type' => 2, 'status' => 0))->order('price asc,id asc')->find();
            $a = $mo->table('tw_trade')->getLastSql();
//            var_dump($a);
//            echo '<br>';
//            if(!$sell){
//                $map['userid'] = array('neq',$buy['userid']);
//                $buy = $mo->table('tw_trade')->where(array('market' => $market,'type' => 1, 'status' => 0))->where($map)->order('price desc,id asc')->find();
//                $map['userid'] = array('neq',$buy['userid']);
//                $sell = $mo->table('tw_trade')->where(array('market' => $market, 'type' => 2, 'status' => 0))->where($map)->order('price asc,id asc')->find();
//            }
//            $a = $mo->table('tw_trade')->getLastSql();
//            var_dump($a);
            // }

            if ($sell['id'] < $buy['id']) {
                $type = 1;
            } else {
                $type = 2;
            }
//            echo '<pre>1';
//var_dump($buy);
//            echo '<pre>2';
//            var_dump($sell);
//            echo '<pre>3';
//            var_dump((floatval($buy['price']) - floatval($sell['price'])));
//            1，买单单价需大于卖单单价
            if ($buy && $sell && (0 <= floatval($buy['price']) - floatval($sell['price']))) {
                $rs = array();
//                echo 111;
//                没有任何操作先注释20181220
//                if ($buy['num'] <= $buy['deal']) { }
//                if ($sell['num'] <= $sell['deal']) { }

                // $amount = min(round($buy['num'] - $buy['deal'], 8 - C('market')[$market]['round']), round($sell['num'] - $sell['deal'], 8 - C('market')[$market]['round']));
                // $amount = round($amount, 8 - C('market')[$market]['round']);//20171031
//                2，买方或卖方待交易数量取最小的一方数量作为待交易数量
                $amount = min(round($buy['num'] - $buy['deal'], C('market')[$market]['round']), round($sell['num'] - $sell['deal'],  C('market')[$market]['round']));
                $amount = round($amount,  C('market')[$market]['round']);
//                3，待交易数量小于等0时不进行交易
                if ($amount <= 0) {
                    $log = '错误1交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . "\n";
                    $log .= 'ERR: 成交数量出错，数量是' . $amount;
                    mlog($log);
                    $mo->rollback();
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                    break;
                }
//echo 1;
                if ($type == 1) {
                    $price = $sell['price'];
                } else if ($type == 2) {
                    $price = $buy['price'];
                } else { break; }

                if (!$price) {
                    $log = '错误2交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . ' 成交数量' . $amount . "\n";
                    $log .= 'ERR: 成交价格出错，价格是' . $price;
                    mlog($log);
                    $mo->rollback();
                    break;
                } else {
                    // TODO: SEPARATE
                    $price = round($price, C('market')[$market]['round']);
                }
//                4，计算交易币总金额（不包含手续费）
                $mum = round($price * $amount, 8);
                if (!$mum) {
                    $log = '错误3交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . "\n";
                    $log .= 'ERR: 成交总额出错，总额是' . $mum;
                    mlog($log);
                    $mo->rollback();
                    break;
                } else {
                    $mum = round($mum, 8);
                }
//                5，计算交易买方所需金额（包含手续费）
                if ($fee_buy) {
                    $buyuser = M('User')->where(array('id' => $buy['userid']))->find();
                    if($buyuser['lv'] == 0){
                        $buy_fee = round(($mum / 100) * $fee_buy, 8);
                        $buy_save = round(($mum / 100) * (100 + $fee_buy),8);
                    } else {
                        $buy_fee = 0;
                        $buy_save = $mum;
                    }
                } else {
                    $buy_fee = 0;
                    $buy_save = $mum;
                }

                if (!$buy_save) {
                    $log = '错误4交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . '  成交数量' . $amount . '  成交价格' . $price . '成交总额' . $mum . "\n";
                    $log .= 'ERR: 买家更新数量出错，更新数量是' . $buy_save;
                    mlog($log);
                    $mo->rollback();
                    break;
                }
//                6，计算交易卖方所需金额（包含手续费）
                if ($fee_sell) {
                    $selluser = M('User')->where(array('id' => $buy['userid']))->find();
                    if ($selluser['lv']==0) {
                        $sell_fee = round(($mum / 100) * $fee_sell, 8);
                        $sell_save = round(($mum / 100) * (100 - $fee_sell), 8);
                    } else {
                        $sell_fee = 0;
                        $sell_save = $mum;
                    }
                } else {
                    $sell_fee = 0;
                    $sell_save = $mum;
                }
//echo 2;
                if (!$sell_save) {
                    $log = '错误5交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                    $log .= 'ERR: 卖家更新数量出错，更新数量是' . $sell_save;
                    mlog($log);
                    $mo->rollback();
                    break;
                }

                if ($buy['userid'] > 0) {
                    $user_buy = M('UserCoin')->where(array('userid' => $buy['userid']))->find();
                    if (!$user_buy[$rmb . 'd']) {
                        $log = '错误6交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家财产错误，冻结财产是' . $user_buy[$rmb . 'd'];
                        mlog($log);
                        break;
                    }
                    if ($user_buy[$rmb . 'd'] < 1.0E-8) {
                        $log = '错误88交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新冻结人民币出现错误,应该更新' . $buy_save . '账号余额' . $user_buy[$rmb . 'd'] . '进行错误处理';
                        mlog($log);
                        M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                        break;
                    }
                    if ($buy_save <= round($user_buy[$rmb . 'd'], 8)) {
                        $save_buy_rmb = $buy_save;
                    } else if ($buy_save <= round($user_buy[$rmb . 'd'], 8) + 1) {
                        $save_buy_rmb = $user_buy[$rmb . 'd'];
                        $log = '错误8交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新冻结人民币出现误差,应该更新' . $buy_save . '账号余额' . $user_buy[$rmb . 'd'] . '实际更新' . $save_buy_rmb;
                        mlog($log);
                    } else {
                        $log = '错误9交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新冻结人民币出现错误,应该更新' . $buy_save . '账号余额' . $user_buy[$rmb . 'd'] . '进行错误处理';
                        mlog($log);
                        M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                        break;
                    }
                    if (!$save_buy_rmb) {
                        $log = '错误12交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新数量出错错误,更新数量是' . $save_buy_rmb;
                        mlog($log);
                        M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                        break;
                    }
                } else {
                    $save_buy_rmb = 0;
                }

                if ($sell['userid']>0) {
                    $user_sell = M('UserCoin')->where(array('userid' => $sell['userid']))->find();
                    if (!$user_sell[$xnb . 'd']) {
                        $log = '错误7交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 卖家财产错误，冻结财产是' . $user_sell[$xnb . 'd'];
                        mlog($log);
                        break;
                    }

                    // TODO: SEPARATE
                    if ($amount <= round($user_sell[$xnb . 'd'], C('market')[$market]['round'])) {
                        $save_sell_xnb = $amount;
                    } else {
                        // TODO: SEPARATE
                        if ($amount <= round($user_sell[$xnb . 'd'], C('market')[$market]['round']) + 1) {
                            $save_sell_xnb = $user_sell[$xnb . 'd'];
                            $log = '错误10交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                            $log .= 'ERR: 卖家更新冻结虚拟币出现误差,应该更新' . $amount . '账号余额' . $user_sell[$xnb . 'd'] . '实际更新' . $save_sell_xnb;
                            mlog($log);
                        } else {
                            $log = '错误11交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                            $log .= 'ERR: 卖家更新冻结虚拟币出现错误,应该更新' . $amount . '账号余额' . $user_sell[$xnb . 'd'] . '进行错误处理';
                            mlog($log);
                            M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                            break;
                        }
                    }
                    if (!$save_sell_xnb) {
                        $log = '错误13交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 卖家更新数量出错错误,更新数量是' . $save_sell_xnb;
                        mlog($log);
                        M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                        break;
                    }
                }

//                $mo->execute('set autocommit=0');
//                $mo->execute('lock tables tw_trade write,tw_trade_log write,tw_user write,tw_user_coin write,tw_invit write ,tw_finance write,tw_coin write,tw_mining write,tw_market read,tw_config read');
//              7.添加买卖方交易记录
                $rs[] = $mo->table('tw_trade')->where(array('id' => $buy['id']))->setInc('deal', $amount);
                $rs[] = $mo->table('tw_trade')->where(array('id' => $sell['id']))->setInc('deal', $amount);

                $rs[] = $finance_nameid = $mo->table('tw_trade_log')->add(array('userid' => $buy['userid'], 'peerid' => $sell['userid'], 'market' => $market, 'price' => $price, 'num' => $amount, 'mum' => $mum, 'type' => $type, 'fee_buy' => $buy_fee, 'fee_sell' => $sell_fee, 'addtime' => time(), 'status' => 1));

                if ($buy['userid']>0) {
                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setInc($xnb, $amount);
                    $finance = $mo->table('tw_finance')->where(array('userid' => $buy['userid']))->order('id desc')->find();
                    $finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();

                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setDec($rmb . 'd', $save_buy_rmb);
                    $finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();
                    $finance_hash = md5($buy['userid'] . $finance_num_user_coin['cnc'] . $finance_num_user_coin['cncd'] . $mum . $finance_mum_user_coin['cnc'] . $finance_mum_user_coin['cncd'] . MSCODE . 'tp3.net.cn');
                    $finance_num = $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'];

                    if ($finance['mum'] < $finance_num) {
                        $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                    } else {
                        $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                    }

                    $rs[] = $mo->table('tw_finance')->add(array('userid' => $buy['userid'], 'coinname' => 'cnc', 'num_a' => $finance_num_user_coin['cnc'], 'num_b' => $finance_num_user_coin['cncd'], 'num' => $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'], 'fee' => $save_buy_rmb, 'type' => 2, 'name' => 'tradelog', 'nameid' => $finance_nameid, 'remark' => '交易中心-成功买入-市场' . $market, 'mum_a' => $finance_mum_user_coin['cnc'], 'mum_b' => $finance_mum_user_coin['cncd'], 'mum' => $finance_mum_user_coin['cnc'] + $finance_mum_user_coin['cncd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
                    $finance = $mo->table('tw_finance')->where(array('userid' => $buy['userid']))->order('id desc')->find();
                } else {
                    $finance = 1; // 如果用户是0,设置为1
                }

                if($sell['userid']>0){
                    $finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->find();
                    // var_dump($finance_num_user_coin);die;
                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setInc($rmb, $sell_save);
                    $finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->find();
                    $finance_hash = md5($sell['userid'] . $finance_num_user_coin['cnc'] . $finance_num_user_coin['cncd'] . $mum . $finance_mum_user_coin['cnc'] . $finance_mum_user_coin['cncd'] . MSCODE . 'tp3.net.cn');
                    // var_dump($finance);die;

                    $finance_num = $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'];
                    if ($finance['mum'] < $finance_num) {
                        $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                    } else {
                        $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                    }

                    // var_dump($finance_status);die;
                    $rs[] = $mo->table('tw_finance')->add(array('userid' => $sell['userid'], 'coinname' => 'cnc', 'num_a' => $finance_num_user_coin['cnc'], 'num_b' => $finance_num_user_coin['cncd'], 'num' => $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'], 'fee' => $save_buy_rmb, 'type' => 1, 'name' => 'tradelog', 'nameid' => $finance_nameid, 'remark' => '交易中心-成功卖出-市场' . $market, 'mum_a' => $finance_mum_user_coin['cnc'], 'mum_b' => $finance_mum_user_coin['cncd'], 'mum' => $finance_mum_user_coin['cnc'] + $finance_mum_user_coin['cncd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
                    // die('ok');
                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setDec($xnb . 'd', $save_sell_xnb);
                }

                $buy_list = $mo->table('tw_trade')->where(array('id' => $buy['id'], 'status' => 0))->find();
                if ($buy_list) {
                    if ($buy_list['num'] <= $buy_list['deal']) {
                        $rs[] = $mo->table('tw_trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    }
                }

                $sell_list = $mo->table('tw_trade')->where(array('id' => $sell['id'], 'status' => 0))->find();
                if ($sell_list) {
                    if ($sell_list['num'] <= $sell_list['deal']) {
                        $rs[] = $mo->table('tw_trade')->where(array('id' => $sell['id']))->setField('status', 1);
                    }
                }

                if ($price < $buy['price']) {
                    $chajia_dong = round((($amount * $buy['price']) / 100) * (100 + $fee_buy), 8);
                    $chajia_shiji = round((($amount * $price) / 100) * (100 + $fee_buy), 8);
                    $chajia = round($chajia_dong - $chajia_shiji, 8);

                    if ($chajia && $buy['userid']>0) {//不处理0的用户
                        $chajia_user_buy = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();

                        if ($chajia <= round($chajia_user_buy[$rmb . 'd'], 8)) {
                            $chajia_save_buy_rmb = $chajia;
                        } else if ($chajia <= round($chajia_user_buy[$rmb . 'd'], 8) + 1) {
                            $chajia_save_buy_rmb = $chajia_user_buy[$rmb . 'd'];
                            mlog('错误91交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount, '成交价格' . $price . '成交总额' . $mum . "\n");
                            mlog('交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '成交数量' . $amount . '交易方式：' . $type . '卖家更新冻结虚拟币出现误差,应该更新' . $chajia . '账号余额' . $chajia_user_buy[$rmb . 'd'] . '实际更新' . $chajia_save_buy_rmb);
                        } else {
                            mlog('错误92交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount, '成交价格' . $price . '成交总额' . $mum . "\n");
                            mlog('交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '成交数量' . $amount . '交易方式：' . $type . '卖家更新冻结虚拟币出现错误,应该更新' . $chajia . '账号余额' . $chajia_user_buy[$rmb . 'd'] . '进行错误处理');
//                            $mo->execute('rollback');
//                            $mo->execute('unlock tables');
                            $mo->rollback();
                            M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                            M('Trade')->execute('commit');
                            break;
                        }

                        if ($chajia_save_buy_rmb) {
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setDec($rmb . 'd', $chajia_save_buy_rmb);
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setInc($rmb, $chajia_save_buy_rmb);
                        }
                    }
                }

                $you_buy = $mo->table('tw_trade')->where(array(
                    'market' => array('like', '%' . $rmb . '%'),
                    'status' => 0,
                    'userid' => $buy['userid']
                ))->find();
                $you_sell = $mo->table('tw_trade')->where(array(
                    'market' => array('like', '%' . $xnb . '%'),
                    'status' => 0,
                    'userid' => $sell['userid']
                ))->find();
                // var_dump($you_sell);die;

                if (!$you_buy) {
                    $you_user_buy = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();
                    if (0 < $you_user_buy[$rmb . 'd']) {
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setField($rmb . 'd', 0);
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setInc($rmb, $you_user_buy[$rmb . 'd']);
                    }
                }
                if (!$you_sell) {
                    $you_user_sell = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->find();
                    if (0 < $you_user_sell[$xnb . 'd']) {
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setField($xnb . 'd', 0);
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setInc($rmb, $you_user_sell[$xnb . 'd']);
                    }
                }

                $invit_buy_user = $mo->table('tw_user')->where(array('id' => $buy['userid']))->find();
                $invit_sell_user = $mo->table('tw_user')->where(array('id' => $sell['userid']))->find();
                $xnblx = M('Coin')->where(array('name'=>$xnb))->find();//交易的虚拟币类型

                if($rmb == 'cnc'){ $rmb1 = $invit_coin; } else { $rmb1 = $rmb; } // 交易佣金返现类型

                // 查询市场最新成交价
                $markets = M('market')->where(array('name' => $rmb1.'_cnc'))->field('new_price')->find();
                if ($new_price) {$new_price = $price;} else {$new_price = 0.001;} //适用于人民币交易区

                if ($invit_buy && $buy['userid']>0) {
                    // 买入交易佣金赠送给上家
                    if ($invit_1) {
                        if ($buy_fee) {
                            if ($invit_buy_user['invit_1']) {
                                //$invit_buy_save_1 = round(($buy_fee / 100) * $invit_1, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_buy_save_1 = round((($buy_fee * ($invit_1 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_buy_save_1) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_buy_user['invit_1'], 'invit' => $buy['userid'], 'name' => '一代买入赠送', 'type' => $xnblx['title'].'买入交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_buy_user['invit_2']) {
                                //$invit_buy_save_2 = round(($buy_fee / 100) * $invit_2, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_buy_save_2 = round((($buy_fee * ($invit_2 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_buy_save_2) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_buy_user['invit_2'], 'invit' => $buy['userid'], 'name' => '二代买入赠送', 'type' => $xnblx['title'].'买入交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_2, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_buy_user['invit_3']) {
                                //$invit_buy_save_3 = round(($buy_fee / 100) * $invit_3, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_buy_save_3 = round((($buy_fee * ($invit_3 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_buy_save_3) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_buy_user['invit_3'], 'invit' => $buy['userid'], 'name' => '三代买入赠送', 'type' => $xnblx['title'].'买入交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_3, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }
                        }
                    }

                    // 卖出交易佣金赠送给上家
                    if ($invit_sell && $invit_sell['userid']>0) {//不处理0用户
                        if ($sell_fee) {
                            if ($invit_sell_user['invit_1']) {
                                //$invit_sell_save_1 = round(($sell_fee / 100) * $invit_1, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_sell_save_1 = round((($sell_fee * ($invit_1 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_sell_save_1) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_sell_user['invit_1'], 'invit' => $sell['userid'], 'name' => '一代卖出赠送', 'type' => $xnblx['title'].'卖出交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_sell_user['invit_2']) {
                                //$invit_sell_save_2 = round(($sell_fee / 100) * $invit_2, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_sell_save_2 = round((($sell_fee * ($invit_2 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_sell_save_2) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_sell_user['invit_2'], 'invit' => $sell['userid'], 'name' => '二代卖出赠送', 'type' => $xnblx['title'].'卖出交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_2, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_sell_user['invit_3']) {
                                //$invit_sell_save_3 = round(($sell_fee / 100) * $invit_3, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_sell_save_3 = round((($sell_fee * ($invit_3 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_sell_save_3) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_sell_user['invit_3'], 'invit' => $sell['userid'], 'name' => '三代卖出赠送', 'type' => $xnblx['title'].'卖出交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_3, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }
                        }
                    }
                }


                // 交易挖矿模块
                $Configs = M('config')->where(array('id' => 1))->find();
                $coin_name_s = $Configs['mining_coin']; // 交易挖矿赠送币种
                $mining_coin_num = $Configs['mining_coin_num']; // 交易挖矿比例

                // 查询市场最新成交价
                $markets_2 = M('market')->where(array('name' => $coin_name_s.'_cnc'))->field('new_price')->find();

                if ($Configs['mining_type'] == 1) {
                    // 判断是否自买自卖 && 判断是否做市商
                    if ($buy['userid'] == $sell['userid'] || $buyuser['lv']==1 || $selluser['lv']==1) {}else {
                        if ($mining_coin_num && $buy['userid']>0) {
                            // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                            $mining_buy_save = round((($buy_fee * ($mining_coin_num / 100)) * $new_price) / $markets_2['new_price'], 8);

                            $rs[] = $mo->table('tw_mining')->add(array('userid' => $buy['userid'], 'name' => '交易挖矿买入奖励', 'type' => '买入'.$xnblx['title'].'交易挖矿赠送'.strtoupper($coin_name_s), 'num' => $amount, 'mum' => $mum, 'fee' => $mining_buy_save, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name_s)));
                        }
                        if ($mining_coin_num && $sell['userid']>0) {

                            // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                            $mining_sell_save = round((($sell_fee * ($mining_coin_num / 100)) * $new_price) / $markets_2['new_price'], 8);

                            $rs[] = $mo->table('tw_mining')->add(array('userid' => $sell['userid'], 'name' => '交易挖矿卖出奖励', 'type' => '卖出'.$xnblx['title'].'交易挖矿赠送'.strtoupper($coin_name_s), 'num' => $amount, 'mum' => $mum, 'fee' => $mining_sell_save, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name_s)));
                        }
                    }

                }
//              清空交易信息缓存
                if (check_arr($rs)) {

//                    $workerman =A('Wokerman');
//                    if($type==1){
//                        //买单给卖家推送
//                        $workerman->weituo($sell['userid'],$market);
//                    }else if($type==2){
//                        //卖单给买家推送
//                        $workerman->weituo($buy['userid'],$market);
//                    }
//                    $workerman->getDeal($market);

                    $workerman =A('Wokerman');
                    if($type==1){
                        //买单给卖家推送
                        $workerman->weituo($sell['userid'],$market);
                    }else if($type==2){
                        //卖单给买家推送
                        $workerman->weituo($buy['userid'],$market);
                    }
                   $workerman->getDeal($market,1);
//                    $mo->execute('commit');

//                    $mo->execute('unlock tables');
                    $new_trade_btchanges = 1;
                    $coin = $xnb;
                    S('allsum', null);
                    S('getJsonTop' . $market, null);
                    S('getTradelog' . $market, null);
                    S('getDepth' . $market . '1', null);
                    S('getDepth' . $market . '3', null);
                    S('getDepth' . $market . '4', null);
                    S('ChartgetJsonData' . $market, null);
                    S('allcoin', null);
                    S('trends', null);
                } else {
                    $mo->rollback();
//                    $mo->execute('rollback');
//                    $mo->execute('unlock tables');
                }
            } else {
                break;
            }

            unset($rs);
        }

        $mo->commit();
//        更新市场信息
        if ($new_trade_btchanges) {
//            最新的交易价格
            $new_price = round(M('TradeLog')->where(array('market' => $market, 'status' => 1))->order('id desc')->getField('price'), 6);
//            未完成交易买单最高价格
            $buy_price = round(M('Trade')->where(array('type' => 1, 'market' => $market, 'status' => 0))->max('price'), 6);
//            未完成交易卖单最低价格
            $sell_price = round(M('Trade')->where(array('type' => 2, 'market' => $market, 'status' => 0))->min('price'), 6);
//            当天交易记录最低价格
            $min_price = round(M('TradeLog')->where(array(
                'market'  => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->min('price'), 6);
//            当天交易记录最高价格
            $max_price = round(M('TradeLog')->where(array(
                'market'  => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->max('price'), 6);
//            当天成交总数量
            $volume = round(M('TradeLog')->where(array(
                'market'  => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->sum('num'), 6);
//            当天开盘价格
            $sta_price = round(M('TradeLog')->where(array(
                'market'  => $market,
                'status'  => 1,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->order('id asc')->getField('price'), 6);
//            更新市场信息
            $Cmarket = M('Market')->where(array('name' => $market))->find();
            if ($Cmarket['new_price'] != $new_price) {
                $upCoinData['new_price'] = $new_price;
            }
            if ($Cmarket['buy_price'] != $buy_price) {
                $upCoinData['buy_price'] = $buy_price;
            }
            if ($Cmarket['sell_price'] != $sell_price) {
                $upCoinData['sell_price'] = $sell_price;
            }
            if ($Cmarket['min_price'] != $min_price) {
                $upCoinData['min_price'] = $min_price;
            }
            if ($Cmarket['max_price'] != $max_price) {
                $upCoinData['max_price'] = $max_price;
            }
            if ($Cmarket['volume'] != $volume) {
                $upCoinData['volume'] = $volume;
            }

            // 计算涨跌幅
            $change = round((($new_price - $Cmarket['hou_price']) / $Cmarket['hou_price']) * 100, 2);
            $upCoinData['change'] = $change;

            if ($upCoinData) {
                M('Market')->where(array('name' => $market))->save($upCoinData);
//                M('Market')->execute('commit');
                S('home_market', null);
            }
        }
    }
    public function autoMatchingTradeall($market = NULL)
    {
        $whereTime = time()-120;
        $updateStatusWhere = [
            'userid' => 0,
            'status' => 0,
            'addtime' => ['lt',$whereTime]
        ];
        M('trade')->where($updateStatusWhere)->setField('status',1);
        if (!$market) {
            return false;
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }
echo 11111;
        $fee_buy = C('market')[$market]['fee_buy'];
        $fee_sell = C('market')[$market]['fee_sell'];
        $invit_buy = C('market')[$market]['invit_buy'];
        $invit_sell = C('market')[$market]['invit_sell'];
        $invit_coin = C('market')[$market]['invit_coin'];
        $invit_1 = C('market')[$market]['invit_1'];
        $invit_2 = C('market')[$market]['invit_2'];
        $invit_3 = C('market')[$market]['invit_3'];

        $new_price = C('market')[$market]['new_price'];

        $mo = M();
        $mo->startTrans();
//        $mo->execute('set autocommit=0');
        $new_trade_btchanges = 0;

//        for (; true; ) {//先查找会员订单,如果找不到会员订单,再成交虚拟订单20170919
        for ($i = 1; $i<10; $i++) {//先查找会员订单,如果找不到会员订单,再成交虚拟订单20170919
            // 匹配非0会员↓
            $buy = $mo->table('tw_trade')->where(array('market' => $market,'userid' => 0, 'type' => 1, 'status' => 0))->order('price desc,id asc')->find();
            // if(!$buy){
            // 匹配所有会员↓
//            $buy = $mo->table('tw_trade')->where(array('market' => $market,'type' => 1, 'status' => 0))->order('price desc,id asc')->find();
            $a = $mo->table('tw_trade')->getLastSql();
            // }
            $sell = $mo->table('tw_trade')->where(array('market' => $market,'userid' => 0, 'type' => 2, 'status' => 0))->order('price asc,id asc')->find();
            // if(!$sell){
//            $map['userid'] = array('neq',$buy['userid']);
//            $sell = $mo->table('tw_trade')->where(array('market' => $market, 'type' => 2, 'status' => 0))->order('price asc,id asc')->find();
            $a = $mo->table('tw_trade')->getLastSql();
//            var_dump($a);
//            echo '<br>';
//            if(!$sell){
//                $map['userid'] = array('neq',$buy['userid']);
//                $buy = $mo->table('tw_trade')->where(array('market' => $market,'type' => 1, 'status' => 0))->where($map)->order('price desc,id asc')->find();
//                $map['userid'] = array('neq',$buy['userid']);
//                $sell = $mo->table('tw_trade')->where(array('market' => $market, 'type' => 2, 'status' => 0))->where($map)->order('price asc,id asc')->find();
//            }
//            $a = $mo->table('tw_trade')->getLastSql();
//            var_dump($a);
            // }

            if ($sell['id'] < $buy['id']) {
                $type = 1;
            } else {
                $type = 2;
            }
            echo '<pre>1';
var_dump($buy);
            echo '<pre>2';
            var_dump($sell);
            echo '<pre>3';
            var_dump((floatval($buy['price']) - floatval($sell['price'])));
//            1，买单单价需大于卖单单价
            if ($buy && $sell && (0 <= floatval($buy['price']) - floatval($sell['price']))) {
                $rs = array();
//                echo 111;
//                没有任何操作先注释20181220
//                if ($buy['num'] <= $buy['deal']) { }
//                if ($sell['num'] <= $sell['deal']) { }

                // $amount = min(round($buy['num'] - $buy['deal'], 8 - C('market')[$market]['round']), round($sell['num'] - $sell['deal'], 8 - C('market')[$market]['round']));
                // $amount = round($amount, 8 - C('market')[$market]['round']);//20171031
//                2，买方或卖方待交易数量取最小的一方数量作为待交易数量
                $amount = min(round($buy['num'] - $buy['deal'], C('market')[$market]['round']), round($sell['num'] - $sell['deal'],  C('market')[$market]['round']));
                $amount = round($amount,  C('market')[$market]['round']);
//                3，待交易数量小于等0时不进行交易
                if ($amount <= 0) {
                    $log = '错误1交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . "\n";
                    $log .= 'ERR: 成交数量出错，数量是' . $amount;
                    mlog($log);
                    $mo->rollback();
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                    break;
                }
//echo 1;
                if ($type == 1) {
                    $price = $sell['price'];
                } else if ($type == 2) {
                    $price = $buy['price'];
                } else { break; }

                if (!$price) {
                    $log = '错误2交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . ' 成交数量' . $amount . "\n";
                    $log .= 'ERR: 成交价格出错，价格是' . $price;
                    mlog($log);
                    $mo->rollback();
                    break;
                } else {
                    // TODO: SEPARATE
                    $price = round($price, C('market')[$market]['round']);
                }
//                4，计算交易币总金额（不包含手续费）
                $mum = round($price * $amount, 8);
                if (!$mum) {
                    $log = '错误3交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . "\n";
                    $log .= 'ERR: 成交总额出错，总额是' . $mum;
                    mlog($log);
                    $mo->rollback();
                    break;
                } else {
                    $mum = round($mum, 8);
                }
//                5，计算交易买方所需金额（包含手续费）
                if ($fee_buy) {
                    $buyuser = M('User')->where(array('id' => $buy['userid']))->find();
                    if($buyuser['lv'] == 0){
                        $buy_fee = round(($mum / 100) * $fee_buy, 8);
                        $buy_save = round(($mum / 100) * (100 + $fee_buy),8);
                    } else {
                        $buy_fee = 0;
                        $buy_save = $mum;
                    }
                } else {
                    $buy_fee = 0;
                    $buy_save = $mum;
                }

                if (!$buy_save) {
                    $log = '错误4交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . '  成交数量' . $amount . '  成交价格' . $price . '成交总额' . $mum . "\n";
                    $log .= 'ERR: 买家更新数量出错，更新数量是' . $buy_save;
                    mlog($log);
                    $mo->rollback();
                    break;
                }
//                6，计算交易卖方所需金额（包含手续费）
                if ($fee_sell) {
                    $selluser = M('User')->where(array('id' => $buy['userid']))->find();
                    if ($selluser['lv']==0) {
                        $sell_fee = round(($mum / 100) * $fee_sell, 8);
                        $sell_save = round(($mum / 100) * (100 - $fee_sell), 8);
                    } else {
                        $sell_fee = 0;
                        $sell_save = $mum;
                    }
                } else {
                    $sell_fee = 0;
                    $sell_save = $mum;
                }
//echo 2;
                if (!$sell_save) {
                    $log = '错误5交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                    $log .= 'ERR: 卖家更新数量出错，更新数量是' . $sell_save;
                    mlog($log);
                    $mo->rollback();
                    break;
                }

                if ($buy['userid'] > 0) {
                    $user_buy = M('UserCoin')->where(array('userid' => $buy['userid']))->find();
                    if (!$user_buy[$rmb . 'd']) {
                        $log = '错误6交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家财产错误，冻结财产是' . $user_buy[$rmb . 'd'];
                        mlog($log);
                        break;
                    }
                    if ($user_buy[$rmb . 'd'] < 1.0E-8) {
                        $log = '错误88交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新冻结人民币出现错误,应该更新' . $buy_save . '账号余额' . $user_buy[$rmb . 'd'] . '进行错误处理';
                        mlog($log);
                        M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                        break;
                    }
                    if ($buy_save <= round($user_buy[$rmb . 'd'], 8)) {
                        $save_buy_rmb = $buy_save;
                    } else if ($buy_save <= round($user_buy[$rmb . 'd'], 8) + 1) {
                        $save_buy_rmb = $user_buy[$rmb . 'd'];
                        $log = '错误8交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新冻结人民币出现误差,应该更新' . $buy_save . '账号余额' . $user_buy[$rmb . 'd'] . '实际更新' . $save_buy_rmb;
                        mlog($log);
                    } else {
                        $log = '错误9交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新冻结人民币出现错误,应该更新' . $buy_save . '账号余额' . $user_buy[$rmb . 'd'] . '进行错误处理';
                        mlog($log);
                        M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                        break;
                    }
                    if (!$save_buy_rmb) {
                        $log = '错误12交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新数量出错错误,更新数量是' . $save_buy_rmb;
                        mlog($log);
                        M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                        break;
                    }
                } else {
                    $save_buy_rmb = 0;
                }

                if ($sell['userid']>0) {
                    $user_sell = M('UserCoin')->where(array('userid' => $sell['userid']))->find();
                    if (!$user_sell[$xnb . 'd']) {
                        $log = '错误7交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 卖家财产错误，冻结财产是' . $user_sell[$xnb . 'd'];
                        mlog($log);
                        break;
                    }

                    // TODO: SEPARATE
                    if ($amount <= round($user_sell[$xnb . 'd'], C('market')[$market]['round'])) {
                        $save_sell_xnb = $amount;
                    } else {
                        // TODO: SEPARATE
                        if ($amount <= round($user_sell[$xnb . 'd'], C('market')[$market]['round']) + 1) {
                            $save_sell_xnb = $user_sell[$xnb . 'd'];
                            $log = '错误10交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                            $log .= 'ERR: 卖家更新冻结虚拟币出现误差,应该更新' . $amount . '账号余额' . $user_sell[$xnb . 'd'] . '实际更新' . $save_sell_xnb;
                            mlog($log);
                        } else {
                            $log = '错误11交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                            $log .= 'ERR: 卖家更新冻结虚拟币出现错误,应该更新' . $amount . '账号余额' . $user_sell[$xnb . 'd'] . '进行错误处理';
                            mlog($log);
                            M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                            break;
                        }
                    }
                    if (!$save_sell_xnb) {
                        $log = '错误13交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 卖家更新数量出错错误,更新数量是' . $save_sell_xnb;
                        mlog($log);
                        M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                        break;
                    }
                }

//                $mo->execute('set autocommit=0');
//                $mo->execute('lock tables tw_trade write,tw_trade_log write,tw_user write,tw_user_coin write,tw_invit write ,tw_finance write,tw_coin write,tw_mining write,tw_market read,tw_config read');
//              7.添加买卖方交易记录
                $rs[] = $mo->table('tw_trade')->where(array('id' => $buy['id']))->setInc('deal', $amount);
                $rs[] = $mo->table('tw_trade')->where(array('id' => $sell['id']))->setInc('deal', $amount);

                $rs[] = $finance_nameid = $mo->table('tw_trade_log')->add(array('userid' => $buy['userid'], 'peerid' => $sell['userid'], 'market' => $market, 'price' => $price, 'num' => $amount, 'mum' => $mum, 'type' => $type, 'fee_buy' => $buy_fee, 'fee_sell' => $sell_fee, 'addtime' => time(), 'status' => 1));
                mlog($mo->table('tw_trade_log')->getLastSql());
                if ($buy['userid']>0) {
                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setInc($xnb, $amount);
                    $finance = $mo->table('tw_finance')->where(array('userid' => $buy['userid']))->order('id desc')->find();
                    $finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();

                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setDec($rmb . 'd', $save_buy_rmb);
                    $finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();
                    $finance_hash = md5($buy['userid'] . $finance_num_user_coin['cnc'] . $finance_num_user_coin['cncd'] . $mum . $finance_mum_user_coin['cnc'] . $finance_mum_user_coin['cncd'] . MSCODE . 'tp3.net.cn');
                    $finance_num = $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'];

                    if ($finance['mum'] < $finance_num) {
                        $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                    } else {
                        $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                    }

                    $rs[] = $mo->table('tw_finance')->add(array('userid' => $buy['userid'], 'coinname' => 'cnc', 'num_a' => $finance_num_user_coin['cnc'], 'num_b' => $finance_num_user_coin['cncd'], 'num' => $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'], 'fee' => $save_buy_rmb, 'type' => 2, 'name' => 'tradelog', 'nameid' => $finance_nameid, 'remark' => '交易中心-成功买入-市场' . $market, 'mum_a' => $finance_mum_user_coin['cnc'], 'mum_b' => $finance_mum_user_coin['cncd'], 'mum' => $finance_mum_user_coin['cnc'] + $finance_mum_user_coin['cncd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
                    $finance = $mo->table('tw_finance')->where(array('userid' => $buy['userid']))->order('id desc')->find();
                } else {
                    $finance = 1; // 如果用户是0,设置为1
                }

                if($sell['userid']>0){
                    $finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->find();
                    // var_dump($finance_num_user_coin);die;
                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setInc($rmb, $sell_save);
                    $finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->find();
                    $finance_hash = md5($sell['userid'] . $finance_num_user_coin['cnc'] . $finance_num_user_coin['cncd'] . $mum . $finance_mum_user_coin['cnc'] . $finance_mum_user_coin['cncd'] . MSCODE . 'tp3.net.cn');
                    // var_dump($finance);die;

                    $finance_num = $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'];
                    if ($finance['mum'] < $finance_num) {
                        $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                    } else {
                        $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                    }

                    // var_dump($finance_status);die;
                    $rs[] = $mo->table('tw_finance')->add(array('userid' => $sell['userid'], 'coinname' => 'cnc', 'num_a' => $finance_num_user_coin['cnc'], 'num_b' => $finance_num_user_coin['cncd'], 'num' => $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'], 'fee' => $save_buy_rmb, 'type' => 1, 'name' => 'tradelog', 'nameid' => $finance_nameid, 'remark' => '交易中心-成功卖出-市场' . $market, 'mum_a' => $finance_mum_user_coin['cnc'], 'mum_b' => $finance_mum_user_coin['cncd'], 'mum' => $finance_mum_user_coin['cnc'] + $finance_mum_user_coin['cncd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
                    // die('ok');
                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setDec($xnb . 'd', $save_sell_xnb);
                }

                $buy_list = $mo->table('tw_trade')->where(array('id' => $buy['id'], 'status' => 0))->find();
                if ($buy_list) {
                    if ($buy_list['num'] <= $buy_list['deal']) {
                        $rs[] = $mo->table('tw_trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    }
                }

                $sell_list = $mo->table('tw_trade')->where(array('id' => $sell['id'], 'status' => 0))->find();
                if ($sell_list) {
                    if ($sell_list['num'] <= $sell_list['deal']) {
                        $rs[] = $mo->table('tw_trade')->where(array('id' => $sell['id']))->setField('status', 1);
                    }
                }

                if ($price < $buy['price']) {
                    $chajia_dong = round((($amount * $buy['price']) / 100) * (100 + $fee_buy), 8);
                    $chajia_shiji = round((($amount * $price) / 100) * (100 + $fee_buy), 8);
                    $chajia = round($chajia_dong - $chajia_shiji, 8);

                    if ($chajia && $buy['userid']>0) {//不处理0的用户
                        $chajia_user_buy = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();

                        if ($chajia <= round($chajia_user_buy[$rmb . 'd'], 8)) {
                            $chajia_save_buy_rmb = $chajia;
                        } else if ($chajia <= round($chajia_user_buy[$rmb . 'd'], 8) + 1) {
                            $chajia_save_buy_rmb = $chajia_user_buy[$rmb . 'd'];
                            mlog('错误91交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount, '成交价格' . $price . '成交总额' . $mum . "\n");
                            mlog('交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '成交数量' . $amount . '交易方式：' . $type . '卖家更新冻结虚拟币出现误差,应该更新' . $chajia . '账号余额' . $chajia_user_buy[$rmb . 'd'] . '实际更新' . $chajia_save_buy_rmb);
                        } else {
                            mlog('错误92交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount, '成交价格' . $price . '成交总额' . $mum . "\n");
                            mlog('交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '成交数量' . $amount . '交易方式：' . $type . '卖家更新冻结虚拟币出现错误,应该更新' . $chajia . '账号余额' . $chajia_user_buy[$rmb . 'd'] . '进行错误处理');
//                            $mo->execute('rollback');
//                            $mo->execute('unlock tables');
                            $mo->rollback();
                            M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                            M('Trade')->execute('commit');
                            break;
                        }

                        if ($chajia_save_buy_rmb) {
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setDec($rmb . 'd', $chajia_save_buy_rmb);
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setInc($rmb, $chajia_save_buy_rmb);
                        }
                    }
                }

                $you_buy = $mo->table('tw_trade')->where(array(
                    'market' => array('like', '%' . $rmb . '%'),
                    'status' => 0,
                    'userid' => $buy['userid']
                ))->find();
                $you_sell = $mo->table('tw_trade')->where(array(
                    'market' => array('like', '%' . $xnb . '%'),
                    'status' => 0,
                    'userid' => $sell['userid']
                ))->find();
                // var_dump($you_sell);die;

                if (!$you_buy) {
                    $you_user_buy = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();
                    if (0 < $you_user_buy[$rmb . 'd']) {
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setField($rmb . 'd', 0);
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setInc($rmb, $you_user_buy[$rmb . 'd']);
                    }
                }
                if (!$you_sell) {
                    $you_user_sell = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->find();
                    if (0 < $you_user_sell[$xnb . 'd']) {
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setField($xnb . 'd', 0);
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setInc($rmb, $you_user_sell[$xnb . 'd']);
                    }
                }

                $invit_buy_user = $mo->table('tw_user')->where(array('id' => $buy['userid']))->find();
                $invit_sell_user = $mo->table('tw_user')->where(array('id' => $sell['userid']))->find();
                $xnblx = M('Coin')->where(array('name'=>$xnb))->find();//交易的虚拟币类型

                if($rmb == 'cnc'){ $rmb1 = $invit_coin; } else { $rmb1 = $rmb; } // 交易佣金返现类型

                // 查询市场最新成交价
                $markets = M('market')->where(array('name' => $rmb1.'_cnc'))->field('new_price')->find();
                if ($new_price) {$new_price = $price;} else {$new_price = 0.001;} //适用于人民币交易区

                if ($invit_buy && $buy['userid']>0) {
                    // 买入交易佣金赠送给上家
                    if ($invit_1) {
                        if ($buy_fee) {
                            if ($invit_buy_user['invit_1']) {
                                //$invit_buy_save_1 = round(($buy_fee / 100) * $invit_1, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_buy_save_1 = round((($buy_fee * ($invit_1 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_buy_save_1) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_buy_user['invit_1'], 'invit' => $buy['userid'], 'name' => '一代买入赠送', 'type' => $xnblx['title'].'买入交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_buy_user['invit_2']) {
                                //$invit_buy_save_2 = round(($buy_fee / 100) * $invit_2, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_buy_save_2 = round((($buy_fee * ($invit_2 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_buy_save_2) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_buy_user['invit_2'], 'invit' => $buy['userid'], 'name' => '二代买入赠送', 'type' => $xnblx['title'].'买入交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_2, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_buy_user['invit_3']) {
                                //$invit_buy_save_3 = round(($buy_fee / 100) * $invit_3, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_buy_save_3 = round((($buy_fee * ($invit_3 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_buy_save_3) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_buy_user['invit_3'], 'invit' => $buy['userid'], 'name' => '三代买入赠送', 'type' => $xnblx['title'].'买入交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_3, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }
                        }
                    }

                    // 卖出交易佣金赠送给上家
                    if ($invit_sell && $invit_sell['userid']>0) {//不处理0用户
                        if ($sell_fee) {
                            if ($invit_sell_user['invit_1']) {
                                //$invit_sell_save_1 = round(($sell_fee / 100) * $invit_1, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_sell_save_1 = round((($sell_fee * ($invit_1 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_sell_save_1) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_sell_user['invit_1'], 'invit' => $sell['userid'], 'name' => '一代卖出赠送', 'type' => $xnblx['title'].'卖出交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_sell_user['invit_2']) {
                                //$invit_sell_save_2 = round(($sell_fee / 100) * $invit_2, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_sell_save_2 = round((($sell_fee * ($invit_2 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_sell_save_2) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_sell_user['invit_2'], 'invit' => $sell['userid'], 'name' => '二代卖出赠送', 'type' => $xnblx['title'].'卖出交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_2, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_sell_user['invit_3']) {
                                //$invit_sell_save_3 = round(($sell_fee / 100) * $invit_3, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_sell_save_3 = round((($sell_fee * ($invit_3 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_sell_save_3) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_sell_user['invit_3'], 'invit' => $sell['userid'], 'name' => '三代卖出赠送', 'type' => $xnblx['title'].'卖出交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_3, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }
                        }
                    }
                }


                // 交易挖矿模块
                $Configs = M('config')->where(array('id' => 1))->find();
                $coin_name_s = $Configs['mining_coin']; // 交易挖矿赠送币种
                $mining_coin_num = $Configs['mining_coin_num']; // 交易挖矿比例

                // 查询市场最新成交价
                $markets_2 = M('market')->where(array('name' => $coin_name_s.'_cnc'))->field('new_price')->find();

                if ($Configs['mining_type'] == 1) {
                    // 判断是否自买自卖 && 判断是否做市商
                    if ($buy['userid'] == $sell['userid'] || $buyuser['lv']==1 || $selluser['lv']==1) {}else {
                        if ($mining_coin_num && $buy['userid']>0) {
                            // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                            $mining_buy_save = round((($buy_fee * ($mining_coin_num / 100)) * $new_price) / $markets_2['new_price'], 8);

                            $rs[] = $mo->table('tw_mining')->add(array('userid' => $buy['userid'], 'name' => '交易挖矿买入奖励', 'type' => '买入'.$xnblx['title'].'交易挖矿赠送'.strtoupper($coin_name_s), 'num' => $amount, 'mum' => $mum, 'fee' => $mining_buy_save, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name_s)));
                        }
                        if ($mining_coin_num && $sell['userid']>0) {

                            // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                            $mining_sell_save = round((($sell_fee * ($mining_coin_num / 100)) * $new_price) / $markets_2['new_price'], 8);

                            $rs[] = $mo->table('tw_mining')->add(array('userid' => $sell['userid'], 'name' => '交易挖矿卖出奖励', 'type' => '卖出'.$xnblx['title'].'交易挖矿赠送'.strtoupper($coin_name_s), 'num' => $amount, 'mum' => $mum, 'fee' => $mining_sell_save, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name_s)));
                        }
                    }

                }
                $mo->commit();
//              清空交易信息缓存
                if (check_arr($rs)) {
//                    $mo->execute('commit');

//                    $mo->execute('unlock tables');
                    $new_trade_btchanges = 1;
                    $coin = $xnb;
                    S('allsum', null);
                    S('getJsonTop' . $market, null);
                    S('getTradelog' . $market, null);
                    S('getDepth' . $market . '1', null);
                    S('getDepth' . $market . '3', null);
                    S('getDepth' . $market . '4', null);
                    S('ChartgetJsonData' . $market, null);
                    S('allcoin', null);
                    S('trends', null);
                } else {
                    $mo->rollback();
//                    $mo->execute('rollback');
//                    $mo->execute('unlock tables');
                }
            } else {
                break;
            }

            unset($rs);
        }

//        $mo->commit();
//        更新市场信息
       // if ($new_trade_btchanges) {
        if (1) {
//            最新的交易价格
            $new_price = round(M('TradeLog')->where(array('market' => $market, 'status' => 1))->order('id desc')->getField('price'), 6);
//            未完成交易买单最高价格
            $buy_price = round(M('Trade')->where(array('type' => 1, 'market' => $market, 'status' => 0))->max('price'), 6);
//            未完成交易卖单最低价格
            $sell_price = round(M('Trade')->where(array('type' => 2, 'market' => $market, 'status' => 0))->min('price'), 6);
//            当天交易记录最低价格
            $min_price = round(M('TradeLog')->where(array(
                'market'  => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->min('price'), 6);
//            当天交易记录最高价格
            $max_price = round(M('TradeLog')->where(array(
                'market'  => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->max('price'), 6);
//            当天成交总数量
            $volume = round(M('TradeLog')->where(array(
                'market'  => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->sum('num'), 6);
//            当天开盘价格
            $sta_price = round(M('TradeLog')->where(array(
                'market'  => $market,
                'status'  => 1,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->order('id asc')->getField('price'), 6);
//            更新市场信息
            $Cmarket = M('Market')->where(array('name' => $market))->find();
            if ($Cmarket['new_price'] != $new_price) {
                $upCoinData['new_price'] = $new_price;
            }
            if ($Cmarket['buy_price'] != $buy_price) {
                $upCoinData['buy_price'] = $buy_price;
            }
            if ($Cmarket['sell_price'] != $sell_price) {
                $upCoinData['sell_price'] = $sell_price;
            }
            if ($Cmarket['min_price'] != $min_price) {
                $upCoinData['min_price'] = $min_price;
            }
            if ($Cmarket['max_price'] != $max_price) {
                $upCoinData['max_price'] = $max_price;
            }
            if ($Cmarket['volume'] != $volume) {
                $upCoinData['volume'] = $volume;
            }

            // 计算涨跌幅
            //最新成交价 减去昨日收盘价除以收盘价 乘以100
            $change = round((($new_price - $Cmarket['hou_price']) / $Cmarket['hou_price']) * 100, 2);
            $upCoinData['change'] = $change;

            if ($upCoinData) {
                M('Market')->where(array('name' => $market))->save($upCoinData);
//                M('Market')->execute('commit');
                S('home_market', null);
            }
        }

//        try{
//            $workerman =A('Wokerman');
//            $workerman->sendToSuccess($market);
//        }catch(\Think\Exception $e) {
//
//        }
//        $workerman =A('Wokerman');
//        if($type==1){
//            //买单给卖家推送
//            $workerman->weituo($sell['userid'],$market);
//
//        }else if($type==2){
//            //卖单给买家推送
//            $workerman->weituo($buy['userid'],$market);
//        }
    }
    public function autoMatchingTradeallbak($market = NULL)
    {echo $market.'111111';
        echo '<br>';
        if (!$market) {
            return false;
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }

        $fee_buy = C('market')[$market]['fee_buy'];
        $fee_sell = C('market')[$market]['fee_sell'];
        $invit_buy = C('market')[$market]['invit_buy'];
        $invit_sell = C('market')[$market]['invit_sell'];
        $invit_coin = C('market')[$market]['invit_coin'];
        $invit_1 = C('market')[$market]['invit_1'];
        $invit_2 = C('market')[$market]['invit_2'];
        $invit_3 = C('market')[$market]['invit_3'];

        $new_price = C('market')[$market]['new_price'];

        $mo = M();
        $new_trade_btchanges = 0;

//        for (; true; ) {//先查找会员订单,如果找不到会员订单,再成交虚拟订单20170919
        for ($i = 1; $i<10; $i++) {//先查找会员订单,如果找不到会员订单,再成交虚拟订单20170919
//            $mo->startTrans();
            // 匹配非0会员↓
            $buy = $mo->table('tw_trade')->where(array('market' => $market,'userid' => 0, 'type' => 1, 'status' => 0))->order('price desc,id asc')->find();
            // if(!$buy){
            // 匹配所有会员↓
//            $buy = $mo->table('tw_trade')->where(array('market' => $market,'type' => 1, 'status' => 0))->order('price desc,id asc')->find();
            $a = $mo->table('tw_trade')->getLastSql();
            //var_dump($a);
            echo '<br>';
            // }
            $sell = $mo->table('tw_trade')->where(array('market' => $market,'userid' => 0, 'type' => 2, 'status' => 0))->order('price asc,id asc')->find();
            // if(!$sell){
//            $map['userid'] = array('neq',$buy['userid']);
//            $sell = $mo->table('tw_trade')->where(array('market' => $market, 'type' => 2, 'status' => 0))->order('price asc,id asc')->find();
            $a = $mo->table('tw_trade')->getLastSql();
            var_dump($a);
            echo '<br>';
//            if(!$sell){
//                $map['userid'] = array('neq',$buy['userid']);
//                $buy = $mo->table('tw_trade')->where(array('market' => $market,'type' => 1, 'status' => 0))->where($map)->order('price desc,id asc')->find();
//                $map['userid'] = array('neq',$buy['userid']);
//                $sell = $mo->table('tw_trade')->where(array('market' => $market, 'type' => 2, 'status' => 0))->where($map)->order('price asc,id asc')->find();
//            }
//            $a = $mo->table('tw_trade')->getLastSql();
//            var_dump($a);
            // }

            if ($sell['id'] < $buy['id']) {
                $type = 1;
            } else {
                $type = 2;
            }
//            echo '<pre>1';
//var_dump($buy);
//            echo '<pre>2';
//            var_dump($sell);
//            echo '<pre>3';
//            var_dump((floatval($buy['price']) - floatval($sell['price'])));
//            1，买单单价需大于卖单单价
            if ($buy && $sell && (0 <= floatval($buy['price']) - floatval($sell['price']))) {
                $rs = array();
//                echo 111;
//                没有任何操作先注释20181220
//                if ($buy['num'] <= $buy['deal']) { }
//                if ($sell['num'] <= $sell['deal']) { }

                // $amount = min(round($buy['num'] - $buy['deal'], 8 - C('market')[$market]['round']), round($sell['num'] - $sell['deal'], 8 - C('market')[$market]['round']));
                // $amount = round($amount, 8 - C('market')[$market]['round']);//20171031
//                2，买方或卖方待交易数量取最小的一方数量作为待交易数量
                $amount = min(round($buy['num'] - $buy['deal'], C('market')[$market]['round']), round($sell['num'] - $sell['deal'],  C('market')[$market]['round']));
                $amount = round($amount,  C('market')[$market]['round']);
//                3，待交易数量小于等0时不进行交易
                if ($amount <= 0) {
                    $log = '错误1交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . "\n";
                    $log .= 'ERR: 成交数量出错，数量是' . $amount;
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
//                    $mo->commit();
                    break;
                }
//echo 1;
                if ($type == 1) {
                    $price = $sell['price'];
                } else if ($type == 2) {
                    $price = $buy['price'];
                } else { break; }

                if (!$price) {
                    $log = '错误2交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . ' 成交数量' . $amount . "\n";
                    $log .= 'ERR: 成交价格出错，价格是' . $price;
//                    $mo->commit();
                    break;
                } else {
                    // TODO: SEPARATE
                    $price = round($price, C('market')[$market]['round']);
                }
//                4，计算交易币总金额（不包含手续费）
                $mum = round($price * $amount, 8);
                if (!$mum) {
                    $log = '错误3交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . "\n";
                    $log .= 'ERR: 成交总额出错，总额是' . $mum;
                    mlog($log);
                    break;
                } else {
                    $mum = round($mum, 8);
                }
//                5，计算交易买方所需金额（包含手续费）
                if ($fee_buy) {
                    $buyuser = M('User')->where(array('id' => $buy['userid']))->find();
                    if($buyuser['lv'] == 0){
                        $buy_fee = round(($mum / 100) * $fee_buy, 8);
                        $buy_save = round(($mum / 100) * (100 + $fee_buy),8);
                    } else {
                        $buy_fee = 0;
                        $buy_save = $mum;
                    }
                } else {
                    $buy_fee = 0;
                    $buy_save = $mum;
                }

                if (!$buy_save) {
                    $log = '错误4交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . '  成交数量' . $amount . '  成交价格' . $price . '成交总额' . $mum . "\n";
                    $log .= 'ERR: 买家更新数量出错，更新数量是' . $buy_save;
                    mlog($log);
                    break;
                }
//                6，计算交易卖方所需金额（包含手续费）
                if ($fee_sell) {
                    $selluser = M('User')->where(array('id' => $buy['userid']))->find();
                    if ($selluser['lv']==0) {
                        $sell_fee = round(($mum / 100) * $fee_sell, 8);
                        $sell_save = round(($mum / 100) * (100 - $fee_sell), 8);
                    } else {
                        $sell_fee = 0;
                        $sell_save = $mum;
                    }
                } else {
                    $sell_fee = 0;
                    $sell_save = $mum;
                }
//echo 2;
                if (!$sell_save) {
                    $log = '错误5交易市场' . $market . '出错：买入订单:' . $buy['id'] . '  卖出订单：' . $sell['id'] . '  交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                    $log .= 'ERR: 卖家更新数量出错，更新数量是' . $sell_save;
                    mlog($log);
                    break;
                }

                if ($buy['userid'] > 0) {
                    $user_buy = M('UserCoin')->where(array('userid' => $buy['userid']))->find();
                    if (!$user_buy[$rmb . 'd']) {
                        $log = '错误6交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家财产错误，冻结财产是' . $user_buy[$rmb . 'd'];
                        mlog($log);
                        break;
                    }
                    if ($user_buy[$rmb . 'd'] < 1.0E-8) {
                        $log = '错误88交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新冻结人民币出现错误,应该更新' . $buy_save . '账号余额' . $user_buy[$rmb . 'd'] . '进行错误处理';
                        mlog($log);
                        M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                        break;
                    }
                    if ($buy_save <= round($user_buy[$rmb . 'd'], 8)) {
                        $save_buy_rmb = $buy_save;
                    } else if ($buy_save <= round($user_buy[$rmb . 'd'], 8) + 1) {
                        $save_buy_rmb = $user_buy[$rmb . 'd'];
                        $log = '错误8交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新冻结人民币出现误差,应该更新' . $buy_save . '账号余额' . $user_buy[$rmb . 'd'] . '实际更新' . $save_buy_rmb;
                        mlog($log);
                    } else {
                        $log = '错误9交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新冻结人民币出现错误,应该更新' . $buy_save . '账号余额' . $user_buy[$rmb . 'd'] . '进行错误处理';
                        mlog($log);
                        M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                        break;
                    }
                    if (!$save_buy_rmb) {
                        $log = '错误12交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 买家更新数量出错错误,更新数量是' . $save_buy_rmb;
                        mlog($log);
                        M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                        break;
                    }
                } else {
                    $save_buy_rmb = 0;
                }

                if ($sell['userid']>0) {
                    $user_sell = M('UserCoin')->where(array('userid' => $sell['userid']))->find();
                    if (!$user_sell[$xnb . 'd']) {
                        $log = '错误7交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 卖家财产错误，冻结财产是' . $user_sell[$xnb . 'd'];
                        mlog($log);
                        break;
                    }

                    // TODO: SEPARATE
                    if ($amount <= round($user_sell[$xnb . 'd'], C('market')[$market]['round'])) {
                        $save_sell_xnb = $amount;
                    } else {
                        // TODO: SEPARATE
                        if ($amount <= round($user_sell[$xnb . 'd'], C('market')[$market]['round']) + 1) {
                            $save_sell_xnb = $user_sell[$xnb . 'd'];
                            $log = '错误10交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                            $log .= 'ERR: 卖家更新冻结虚拟币出现误差,应该更新' . $amount . '账号余额' . $user_sell[$xnb . 'd'] . '实际更新' . $save_sell_xnb;
                            mlog($log);
                        } else {
                            $log = '错误11交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                            $log .= 'ERR: 卖家更新冻结虚拟币出现错误,应该更新' . $amount . '账号余额' . $user_sell[$xnb . 'd'] . '进行错误处理';
                            mlog($log);
                            M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                            break;
                        }
                    }
                    if (!$save_sell_xnb) {
                        $log = '错误13交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount . '成交价格' . $price . '成交总额' . $mum . "\n";
                        $log .= 'ERR: 卖家更新数量出错错误,更新数量是' . $save_sell_xnb;
                        mlog($log);
                        M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                        break;
                    }
                }

//                $mo->execute('set autocommit=0');
//                $mo->execute('lock tables tw_trade write,tw_trade_log write,tw_user write,tw_user_coin write,tw_invit write ,tw_finance write,tw_coin write,tw_mining write,tw_market read,tw_config read');
//              7.添加买卖方交易记录
                $rs[] = $mo->table('tw_trade')->where(array('id' => $buy['id']))->setInc('deal', $amount);
                $rs[] = $mo->table('tw_trade')->where(array('id' => $sell['id']))->setInc('deal', $amount);

                $rs[] = $finance_nameid = $mo->table('tw_trade_log')->add(array('userid' => $buy['userid'], 'peerid' => $sell['userid'], 'market' => $market, 'price' => $price, 'num' => $amount, 'mum' => $mum, 'type' => $type, 'fee_buy' => $buy_fee, 'fee_sell' => $sell_fee, 'addtime' => time(), 'status' => 1));

                if ($buy['userid']>0) {
                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setInc($xnb, $amount);
                    $finance = $mo->table('tw_finance')->where(array('userid' => $buy['userid']))->order('id desc')->find();
                    $finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();

                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setDec($rmb . 'd', $save_buy_rmb);
                    $finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();
                    $finance_hash = md5($buy['userid'] . $finance_num_user_coin['cnc'] . $finance_num_user_coin['cncd'] . $mum . $finance_mum_user_coin['cnc'] . $finance_mum_user_coin['cncd'] . MSCODE . 'tp3.net.cn');
                    $finance_num = $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'];

                    if ($finance['mum'] < $finance_num) {
                        $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                    } else {
                        $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                    }

                    $rs[] = $mo->table('tw_finance')->add(array('userid' => $buy['userid'], 'coinname' => 'cnc', 'num_a' => $finance_num_user_coin['cnc'], 'num_b' => $finance_num_user_coin['cncd'], 'num' => $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'], 'fee' => $save_buy_rmb, 'type' => 2, 'name' => 'tradelog', 'nameid' => $finance_nameid, 'remark' => '交易中心-成功买入-市场' . $market, 'mum_a' => $finance_mum_user_coin['cnc'], 'mum_b' => $finance_mum_user_coin['cncd'], 'mum' => $finance_mum_user_coin['cnc'] + $finance_mum_user_coin['cncd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
                    $finance = $mo->table('tw_finance')->where(array('userid' => $buy['userid']))->order('id desc')->find();
                } else {
                    $finance = 1; // 如果用户是0,设置为1
                }

                if($sell['userid']>0){
                    $finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->find();
                    // var_dump($finance_num_user_coin);die;
                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setInc($rmb, $sell_save);
                    $finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->find();
                    $finance_hash = md5($sell['userid'] . $finance_num_user_coin['cnc'] . $finance_num_user_coin['cncd'] . $mum . $finance_mum_user_coin['cnc'] . $finance_mum_user_coin['cncd'] . MSCODE . 'tp3.net.cn');
                    // var_dump($finance);die;

                    $finance_num = $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'];
                    if ($finance['mum'] < $finance_num) {
                        $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                    } else {
                        $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                    }

                    // var_dump($finance_status);die;
                    $rs[] = $mo->table('tw_finance')->add(array('userid' => $sell['userid'], 'coinname' => 'cnc', 'num_a' => $finance_num_user_coin['cnc'], 'num_b' => $finance_num_user_coin['cncd'], 'num' => $finance_num_user_coin['cnc'] + $finance_num_user_coin['cncd'], 'fee' => $save_buy_rmb, 'type' => 1, 'name' => 'tradelog', 'nameid' => $finance_nameid, 'remark' => '交易中心-成功卖出-市场' . $market, 'mum_a' => $finance_mum_user_coin['cnc'], 'mum_b' => $finance_mum_user_coin['cncd'], 'mum' => $finance_mum_user_coin['cnc'] + $finance_mum_user_coin['cncd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
                    // die('ok');
                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setDec($xnb . 'd', $save_sell_xnb);
                }

                $buy_list = $mo->table('tw_trade')->where(array('id' => $buy['id'], 'status' => 0))->find();
                if ($buy_list) {
                    if ($buy_list['num'] <= $buy_list['deal']) {
                        $rs[] = $mo->table('tw_trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    }
                }

                $sell_list = $mo->table('tw_trade')->where(array('id' => $sell['id'], 'status' => 0))->find();
                if ($sell_list) {
                    if ($sell_list['num'] <= $sell_list['deal']) {
                        $rs[] = $mo->table('tw_trade')->where(array('id' => $sell['id']))->setField('status', 1);
                    }
                }

                if ($price < $buy['price']) {
                    $chajia_dong = round((($amount * $buy['price']) / 100) * (100 + $fee_buy), 8);
                    $chajia_shiji = round((($amount * $price) / 100) * (100 + $fee_buy), 8);
                    $chajia = round($chajia_dong - $chajia_shiji, 8);

                    if ($chajia && $buy['userid']>0) {//不处理0的用户
                        $chajia_user_buy = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();

                        if ($chajia <= round($chajia_user_buy[$rmb . 'd'], 8)) {
                            $chajia_save_buy_rmb = $chajia;
                        } else if ($chajia <= round($chajia_user_buy[$rmb . 'd'], 8) + 1) {
                            $chajia_save_buy_rmb = $chajia_user_buy[$rmb . 'd'];
                            mlog('错误91交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount, '成交价格' . $price . '成交总额' . $mum . "\n");
                            mlog('交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '成交数量' . $amount . '交易方式：' . $type . '卖家更新冻结虚拟币出现误差,应该更新' . $chajia . '账号余额' . $chajia_user_buy[$rmb . 'd'] . '实际更新' . $chajia_save_buy_rmb);
                        } else {
                            mlog('错误92交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '交易方式：' . $type . '成交数量' . $amount, '成交价格' . $price . '成交总额' . $mum . "\n");
                            mlog('交易市场' . $market . '出错：买入订单:' . $buy['id'] . '卖出订单：' . $sell['id'] . '成交数量' . $amount . '交易方式：' . $type . '卖家更新冻结虚拟币出现错误,应该更新' . $chajia . '账号余额' . $chajia_user_buy[$rmb . 'd'] . '进行错误处理');
//                            $mo->execute('rollback');
//                            $mo->execute('unlock tables');
                            M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                            M('Trade')->execute('commit');
                            break;
                        }

                        if ($chajia_save_buy_rmb) {
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setDec($rmb . 'd', $chajia_save_buy_rmb);
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setInc($rmb, $chajia_save_buy_rmb);
                        }
                    }
                }

                $you_buy = $mo->table('tw_trade')->where(array(
                    'market' => array('like', '%' . $rmb . '%'),
                    'status' => 0,
                    'userid' => $buy['userid']
                ))->find();
                $you_sell = $mo->table('tw_trade')->where(array(
                    'market' => array('like', '%' . $xnb . '%'),
                    'status' => 0,
                    'userid' => $sell['userid']
                ))->find();
                // var_dump($you_sell);die;

                if (!$you_buy) {
                    $you_user_buy = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->find();
                    if (0 < $you_user_buy[$rmb . 'd']) {
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setField($rmb . 'd', 0);
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $buy['userid']))->setInc($rmb, $you_user_buy[$rmb . 'd']);
                    }
                }
                if (!$you_sell) {
                    $you_user_sell = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->find();
                    if (0 < $you_user_sell[$xnb . 'd']) {
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setField($xnb . 'd', 0);
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $sell['userid']))->setInc($rmb, $you_user_sell[$xnb . 'd']);
                    }
                }

                $invit_buy_user = $mo->table('tw_user')->where(array('id' => $buy['userid']))->find();
                $invit_sell_user = $mo->table('tw_user')->where(array('id' => $sell['userid']))->find();
                $xnblx = M('Coin')->where(array('name'=>$xnb))->find();//交易的虚拟币类型

                if($rmb == 'cnc'){ $rmb1 = $invit_coin; } else { $rmb1 = $rmb; } // 交易佣金返现类型

                // 查询市场最新成交价
                $markets = M('market')->where(array('name' => $rmb1.'_cnc'))->field('new_price')->find();
                if ($new_price) {$new_price = $price;} else {$new_price = 0.001;} //适用于人民币交易区

                if ($invit_buy && $buy['userid']>0) {
                    // 买入交易佣金赠送给上家
                    if ($invit_1) {
                        if ($buy_fee) {
                            if ($invit_buy_user['invit_1']) {
                                //$invit_buy_save_1 = round(($buy_fee / 100) * $invit_1, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_buy_save_1 = round((($buy_fee * ($invit_1 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_buy_save_1) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_buy_user['invit_1'], 'invit' => $buy['userid'], 'name' => '一代买入赠送', 'type' => $xnblx['title'].'买入交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_buy_user['invit_2']) {
                                //$invit_buy_save_2 = round(($buy_fee / 100) * $invit_2, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_buy_save_2 = round((($buy_fee * ($invit_2 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_buy_save_2) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_buy_user['invit_2'], 'invit' => $buy['userid'], 'name' => '二代买入赠送', 'type' => $xnblx['title'].'买入交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_2, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_buy_user['invit_3']) {
                                //$invit_buy_save_3 = round(($buy_fee / 100) * $invit_3, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_buy_save_3 = round((($buy_fee * ($invit_3 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_buy_save_3) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_buy_user['invit_3'], 'invit' => $buy['userid'], 'name' => '三代买入赠送', 'type' => $xnblx['title'].'买入交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_3, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }
                        }
                    }

                    // 卖出交易佣金赠送给上家
                    if ($invit_sell && $invit_sell['userid']>0) {//不处理0用户
                        if ($sell_fee) {
                            if ($invit_sell_user['invit_1']) {
                                //$invit_sell_save_1 = round(($sell_fee / 100) * $invit_1, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_sell_save_1 = round((($sell_fee * ($invit_1 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_sell_save_1) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_sell_user['invit_1'], 'invit' => $sell['userid'], 'name' => '一代卖出赠送', 'type' => $xnblx['title'].'卖出交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_sell_user['invit_2']) {
                                //$invit_sell_save_2 = round(($sell_fee / 100) * $invit_2, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_sell_save_2 = round((($sell_fee * ($invit_2 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_sell_save_2) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_sell_user['invit_2'], 'invit' => $sell['userid'], 'name' => '二代卖出赠送', 'type' => $xnblx['title'].'卖出交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_2, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }

                            if ($invit_sell_user['invit_3']) {
                                //$invit_sell_save_3 = round(($sell_fee / 100) * $invit_3, 7);
                                // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                                $invit_sell_save_3 = round((($sell_fee * ($invit_3 / 100)) * $new_price) / $markets['new_price'], 8);
                                if ($invit_sell_save_3) {
                                    $rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_sell_user['invit_3'], 'invit' => $sell['userid'], 'name' => '三代卖出赠送', 'type' => $xnblx['title'].'卖出交易赠送'.strtoupper($rmb1), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_3, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($rmb1)));
                                }
                            }
                        }
                    }
                }


                // 交易挖矿模块
                $Configs = M('config')->where(array('id' => 1))->find();
                $coin_name_s = $Configs['mining_coin']; // 交易挖矿赠送币种
                $mining_coin_num = $Configs['mining_coin_num']; // 交易挖矿比例

                // 查询市场最新成交价
                $markets_2 = M('market')->where(array('name' => $coin_name_s.'_cnc'))->field('new_price')->find();

                if ($Configs['mining_type'] == 1) {
                    // 判断是否自买自卖 && 判断是否做市商
                    if ($buy['userid'] == $sell['userid'] || $buyuser['lv']==1 || $selluser['lv']==1) {}else {
                        if ($mining_coin_num && $buy['userid']>0) {
                            // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                            $mining_buy_save = round((($buy_fee * ($mining_coin_num / 100)) * $new_price) / $markets_2['new_price'], 8);

                            $rs[] = $mo->table('tw_mining')->add(array('userid' => $buy['userid'], 'name' => '交易挖矿买入奖励', 'type' => '买入'.$xnblx['title'].'交易挖矿赠送'.strtoupper($coin_name_s), 'num' => $amount, 'mum' => $mum, 'fee' => $mining_buy_save, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name_s)));
                        }
                        if ($mining_coin_num && $sell['userid']>0) {

                            // 赠送数量公式 ((盈利交易手续费  * (赠送比例 / 100)) * 当前市场价格) / 赠送币种当前价格
                            $mining_sell_save = round((($sell_fee * ($mining_coin_num / 100)) * $new_price) / $markets_2['new_price'], 8);

                            $rs[] = $mo->table('tw_mining')->add(array('userid' => $sell['userid'], 'name' => '交易挖矿卖出奖励', 'type' => '卖出'.$xnblx['title'].'交易挖矿赠送'.strtoupper($coin_name_s), 'num' => $amount, 'mum' => $mum, 'fee' => $mining_sell_save, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name_s)));
                        }
                    }

                }
                echo 33434;
//              清空交易信息缓存
                if (check_arr($rs)) {
//                    $workerman =A('Wokerman');
                    if($type==1){
                        // $workerman->getNewMarkets($buy['userid'],$market);

                        //$workerman->weituo($buy['userid'],$market);
//                        $workerman->weituo($sell['userid'],$market);

                    }else if($type==2){
                        //$workerman->getNewMarkets($sell['userid'],$market);
//                        $workerman->weituo($buy['userid'],$market);
                        //$workerman->weituo($sell['userid'],$market);
                    }
//                    $mo->execute('commit');

//                    $mo->execute('unlock tables');
                    $new_trade_btchanges = 1;
                    $coin = $xnb;
                    S('allsum', null);
                    S('getJsonTop' . $market, null);
                    S('getTradelog' . $market, null);
                    S('getDepth' . $market . '1', null);
                    S('getDepth' . $market . '3', null);
                    S('getDepth' . $market . '4', null);
                    S('ChartgetJsonData' . $market, null);
                    S('allcoin', null);
                    S('trends', null);
                } else {
//                    $mo->execute('rollback');
//                    $mo->execute('unlock tables');
                }
            } else {
                break;
            }

            unset($rs);
        }


//        更新市场信息
        if ($new_trade_btchanges) {
//            最新的交易价格
            $new_price = round(M('TradeLog')->where(array('market' => $market, 'status' => 1))->order('id desc')->getField('price'), 6);
//            未完成交易买单最高价格
            $buy_price = round(M('Trade')->where(array('type' => 1, 'market' => $market, 'status' => 0))->max('price'), 6);
//            未完成交易卖单最低价格
            $sell_price = round(M('Trade')->where(array('type' => 2, 'market' => $market, 'status' => 0))->min('price'), 6);
//            当天交易记录最低价格
            $min_price = round(M('TradeLog')->where(array(
                'market'  => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->min('price'), 6);
//            当天交易记录最高价格
            $max_price = round(M('TradeLog')->where(array(
                'market'  => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->max('price'), 6);
//            当天成交总数量
            $volume = round(M('TradeLog')->where(array(
                'market'  => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->sum('num'), 6);
//            当天开盘价格
            $sta_price = round(M('TradeLog')->where(array(
                'market'  => $market,
                'status'  => 1,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->order('id asc')->getField('price'), 6);
//            更新市场信息
            $Cmarket = M('Market')->where(array('name' => $market))->find();
            if ($Cmarket['new_price'] != $new_price) {
                $upCoinData['new_price'] = $new_price;
            }
            if ($Cmarket['buy_price'] != $buy_price) {
                $upCoinData['buy_price'] = $buy_price;
            }
            if ($Cmarket['sell_price'] != $sell_price) {
                $upCoinData['sell_price'] = $sell_price;
            }
            if ($Cmarket['min_price'] != $min_price) {
                $upCoinData['min_price'] = $min_price;
            }
            if ($Cmarket['max_price'] != $max_price) {
                $upCoinData['max_price'] = $max_price;
            }
            if ($Cmarket['volume'] != $volume) {
                $upCoinData['volume'] = $volume;
            }

            // 计算涨跌幅
            $change = round((($new_price - $Cmarket['hou_price']) / $Cmarket['hou_price']) * 100, 2);
            $upCoinData['change'] = $change;

            if ($upCoinData) {
                M('Market')->where(array('name' => $market))->save($upCoinData);
//                M('Market')->execute('commit');
                S('home_market', null);
            }
        }
    }
   public function intro() {
        $market = strtolower(I('market'));
        if(!$market){
            $market = cookie('intro_market');
        }else{
            cookie('intro_market',$market);
        }
        $language = LANG_SET;
        $intro = M('intro')->where(['name'=>$market,'language'=>$language])->find();
//        dump(M()->getLastSql());exit;
        $this -> assign('intro',$intro);
        $this->display("Trade/intro");
   }

    function upTradeTimeLog($text)
    {
        $time = date('Y-m-d H:i:s',time());
        $texts = $time. ' ' . $text . "\n";
        $date = date('Y-m-d',time());
        $isSuccess = file_put_contents('./Log/Home/uptradetime/'.$date.'uptrade.log', $texts, FILE_APPEND);
        if($isSuccess === false){
            $this->paylog($text);
        }
        // var_dump($text);
    }
}
?>