<?php
namespace Home\Controller;

class QueueController extends HomeController
{

    public function index()
    {
        foreach (C('market') as $k => $v) {}
        foreach (C('coin_list') as $k => $v) {}
    }
	
	//新增，待测试
	public function monijiaoyi()
	{
		if (APP_DEMO) {
			echo addtime(time()) . "\n";

			foreach (C("market") as $k => $v) {
				echo "----模拟交易----" . $v["name"] . "------------";
				echo $this->upTrade($v["name"]);
				echo "\n";
			}

			echo "模拟交易0k " . "\n";
		}
	}

    public function checkYichang()
    {
        $mo = M();
        $mo->execute('set autocommit=0');
//        $mo->execute('lock tables tw_trade write');
        $Trade = M('Trade')->where('deal > num')->order('id desc')->find();

        if ($Trade) {
            if ($Trade['status'] == 0) {
                $mo->table('tw_trade')->where(array('id' => $Trade['id']))->save(array('deal' => Num($Trade['num']), 'status' => 1));
            } else {
                $mo->table('tw_trade')->where(array('id' => $Trade['id']))->save(array('deal' => Num($Trade['num'])));
            }

            $mo->execute('commit');
//            $mo->execute('unlock tables');
        } else {
            $mo->execute('rollback');
//            $mo->execute('unlock tables');
        }
    }
	public function testTrade(){
        A('Trade')->upTrade43ASD4F65A4SDF6();
    }
	//匹配交易
    public function checkDapan()
    {
        // 处理开盘闭盘交易时间===开始
        $times = date('G', time());
        $minute = date('i', time());
        $minute = intval($minute);

        foreach (C('market') as $k => $v) {
            if (($times <= $v['start_time'] && $minute < intval($v['start_minute'])) || ($times > $v['stop_time'] && $minute >= intval($v['stop_minute']))) {
                continue;
            }
            if (($times < $v['start_time']) || $times > $v['stop_time']) {
                continue;
            } else {
                if ($times == $v['start_time']) {
                    if ($minute < intval($v['start_minute'])) {
                        continue;
                    }
                } elseif ($times == $v['stop_time']) {
                    if (($minute > $v['stop_minute'])) {
                        continue;
                    }
                }
            }
            // 处理周六周日是否可交易===开始
            $weeks = date('N', time());
            if (!$v['agree6']) {
                if ($weeks == 6) {
                    continue;
                }
            }
            if (!$v['agree7']) {
                if ($weeks == 7) {
                    continue;
                }
            }
            //处理周六周日是否可交易===结束
            A('Trade')->matchingTradeall($v['name']);//匹配交易
        }
    }

    public function checkUsercoin()
    {
        foreach (C('coin') as $k => $v) {}
    }

    public function yichang()
    {
        foreach (C('market') as $k => $v) {
            $this->setMarket($v['name']);
        }
        foreach (C('coin_list') as $k => $v) {
            $this->setcoin($v['name']);
        }

        //$this->chack_dongjie_coin();
    }


    public function chack_dongjie_coin()
    {
        $max_userid = S('queue_max_userid');
        if (!$max_userid) {
            $max_userid = M('User')->max('id');
            S('queue_max_userid', $max_userid);
        }

        $zuihou_userid = S('queue_zuihou_userid');
        if (!$zuihou_userid) {
            $zuihou_userid = M('User')->min('id');
        }

        $x = 0;

        for (; $x <= 30; $x++) {
            if ($max_userid < ($zuihou_userid + $x)) {
                S('queue_zuihou_userid', null);
                S('queue_max_userid', null);
                break;
            } else {
                S('queue_zuihou_userid', $zuihou_userid + $x + 1);
            }

            $user = M('UserCoin')->where(array('userid' => $zuihou_userid + $x))->find();

            if (is_array($user)) {
                foreach (C('coin_list') as $k => $v) {
                    if (0 < $user[$v['name'] . 'd']) {
                        /*$mo = M();
                        $mo->execute('set autocommit=0');
                        $mo->execute('lock tables tw_user_coin write  , tw_trade write ');*/
                        $rs = array();
                        $rs = M('Trade')->where(array(
                            'market' => $v['name'] . "_cny",
                            'status' => 0,
                            'userid' => $user['userid']
                        ))->find();

                        if (!$rs) {
                            M('UserCoin')->where(array('userid' => $user['userid']))->setField($v['name'] . 'd', 0);
                        }
                    }
                }
            }
        }
    }

    public function move()
    {
        M('Trade')->where(array('status' => '-1'))->setField('status', 1);

        foreach (C('market') as $k => $v) {
            $this->setMarket($v['name']);
        }
        foreach (C('coin_list') as $k => $v) {
            $this->setcoin($v['name']);
        }
    }

    public function setMarket($market = NULL)
    {
        // 过滤非法字符----------------S
        if (checkstr($market)) {
            $this->error('您输入的信息有误！');
        }
        // 过滤非法字符----------------E

        if (!$market) {
            return null;
        }

        $market_json = M('Market_json')->where(array('name' => $market))->order('id desc')->find();

        if ($market_json) {
            $addtime = $market_json['addtime'] + 60;
        } else {
            $addtime = M('TradeLog')->where(array('market' => $market))->order('addtime asc')->find()['addtime'];
        }

        $t = $addtime;
        $start = mktime(0, 0, 0, date('m', $t), date('d', $t), date('Y', $t));
        $end = mktime(23, 59, 59, date('m', $t), date('d', $t), date('Y', $t));
        $trade_num = M('TradeLog')->where(array(
            'market' => $market,
            'addtime' => array(
                array('egt', $start),
                array('elt', $end)
            )
        ))->sum('num');

        if ($trade_num) {
            $trade_mum = M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('mum');
            $trade_fee_buy = M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('fee_buy');
            $trade_fee_sell = M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('fee_sell');
			
            $d = array($trade_num, $trade_mum, $trade_fee_buy, $trade_fee_sell);

            if (M('Market_json')->where(array('name' => $market, 'addtime' => $end))->find()) {
                M('Market_json')->where(array('name' => $market, 'addtime' => $end))->save(array('data' => json_encode($d)));
            } else {
                M('Market_json')->add(array('name' => $market, 'data' => json_encode($d), 'addtime' => $end));
            }
        } else {
            $d = null;

            if (M('Market_json')->where(array('name' => $market, 'data' => ''))->find()) {
                M('Market_json')->where(array('name' => $market, 'data' => ''))->save(array('addtime' => $end));
            } else {
                M('Market_json')->add(array('name' => $market, 'data' => '', 'addtime' => $end));
            }
        }
    }

    public function setcoin($coinname = NULL)
    {
        // 过滤非法字符----------------S
        if (checkstr($coinname)) {
            $this->error('您输入的信息有误！');
        }
        // 过滤非法字符----------------E

        if (!$coinname) {
            return null;
        }

        if (C('coin')[$coinname]['type'] == 'qbb') {
            $dj_username = C('coin')[$coinname]['dj_yh'];
            $dj_password = C('coin')[$coinname]['dj_mm'];
            $dj_address = C('coin')[$coinname]['dj_zj'];
            $dj_port = C('coin')[$coinname]['dj_dk'];
            $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
            $json = $CoinClient->getinfo();

            if (!isset($json['version']) || !$json['version']) {
                return null;
            }

            $data['trance_mum'] = $json['balance'];
        } else {
            $data['trance_mum'] = 0;
        }

        $market_json = M('CoinJson')->where(array('name' => $coinname))->order('id desc')->find();
        if ($market_json) {
            $addtime = $market_json['addtime'] + 60;
        } else {
            $addtime = M('Myzr')->where(array('name' => $coinname))->order('id asc')->find()['addtime'];
        }

        $t = $addtime;
        $start = mktime(0, 0, 0, date('m', $t), date('d', $t), date('Y', $t));
        $end = mktime(23, 59, 59, date('m', $t), date('d', $t), date('Y', $t));

        if ($addtime) {
            if ((time() + (60 * 60 * 24)) < $addtime) {
                return null;
            }

            $trade_num = M('UserCoin')->where(array(
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum($coinname);
            $trade_mum = M('UserCoin')->where(array(
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum($coinname . 'd');
            $aa = $trade_num + $trade_mum;

            if (C($coinname)['type'] == 'qbb') {
                $bb = $json['balance'];
            } else {
                $bb = 0;
            }

            $trade_fee_buy = M('Myzr')->where(array(
                'name' => $coinname,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('fee');
            $trade_fee_sell = M('Myzc')->where(array(
                'name' => $coinname,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('fee');
			
            $d = array($aa, $bb, $trade_fee_buy, $trade_fee_sell);

            if (M('CoinJson')->where(array('name' => $coinname, 'addtime' => $end))->find()) {
                M('CoinJson')->where(array('name' => $coinname, 'addtime' => $end))->save(array('data' => json_encode($d)));
            } else {
                M('CoinJson')->add(array('name' => $coinname, 'data' => json_encode($d), 'addtime' => $end));
            }
        }
    }

    public function paicuo()
    {
        foreach (C('market') as $k => $v) {}
    }
	
	// 更新市场价格
    public function houprice()
    {
        $markets = M('market')->field('name,hou_price')->where(array('status' => 1))->select();
        foreach ($markets as $k => $v) {
            $market = $v['name'];
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
                var_dump($new_price);
//            更新市场信息
                $Cmarket = M('Market')->where(array('name' => $market))->find();
                if ($Cmarket['new_price'] != $new_price) {
                    echo 1;
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
                    echo 2;
                    M('Market')->where(array('name' => $market))->save($upCoinData);
//                M('Market')->execute('commit');
                    S('home_market', null);
                }
            }
//            if (!$v['hou_price'] || (date('H', time()) == '0')) {
                if (true) {
                $t = time();
                $start = mktime(0, 0, 0, date('m', $t), date('d', $t), date('Y', $t));

                $hou_price = M('TradeLog')->where(array(
                    'market' => $v['name'],
                    'addtime' => array('lt', $start)
                ))->order('id desc')->getField('price');

                if (!$hou_price) {
                    $hou_price = M('TradeLog')->where(array('market' => $v['name']))->order('id asc')->getField('price');
                }
                M('Market')->where(array('name' => $v['name']))->setField('hou_price', $hou_price);

                S('home_market', null);
            }

            echo $hou_price;
        }
    }
	
	/** 同步钱包转入记录 **/
    public function qianbao()
    {
        $coinList = M('Coin')->where(array('status' => 1))->select();
        foreach ($coinList as $k => $v) {
            if ($v['type'] != 'qbb') {
                continue;
            }

            $coin = $v['name'];
            $coinid = $v['id'];
            if ($coin == 'usdt') {
//                $this->usdt();
                $this->usdtTrade();
                continue;
            }
            if (!$coin) {
                echo 'MM';
                continue;
            }
            if ($coin == 'eth') {
                $this->ethonlinea88b77c11d0a9d();
                continue;
            }
            if ($coin == 'etc') {
                $this->etconlinea88b77c11d0a9d();
                continue;
            }
            if ($coin == 'astc') {
                $this->astconlinea88b77c11d0a9d();
                continue;
            }
            
            $dj_username = C('coin')[$coin]['dj_yh'];
            $dj_password = C('coin')[$coin]['dj_mm'];
            $dj_address = C('coin')[$coin]['dj_zj'];
            $dj_port = C('coin')[$coin]['dj_dk'];
            $candh = C('coin')[$coin]['change'];
            $cancoin = C('coin')[$coin]['changecoin'];

            //分级推广赠送百分比
            // $type_give['type1_give']=C('coin')[$coin]['type1_give']/100;
            // $type_give['type2_give']=C('coin')[$coin]['type2_give']/100;
            // $type_give['type3_give']=C('coin')[$coin]['type4_give']/100;

            if ($candh == 1) {
                $setcoin = $cancoin;
                $rate = C('coin')[$coin]['huilv'];
            } else {
                $setcoin = $coin;
                $rate = 1;
            }
			
            echo 'start ' . $coin . "\n";
            $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
            $json = $CoinClient->getinfo();
            if (!isset($json['version']) || !$json['version']) {
                echo '###ERR#####***** ' . $coin . ' connect fail***** ####ERR####>' . "\n";
                continue;
            }

            echo 'Cmplx ' . $coin . ' start,connect ' . (empty($CoinClient) ? 'fail' : 'ok') . ' :' . "\n";
            $listtransactions = $CoinClient->listtransactions('*', 1000, 0);
            echo 'listtransactions:' . count($listtransactions) . "\n";
            $omnilist = $CoinClient->omni_listtransactions();
            if ($listtransactions != "nodata") {
                krsort($listtransactions);
                foreach ($omnilist as $k => $v) {
                    $omnitxid[$k] = $v['txid'];
                }
                foreach ($listtransactions as $trans) {
                    if (!$trans['account']) {
                        echo 'empty account continue' . "\n";
                        continue;
                    }
                    if (in_array($trans['txid'], $$omnitxid)) {
                        echo 'USDT find,continue!' . "\n";
                        continue;
                    }
                    if (!($user = M('User')->where(array('username' => $trans['account']))->find())) {
                        echo 'no account find continue' . "\n";
                        continue;
                    }

                    if (M('Myzr')->where(array('txid' => $trans['txid'], 'status' => '1', 'username' => $trans['address']))->find()) {
                        echo 'TXID & ADDR find,continue.' . "\n";
                        continue;
                    }
                    echo 'all check ok ' . "\n";

                    if ($trans['category'] == 'receive') {
                        echo 'start receive do:' . "\n";
                        $sfee = 0;
                        $true_amount = $trans['amount'];

                        if (C('coin')[$coin]['zr_zs']) {
                            $song = round(($trans['amount'] / 100) * C('coin')[$coin]['zr_zs'], 8);

                            if ($song) {
                                $sfee = $song;
                                $trans['amount'] = $trans['amount'] + $song;
                            }
                        }

                        if ($trans['confirmations'] < C('coin')[$coin]['zr_dz']) {
                            echo $trans['account'] . ' confirmations ' . $trans['confirmations'] . ' not elengh ' . C('coin')[$coin]['zr_dz'] . ' continue ' . "\n";
                            echo 'confirmations <  c_zr_dz continue' . "\n";

                            if ($res = M('myzr')->where(array('txid' => $trans['txid'], 'userid' => $user['id']))->find()) {
                                M('myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => intval($trans['confirmations'] - C('coin')[$coin]['zr_dz'])));
                            } else {
                                M('myzr')->add(array('userid' => $user['id'], 'username' => $trans['address'], 'coinname' => $coin, 'fee' => $sfee, 'txid' => $trans['txid'], 'num' => $true_amount, 'mum' => $trans['amount'], 'addtime' => time(), 'status' => intval($trans['confirmations'] - C('coin')[$coin]['zr_dz'])));
                            }

                            continue;
                        } else {
                            echo $trans['txid'] . 'confirmations full.' . "\n";
                        }
                        try {
                            $mo = M();
                            $mo->execute('set autocommit=0');


//                            $mo->execute('lock tables  tw_user_coin write , tw_myzr  write  ,tw_finance_log write');

                            $user_zj_coin = $mo->table('tw_user_coin')->where(array('userid' => $user['id']))->find();

                            $rs = array();
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $user['id']))->setInc($setcoin, ($trans['amount'] / $rate));
                            // $invit_uid[$user['invit_1']]='type1_give';
                            // $invit_uid[$user['invit_2']]='type2_give';
                            // $invit_uid[$user['invit_3']]='type3_give';
                            // $invit=M('User')->where(array('id'=>array('in',array($user['invit_1'],$user['invit_2'],$user['invit_3']))))->select();
                            // foreach ($invit as $v){
                            //     $trans_coin=$trans['amount']/$rate*$type_give[$invit_uid[$v['id']]];
                            //     $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $v['id']))->setInc( $setcoin, $trans_coin);
                            //     $rs[] = $mo->table('tw_finance_log')->add(array(
                            //         'username' => $v['username'],
                            //         'adminname' => $trans['address'],
                            //         'addtime' => time(),
                            //         'plusminus' => 1,
                            //         'amount' => $trans_coin,
                            //         'optype' => 7,
                            //         'position' => 1,
                            //         'cointype' => 3,
                            //         'old_amount' => $v[$coin],
                            //         'new_amount' => $v[$coin]+$trans_coin,
                            //         'userid' => $v['id'],
                            //         'adminid' => session('userId'),'addip'=>'钱包地址'));
                            // }
                            // $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $user['id']))->setInc($coin, $trans['amount']);

                            if ($res = $mo->table('tw_myzr')->where(array('txid' => $trans['txid'], 'userid' => $user['id']))->find()) {
                                echo 'tw_myzr find and set status 1.';
                                $rs[] = $mo->table('tw_myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => 1));
                            } else {
                                echo 'tw_myzr not find and add a new tw_myzr.' . "\n";
                                $rs[] = $mo->table('tw_myzr')->add(array('userid' => $user['id'], 'username' => $trans['address'], 'coinname' => $coin, 'fee' => $sfee, 'txid' => $trans['txid'], 'num' => $true_amount, 'mum' => $trans['amount'], 'addtime' => time(), 'status' => 1));
                            }

                            $user_zjw_coin = $mo->table('tw_user_coin')->where(array('userid' => $user['id']))->find();

                            // 处理资金变更日志-----------------S

                            // 判断币种
                            // switch ($coin) {
                            //     case 'cny':
                            //         $cointype = 1;//人民币类型1
                            //         break;
                            //     case 'hyjf':
                            //         $cointype = 2;//汇云品种类型2
                            //         break;
                            //     default:
                            //         $cointype = 3;//其他币种类型3
                            //         break;
                            // }

                            $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user['username'], 'adminname' => $trans['address'], 'addtime' => time(), 'plusminus' => 1, 'amount' => $trans['amount'], 'optype' => 7, 'position' => 1, 'cointype' => $coinid, 'old_amount' => $user_zj_coin[$coin], 'new_amount' => $user_zjw_coin[$coin], 'userid' => $user['id'], 'adminid' => session('userId'), 'addip' => '钱包地址'));

                            // 处理资金变更日志-----------------E

                            if (check_arr($rs)) {
                                $mo->execute('commit');
                                echo $trans['amount'] . ' receive ok ' . $coin . ' ' . $trans['amount'];
//                                $mo->execute('unlock tables');
                                echo 'commit ok' . "\n";
                            } else {
                                throw new \Think\Exception('receive fail');
                            }
                        } catch (\Think\Exception $e) {
                            echo $trans['amount'] . 'receive fail ' . $coin . ' ' . $trans['amount'];
                            // echo var_export($e, true);
                            $mo->execute('rollback');
//                            $mo->execute('unlock tables');
                            echo 'rollback ok.' . "\n";
                        }
                    }
                }
            }

            if ($trans['category'] == 'send') {
                echo 'start send do:' . "\n";
                if (3 <= $trans['confirmations']) {
                    $myzc = M('Myzc')->where(array('txid' => $trans['txid']))->find();
                    if ($myzc) {
                        if ($myzc['status'] == 0) {
                            M('Myzc')->where(array('txid' => $trans['txid']))->save(array('status' => 1));
                            echo $trans['amount'] . '成功转出' . $coin . ' 确定';
                        }
                    }
                }
            }
        }
    }
	
	/** 计算趋势,每天运行一次即可 **/
	public function tendency()
	{
		foreach (C("market") as $k => $v) {
			echo "----计算趋势----" . $v["name"] . "------------<br>";
			$tendency_time = 4; //间隔时间4小时
			$t = time();
			$tendency_str = $t - (24 * 60 * 60 * 3); //当前时间的3天前

			for ($x = 0; $x <= 18; $x++) { //18次,72个小时
				$na = $tendency_str + (60 * 60 * $tendency_time * $x);
				$nb = $tendency_str + (60 * 60 * $tendency_time * ($x + 1));
				$b = M("TradeLog")->where("addtime >=" . $na . " and addtime <" . $nb . " and market ='" . $v["name"] . "'")->max("price");

				if (!$b) { $b = 0; }
				$rs[] = array($na, $b);
			}

			M("Market")->where(array("name" => $v["name"]))->setField("tendency", json_encode($rs));
			unset($rs);
			echo "计算成功!";
			echo "\n";
		}
		echo "趋势计算0k " . "\n";
	}
	
	/** 计算行情 **/
    public function chart()
    {
		foreach (C("market") as $k => $v) {
			$this->setTradeJson($v["name"]);
		}
		echo "计算行情0k " . "\n";
    }

	public function setTradeJson($market)
	{
        if (checkstr($market)) {
            $this->error('您输入的信息有误！');
        }
		
		$timearr = array(1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080);
		foreach ($timearr as $k => $v) {
			$tradeJson = M("TradeJson")->where(array("market" => $market, "type" => $v))->order("id desc")->find();
			if ($tradeJson) {
				$addtime = $tradeJson["addtime"];
			} else {
				$addtime = M("TradeLog")->where(array("market" => $market))->order("id asc")->getField("addtime");
			}
			if ($addtime) {
				$youtradelog = M("TradeLog")->where("addtime >=" . $addtime . "  and market ='" . $market . "'")->sum("num");
			}

			if ($youtradelog) {
				if ($v == 1) {
					$start_time = $addtime;
				} else {
					$start_time = mktime(date("H", $addtime), floor(date("i", $addtime) / $v) * $v, 0, date("m", $addtime), date("d", $addtime), date("Y", $addtime));
				}
				
				for ($x = 0; $x <= 20; $x++) {
					$na = $start_time + (60 * $v * $x);
					$nb = $start_time + (60 * $v * ($x + 1));

					if (time() < $na) {
						break;
					}

					$sum = M("TradeLog")->where("addtime >=" . $na . " and addtime <" . $nb . " and market ='" . $market . "'")->sum("num");
					
					if ($sum) {
						$sta = M("TradeLog")->where("addtime >=" . $na . " and addtime <" . $nb . " and market ='" . $market . "'")->order("id asc")->getField("price");
						$max = M("TradeLog")->where("addtime >=" . $na . " and addtime <" . $nb . " and market ='" . $market . "'")->max("price");
						$min = M("TradeLog")->where("addtime >=" . $na . " and addtime <" . $nb . " and market ='" . $market . "'")->min("price");
						$end = M("TradeLog")->where("addtime >=" . $na . " and addtime <" . $nb . " and market ='" . $market . "'")->order("id desc")->getField("price");
						$d = array($na, $sum, $sta, $max, $min, $end);//时间，成交量,成交价,最高价,最低价,收盘价
												
						
						// 判断是否有最新成交记录
						$jiansuotime = M("TradeLog")->where(array("market" => $market))->order("id desc")->find();
						$times = floor((time()-$jiansuotime['addtime'])%86400/60);
						if ($times >= 1){
							$jiansuo = M("TradeJson")->where(array("market" => $market, "data" => json_encode($d), "addtime" => $na, "type" => $v))->find();
							if ($jiansuo) {
								$sdfds = array();
								$sdfds['market'] = $market;
								$sdfds['price'] = $sta;
								$sdfds['num'] = 0;
								$sdfds['mum'] = 0;
								$sdfds['type'] = 1;
								$sdfds['addtime'] = time();
								$sdfds['status'] = 0;

								$aa = M("TradeLog")->add($sdfds);
								M("TradeJson")->execute("commit");
								sleep(1);
							}
						}
						
						if (M("TradeJson")->where(array("market" => $market, "addtime" => $na, "type" => $v))->find()) {
							M("TradeJson")->where(array("market" => $market, "addtime" => $na, "type" => $v))->save(array("data" => json_encode($d)));
						} else {
							$aa = M("TradeJson")->add(array("market" => $market, "data" => json_encode($d), "addtime" => $na, "type" => $v));
							M("TradeJson")->execute("commit");
							M("TradeJson")->where(array("market" => $market, "data" => "", "type" => $v))->delete();
							M("TradeJson")->execute("commit");
						}

					} else {
						M("TradeJson")->add(array("market" => $market, "data" => "", "addtime" => $na, "type" => $v));
						M("TradeJson")->execute("commit");
					}
				}
			}
		}
		return "计算成功!";
	}

    /** 计算行情 **/
    public function charttest()
    {
        foreach (C("market") as $k => $v) {
            $this->setTradeJsontest($v["name"]);
        }
        echo "计算行情0k " . "\n";
    }

    public function setTradeJsontest($market)
    {
        if (checkstr($market)) {
            $this->error('您输入的信息有误！');
        }

        $timearr = array(1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080);
        foreach ($timearr as $k => $v) {
            $tradeJson = M("TradeJson")->where(array("market" => $market, "type" => $v))->order("id desc")->find();
            if ($tradeJson) {
                $addtime = $tradeJson["addtime"];
            } else {
                $addtime = M("TradeLog")->where(array("market" => $market))->order("id asc")->getField("addtime");
            }
            if ($addtime) {
                $youtradelog = M("TradeLog")->where("addtime >=" . $addtime . "  and market ='" . $market . "'")->sum("num");
            }

            if ($youtradelog) {
                if ($v == 1) {
                    $start_time = $addtime;
                } else {
                    $start_time = mktime(date("H", $addtime), floor(date("i", $addtime) / $v) * $v, 0, date("m", $addtime), date("d", $addtime), date("Y", $addtime));
                }

                for ($x = 0; $x <= 20; $x++) {
                    $na = $start_time + (60 * $v * $x);
                    $nb = $start_time + (60 * $v * ($x + 1));

                    if (time() < $na) {
                        break;
                    }

                    $sum = M("TradeLog")->where("addtime >=" . $na . " and addtime <" . $nb . " and market ='" . $market . "'")->sum("num");

                    if ($sum) {
                        $sta = M("TradeLog")->where("addtime >=" . $na . " and addtime <" . $nb . " and market ='" . $market . "'")->order("id asc")->getField("price");
                        $max = M("TradeLog")->where("addtime >=" . $na . " and addtime <" . $nb . " and market ='" . $market . "'")->max("price");
                        $min = M("TradeLog")->where("addtime >=" . $na . " and addtime <" . $nb . " and market ='" . $market . "'")->min("price");
                        $end = M("TradeLog")->where("addtime >=" . $na . " and addtime <" . $nb . " and market ='" . $market . "'")->order("id desc")->getField("price");
                        $d = array($na, $sum, $sta, $max, $min, $end);//时间，成交量,成交价,最高价,最低价,收盘价


                        // 判断是否有最新成交记录
                        $jiansuotime = M("TradeLog")->where(array("market" => $market))->order("id desc")->find();
                        $times = floor((time()-$jiansuotime['addtime'])%86400/60);
                        if ($times >= 1){
                            $jiansuo = M("TradeJson")->where(array("market" => $market, "data" => json_encode($d), "addtime" => $na, "type" => $v))->find();
                            if ($jiansuo) {
                                $sdfds = array();
                                $sdfds['market'] = $market;
                                $sdfds['price'] = $sta;
                                $sdfds['num'] = 0;
                                $sdfds['mum'] = 0;
                                $sdfds['type'] = 1;
                                $sdfds['addtime'] = time();
                                $sdfds['status'] = 0;

                                $aa = M("TradeLog")->add($sdfds);
                                echo 1;
                                echo M("TradeLog")->getLastSql();
                                echo '<hr>';
                                M("TradeJson")->execute("commit");
                                sleep(1);
                            }
                        }

                        if (M("TradeJson")->where(array("market" => $market, "addtime" => $na, "type" => $v))->find()) {
                            M("TradeJson")->where(array("market" => $market, "addtime" => $na, "type" => $v))->save(array("data" => json_encode($d)));
                            echo 2;
                            echo M("TradeJson")->getLastSql();
                            echo '<hr>';
                        } else {
                            $aa = M("TradeJson")->add(array("market" => $market, "data" => json_encode($d), "addtime" => $na, "type" => $v));
                            echo '3';
                            echo M("TradeJson")->getLastSql();
                            echo '<hr>';
                            M("TradeJson")->execute("commit");
                            M("TradeJson")->where(array("market" => $market, "data" => "", "type" => $v))->delete();
                            echo 4;
                            echo M("TradeJson")->getLastSql();
                            echo '<hr>';
                            M("TradeJson")->execute("commit");
                        }

                    } else {
                        M("TradeJson")->add(array("market" => $market, "data" => "", "addtime" => $na, "type" => $v));
                        echo 5;
                        echo M("TradeJson")->getLastSql();
                        M("TradeJson")->execute("commit");
                    }
                }
            }
        }
        return "计算成功!";
    }

    public function setTradeJsonnew($market)
    {
        // set_time_limit(0);
        // 过滤非法字符----------------S
        if (checkstr($market)) {
            $this->error('您输入的信息有误！');
        }
        // 过滤非法字符----------------E

        $timearr = array(1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080);
        foreach ($timearr as $k => $v) {
            $tradeJson = M('TradeJson')->where(array('market' => $market, 'type' => $v))->order('id desc')->find();
			// var_dump($tradeJson);die;
            if ($tradeJson) {
                $addtime = $tradeJson['addtime'];
            } else {
                $addtime = M('TradeLog')->where(array('market' => $market))->order('id asc')->getField('addtime');
            }

            if ($addtime) {
                $youtradelog = M('TradeLog')->where('addtime >=' . $addtime . '  and market =\'' . $market . '\'')->sum('num');
            }
            //$addtime = 1489334400;
            if ($youtradelog) {
                if ($v == 1) {
                    $start_time = $addtime;
                } else {
                    $start_time = mktime(date('H', $addtime), floor(date('i', $addtime) / $v) * $v, 0, date('m', $addtime), date('d', $addtime), date('Y', $addtime));
                }

                $nows = time();
                $chas = ceil(($nows - $start_time) / 60) + 20;

                $x = 0;
                for (; $x <= $chas; $x++) {
                    $na = $start_time + (60 * $v * $x);
                    $nb = $start_time + (60 * $v * ($x + 1));

                    // 过滤以下时间显示
                    $new_v = array(1, 3, 5, 10, 15, 30, 60, 120);
                    if (in_array($v, $new_v)) {
                        // 处理开盘闭盘交易时间===开始
                        $time_na = date('G', $na);
                        $time_nb = date('G', $nb);

                        $stops = C('market')[$market]['stop_time'] + 1;
                        if ($time_na < C('market')[$market]['start_time'] || $time_na >= $stops) {
                            if ($time_nb < C('market')[$market]['start_time'] || $time_nb >= $stops) {
                                continue;
                            }
                        }
                        // 处理开盘闭盘交易时间===结束
                        // 处理周六周日是否可交易===开始
                        // $weeks = date('N',time());
                        $weeks_na = date('N', $na);
                        $weeks_nb = date('N', $nb);
                        if (!C('market')[$market]['agree6']) {
                            if ($weeks_na == 6 && $weeks_nb == 6) {
                                continue;
                            }
                        }
                        if (!C('market')[$market]['agree7']) {
                            if ($weeks_na == 7 && $weeks_nb == 7) {
                                continue;
                            }
                        }
                        // 处理周六周日是否可交易===结束
                    }

                    if (time() < $na) {
                        break;
                    }

                    $sum = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->sum('num');

                    if ($sum) {
                        $sta = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->order('id asc')->getField('price');
                        $max = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->max('price');
                        $min = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->min('price');
                        $end = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->order('id desc')->getField('price');
                        $d = array($na, $sum, $sta, $max, $min, $end);

                        if (M('TradeJson')->where(array('market' => $market, 'addtime' => $na, 'type' => $v))->find()) {
                            M('TradeJson')->where(array('market' => $market, 'addtime' => $na, 'type' => $v))->save(array('data' => json_encode($d)));
                        } else {
                            $aa = M('TradeJson')->add(array('market' => $market, 'data' => json_encode($d), 'addtime' => $na, 'type' => $v));
                            M('TradeJson')->execute('commit');
                            M('TradeJson')->where(array('market' => $market, 'data' => '', 'type' => $v))->delete();
                            M('TradeJson')->execute('commit');
                        }
                    }
                    /* else {
                        M('TradeJson')->add(array('market' => $market, 'data' => '', 'addtime' => $na, 'type' => $v));
                        M('TradeJson')->execute('commit');
                    } */
                }
            }
        }
        return '计算成功!';
    }

    function overtimedd()
    {
        //删除超过30分钟未成交的虚假订单
        $map['addtime'] = array('lt', (time() - 60 * 30));
        $map['userid'] = 0;
        $map['status'] = 0;
        $deldd = M('trade')->where($map)->delete();
        if ($deldd) {
            echo '已清除' . $deldd . '条数据';
            exit;
        } else {
            echo '没有可以清除的数据';
            exit;
        }

    }

    public function getmarketlist()
    {
        $marketlist = M('market')->select();
        $sdmarket = array();
        foreach ($marketlist as $k => $v) {
            # code...
            if ($v['shuadan'] == 1) {
                $sdmarket[$k]['name'] = $v['name'];
            }
        }
        if ($sdmarket) {
            exit (json_encode($sdmarket));
        } else {
            return 'error';
        }
    }
	
	// 行情爬虫 CoinMarketCap
    public function btctormb()
    {
        // $url='https://api.kucoin.com/v1/open/currencies';
        //$url1 = 'https://api.coinmarketcap.com/v1/ticker/bitcoin/?convert=CNY';//okcoin,不用美元转换了
        //$url2 = 'https://api.coinmarketcap.com/v1/ticker/Ethereum/?convert=CNY';//okcoin,不用美元转换了
        $url3 = 'https://api.coinmarketcap.com/v1/ticker/tether/?convert=CNY';//okcoin,不用美元转换了
		
        //$content1 = file_get_contents($url1);
        //$content2 = file_get_contents($url2);
		
        $content3 = file_get_contents($url3);
        //$content1 = json_decode($content1, true);
        //$content2 = json_decode($content2, true);
        $content3 = json_decode($content3, true);
        // dump($content1);
		
        //$btc = round($content1[0]['price_cny'], 2);
        //$eth = round($content2[0]['price_cny'], 2);
        $usdt = round($content3[0]['price_cny'], 2);
		
        //$btclast = $content1[0]['last_updated'];
        //$ethlast = $content2[0]['last_updated'];
		$usdtlast = $content3[0]['last_updated'];
		
        // $rmb=$content['data']['rates']['BTC']['CNY'];
        // dump($btclast);
		
        $map['usdt'] = $usdt - ($usdt * 0.01); //价格做过额外调整
		//$map['btc'] = $btc;
        //$map['eth'] = $eth;
		
		$map['usdtlast'] = $usdtlast;
        //$map['btclast'] = $btclast;
        //$map['ethlast'] = $ethlast;
		
        // if($content1['ticker']['last']>0 or $content2['ticker']['last']>0){
        $config = M('config')->where(array('id' => 1))->save($map);
        // }
        if ($config) {
            echo 'OK';
        }
    }

	// 行情爬虫 gate.io比特儿海外
    public function gatetormb()
    {
		$config = M('config')->where(array('id' => 1))->find();
		
		$marketData = M('Market')->where(array('shuadan' => 1,'sdtype' => 1))->select();
		foreach ($marketData as $k => $v) {
			
			if (!$v['name']) {
				echo '交易市场错误'; die();
			} else {
				$xnb[$k] = explode('_', $v['name'])[0];
				$rmb[$k] = explode('_', $v['name'])[1];
			}
			
			if ($rmb[$k] == Anchor_CNY) {
				$rmb2[$k] = 'usdt';
			} else {
				$rmb2[$k] = $rmb[$k];
			}
			
			$url = 'http://data.gateio.io/api2/1/ticker/'.$xnb[$k].'_'.$rmb2[$k];
			$content = file_get_contents($url);
			$content = json_decode($content, true);

			//print_r($content);
			if ($rmb[$k] == Anchor_CNY) {
				$content['lowestAsk'] = $content['lowestAsk'] * $config['usdt']; //卖一价
				$content['highestBid'] = $content['highestBid'] * $config['usdt']; //买一价
				$wei = 10000000;
				if (floatval($content['lowestAsk']) < 10) {
					$wei = 10000000;
				}
				if (floatval($content['highestBid']) < 10) {
					$wei = 10000000;
				}
			} else {
				$wei = 10000000;
				if (floatval($content['lowestAsk']) < 10) {
					$wei = 10000000;
				}
				if (floatval($content['highestBid']) < 10) {
					$wei = 10000000;
				}
			}
			
			$min_price = $content['highestBid'] * $wei; //最低价格
			$max_price = $content['lowestAsk'] * $wei; //最高价格

            if ($max_price < $min_price) {
                $min_price = $max_price;
                $max_price = $min_price;
            }
			
			$map['sdlow'] = round($min_price / $wei, 6); //最低价格
			$map['sdhigh'] = round($max_price / $wei, 6); //最高价格
			
			if (M('Market')->where(array('name' => $v['name']))->save($map)) {
				if (!$map['sdlow'] || !$map['sdhigh']) {
					echo $v['name'].' - Error 1'.'<br>';
				} else {
					echo $v['name'].' - OK'.'<br>';
				}
			} else {
				echo $v['name'].' - Error 2'.'<br>';
			}
		}
		
		if (!$marketData) {
			echo '查询设置不存在';
		}
    }
//    机器人刷单
    public function allautojqr(){
        $markets = M('market')->field('name,market_price,sell_price')->where(array('shuadan' => 1))->select();
//        var_dump($markets);exit;
        foreach ($markets as $key => $val){
            if($val['market_price'] == 1){
                for ($i = 0;$i<2;$i++){
                    $this->autojy3($val['name'],$val['sell_price']);
                    sleep(2);
                }
            }else{
                for ($i = 0;$i<2;$i++){
                    $this->autojy2($val['name']);
                    sleep(3);
                }
            }
        }
//        $workerman =A('Wokerman');
//        $workerman->sendToSuccess($market);
    }
	/** 机器人交易刷单 **/
    public function autojy2($market = NULL)
    {
        // 过滤非法字符----------------S
        if (checkstr($market)) {
            $this->error('您输入的信息有误！');
        }
        // 过滤非法字符----------------E
		
        $config = M('config')->where(array('id' => 1))->find();
        $type = rand(1, 2);

        if (!$market) {
            $market = C('market_mr');
        }

        if (!C('market')[$market]) {
            echo '交易市场错误'; die();
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }
        $marketinfo = M('market')->where(array('name' => $market))->find();//获取市场信息

        if ($marketinfo['shuadan'] == 1) {//开启刷单则进行

			if ($marketinfo['sdtype'] == 1) { //同步第三方价格
				if ($rmb == Anchor_CNY) {
					$rmb2 = 'usdt';
				} else {
					$rmb2 = $rmb;
				}

				$url = 'https://data.gate.io/api2/1/ticker/'.$xnb.'_'.$rmb2; //gate.io,不用美元转换了
//                echo $url;exit;
//                $url = 'https://data.gate.io/api2/1/ticker/btc_usdt';
				$content = file_get_contents($url);
				$content = json_decode($content, true);
//				print_r($content);exit;
				
				if ($rmb == Anchor_CNY) {
					$content['lowestAsk'] = $content['lowestAsk'] * $config['usdt']; //卖一价
					$content['highestBid'] = $content['highestBid'] * $config['usdt']; //买一价
					$wei = 10000000;
					if (floatval($content['lowestAsk']) < 10) {
						$wei = 10000000;
					}
					if (floatval($content['highestBid']) < 10) {
						$wei = 10000000;
					}
				} else {
					$wei = 10000000;
					if (floatval($content['lowestAsk']) < 10) {
						$wei = 10000000;
					}
					if (floatval($content['highestBid']) < 10) {
						$wei = 10000000;
					}
				}
				$wei = 1;
				$min_price = $content['highestBid'] * $wei; //下限价格
				$max_price = $content['lowestAsk'] * $wei; //上限价格
			} else {
				$wei = 1;
				$min_price = $marketinfo['sdlow'] * $wei; //最低价格
				$max_price = $marketinfo['sdhigh'] * $wei; //最高价格
			}
			
//			$wei = 1;
//			//sdlow刷单最低价格
//			$min_price = $marketinfo['sdlow'] * $wei; //最低价格
//            //sdhigh刷单最低价格
//			$max_price = $marketinfo['sdhigh'] * $wei; //最高价格
            //sdlow_num刷单最低数量
			$min_x_num = $marketinfo['sdlow_num'] * $wei; //最低数量
            //sdhigh_num刷单最高数量
			$max_x_num = $marketinfo['sdhigh_num'] * $wei; //最高数量

            // dump($content) ;
            //$markets = M('market')->where(array('name' => $market))->getField('id,round,round_mum',1);
			$marketround = $marketinfo['round']; //获取交易价小数点
			$marketmum = $marketinfo['round_mum']; //获取交易数量小数点
			//若刷单最高价小于刷单最低价 那么则进行互换
            if ($max_price < $min_price) {
                $nase = $min_price;
                $min_price = $max_price;
                $max_price = $nase;
               // $min_price = $max_price;
                //$max_price = $min_price;
            }
			
            //如果设置了最高,最低刷单上下线,则价格按照此区域运行
            if ($marketinfo['sdhigh'] > 0 && !$marketinfo['zhang']) {
                $max_price = $marketinfo['sdhigh'] * $wei;
            }
            if ($marketinfo['sdlow'] > 0 && !$marketinfo['die']) {
                $min_price = $marketinfo['sdlow'] * $wei;
            }
            if ($marketinfo['zhang'] > 0) {

                $max_price = $marketinfo['hou_price'] * (1 + $marketinfo['zhang']) * $wei;
            }
            if ($marketinfo['die'] > 0) {
                $min_price = $marketinfo['hou_price'] * (1 + $marketinfo['die']) * $wei;
            }
			
			if (strlen($min_price) > 3||strlen($max_price) > 3) {
				$tbsss = str_pad(1,$marketround+2,"0",STR_PAD_RIGHT);
			} else {
				$tbsss = 1000;
			}
var_dump($market);
			var_dump($min_price);
			$min_price = $min_price * $tbsss;
			$max_price = $max_price * $tbsss;
            $price = round((rand($min_price, $max_price)/$tbsss) / $wei, $marketround);//随机价
            // $price = $content['last'];//最新价
            //echo $price;die();

/*            $price = round($price, $marketround); //OKcoin直接用美元行情
            $price=round($price/$config['usd'],$marketround); //人民币行情转换成美元行情*/
            // dump($num);
			
			// 刷单数量
			if ($max_x_num >0 && $min_x_num >0) {
				if ($max_x_num > 99999) {
				    $muls = 1;
				} else {
				    $muls = 10000;
				}
				if ($min_x_num > 99999) {
				    $muns = 1;
				} else {
				    $muns = 10000;
				}
				
                $max_num = round($max_x_num * $muls, $marketmum);
                $min_num = round($min_x_num * $muns, $marketmum);
				
/*				if ($muls >= 10000 || $muns >= 10000) {
					$num = round(rand($min_num, $max_num) / 10000, $marketmum);
				} else {
					$num = round(rand($min_num, $max_num), $marketmum);
				}*/
				
				$num = round(rand($min_num, $max_num) / $muns, $marketmum);
				
			} else {
                $max_num = round(9.9999 * 10000, $marketmum);
                $min_num = round(0.0001 * 10000, $marketmum);
                $num = round(rand($min_num, $max_num) / 10000, $marketmum);
            }
			
            if (!$price) { echo '交易价格格式错误';die(); }
            if (!check($num, 'double')) { echo '交易数量格式错误';die(); }
            if (($type != 1) && ($type != 2)) { echo '交易类型格式错误';die(); }
            if (!$price) { echo '交易价格错误';die(); }
			
            $num = round(trim($num), 5);
            if (!check($num, 'double')) { echo '交易数量错误';die(); }
			
            $mum = round($num * $price, 8);
            if (!$rmb) { echo '数据错误1';die(); }
            if (!$xnb) { echo '数据错误2';die(); }
            if (!$market) { echo '数据错误3';die(); }
            if (!$price) { echo '数据错误4';die(); }
            if (!$num) { echo '数据错误5';die(); }
            if (!$mum) { echo '数据错误6';die(); }
            if (!$type) { echo '数据错误7';die(); }
			
            $mo = M();
//            $mo->execute('set autocommit=0');
//            $mo->execute('lock tables tw_trade write ');
            $rs = array();
			if ($price <= 0 || $num <= 0 || $mum <= 0) { echo '交易失败！';die(); }
            if ($type == 1) {
                $rs[] = $mo->table('tw_trade')->add(array('userid' => 0, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => 0, 'type' => 1, 'addtime' => time(), 'status' => 0));
            } else if ($type == 2) {
                $rs[] = $mo->table('tw_trade')->add(array('userid' => 0, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => 0, 'type' => 2, 'addtime' => time(), 'status' => 0));
            } else {
                $mo->execute('rollback');
//                $mo->execute('unlock tables');
                echo '交易类型错误';
                die();
            }
            if (check_arr($rs)) {
                $mo->execute('commit');
//                $mo->execute('unlock tables');
                // A('Trade')->matchingAutoTrade($market);//匹配虚拟订单
//                A('Trade')->matchingTradeall($market);//匹配所有订单
                A('Trade')->autoMatchingTradeall($market);//匹配机器人订单
                echo '交易成功！';
            } else {
                $mo->execute('rollback');
//                $mo->execute('unlock tables');
                echo '交易失败！';
            }
        } else {
            echo '市场未开启刷单!';
        }
    }

    public function autojy3($market = null){
//        $market = 'btc_usdt';
       if(!$market){
           return false;
       }
        $mo = M();
        $wei = 1;
        $marketinfo = M('market')->where(array('name' => $market))->find();//获取市场信息
        $buy = $mo->table('tw_trade')->where(array('market' => $market,'userid' => array('gt',0), 'type' => 1, 'status' => 0))->order('price desc,id asc')->find();
        $sell = $mo->table('tw_trade')->where(array('market' => $market,'userid' => array('gt',0), 'type' => 2, 'status' => 0))->order('price asc,id asc')->find();
        $min_x_num = $marketinfo['sdlow_num'] * $wei; //最低数量
        //sdhigh_num刷单最高数量
        $max_x_num = $marketinfo['sdhigh_num'] * $wei; //最高数量

        // dump($content) ;
        //$markets = M('market')->where(array('name' => $market))->getField('id,round,round_mum',1);
//        $marketround = $marketinfo['round']; //获取交易价小数点
        $marketmum = $marketinfo['round_mum']; //获取交易数量小数点

        $max_num = round($max_x_num * 10000, $marketmum);
        $min_num = round($min_x_num * 10000, $marketmum);
        $num = round(rand($min_num, $max_num) / 10000, $marketmum);
        $buy = $buy['price'];
        $sell = $sell['price'];
        if($buy > $sell){
            $a = $buy;
            $buy = $sell;
            $sell = $buy;
        }
        $price = rand($buy*10000000,$sell*10000000);
        $price = $price/10000000;
//        var_dump($buy);
//        var_dump($sell);
//        var_dump($price);
        if($price<=0){
            return false;
        }
        $mum = round($num * $price, 8);
        $type = rand(1,2);
        if($type == 1){
            $mo->table('tw_trade')->add(array('userid' => 0, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => 0, 'type' => 1, 'addtime' => time(), 'status' => 0));
            $mo->table('tw_trade')->add(array('userid' => 0, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => 0, 'type' => 2, 'addtime' => time(), 'status' => 0));
        }else{
            $mo->table('tw_trade')->add(array('userid' => 0, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => 0, 'type' => 2, 'addtime' => time(), 'status' => 0));
            $mo->table('tw_trade')->add(array('userid' => 0, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => 0, 'type' => 1, 'addtime' => time(), 'status' => 0));
        }
        A('Trade')->autoMatchingTradeall($market);//匹配机器人订单
//        echo 2323232;
//        try {
//            echo 1111;
//            $workerman = A('Wokerman');
//            $workerman->sendToSuccess($market);
//            echo 3333;
//        }catch(\Think\Exception $e) {
//            echo 22222;
//            var_dump($e);
//        }
    }
    /** 机器人交易刷单 **/
    public function autojy4($market = NULL)
    {$market = 'eth_usdt';
        // 过滤非法字符----------------S
        if (checkstr($market)) {
            $this->error('您输入的信息有误！');
        }
        // 过滤非法字符----------------E

        $config = M('config')->where(array('id' => 1))->find();
        $type = rand(1, 2);

        if (!$market) {
            $market = C('market_mr');
        }

        if (!C('market')[$market]) {
            echo '交易市场错误'; die();
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }
        $marketinfo = M('market')->where(array('name' => $market))->find();//获取市场信息

        if ($marketinfo['shuadan'] == 1) {//开启刷单则进行

            if ($marketinfo['sdtype'] == 1) { //同步第三方价格
                if ($rmb == Anchor_CNY) {
                    $rmb2 = 'usdt';
                } else {
                    $rmb2 = $rmb;
                }

                $url = 'https://data.gate.io/api2/1/ticker/'.$xnb.'_'.$rmb2; //gate.io,不用美元转换了
//                echo $url;exit;
//                $url = 'https://data.gate.io/api2/1/ticker/btc_usdt';
                $content = file_get_contents($url);
                $content = json_decode($content, true);
//				print_r($content);exit;

                if ($rmb == Anchor_CNY) {
                    $content['lowestAsk'] = $content['lowestAsk'] * $config['usdt']; //卖一价
                    $content['highestBid'] = $content['highestBid'] * $config['usdt']; //买一价
                    $wei = 10000000;
                    if (floatval($content['lowestAsk']) < 10) {
                        $wei = 10000000;
                    }
                    if (floatval($content['highestBid']) < 10) {
                        $wei = 10000000;
                    }
                } else {
                    $wei = 10000000;
                    if (floatval($content['lowestAsk']) < 10) {
                        $wei = 10000000;
                    }
                    if (floatval($content['highestBid']) < 10) {
                        $wei = 10000000;
                    }
                }
                $wei = 1;
                $min_price = $content['highestBid'] * $wei; //下限价格
                $max_price = $content['lowestAsk'] * $wei; //上限价格
            } else {
                $wei = 1;
                $min_price = $marketinfo['sdlow'] * $wei; //最低价格
                $max_price = $marketinfo['sdhigh'] * $wei; //最高价格
            }

//			$wei = 1;
//			//sdlow刷单最低价格
//			$min_price = $marketinfo['sdlow'] * $wei; //最低价格
//            //sdhigh刷单最低价格
//			$max_price = $marketinfo['sdhigh'] * $wei; //最高价格
            //sdlow_num刷单最低数量
            $min_x_num = $marketinfo['sdlow_num'] * $wei; //最低数量
            //sdhigh_num刷单最高数量
            $max_x_num = $marketinfo['sdhigh_num'] * $wei; //最高数量

            // dump($content) ;
            //$markets = M('market')->where(array('name' => $market))->getField('id,round,round_mum',1);
            $marketround = $marketinfo['round']; //获取交易价小数点
            $marketmum = $marketinfo['round_mum']; //获取交易数量小数点
            //若刷单最高价小于刷单最低价 那么则进行互换
            if ($max_price < $min_price) {
                $nase = $min_price;
                $min_price = $max_price;
                $max_price = $nase;
                // $min_price = $max_price;
                //$max_price = $min_price;
            }

            //如果设置了最高,最低刷单上下线,则价格按照此区域运行
            if ($marketinfo['sdhigh'] > 0 && !$marketinfo['zhang']) {
                $max_price = $marketinfo['sdhigh'] * $wei;
            }
            if ($marketinfo['sdlow'] > 0 && !$marketinfo['die']) {
                $min_price = $marketinfo['sdlow'] * $wei;
            }
            if ($marketinfo['zhang'] > 0) {

                $max_price = $marketinfo['hou_price'] * (1 + $marketinfo['zhang']) * $wei;
            }
            if ($marketinfo['die'] > 0) {
                $min_price = $marketinfo['hou_price'] * (1 + $marketinfo['die']) * $wei;
            }

            if (strlen($min_price) > 3||strlen($max_price) > 3) {
                $tbsss = str_pad(1,$marketround+2,"0",STR_PAD_RIGHT);
            } else {
                $tbsss = 1000;
            }
            var_dump($market);
            var_dump($min_price);
            $min_price = $min_price * $tbsss;
            $max_price = $max_price * $tbsss;
            $price = round((rand($min_price, $max_price)/$tbsss) / $wei, $marketround);//随机价
            // $price = $content['last'];//最新价
            //echo $price;die();

            /*            $price = round($price, $marketround); //OKcoin直接用美元行情
                        $price=round($price/$config['usd'],$marketround); //人民币行情转换成美元行情*/
            // dump($num);

            // 刷单数量
            if ($max_x_num >0 && $min_x_num >0) {
                if ($max_x_num > 99999) {
                    $muls = 1;
                } else {
                    $muls = 10000;
                }
                if ($min_x_num > 99999) {
                    $muns = 1;
                } else {
                    $muns = 10000;
                }

                $max_num = round($max_x_num * $muls, $marketmum);
                $min_num = round($min_x_num * $muns, $marketmum);

                /*				if ($muls >= 10000 || $muns >= 10000) {
                                    $num = round(rand($min_num, $max_num) / 10000, $marketmum);
                                } else {
                                    $num = round(rand($min_num, $max_num), $marketmum);
                                }*/

                $num = round(rand($min_num, $max_num) / $muns, $marketmum);

            } else {
                $max_num = round(9.9999 * 10000, $marketmum);
                $min_num = round(0.0001 * 10000, $marketmum);
                $num = round(rand($min_num, $max_num) / 10000, $marketmum);
            }

            if (!$price) { echo '交易价格格式错误';die(); }
            if (!check($num, 'double')) { echo '交易数量格式错误';die(); }
            if (($type != 1) && ($type != 2)) { echo '交易类型格式错误';die(); }
            if (!$price) { echo '交易价格错误';die(); }

            $num = round(trim($num), 5);
            if (!check($num, 'double')) { echo '交易数量错误';die(); }

            $mum = round($num * $price, 8);
            if (!$rmb) { echo '数据错误1';die(); }
            if (!$xnb) { echo '数据错误2';die(); }
            if (!$market) { echo '数据错误3';die(); }
            if (!$price) { echo '数据错误4';die(); }
            if (!$num) { echo '数据错误5';die(); }
            if (!$mum) { echo '数据错误6';die(); }
            if (!$type) { echo '数据错误7';die(); }

            $mo = M();
//            $mo->execute('set autocommit=0');
//            $mo->execute('lock tables tw_trade write ');
            $rs = array();
            if ($price <= 0 || $num <= 0 || $mum <= 0) { echo '交易失败！';die(); }
            if ($type == 1) {
                $rs[] = $mo->table('tw_trade')->add(array('userid' => 0, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => 0, 'type' => 1, 'addtime' => time(), 'status' => 0));
            } else if ($type == 2) {
                $rs[] = $mo->table('tw_trade')->add(array('userid' => 0, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => 0, 'type' => 2, 'addtime' => time(), 'status' => 0));
            } else {
                $mo->execute('rollback');
//                $mo->execute('unlock tables');
                echo '交易类型错误';
                die();
            }
            if (check_arr($rs)) {
                $mo->execute('commit');
//                $mo->execute('unlock tables');
                // A('Trade')->matchingAutoTrade($market);//匹配虚拟订单
//                A('Trade')->matchingTradeall($market);//匹配所有订单
                A('Trade')->autoMatchingTradeall($market);//匹配机器人订单
                echo '交易成功！';
            } else {
                $mo->execute('rollback');
//                $mo->execute('unlock tables');
                echo '交易失败！';
            }
        } else {
            echo '市场未开启刷单!';
        }
    }
    function randomFloat($min, $max)
    {
		//生成随机浮点数
        // return $min + mt_rand() / mt_getrandmax() * ($max - $min);
        return round($min + mt_rand() / mt_getrandmax() * ($max - $min), 1);
    }

    public function autotrade($market = NULL, $price, $num, $type, $userid)
    {
        // 处理开盘闭盘交易时间===开始
        $times = date('G', time());
        $minute = date('i', time());
        $minute = intval($minute);
        if (($times <= C('market')[$market]['start_time'] && $minute < intval(C('market')[$market]['start_minute'])) || ($times > C('market')[$market]['stop_time'] && $minute >= intval(C('market')[$market]['stop_minute']))) {
            $this->error('该时间为闭盘时间！');
        }
        if (($times < C('market')[$market]['start_time']) || $times > C('market')[$market]['stop_time']) {
            $this->error('该时间为闭盘时间！');
        } else {
            if ($times == C('market')[$market]['start_time']) {
                if ($minute < intval(C('market')[$market]['start_minute'])) {
                    $this->error('该时间为闭盘时间！');
                }
            } elseif ($times == C('market')[$market]['stop_time']) {
                if (($minute > C('market')[$market]['stop_minute'])) {
                    $this->error('该时间为闭盘时间！');
                }
            }
        }
        // 处理周六周日是否可交易===开始
        $weeks = date('N', time());
        if (!C('market')[$market]['agree6']) {
            if ($weeks == 6) {
                $this->error('您好，周六为闭盘时间！');
            }
        }
        if (!C('market')[$market]['agree7']) {
            if ($weeks == 7) {
                $this->error('您好，周日为闭盘时间！');
            }
        }
        //处理周六周日是否可交易===结束
        if (!check($price, 'double')) {
            $this->error('交易价格格式错误');
        }
        if (!check($num, 'double')) {
            $this->error('交易数量格式错误');
        }
        if (($type != 1) && ($type != 2)) {
            $this->error('交易类型格式错误');
        }

        if ($type == 1) {
            if (!$num) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);
                $this->error('单笔买入最小交易数量为：' . C('market')[$market]['trade_buy_num_min'] . ' ' . $nnn_coin . '!');
            }
            if ($num < C('market')[$market]['trade_buy_num_min']) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);
                $this->error('单笔买入最小交易数量为：' . C('market')[$market]['trade_buy_num_min'] . ' ' . $nnn_coin . '!');
            }
            if ($num > C('market')[$market]['trade_buy_num_max']) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);
                $this->error('单笔买入最大交易数量为：' . C('market')[$market]['trade_buy_num_max'] . ' ' . $nnn_coin . '!');
            }
        }
        if ($type == 2) {
            if (!$num) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);
                $this->error('单笔卖出最小交易数量为：' . C('market')[$market]['trade_sell_num_min'] . ' ' . $nnn_coin . '!');
            }
            if ($num < C('market')[$market]['trade_sell_num_min']) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);
                $this->error('单笔卖出最小交易数量为：' . C('market')[$market]['trade_sell_num_min'] . ' ' . $nnn_coin . '!');
            }
            if ($num > C('market')[$market]['trade_sell_num_max']) {
                $nnn_coin = explode('_', $market);
                $nnn_coin = strtoupper($nnn_coin[0]);
                $this->error('单笔卖出最大交易数量为：' . C('market')[$market]['trade_sell_num_max'] . ' ' . $nnn_coin . '!');
            }
        }

        $user = M('User')->where(array('id' => $userid))->find();

        if (!C('market')[$market]) {
            $this->error('交易市场错误');
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }

        if (!C('market')[$market]['trade']) {
            $this->error('当前市场禁止交易');
        }

        $price = round(floatval($price), C('market')[$market]['round']);
        if (!$price) {
            $this->error('交易价格错误' . $price);
        }

        $num = round($num, 8 - C('market')[$market]['round']);
        if (!check($num, 'double')) {
            $this->error('交易数量错误');
        }
        if ($type == 1) {
            $min_price = (C('market')[$market]['buy_min'] ? C('market')[$market]['buy_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['buy_max'] ? C('market')[$market]['buy_max'] : 10000000);
        } else if ($type == 2) {
            $min_price = (C('market')[$market]['sell_min'] ? C('market')[$market]['sell_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['sell_max'] ? C('market')[$market]['sell_max'] : 10000000);
        } else {
            $this->error('交易类型错误');
        }
		
        if ($max_price < $price) {
            // $this->error('交易价格超过今日涨幅限制！');
            $price = $max_price;
        }
        if ($price < $min_price) {
            // $this->error('交易价格超过今日跌幅限制！');
            $price = $min_price;
        }
		
        $hou_price = C('market')[$market]['hou_price'];
        if ($hou_price) {
            if (C('market')[$market]['zhang']) {
                // TODO: SEPARATE
                $zhang_price = round(($hou_price / 100) * (100 + C('market')[$market]['zhang']), C('market')[$market]['round']);
                if ($zhang_price < $price) {
                    echo "交易价格超过今日涨幅限制！";
                    // $this->error('交易价格超过今日涨幅限制！');
                    // $price=$zhang_pric-(rand(-10,10)/100);
                }
            }

            if (C('market')[$market]['die']) {
                $die_price = round(($hou_price / 100) * (100 - C('market')[$market]['die']), C('market')[$market]['round']);
                if ($price < $die_price) {
                    echo "交易价格超过今日跌幅限制";
                    // $this->error('交易价格超过今日跌幅限制！');
                    // $price=$die_price+(rand(-10,10)/100);
                }
            }

        }

        $user_coin = M('UserCoin')->where(array('userid' => $userid))->find();

        if ($type == 1) {
            $trade_fee = C('market')[$market]['fee_buy'];
            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 + $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }
            if ($user_coin[$rmb] < $mum) {
                $this->error(C('coin')[$rmb]['title'] . '余额不足！');
            }

        } else if ($type == 2) {
            $trade_fee = C('market')[$market]['fee_sell'];
            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 - $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }
            if ($user_coin[$xnb] < $num) {
                $this->error(C('coin')[$xnb]['title'] . '余额不足！');
            }
        } else {
            $this->error('交易类型错误');
        }
		
        if (!$rmb) {
            $this->error('数据错误101');
        }
        if (!$xnb) {
            $this->error('数据错误102');
        }
        if (!$market) {
            $this->error('数据错误103');
        }
        if (!$price) {
            $this->error('数据错误104');
        }
        if (!$num) {
            $this->error('数据错误105');
        }
        if (!$mum) {
            $this->error('数据错误106');
        }
        if (!$type) {
            $this->error('数据错误107');
        }

        try {

            $mo = M();
            $mo->execute('set autocommit=0');
            // $mo->execute('lock tables tw_trade write ,tw_user_coin write ,tw_finance write');
//            $mo->execute('lock tables tw_trade write ,tw_user_coin write ,tw_finance write,tw_finance_log write,tw_user write');//处理资金变更日志

            $rs = array();
            $user_coin = $mo->table('tw_user_coin')->where(array('userid' => $userid))->find();

            if ($type == 1) {
                if ($user_coin[$rmb] < $mum) {
                    throw new \Think\Exception(C('coin')[$rmb]['title'] . '余额不足！');
                }

                $finance = $mo->table('tw_finance')->where(array('userid' => $userid))->order('id desc')->find();
                $finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $userid))->find();

                $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $userid))->setDec($rmb, $mum);
                $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $userid))->setInc($rmb . 'd', $mum);
                $rs[] = $finance_nameid = $mo->table('tw_trade')->add(array('userid' => $userid, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 1, 'addtime' => time(), 'status' => 0, 'sort' => 1));//sort=1设置为自动刷单,以便交易的时候随机显示买卖

                $finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $userid))->find();
                $finance_hash = md5($userid . $finance_num_user_coin['cny'] . $finance_num_user_coin['cnyd'] . $mum . $finance_mum_user_coin['cny'] . $finance_mum_user_coin['cnyd'] . MSCODE . 'tp3.net.cn');
                $finance_num = $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'];

                // 处理资金变更日志-----------------S
                $user_n_info = $mo->table('tw_user')->where(array('id' => $userid))->find();

                $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 0, 'amount' => $mum, 'optype' => 18, 'cointype' => 1, 'old_amount' => $finance_num_user_coin[$rmb], 'new_amount' => $finance_mum_user_coin[$rmb], 'userid' => $userid, 'adminid' => $userid, 'addip' => get_client_ip(), 'position' => 1));

                $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 1, 'amount' => $mum, 'optype' => 20, 'cointype' => 1, 'old_amount' => $finance_num_user_coin[$rmb . 'd'], 'new_amount' => $finance_mum_user_coin[$rmb . 'd'], 'userid' => $userid, 'adminid' => $userid, 'addip' => get_client_ip(), 'position' => 1));
                // 处理资金变更日志-----------------E

                if ($finance['mum'] < $finance_num) {
                	$finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                } else {
                    $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                }

                $rs[] = $mo->table('tw_finance')->add(array('userid' => $userid, 'coinname' => 'cny', 'num_a' => $finance_num_user_coin['cny'], 'num_b' => $finance_num_user_coin['cnyd'], 'num' => $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'], 'fee' => $mum, 'type' => 2, 'name' => 'trade', 'nameid' => $finance_nameid, 'remark' => '交易中心-委托买入-市场' . $market, 'mum_a' => $finance_mum_user_coin['cny'], 'mum_b' => $finance_mum_user_coin['cnyd'], 'mum' => $finance_mum_user_coin['cny'] + $finance_mum_user_coin['cnyd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
				
            } else if ($type == 2) {
				
                if ($user_coin[$xnb] < $num) {
                    throw new \Think\Exception(C('coin')[$xnb]['title'] . '余额不足！');
                }

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

                $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 0, 'amount' => $num, 'optype' => 19, 'cointype' => $cointype, 'old_amount' => $fin_user_coin[$xnb], 'new_amount' => $fin_user_coin_new[$xnb], 'userid' => $userid, 'adminid' => $userid, 'addip' => get_client_ip(), 'position' => 1));

                $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_n_info['username'], 'adminname' => $user_n_info['username'], 'addtime' => time(), 'plusminus' => 1, 'amount' => $num, 'optype' => 21, 'cointype' => $cointype, 'old_amount' => $fin_user_coin[$xnb . 'd'], 'new_amount' => $fin_user_coin_new[$xnb . 'd'], 'userid' => $userid, 'adminid' => $userid, 'addip' => get_client_ip(), 'position' => 1));

                // 处理资金变更日志-----------------E
            } else {
                throw new \Think\Exception('交易类型错误');
            }
            if (check_arr($rs)) {
                $mo->execute('commit');
//                $mo->execute('unlock tables');
            } else {
                throw new \Think\Exception('交易失败！');
            }
        } catch (\Think\Exception $e) {

            $mo->execute('rollback');
//            $mo->execute('unlock tables');
            $this->error('交易失败！');
        }

        S('getDepth', null);
        $this->success('交易成功！');
    }

    function ccapikey()
    {
        header('Content-type: application/json');
        $accesskey = 'RkAyda9huaQYux6R'; //定义APIKEY
        if ($_POST['accesskey'] || $_GET['accesskey']) {
            if (!$_GET['user'] || !$_GET['userid'] || !$_GET['account'] || !$_GET['apikey']) {
                $result = array('code' => 0, 'msg' => 'Parmars is lost!');
                echo json_encode($result);
                exit;
            }
            $user = M('user')->where(array('username' => $_GET['account'], 'apikey' => $_GET['apikey']))->find();
            if (!$user) {
                $result = array('code' => 0, 'msg' => 'User or APIKEY not found!');
                echo json_encode($result);
                exit;
            } elseif ($user['otcuser'] or $user['otcuserid']) {
                $result = array('code' => 0, 'msg' => 'User alreay binded!');
                echo json_encode($result);
                exit;
            } else {
                $map['otcuser'] = $_GET['user'];
                $map['otcuserid'] = $_GET['userid'];
                $rs = M('user')->where(array('username' => $_GET['account'], 'apikey' => $_GET['apikey']))->save($map);
                if ($rs) {
                    $result = array('code' => 1, 'msg' => 'BIND Success!', 'user' => $user['username'], 'userid' => $user['id']);
                    echo json_encode($result);
                    exit;
                } else {
                    $result = array('code' => 0, 'msg' => 'Bind Failed!');
                    echo json_encode($result);
                    exit;
                }
            }
        } else {
            echo 'Yo!';
        }
    }

    function sendbb()
    {
        header('Content-type: application/json');
        // $accesskey = 'RkAyda9huaQYux6R';//定义APIKEY
        if ($_GET['accesskey'] && $_GET['accesskey'] == C('BBAPIKEY')) {
            // if($_POST['accesskey'] ||$_GET['accesskey']){
            if (!$_GET['user'] || !$_GET['num'] || !$_GET['coin']) {
                $result = array('code' => 0, 'msg' => 'Parmars is lost!');
                echo json_encode($result);
                exit;
            }
            $user = M('user')->where(array('username' => $_GET['user']))->find();
            $coin = M('coin')->where(array('name' => $_GET['coin'], 'status' => 1))->find();
            $usercoin = M('user_coin')->where(array('userid' => $user['id']))->find();
            if (!$user) {
                $result = array('code' => 0, 'msg' => 'User  not found!');
                echo json_encode($result);
                exit;
            } elseif (!$coin) {
                $result = array('code' => 0, 'msg' => 'Coin  not found!');
                echo json_encode($result);
                exit;
            } else {
                try {
                    $mo = M();
                    $mo->startTrans();
                    $rs = array();
                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $user['id']))->setInc($coin['name'], $_GET['num']);

                    $rs[] = $mo->table('tw_myzr')->add(array('userid' => $user['id'], 'username' => '转入自场外交易', 'coinname' => $coin['name'], 'txid' => '转入自场外交易用户:' . $user['username'], 'num' => $_GET['num'], 'fee' => 0, 'mum' => $_GET['num'], 'addtime' => time(), 'status' => 1));

                    // 处理资金变更日志-----------------S
                    $user_zj_coin = $mo->table('tw_user_coin')->where(array('userid' => $user['id']))->find();

                    // 转出人记录
                    $mo->table('tw_finance_log')->add(array('username' => $user['username'], 'adminname' => $user['username'], 'addtime' => time(), 'plusminus' => 0, 'amount' => $_GET['num'], 'optype' => 6, 'position' => 1, 'cointype' => $coin['id'], 'old_amount' => $usercoin[$coin['name']], 'new_amount' => $user_zj_coin[$coin['name']], 'userid' => $user['id'], 'adminid' => $user['id'], 'addip' => get_client_ip()));

                    // 处理资金变更日志-----------------E

                    if (check_arr($rs)) {
                        $mo->commit();
                        $result = array('code' => 1, 'msg' => 'Success!');
                        echo json_encode($result);
                        exit;
                    } else {
                        throw new \Think\Exception('转账失败,请重试01!');
                    }
                } catch (\Think\Exception $e) {
                    $mo->rollback();
                    $result = array('code' => 0, 'msg' => 'Failed,error code:101!');
                    echo json_encode($result);
                    exit;
                }

            }
        } else {
            echo 'Yo!';
        }
    }
    function paylog($text)
    {
        $time = date('Y-m-d H:i:s',time());
        $texts = $time. ' ' . $text . "\n";
        $date = date('Y-m-d',time());
        $isSuccess = file_put_contents('./Log/Home/astc_collect/'.$date.'trade.log', $texts, FILE_APPEND);
        if($isSuccess === false){
            $this->paylog($text);
        }
        // var_dump($text);
    }
    /**
     * 发送get请求
     * @param string $url
     * @return mixed
     */
    public function doGet($url)
    {
        //初始化
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        // 执行后不直接打印出来
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 不从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);

        return $output;
    }

	/*ASTC*/
    public function astconlinea88b77c11d0a9d($height = 1){
        set_time_limit(0);
        ignore_user_abort();

        $coin = 'astc';

        // 查找已经存在最大区块高度
        if($height == 1){
            $height = M()->table('tw_myzr')->max('block_height');
        }
        $height = ++$height;

        //根据区块高度接口获取区块信息
        $url = 'brapi.astc.ink/api/V1/HeightChain?chain='.$height;
        $result = $this -> doGet($url);
        $result = json_decode($result, true);

        $text = '<<<<<<<<<<0查询的区块信息'.json_encode($result);
        $this->paylog($text);

        if($result['code'] == 200){
            if(empty($result['message'])){
                $text = "1查询高度：".$height."；message返回空，查询失败！";
                $this->paylog($text);
            }
            //区块中是否存在交易
            if($result['message']['tradeNum'] > 0){
                //存在交易时，根据区块高度获取详细交易信息
                $tradeUrl = 'brapi.astc.ink/api/V1/Hashtrades?height='.$result['message']['block_heigh'];
                $info = $this -> doGet($tradeUrl);
                $info = json_decode($info, true);

                $text = "2接口交易信息：".json_encode($info);
                var_dump($text);
                $this->paylog($text);
                var_dump($info['code']);
                var_dump($info['message']);
                if($info['code'] == 200 && !empty($info['message'])){
                    echo 111;
                    foreach ($info['message'] as $v2){
                        //查找钱包地址对应的账户
                        $user_addr = M()->table('tw_user_coin')->where(array($coin . 'b' => $v2['get_addr']))->find();
echo 000;
                        //钱包地址是否属于交易所
                        if($user_addr){
                            echo 1111;
                            $mo = M();
                            $mo->startTrans();
                            $rs = array();
                            try{
                                //更新用户资产信息
                                $trade_time = strtotime($v2['trade_time']);
                                $rs[] = $mo->table('tw_myzr')->add(array('userid' => $user_addr['userid'], 'username' => $v2['get_addr'], 'coinname' => $coin, 'fee' => $v2['trade_fee'], 'txid' => $v2['trade_hash'], 'num' => $v2['output_money'], 'mum' => $v2['output_money'], 'addtime' => $trade_time,'endtime' => time(), 'status' => 1,'block_height' => $v2['block_heigh']));
                                $sql1 = $mo->table('tw_myzr')->getLastSql();
                                $rs[] = $mo->table('tw_user_coin')->where(array($coin . 'b' => $v2['get_addr']))->setInc($coin, $v2['output_money']);//写入用户余额
                                $sql2 = $mo->table('tw_user_coin')->getLastSql();
                                $sql = 'sql1:'.$sql1.'sql2:'.$sql2;
                                if (check_arr($rs)) {
                                    $text = "3更新用户astc资产信息：".json_encode($rs).'更新SQL'.$sql;
                                    $this->paylog($text);

                                    // 钱包服务器信息
                                    $host = C('trade_url');
                                    $AsClient = Astccommon(C('url'));
                                    // astc中心账号
                                    $getAddr =  'ax06dea7bac1285776586cf1ae9f45c9e3';
                                    // 转入金额
                                    $outputMoney = $v2['output_money'];
                                    // 手续费
                                    $tradeFee = '0.00';
                                    // 操作时间
                                    $timestamp = time();
                                    // 用户钱包地址
                                    $astc_account = $v2['get_addr'];
                                    // 拼接签名字符串（A-z）,生成签名
                                    $str = "&getAddr=$getAddr&outputMoney=$outputMoney&payAddr=$astc_account&timestamp=$timestamp&tradeFee=$tradeFee&AccessKey=mochain_access_blockchain";
                                    $sign = strtoupper(md5($str));
                                    $arr = [
                                        'payAddr'=>$astc_account,
                                        'getAddr' =>$getAddr,
                                        'outputMoney'=>$outputMoney,
                                        'tradeFee'=> $tradeFee,
                                        'timestamp'=>$timestamp,
                                        'sign'=>$sign
                                    ];
                                    // 个人资产汇总到总账地址
                                    $send_status = $AsClient->astc_createTrade($host,$arr);

                                    if($send_status['code'] != 200 ){
                                        $mo->rollback();
                                        $text = "4中心账号转出：".$send_status['code']."汇总操作信息：".json_encode($arr);
                                        $this->paylog($text);
                                    }else{

                                        $text = "4Astc转入中心账号信息：".json_encode($send_status);
                                        $text .= $str;
                                        echo $text;

                                        $text = "5Astc成功汇入中心账号返回信息：".json_encode($send_status,JSON_UNESCAPED_UNICODE);
                                        $text .= '用户成功更新astc资产SQL'.$sql;

                                        $this->paylog($text);
                                        // 开启充值赠送  充值成功赠送Hrc糖果
                                       /* $Config = M('Config')->where(array('id' => 1))->find();
                                        if($Config['up_give_type']){
                                            // 多充多送
                                            if($outputMoney >= $Config['give_money']){
                                                $user_give['hrc'] =  $user_addr['hrc'] + $Config['astc_give_num'];
                                                $result[] = M()->table('tw_user_coin')->where(array('userid' => $user_addr['userid']))->save($user_give);
                                                if (!check_arr($rs)){
                                                    $text = "6用户id：".$user_addr['userid']."；Astc充值金额".json_encode($outputMoney)."；Astc充值赠送Hrc数量".json_encode($user_give['hrc']);
                                                    $this->paylog($text);
                                                    $mo->rollback();
                                                    echo '充值Astc赠送hrc糖果失败' . "\n";
                                                }
                                            }
                                        }*/
                                        $mo->commit();
                                    }

                                } else {
                                    $mo->rollback();
                                    $text = "6更新交易所用户资产失败SQL：".$sql;
                                    $this->paylog($text);
                                    throw new \Think\Exception('数据写入失败！');
                                }
                            }catch(\Think\Exception $e){
                                $error = '7捕获异常'.json_encode($e,JSON_UNESCAPED_UNICODE);
                                $this->paylog($error);
                                $mo->rollback();
                                $this->error('数据写入失败！');
                            }
                        }else{
                            M('myzr')->where('id=1')->setField('block_height',$v2['block_heigh']);
                            $log_sql = M('myzr')->getLastSql();
                            $logText = '8捕获异常'.$log_sql;
                            $logText .= '>>>>>>>>>>';
                            $this->paylog($logText);
                            sleep(1);
                            $this->astconlinea88b77c11d0a9d($v2['block_heigh']);
                        }
                    }
                }else{
                    $a = M('myzr')->where('id=1')->setField('block_height',$result['message']['block_heigh']);
                    $text = "2.1接口交易信息：".json_encode($info);
                    $text .= '>>>>>>>>>>';
                    $this->paylog($text);
                    echo '返回为空,查询失败！';
                    $this->astconlinea88b77c11d0a9d($result['message']['block_heigh']);
                }
            }else{
                $a = M('myzr')->where('id=1')->setField('block_height',$result['message']['block_heigh']);
                $text = '9'.$result['message']['block_heigh'].'区块交易订单量为空';
                $text .= '>>>>>>>>>>';
                $this->paylog($text);
                echo '返回为空,查询失败！';
                sleep(1);
                $this->astconlinea88b77c11d0a9d($result['message']['block_heigh']);
            }
        }else{
            $text = '<<<<<<返回系统错误'.json_encode($result).'>>>>>>>';
            $this->paylog($text);
        }
    }

    /**
     * 对个别异常区块高的交易进行同步（一定要小心始用、一定要小心始用、一定要小心始用、一定要小心始用，区块高度大于myzr表中高度时会影响正常astc同步，）
     * @param $height 区块高度
     * @return bool
     */
    public function  astcOnlyOneTurnInaldsfjlasjdf223r42rdf($height){
        if(!$height){
            echo 'errror 1';
            return false;
        }
        //查询区块高度信息
        $url = 'brapi.astc.ink/api/V1/HeightChain?chain='.$height;
        $result = $this -> doGet($url);
        $result = json_decode($result, true);

        //存在区块信息时查询详细交易信息
        if($result['message']['tradeNum'] > 0) {
            $url2 = 'brapi.astc.ink/api/V1/Hashtrades?height=' . $result['message']['block_heigh'];
            $info = $this->doGet($url2);
            $info = json_decode($info, true);
        }else{
            echo 'error 2';
            return false;
        }

        if($info['code'] == 200 && $info['message'] != '') {
            foreach ($info['message'] as $value) {

                //定个小目标
                $flag = true;

                $M = M();
                $M->startTrans();

                //查找钱包地址对应的账户
                $user_addr = $M->table('tw_user_coin')->where(array('astcb' => $value['get_addr']))->find();
                if($user_addr){
                    //记录转入
                    $turnLog = $M->table('tw_myzr')->add(array('userid' => $user_addr['userid'], 'username' => $value['get_addr'], 'coinname' => 'astc', 'fee' => $value['trade_fee'], 'txid' => $value['trade_hash'], 'num' => $value['output_money'], 'mum' => $value['output_money'], 'addtime' => strtotime($value['trade_time']),'endtime' => time(), 'status' => 1,'block_height' => $value['block_heigh']));
                    //添加用户资产
                    $decUserMoney = $M->table('tw_user_coin')->where(array('astcb' => $value['get_addr']))->setInc('astc', $value['output_money']);
                    if(!$turnLog || !$decUserMoney){
                        $M->rollback();
                        $flag = false;
                    }
                }else{
                    $flag = false;
                    echo 'error 3';
                }

                //变更资金操作
                if($flag){
                    $sendData = $this->turnOutsdafasdfasdf($value['get_addr'],'ax06dea7bac1285776586cf1ae9f45c9e3',$value['output_money']);
                    if($sendData['code'] == 200 ){
                        $M->commit();
                        echo 'Success';
                    }else{
                        $M->rollback();
                        echo 'error 4';
                    }
                }else{
                    $M->rollback();
                }
            }
        }
    }

    /**
     * 从一个地址向另一个地址转入astc
     * @param $turnOut 转出地址（付款方地址）
     * @param $turnIn  转入地址（收款方地址）
     * @param $amount  变动资产
     * @return bool|mixed
     */
    public function turnOutsdafasdfasdf($turnOut = '',$turnIn = '',$amount = 0){

        //不用时请关闭，用时请慎用
//        return false;
        $host = C('trade_url');
        $AsClient = Astccommon(C('url'));

        // 转入地址（收款方地址）
        $getAddr = $turnIn;
        $getAddr = 'ax71f799aad6b531fc18af4a3ebf9e3dc8';
        $getAddr = 'ax06dea7bac1285776586cf1ae9f45c9e3';
        //中心账号
//        $getAddr =  'ax06dea7bac1285776586cf1ae9f45c9e3';
        //转出地址（付款方地址）
        $astc_account = $turnOut;
        $astc_account = 'ax71f799aad6b531fc18af4a3ebf9e3dc8';
        //变动资产
        $outputMoney = $amount;
        $outputMoney = 30;
        //手续
        $tradeFee = '0.00';
        //请求时间
        $timestamp = time();

        //拼接签名字符（A-z）并生成签名
        $str = "&getAddr=$getAddr&outputMoney=$outputMoney&payAddr=$astc_account&timestamp=$timestamp&tradeFee=$tradeFee&AccessKey=mochain_access_blockchain";
        $sign = strtoupper(md5($str));
        $arr = [
            'payAddr'=>$astc_account,
            'getAddr' =>$getAddr,
            'outputMoney'=>$outputMoney,
            'tradeFee'=> $tradeFee,
            'timestamp'=>$timestamp,
            'sign'=>$sign
        ];
//echo 222;exit;
        //请求操作
        $send_status = $AsClient->astc_createTrade($host,$arr);
        var_dump($send_status);
        return $send_status;
    }

    public function  astcTurnOutInAll(){

       /* $text = "2交易所转入成功：".json_encode($rs);
        $this->paylog($text);
        echo 'Astc转入成功！' . "\n";*/
        $AsClient = Astccommon(C('url'));
        $astc_account = 'ax06dea7bac1285776586cf1ae9f45c9e3';// 用户账号
        $result = $AsClient -> astc_getBalance(C('url'), $astc_account); // 查询转出账户余额
        var_dump($result);

        echo "<br>"; echo "<br>";
        $host = C('trade_url');
//        $getAddr =  'ax06dea7bac1285776586cf1ae9f45c9e3'; // astc中心账号
        $getAddr =  'ax2e997513ec4ee538e197306220ba2beb'; // 用户账号
        $outputMoney = '3'; // 转入金额
        $tradeFee = '0.00';
        $timestamp = time();
//        $astc_account = 'ax2e997513ec4ee538e197306220ba2beb';// 用户账号
        $astc_account = 'ax06dea7bac1285776586cf1ae9f45c9e3';// astc中心账号

        $str = "&getAddr=$getAddr&outputMoney=$outputMoney&payAddr=$astc_account&timestamp=$timestamp&tradeFee=$tradeFee&AccessKey=mochain_access_blockchain";
        $sign = strtoupper(md5($str)); //签名
        $arr = [
            'payAddr'=>$astc_account,
            'getAddr' =>$getAddr,
            'outputMoney'=>$outputMoney,
            'tradeFee'=> $tradeFee,
            'timestamp'=>$timestamp,
            'sign'=>$sign
        ];

        $send_status = $AsClient->astc_createTrade($host,$arr); //转账到中心账号
        var_dump($send_status);

        if($send_status['code'] != 200 ){
            echo 'Astc转入中心账号失败' . "\n";
            $text = "3中心账号转出：".$send_status['code']."账号信息：".json_decode($arr, true);
            $this->paylog($text);
        }
       // echo 'Astc转入中心账号成功！' . "\n";
    }
    function usdtlog($text)
    {
        $text = addtime(time()) . ' ' . $text . "\n";
        file_put_contents('./usdtTrade.log', $text, FILE_APPEND);
    }
    /** USDT 转入用户钱包-》交易所钱包-》中心账号 **/
    public function usdtTrade()
    {
        header("Content-type: text/html; charset=utf-8");
        $coin = 'usdt';
        $dj_username = C('coin')[$coin]['dj_yh'];
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
        $candh = C('coin')[$coin]['change'];
        $cancoin = C('coin')[$coin]['changecoin'];

        if ($candh == 1) {
            $setcoin = $cancoin;
            $rate = C('coin')[$coin]['huilv'];
        } else {
            $setcoin = $coin;
            $rate = 1;
        }

        //$center_account = "1EcYPWQ5F3hjeiTege9ENraee226Dxvz2N";  //客户中心账号
        $center_account = "1FmexStVZSKdtfJcTknS7B1XmCnVGnpsZ6"; //交易所测试中心账号


        $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
        $json = $CoinClient->getinfo();
        if (!isset($json['version']) || !$json['version']) {
            echo '版本错误!' . "\n";
            //continue;
        }
        echo 'USDT start,connect ' . (empty($CoinClient) ? 'fail' : 'ok') . ' :' . "<br>";

        // 查询区块高度
        $omnilist = $CoinClient->omni_listtransactions('*', 1000, 0);
        foreach ($omnilist as $v) {
//            var_dump($v);

            if($v['referenceaddress'] != $center_account){
                // 转入成功记录是否写入表
                if (M('myzr')->where(array('txid' => $v['txid'], 'status' => 1, 'username' => $v['referenceaddress']))->find()) {
                    echo 'TXID:' . $v['txid'] . '转入成功的记录存在.' . "<br>";
//                    continue;
                }

                // 交易所是否有对应的钱包地址
                if (!($userid = M('user_coin')->where(array('usdtb' => $v['referenceaddress']))->find())) {
                    echo $v['referenceaddress'].'系统未找到对应账户' . "<br>";
                    continue;
                } else {
                    $user = M('user')->where(array('id' => $userid['userid']))->find();
                }

                // 确认次数
                if ($v['confirmations'] < 3) {
                    if ($res = M('myzr')->where(array('txid' => $v['txid'], 'username' => $v['referenceaddress']))->find()) {
                        // 根据 确认次数改变记录的状态
                        M('myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => intval($v['confirmations'] - 3)));
                    } else {
                        M('myzr')->add(array('userid' => $user['id'], 'username' => $v['referenceaddress'], 'coinname' => $coin, 'fee' => $v['fee'], 'txid' => $v['txid'], 'num' => $v['amount'], 'mum' => $v['amount'], 'addtime' => time(), 'status' => intval($v['confirmations'] - 3)));
                    }
                    continue;
                } else {
                    echo '确认次数达到3次,完成.' . "<br>";
                }

                // dump($user);
                $old_coins = M('user_coin')->where(array('userid' => $user['id']))->find();
                try {
                    $mo = M();
                    $mo->startTrans();

                    //$user_zj_coin = $mo->table('tw_user')->where(array('id' => $user['id']))->find();

                    $rs = array();
                    $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $user['id']))->setInc($setcoin, ($v['amount'] / $rate));//根据比例增加币余额

                    if ($res = $mo->table('tw_myzr')->where(array('txid' => $v['txid']))->find()) {
                        echo '设置转入记录status为1,完成!';
                        $rs[] = $mo->table('tw_myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => 1));
                        $log = $v['referenceaddress'].'转入记录写入表成功，txid是：'.$v['txid'];
                        $this->usdtTrade($log);

                    } else {
                        echo '转入记录未找到,添加新的记录.' . "<br>";
                        $rs[] = $mo->table('tw_myzr')->add(array('userid' => $user['id'], 'username' => $v['referenceaddress'], 'coinname' => $coin, 'fee' => $v['fee'], 'txid' => $v['txid'], 'num' => $v['amount'], 'mum' => $v['amount'], 'addtime' => time(), 'status' => 1));
                        $log = $v['referenceaddress'].'转入记录写入表成功，数量：'.$v['amount'];
                        $this->usdtTrade($log);
                        // 处理资金变更日志-----------------E  // optype=30 一代注册赠送类型 'cointype' => 4资金类型-usdt 'plusminus' => 1增加类型
                        $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user['username'], 'adminname' => $v['referenceaddress'], 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $v['amount'], 'optype' => 7, 'cointype' => 4, 'old_amount' => $old_coins[$setcoin], 'new_amount' => $v['amount']+$old_coins[$setcoin], 'userid' => $user['id'], 'adminid' => session('admin_id'),'addip'=>'钱包地址'));

                    }

                    if (check_arr($rs)) {
                        echo $v['referenceaddress'] . ' 交易所转入完成,USDT:' . $v['amount'];
                        echo '确认完成' . "<br>";

                        // 扣除手续费
                        $mum = $v['amount'] - $v['fee']*$v['amount'];
                        echo '转入数量：'.$v['amount'].';手续费：'. $v['fee'].'；扣除手续费后：'.$mum. "<br>";

                        // 查询交易所钱包地址余额
                        $balance= $CoinClient->omni_getbalance($v['referenceaddress'],31);
                        if(!($balance['balance'] >= $mum)){
                            echo '交易所钱包账户余额不足'."<br>";
                        }else{
                            echo 2222222222222;
                            // 转入中心账号
//                            $sendrs = $CoinClient->omni_funded_send('17ze3wTdFimZTaPV9Twkj23f2zi8Y5Jz2n',(string)$center_account, 31,(string)$mum,$center_account);
                            // $sendrs = $CoinClient->omni_send($v['referenceaddress'],$center_account, 31,(string)$mum);
                            if($v['referenceaddress'] != $center_account){
                                $sendrs = $this->getUsdt($v['referenceaddress'], (string)$center_account, (string)$mum);
                            }else{
                                echo $v['referenceaddress'].'记录已存在' . "<br>";
                                continue;
                            }
                        }

                        if($sendrs){
                            // 处理资金变更日志-----------------E  // optype=30 一代注册赠送类型 'cointype' => 4资金类型-usdt 'plusminus' => 1增加类型
                            $mo->table('tw_finance_log')->add(array('username' => $user['username'], 'adminname' => $v['referenceaddress'], 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $v['amount'], 'optype' => 33, 'cointype' => 4, 'old_amount' => $balance['balance'], 'new_amount' => $balance['balance']+$v['amount'], 'userid' => $user['id'], 'adminid' => session('admin_id'),'addip'=>'钱包地址'));
                            echo $v['referenceaddress'] . ' 交易所转入USDT中心账号完成,USDT数量:' . $mum;
                            $mo->commit();

                        }else{
                            echo $v['referenceaddress'] . ' 交易所转入USDT中心账号失败,USDT数量:' . $mum;
                            $mo->rollback();
                        }

                    } else {
                        throw new \Think\Exception('receive fail');
                    }
                } catch (\Think\Exception $e) {
                    echo $v['amount'] . 'receive fail ' . $coin . ' ' . $v['amount'];
                    $mo->execute('rollback');
                    echo 'rollback ok' . "<br>";
                }
            }else{
                // 用户转入地址也是中心账号 - 拒绝

/*                try{
                    $mo = M();
                    $mo->startTrans();
                    // 是否是已成功写入表
//                    if (M('myzr')->where(array('txid' => $v['txid'], 'status' => 1, 'username' => $v['referenceaddress']))->find()) {
//                        echo 'TXID:' . $v['txid'] . '转入成功的记录存在.' . "<br>";
//                        continue;
//                    }
                    // 是否能找到对应钱包地址
                    if (!($userid = M('user_coin')->where(array('usdtb' => $v['referenceaddress']))->find())) {
                        echo '系统未找到对应账户' . "<br>";
                        continue;
                    } else {
                        $user = M('user')->where(array('id' => $userid['userid']))->find();
                    }
                    // 确认次数是否大于3
                    if ($v['confirmations'] < 3) {
                        if ($res = M('myzr')->where(array('txid' => $v['txid'], 'username' => $v['referenceaddress']))->find()) {
                            M('myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => intval($v['confirmations'] - 3)));
                        } else {
                            M('myzr')->add(array('userid' => $user['id'], 'username' => $v['referenceaddress'], 'coinname' => $coin, 'fee' => $v['fee'], 'txid' => $v['txid'], 'num' => $v['amount'], 'mum' => $v['amount'], 'addtime' => time(), 'status' => intval($v['confirmations'] - 3)));
                        }
                        continue;
                    } else {
                        echo '确认次数达到3次,完成.' . "<br>";
                    }

                    $old_coins = M('user_coin')->where(array('userid' => $user['id']))->find();

                    // 扣除手续费
                    $mum = $v['amount'] - $v['fee']*$v['amount'];
                    echo '手续费：'. $v['fee'].'；扣除手续费后：'.$mum. "<br>";

                    $balance= $CoinClient->omni_getbalance($v['referenceaddress'],31);
                    if(!($balance['balance'] >= $mum)){
                        echo '交易所钱包账户余额不足'."<br>";
                    }else{
                        echo 5555555555;
                        // 交易所账号转入中心账号
//                        $sendrs = $CoinClient->omni_funded_send($v['referenceaddress'],(string)$center_account, 31,(string)$mum,$center_account);
                        // $sendrs = $CoinClient->omni_send($v['referenceaddress'],$center_account, 31,(string)$mum);
                        //var_dump($sendrs);
                        $sendrs = $this->getUsdt($v['referenceaddress'], (string)$center_account, (string)$mum);
                    }
                    if($sendrs){
                        // 处理转入表变更
                        $rs = array();
                        $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $user['id']))->setInc($setcoin, ($v['amount'] / $rate));//根据比例增加币余额

                        if ($res = $mo->table('tw_myzr')->where(array('txid' => $v['txid']))->find()) {
                            echo '设置转入记录status为1,完成!';
                            $rs[] = $mo->table('tw_myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => 1));
                        } else {
                            echo '转入记录未找到,添加新的记录.' . "<br>";
                            $rs[] = $mo->table('tw_myzr')->add(array('userid' => $user['id'], 'username' => $v['referenceaddress'], 'coinname' => $coin, 'fee' => $v['fee'], 'txid' => $v['txid'], 'num' => $v['amount'], 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                            $log = $v['referenceaddress'].'转入记录写入表成功，数量：'.$v['amount'];
                            $this->usdtTrade($log);
                            // 处理资金变更日志-----------------E  // optype=30 一代注册赠送类型 'cointype' => 4资金类型-usdt 'plusminus' => 1增加类型
                           $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user['username'], 'adminname' => $v['referenceaddress'], 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $v['amount'], 'optype' => 7, 'cointype' => 4, 'old_amount' => $old_coins[$setcoin], 'new_amount' => $v['amount']+$old_coins[$setcoin], 'userid' => $user['id'], 'adminid' => session('admin_id'),'addip'=>'钱包地址'));
                            echo $v['referenceaddress'] . ' 交易所转入USDT完成,USDT数量:' . $mum;
                        }

                        // 处理资金变更日志-----------------E  // optype=30 一代注册赠送类型 'cointype' => 4资金类型-usdt 'plusminus' => 1增加类型
//                        $mo->table('tw_finance_log')->add(array('username' => $user['username'], 'adminname' => $v['referenceaddress'], 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $v['amount'], 'optype' => 33, 'cointype' => 4, 'old_amount' => $balance['balance'], 'new_amount' => $balance['balance']+$v['amount'], 'userid' => $user['id'], 'adminid' => session('admin_id'),'addip'=>'钱包地址'));
//                        echo $v['referenceaddress'] . ' 交易所转入USDT中心账号完成,USDT数量:' . $mum;

                        $mo->commit();

                    }else{
                        echo $v['referenceaddress'] . ' 交易所转入USDT失败,USDT数量:' . $mum;
                        $mo->rollback();
                    }
                }catch (\Think\Exception $e){
                    echo $v['referenceaddress'] . 'receive fail ' . $coin . ' ' . $v['amount'];
                    $mo->rollback();
                }*/
            }
        }
    }

    private function  getUsdt($getAddr , $center_account, $mum)
    {
        $coin = 'usdt';
        $dj_username = C('coin')[$coin]['dj_yh'];
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
        $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
        $json = $CoinClient->getinfo();
        echo '<pre>';
        var_dump($json);
        $balance= $CoinClient->omni_getbalance('1EcYPWQ5F3hjeiTege9ENraee226Dxvz2N',31);
        echo '<pre>';
        var_dump($balance);
        echo '<pre>';
        var_dump($CoinClient);

        $sendrs = $CoinClient->omni_funded_send($getAddr,(string)$center_account, 31,(string)$mum,'1EcYPWQ5F3hjeiTege9ENraee226Dxvz2N');
//        $sendrs = $CoinClient->omni_send($getAddr,$center_account, 31,(string)$mum);

        return $sendrs;
    }

    function test()
    {
        // 高磊交易所地址  17ze3wTdFimZTaPV9Twkj23f2zi8Y5Jz2n
        // 交易所中心账号  1FmexStVZSKdtfJcTknS7B1XmCnVGnpsZ6
       $res= $this->getUsdt('1FmexStVZSKdtfJcTknS7B1XmCnVGnpsZ6','17ze3wTdFimZTaPV9Twkj23f2zi8Y5Jz2n','0.02' );
        echo '<pre>';
        var_dump($res);
    }

	/** ETH入账 **/
    function ethonlinea88b77c11d0a9d($coin = 'eth')
    {
        set_time_limit(0);
        ignore_user_abort();
        $dj_username = C('coin')[$coin]['dj_yh'];
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
        $pay = EthCommon($dj_address, $dj_port);
        $accounts = $pay->personal_listAccounts();//获取钱包地址列表
        foreach ($accounts as $k => $v) {
            if (strtolower($v) != strtolower($dj_username)) {
                $getdz = M()->table('tw_user_coin')->where(array($coin . 'b' => $v))->find();//查找钱包地址对应的账户
                if ($getdz) {
                    $user = M()->table('tw_user')->where(array('id' => $getdz['userid']))->getField('username');
                    $url = 'http://api.etherscan.io/api?module=account&action=txlist&address=' . $v . '&startblock=5900000&endblock=99999999&sort=asc&apikey=ERXIYCNF6PP3ZNQAWICHJ6N5W7P212AHZI';
                    $fanhui = file_get_contents($url);
                    $fanhui = json_decode($fanhui, true);
                    if ($fanhui['message'] == 'OK') {
                        foreach ($fanhui['result'] as $v2) {
                            if ($v2['to'] == $v && $v2['txreceipt_status'] == 1) {
                                $rs1 = M()->table('tw_myzr')->where(array('txid' => $v2['hash']))->find();
                                if (!$rs1) {
                                    $amount = $v2['value'] / 1000000000000000000;
                                    $rs2 = M()->table('tw_myzr')->add(array('userid' => $getdz['userid'], 'username' => $v, 'coinname' => $coin, 'fee' => 0, 'txid' => $v2['hash'], 'num' => $amount, 'mum' => $amount, 'addtime' => time(), 'status' => 1));
                                    $rs1 = M()->table('tw_user_coin')->where(array($coin . 'b' => $v))->setInc($coin, $amount);//写入用户余额
                                } else {
                                    echo '交易哈希:' . $v2['hash'] . '的交易记录已存在!<br>';
                                }
                            }
                        }
                    } else {
                        echo '账户:' . $v . '交易记录未查询到!<br>';
                    }
                }
            }
        }
    }
	
	/** ETC入账 **/
    function etconlinea88b77c11d0a9d($coin = 'etc')
    {
        $dj_username = C('coin')[$coin]['dj_yh'];
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
        $candh = C('coin')[$coin]['change'];
        $cancoin = C('coin')[$coin]['changecoin'];

        if ($candh == 1) {
            $setcoin = $cancoin;
            $rate = C('coin')[$coin]['huilv'];
        } else {
            $setcoin = $coin;
            $rate = 1;
        }
		
        $pay = EthCommon($dj_address, $dj_port);
        $accounts = $pay->personal_listAccounts();//获取钱包地址列表
        foreach ($accounts as $k => $v) {
            if ($v != $dj_username) {
                $getdz = M()->table('tw_user_coin')->where(array($coin . 'b' => $v))->find();//查找钱包地址对应的账户
                // dump($v);
                if ($getdz) {
                    // $qbbalance=$pay->eth_getBalance($v);//查询钱包地址余额10进制
                    $user = M()->table('tw_user')->where(array('id' => $getdz['userid']))->getField('username');
                    // $url='https://etcchain.com/api/v1/getTransactionsByAddress?address='.$v;//通道1
                    $url = 'https://api.gastracker.io/v1/addr/' . $v . '/transactions';//通道2
                    $fanhui = file_get_contents($url);
                    $fanhui = json_decode($fanhui, true);
                    // dump($fanhui);
					
/*                    //通道1
                    if ($fanhui) {
                        foreach ($fanhui as $v2) {
                            if ($v2['to']==$v && $v2['confirmations']>=3) {
                                $rs1= M()->table('tw_myzr')->where(array('txid'=>$v2['hash']))->find();
                                if (!$rs1) {
                                    $amount=$v2['valueEther'];
                                    $rs2=M()->table('tw_myzr')->add(array('userid' =>$getdz['userid'], 'username' => $v, 'coinname' => $coin, 'fee' => 0, 'txid' =>$v2['hash'], 'num' => $amount, 'mum' => $amount, 'addtime' => time(), 'status' => 1));
                                     $rs1= M()->table('tw_user_coin')->where(array($coin.'b'=>$v))->setInc($setcoin,$amount/$rate);//写入用户余额
                                } else {
                                    echo '交易哈希:'.$v2['hash'].'的交易记录已存在!<br>';
                                }
                            }
                        }
                    } else {
                        echo '账户:'.$v.'交易记录未查询到!<br>';
                    }*/

                    //通道2
                    if (is_array($fanhui['items'])) {
                        foreach ($fanhui['items'] as $v2) {
                            // dump ($v3['to']);
                            $to = strtolower($v2['to']);
                            // dump($v==$to);
                            $amount = $v2['value']['ether'];
                            if ($v == $to && $v2['confirmations'] >= 3) {
                                $rs1 = M()->table('tw_myzr')->where(array('txid' => $v2['hash']))->find();
                                if (!$rs1) {
                                    $amount = $v2['value']['ether'];
                                    $rs2 = M()->table('tw_myzr')->add(array('userid' => $getdz['userid'], 'username' => $v, 'coinname' => $coin, 'fee' => 0, 'txid' => $v2['hash'], 'num' => $amount, 'mum' => $amount, 'addtime' => time(), 'status' => 1));
                                    $rs1 = M()->table('tw_user_coin')->where(array($coin . 'b' => $v))->setInc($setcoin, $amount / $rate);//写入用户余额
                                } else {
                                    echo '交易哈希:' . $v2['hash'] . '的交易记录已存在!<br>';
                                }
                            }
                        }
                    } else {
                        echo '账户:' . $v . '交易记录未查询到!<br>';
                    }
                    //通道2结束
                }
            }
        }
    }

    function etconline($coin = 'etc')
    {
        // $url='https://etcchain.com/api/v1/getTransactionsByAddress?address=0x6b83f808fce08f51adb2e9e35a21a601e702785f';
        $url = 'https://api.gastracker.io/v1/addr/0x22df416f66f5bd61cc43f0e192ba36d066279992/transactions';
        $fanhui = file_get_contents($url);
        $fanhui = json_decode($fanhui, true);
        // dump($fanhui['items']);die;

        $dj_username = C('coin')[$coin]['dj_yh'];
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
        $candh = C('coin')[$coin]['change'];
        $cancoin = C('coin')[$coin]['changecoin'];
		
        if ($candh == 1) {
            $setcoin = $cancoin;
            $rate = C('coin')[$coin]['huilv'];
        } else {
            $setcoin = $coin;
            $rate = 1;
        }
		
        $pay = EthCommon($dj_address, $dj_port);
        $accounts = $pay->personal_listAccounts();//获取钱包地址列表
        foreach ($accounts as $k => $v) {
            if ($v != $dj_username) {
                $getdz = M()->table('tw_user_coin')->where(array($coin . 'b' => $v))->find();//查找钱包地址对应的账户
                // dump($dj_username);
                if ($getdz) {
                    // $qbbalance=$pay->eth_getBalance($v);//查询钱包地址余额10进制
                    $user = M()->table('tw_user')->where(array('id' => $getdz['userid']))->getField('username');
                    $url = 'https://etcchain.com/api/v1/getTransactionsByAddress?address=' . $v;
                    // $url='https://etcchain.com/api/v1/getTransactionsByAddress?address=0x6b83f808fce08f51adb2e9e35a21a601e702785f';
                    $fanhui = file_get_contents($url);
                    $fanhui = json_decode($fanhui, true);
                    // dump($fanhui);
                    if ($fanhui) {
                        foreach ($fanhui as $v2) {
                            if ($v2['to'] == $v && $v2['confirmations'] >= 3) {
                                $rs1 = M()->table('tw_myzr')->where(array('txid' => $v2['hash']))->find();
                                if (!$rs1) {
                                    $amount = $v2['valueEther'];
                                    $rs2 = M()->table('tw_myzr')->add(array('userid' => $getdz['userid'], 'username' => $v, 'coinname' => $coin, 'fee' => 0, 'txid' => $v2['hash'], 'num' => $amount, 'mum' => $amount, 'addtime' => time(), 'status' => 1));
                                    $rs1 = M()->table('tw_user_coin')->where(array($coin . 'b' => $v))->setInc($setcoin, $amount / $rate);//写入用户余额
                                } else {
                                    echo '交易哈希:' . $v2['hash'] . '的交易记录已存在!<br>';
                                }
                            }
                        }
                    } else {
                        echo '账户:' . $v . '交易记录未查询到!<br>';
                    }
                }
            }
        }
    }

    function artsconlinea88b77c11d0a9d($coin = 'arts')
    {
        set_time_limit(0);
        $dj_username = C('coin')[$coin]['dj_yh'];
        $candh = C('coin')[$coin]['change'];
        $cancoin = C('coin')[$coin]['changecoin'];
		
        if ($candh == 1) {
            $setcoin = $cancoin;
            $rate = C('coin')[$coin]['huilv'];
        } else {
            $setcoin = $coin;
            $rate = 1;
        }
		
        $map['artsb'] = array('like', '0x%');
        $accounts = M()->table('tw_user_coin')->where($map)->field('artsb,userid')->select();
        $url = 'http://api.etherscan.io/api?module=account&action=txlist&address=0x228c317d52abd2e389643847c2d859f59680aa1a&startblock=527000&endblock=99999999&sort=asc&apikey=ERXIYCNF6PP3ZNQAWICHJ6N5W7P212AHZI';
        $fanhui = file_get_contents($url);
        $fanhui = json_decode($fanhui, true);
        foreach ($accounts as $k => $v) {
            if ($v['artsb'] != $dj_username) {
                $user = M()->table('tw_user')->where(array('id' => $v['userid']))->getField('username,invit_1');
                if ($fanhui['message'] == 'OK') {
                    foreach ($fanhui['result'] as $v2) {
                        if (strlen($v2['input']) == 138) {//input为区块交易data值
                            $datalist = explode('0x', $v2['input'])[1];
                            $account = substr($datalist, 32, 40);//获取data中的账户
                            $account = '0x' . $account;
                            // dump($account);
                            $amount = substr($datalist, -20);//获取data中的转账数额16进制值
                            $num = hexdec($amount) / 10000;//转化为10进制

                            if ($account == $v['artsb'] && $v2['txreceipt_status'] == 1) {
                                $rs1 = M()->table('tw_myzr')->where(array('txid' => $v2['hash']))->find();
                                if (!$rs1) {
                                    $rs2 = M()->table('tw_myzr')->add(array('userid' => $v['userid'], 'username' => $v['artsb'], 'coinname' => $coin, 'fee' => 0, 'txid' => $v2['hash'], 'num' => $num, 'mum' => $num, 'addtime' => time(), 'status' => 1));
                                    $rs1 = M()->table('tw_user_coin')->where(array($coin . 'b' => $v['artsb']))->setInc($setcoin, $num / $rate);//写入用户余额
                                    echo 'Hash:' . $v2['hash'] . '的交易记录已写入数据库!<br>';
                                } else {
                                    echo 'Hash:' . $v2['hash'] . '的交易记录已存在!<br>';
                                }
                            } else {
                                echo '交易状态:失败!<br>';
                            }
                        }
                    }
                } else {
                    echo '账户:' . $v . '交易记录未查询到!<br>';
                }

            }
        }
    }
	
	/** USDT 步骤 1 **/
    public function usdt()
    {
        header("Content-type: text/html; charset=utf-8");
        $coin = 'usdt';
        $dj_username = C('coin')[$coin]['dj_yh'];
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
        $candh = C('coin')[$coin]['change'];
        $cancoin = C('coin')[$coin]['changecoin'];
		
        if ($candh == 1) {
            $setcoin = $cancoin;
            $rate = C('coin')[$coin]['huilv'];
        } else {
            $setcoin = $coin;
            $rate = 1;
        }
		
        // dump($setcoin);
        $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
        $json = $CoinClient->getinfo();
        if (!isset($json['version']) || !$json['version']) {
            echo '版本错误!' . "\n";
            //continue;
        }
        echo 'USDT start,connect ' . (empty($CoinClient) ? 'fail' : 'ok') . ' :' . "\n";
        $omnilist = $CoinClient->omni_listtransactions('*', 1000, 0);

        foreach ($omnilist as $v) {
            if (M('myzr')->where(array('txid' => $v['txid'], 'status' => 1, 'username' => $v['referenceaddress']))->find()) {
                echo 'TXID:' . $v['txid'] . '转入成功的记录存在.' . "\n";
                continue;
            }

            if (!($userid = M('user_coin')->where(array('usdtb' => $v['referenceaddress']))->find())) {
                echo '系统未找到对应账户' . "\n";
                continue;
            } else {
                $user = M('user')->where(array('id' => $userid['userid']))->find();
            }
            if ($v['confirmations'] < 3) {
                if ($res = M('myzr')->where(array('txid' => $v['txid'], 'username' => $v['referenceaddress']))->find()) {
                    M('myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => intval($v['confirmations'] - 3)));
                } else {
                    M('myzr')->add(array('userid' => $user['id'], 'username' => $v['referenceaddress'], 'coinname' => $coin, 'fee' => $v['fee'], 'txid' => $v['txid'], 'num' => $v['amount'], 'mum' => $v['amount'], 'addtime' => time(), 'status' => intval($v['confirmations'] - 3)));
                }
                continue;
            } else {
                echo '确认次数达到3次,完成.' . "\n";
            }
            // dump($user);
            try {
                $mo = M();
                $mo->execute('set autocommit=0');
//                $mo->execute('lock tables  tw_user write , tw_myzr  write ,tw_user_coin write');

                $user_zj_coin = $mo->table('tw_user')->where(array('id' => $user['id']))->find();

                $rs = array();
                $rs[] = $mo->table('tw_user_coin')->where(array('userid' => $user['id']))->setInc($setcoin, ($v['amount'] / $rate));//根据比例增加币余额

                if ($res = $mo->table('tw_myzr')->where(array('txid' => $v['txid']))->find()) {
                    echo '设置转入记录status为1,完成!';
                    $rs[] = $mo->table('tw_myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => 1));
                } else {
                    echo '转入记录未找到,添加新的记录.' . "\n";
                    $rs[] = $mo->table('tw_myzr')->add(array('userid' => $user['id'], 'username' => $v['referenceaddress'], 'coinname' => $coin, 'fee' => $v['fee'], 'txid' => $v['txid'], 'num' => $v['amount'], 'mum' => $v['amount'], 'addtime' => time(), 'status' => 1));
                    // dump($mo);
                }

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    echo $v['amount'] . ' 转入完成,USDT:' . $v['amount'];
//                    $mo->execute('unlock tables');
                    echo '确认完成' . "\n";
                } else {
                    throw new \Think\Exception('receive fail');
                }
            } catch (\Think\Exception $e) {
                echo $v['amount'] . 'receive fail ' . $coin . ' ' . $v['amount'];
                // echo var_export($rs, true);
                $mo->execute('rollback');
//                $mo->execute('unlock tables');
                echo 'rollback ok' . "\n";
            }
        }
    }

	/** 查询区块高度进行补单（转入不到账使用） **/
    public function tokensonlinea88b77c11d0a9d($coin,$block = NULL)
	{
		set_time_limit(0);
		ignore_user_abort();

		//Token合约设置 FFF
		$coin_config = M('Coin')->where(array('name' => $coin))->find();
		$addr = $coin_config['dj_hydz']; //ERC20合约地址
		$wei = 1e18; //手续费
		
		if($coin=='wicc'){
			$addr = '0x4f878c0852722b0976a955d68b376e4cd4ae99e5';
			$wei = 1e8;
		}
		if($coin=='nuls'){
			$addr = '0xb91318f35bdb262e9423bc7c7c2a3a93dd93c92c';
		}
		if($coin=='zil'){
			$addr = '0x05f4a42e251f2d52b8ed15e9fedaacfcef1fad27';
			$wei = 1e12;
		}
		if($coin=='noc'){
			$addr ='0x2563c68650779d004a250be1d5cbe8b9b29177fd';
		}
		if($coin=='trx'){
			$addr = '0xf230b790e05390fc8295f4d3f60332c93bed42e2';
			$wei = 1e6;
		}
        if($coin=='astc'){
            $addr = 'ax85c10fff494f22c6207032786d043877';
            $wei = 1e6;
        }
/*		if($coin=='fff'){
			$addr = '0xe045e994f17c404691b238b9b154c0998fa28aef';
		}*/
		
		if(!$addr){
			echo 'ERC20合约地址不存在';
			die();
		}
		
		
		$dj_username = C('coin')[$coin]['dj_yh'];
		$map[$coin . 'b'] = array('like', '0x%');
		$accounts = M()->table('tw_user_coin')->where($map)->field($coin . 'b,userid')->select();
		//print_r($accounts);echo '<br>';print_r($map);die();
		
        $getblock = 'http://api.etherscan.io/api?module=proxy&action=eth_blockNumber&apikey=YourApiKeyToken';
        $blockn= file_get_contents($getblock);
        $blockn= json_decode($blockn, true);
        $blockn= explode('0x', $blockn['result'])[1];

        if ($block) {
            $lastblock = $block;
            $fromblock = $block;
        } else {
            $lastblock = hexdec($blockn);
            $fromblock = $lastblock - 80;
        }
		
		$url = 'http://api.etherscan.io/api?module=account&action=txlist&address='.$addr.'&startblock='.$fromblock.'&endblock='.$lastblock.'&sort=asc&apikey=ERXIYCNF6PP3ZNQAWICHJ6N5W7P212AHZI';
		$fanhui = file_get_contents($url);
		$fanhui = json_decode($fanhui, true);
		//echo $coin;
		echo 'start:'.$coin.'.<br>';
		//dump($dj_username);
		foreach ($accounts as $k => $v) {
			if ($v[$coin . 'b'] != $dj_username) {
				$user = M()->table('tw_user')->where(array('id' => $v['userid']))->getField('username');
				if ($fanhui['message'] == 'OK') {
					foreach ($fanhui['result'] as $v2) {
						if (strlen($v2['input']) == 138) {//input为区块交易data值
							$datalist = explode('0x', $v2['input'])[1];
							$account = substr($datalist, 32, 40);//获取data中的账户
							$account = '0x' . $account;
							$amount = substr($datalist, -26);//获取data中的转账数额16进制值
							$num = hexdec($amount) / $wei;//转化为10进制
							if ($account == $v[$coin . 'b']) {
								if ($v2['txreceipt_status'] == 1) {
									$rs1 = M()->table('tw_myzr')->where(array('txid' => $v2['hash']))->find();
									if (!$rs1) {
										$rs2 = M()->table('tw_myzr')->add(array('userid' => $v['userid'], 'username' => $v[$coin . 'b'], 'coinname' => $coin, 'fee' => 0, 'txid' => $v2['hash'], 'num' => $num, 'mum' => $num, 'addtime' => time(), 'status' => 1,'cover' => 1,'fromaddress'=>$v2['from']));
										$rs1 = M()->table('tw_user_coin')->where(array($coin . 'b' => $v[$coin . 'b']))->setInc($coin, $num);//写入用户余额
										echo 'Hash:' . $v2['hash'] . '的交易记录已写入数据库!<br>';
									} else {
										echo 'Hash:' . $v2['hash'] . '的交易记录已存在!<br>';
									}
								} else {
									echo '交易状态:失败!<br>';
								}
							}
						}
					}
				} else {
					echo '账户:' . $v[$coin . 'b'] . '交易记录未查询到!<br>';
				}

			}
		}
	}
	
	/** ETH钱包余额汇总至总钱包 **/
    function ethcovera99b88c77d66e55($coin = 'eth')
    {
        set_time_limit(0);
        ignore_user_abort();
		
		$coin_config = M('Coin')->where(array('name' => 'eth'))->find();
		$waddress = $coin_config['dj_yh'];
		if (!$waddress) {echo $coin.'无法汇总，钱包公钥地址设置';exit;}
		
        $mainbase = strtolower($waddress); //写死,汇总到钱包的公钥
        $eos = strtolower($waddress); //这是转出代币账户,保留余额
        $tip = strtolower($waddress); //这是转出代币账户,保留余额
		
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
        $pay = EthCommon($dj_address, $dj_port);
        $accounts = $pay->personal_listAccounts();//获取钱包地址列表
		//print_r($accounts);echo $dj_password;echo $mainbase;die(); //调试输出钱包地址
        $gasprice = $pay->eth_gasPrice();
		
        if (!$mainbase || !$dj_password) {echo '未设置主账户或密码!<br>';exit;}
        if (in_array($mainbase, $accounts)) {echo '账户存在<br>';} else {echo '账户不存在<br>';exit;}
		
        foreach ($accounts as $k => $v) {
            if (strtolower($v) != $mainbase or strtolower($v) != $eos or strtolower($v) != $tip) {
                $getdz = M()->table('tw_user_coin')->where(array($coin . 'b' => $v))->find();//查找钱包地址对应的账户
                if ($getdz) {
                    $qbbalance = $pay->eth_getBalancehex($v);//查询钱包地址余额,0x格式
                    $showmoney = $pay->fromWei($qbbalance);//查询钱包地址余额10进制
                    $realvalue = $showmoney - 0.00021;//查询钱包地址余额10进制
                    $user = M()->table('tw_user')->where(array('id' => $getdz['userid']))->getField('username');
                    if ($realvalue > 0) {
                        echo '账户' . $v . ' 隶属用户:' . $user . ',余额:' . $showmoney . 'ETH.实转金额:' . $realvalue . '<br>';
                        $sendtrance = $pay->eth_sendTransaction($v, $mainbase, $user, $realvalue);//发送账户所有余额到系统账户
						//dump($sendtrance);//调试
                        if ($sendtrance) {
                            echo '账户' . $v . '转账成功!<br>';
                            $tokendata['coin'] = $coin;
                            $tokendata['address'] = $v;
                            $tokendata['addtime'] = time();
                            $tokendata['txid'] = $sendtrance;
                            $tokendata['num'] = $realvalue;
                            M()->table('tw_ethto')->add($tokendata);//写入eth转出记录
                            $log = '账户' . $v . '存在' . $coin . "\n";
                            $log .= '转入' . $realvalue . ' ETH 至:' . $mainbase . "\n";
                            $log .= '交易HASH:' . $senders . "\n";
                        } else {
							echo '转入失败!<br>';
                            $log = '账户' . $v . '存在' . $coin . "\n";
                            $log .= '转入ETH失败.' . $sendtrance . "\n";
                        }
                        logeth($log);
                    }
                }
            }
        }
    }
	
	/** ETC钱包余额汇总至总钱包 **/
    function etccovera99b88c77d66e55($coin = 'etc')
    {
        set_time_limit(0);
        ignore_user_abort();
		
		$coin_config = M('Coin')->where(array('name' => 'etc'))->find();
		$waddress = $coin_config['dj_yh'];
		if (!$waddress) {echo $coin.'无法汇总，钱包公钥地址设置';exit;}
		
        $mainbase = strtolower($waddress); //写死,汇总到钱包的公钥
		
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
        $pay = EthCommon($dj_address, $dj_port);
        $accounts = $pay->personal_listAccounts();//获取钱包地址列表
        $gasprice = $pay->eth_gasPrice();
		
        if (!$mainbase || !$dj_password) {echo '未设置主账户或密码!<br>';exit;}
        if (in_array($mainbase, $accounts)) {echo '账户存在<br>';} else {echo '账户不存在<br>';exit;}

        foreach ($accounts as $k => $v) {
            if (strtolower($v) != $mainbase) {
                $getdz = M()->table('tw_user_coin')->where(array($coin . 'b' => $v))->find();//查找钱包地址对应的账户
                if ($getdz) {
                    $qbbalance = $pay->eth_getBalancehex($v);//查询钱包地址余额,0x格式
                    $showmoney = $pay->fromWei($qbbalance);//查询钱包地址余额10进制
                    $realvalue = $showmoney - 0.00007;//查询钱包地址余额10进制
                    $user = M()->table('tw_user')->where(array('id' => $getdz['userid']))->getField('username');
                    if ($realvalue > 0) {
                        echo '账户' . $v . ' 隶属用户:' . $user . ',余额:' . $showmoney . 'ETC.实转金额:' . $realvalue . '<br>';
                        $sendtrance = $pay->eth_sendTransaction($v, $mainbase, $user, $realvalue);//发送账户所有余额到系统账户
                        //dump($sendtrance);//调试
                        if ($sendtrance) {
                            echo '账户' . $v . '转账成功!<br>';
                            $log = '账户' . $v . '存在' . $coin . "\n";
                            $log .= '转入' . $realvalue . ' ETC 至:' . $mainbase . "\n";
                            $log .= '交易HASH:' . $senders . "\n";
                        } else {
                            $log = '账户' . $v . '存在' . $coin . "\n";
                            $log .= '转入ETC失败.' . $sendtrance . "\n";
                        }
                        logeth($log);
                    }
                }
            }
        }
    }
	
	/** ERC20代币钱包余额汇总至总钱包 **/
 	function tokencovera88b77c11d0a9d($coin)
    {
    	set_time_limit(0);
    	ignore_user_abort();
        $dj_username =  C('coin')[$coin]['dj_yh'];
        $dj_password =  C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
        $map['coinname'] = $coin;
        $map['cover'] = 1;
        $accounts = M()->table('tw_myzr')->where($map)->select();
        $CoinClient = EthCommon($dj_address, $dj_port);
		
		//Token合约设置 FFF
		$coin_config = M('Coin')->where(array('name' => $coin))->find();
		$addr = $coin_config['dj_hydz']; //ERC20合约地址
		$wei = 1e18; //手续费
		$methodid = '0xa9059cbb';
		
		if($coin=='wicc'){
			$addr = '0x4f878c0852722b0976a955d68b376e4cd4ae99e5';
			$wei = 1e8;
		}
		if($coin=='nuls'){
			$addr = '0xb91318f35bdb262e9423bc7c7c2a3a93dd93c92c';
		}
		if($coin=='zil'){
			$addr='0x05f4a42e251f2d52b8ed15e9fedaacfcef1fad27';
			$wei=1e12;
		}
		if($coin=='noc'){
			$addr = '0x2563c68650779d004a250be1d5cbe8b9b29177fd';
			$methodid = '0x79c65068';
		}
		if($coin=='trx'){
			$addr = '0xf230b790e05390fc8295f4d3f60332c93bed42e2';
			$wei = 1e6;
		}
/*		if($coin=='fff'){
			$addr='0xe045e994f17c404691b238b9b154c0998fa28aef';
		}*/
		
		if(!$addr){
			echo 'ERC20合约地址不存在';
			die();
		}
		
		echo $coin.':start!<br>';
		// dump($accounts);
		foreach ($accounts as $k => $v) {
		// dump($v);
		if (strtolower($v['username'])!=strtolower($dj_username)) {
		$url='https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress='.$addr.'&address='.$v['username'].'&tag=latest&apikey=ERXIYCNF6PP3ZNQAWICHJ6N5W7P212AHZI';
		//contractaddress=合约地址,address=持有代币的地址
		$fanhui = file_get_contents($url);
		$fanhui = json_decode($fanhui,true);
		if ($fanhui['message']=='OK') {
			$numb = $fanhui['result']/$wei;//18位小数
			
                if ($numb >0) {
                	$qbbalance=$CoinClient->eth_getBalancehex($v['username']);
					$showmoney=$CoinClient->fromWei($qbbalance);
					if ($showmoney<0.0004) {
						$sendeth=0.0004-$showmoney;
						$sended=$CoinClient->eth_sendTransaction($dj_username,$v['username'],$dj_password,$sendeth);
						if ($sended) {
							$flag=1;
						} else {
							$flag=0;
							echo '转入ETH失败';
						}
					}
					if ($showmoney>0.0004) {$flag=1;} else {$flag=0;}
					if ($flag) {
						echo $v['username'].'账户ETH余额为:'.$showmoney.','.$coin.'余额为:'.$numb;
						$user=M()->table('tw_user')->where(array('id'=>$v['userid']))->getField('username');
						$mum=bnumber($fanhui['result'],10,16);
						$amounthex=sprintf("%064s",$mum);
						$addr2=explode('0x',  $dj_username)[1];//接受地址
						$dataraw=$methodid.'000000000000000000000000'.$addr2.$amounthex;//拼接data
						$sendrs = $CoinClient->eth_sendTransactionraw($v['username'],$addr,$user,$dataraw);
						
						if (strpos($sendrs,'0x') === 0) {
							 $cover= M()->table('tw_myzr')->where(['id' => $v['id']])->setField('cover', 0);
							$log='账户'.$v['username'].'汇总代币'.$coin."\n";
							$log.='转入'.$mum.' 至:'.$dj_username."\n";
							$log.='交易HASH:'.$sendrs."\n";
							echo '交易HASH:'.$sendrs.'<br>';
						} else {
							$log='账户'.$v['username'].'代币'.$coin.'余额:'.$numb."\n";
							$log.='转出代币失败.'.$sendrs."\n";
							  echo '转出代币失败.'.$sendrs.'<br>';
						}
						  logeth($log);
						}
					} else {
						echo 'account:'.$v['username'].' dont have'.$coin."\n";
						continue;
					}
				} else {
					echo 'account:'.$v['username'].' cannot find in ethscan.'."\n";
					continue;
				}
			}
 		}
	}
    function givelog($text)
    {
        $text =date('Y-m-d H:i:s',time()) . ' ' . $text . "\n";
        file_put_contents('./give.log', $text, FILE_APPEND);
        // var_dump($text);
    }
	// astc用户每天20%比例赠送hrc
    public function giveHrc()
    {
        $Config = M('Config')->where(array('id' => 1))->find();
        $user_money = M()->table('tw_user_coin')->select();
        $mo = M();
        $mo->startTrans();

        // 开启赠送
        if($Config['astc_ratio_type'] > 0){
           foreach($user_money as $k => $v){
               if($v['astc']>0){
                   $user =  M()->table('tw_user')->where(array('id'=>$v['userid']))->select();
                   $num = round($Config['ratio']/100 * $v['astc'],8);
                   $gievMoney['hrc'] = $v['hrc'] + $num;
                   $result[] = M()->table('tw_user_coin')->where(array('id'=>$v['id']))->save($gievMoney);
                   //echo 1;
                   if(check_arr($result)){
                       foreach($user as $value){
                           if($value['id'] == $v['userid']){
                               $rs[] = M('FinanceLog')->add(array('username' => $value['username'], 'adminname' => '自动增加', 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $num, 'optype' => 34, 'cointype' => 9, 'old_amount' => $v['hrc'], 'new_amount' => $gievMoney['hrc'], 'userid' => $v['userid'], 'adminid' => 1,'addip'=>get_client_ip()));
                               //var_dump(M('FinanceLog')->getLastSql());
                           }
                       }
                       $mo->commit();
                       echo "用户".$v['userid']."HRC糖果赠送成功！"."\n";


                   }else{
                       $mo->rollback();
                       $log = "用户".$v['userid']."HRC糖果赠送失败！"."赠送比例:".json_encode($Config['ratio']/100).";hrc余额:".json_encode($v['hrc']).";astc余额:".json_encode($v['astc']);
                       $this->givelog($log);
                       echo "用户".$v['userid']."HRC糖果赠送失败！"."\n";
                   }
               }
           }
       }
    }



}
?>