<?php
namespace Home\Controller;

class ChartController extends HomeController
{
	public function getJsonData($market = NULL, $ajax = 'json')
	{
		// 过滤非法字符----------------S
		if (checkstr($market)) {
			$this->error('您输入的信息有误！');
		}
		// 过滤非法字符----------------E

		if ($market) {
			$data = (APP_DEBUG ? null : S('ChartgetJsonData' . $market));

			if (!$data) {
				$data[0] = $this->getTradeBuy($market);
				$data[1] = $this->getTradeSell($market);
				$data[2] = $this->getTradeLog($market);
				S('ChartgetJsonData' . $market, $data);
			}
			header("Content-type:application/json");
			header('X-Frame-Options: SAMEORIGIN');
			exit(json_encode($data));
		}
	}

	protected function getTradeBuy($market)
	{
		// 过滤非法字符----------------S
		if (checkstr($market)) {
			$this->error('您输入的信息有误！');
		}
		// 过滤非法字符----------------E

		$mo = M();
		$buy = $mo->query('select id,price,sum(num-deal)as nums from tw_trade  where status=0 and type=1 and market =\'' . $market . '\' group by price order by price desc limit 100;');
		$data = '';

		if ($buy) {
			$maxNums = maxArrayKey($buy, 'nums') / 2;
			foreach ($buy as $k => $v) {
				$data .= '<tr><td class="buy"  width="50">买' . ($k + 1) . '</td><td class="buy"  width="80">' . floatval($v['price']) . '</td><td class="buy"  width="120">' . floatval($v['nums']) . '</td><td  width="100"><span class="buySpan" style="width: ' . ((($maxNums < $v['nums'] ? $maxNums : $v['nums']) / $maxNums) * 100) . 'px;" ></span></td></tr>';
			}
		}
		Vendor("XssFilter.XssFilter","",".php");
		$data=\XssFilter::xss_clean($data);
		return $data;
	}

	protected function getTradeSell($market)
	{
		// 过滤非法字符----------------S
		if (checkstr($market)) {
			$this->error('您输入的信息有误！');
		}
		// 过滤非法字符----------------E

		$mo = M();
		$sell = $mo->query('select id,price,sum(num-deal)as nums from tw_trade where status=0 and type=2 and market =\'' . $market . '\' group by price order by price asc limit 100;');
		$data = '';

		if ($sell) {
			$maxNums = maxArrayKey($sell, 'nums') / 2;

			foreach ($sell as $k => $v) {
				$data .= '<tr><td class="sell"  width="50">卖' . ($k + 1) . '</td><td class="sell"  width="80">' . floatval($v['price']) . '</td><td class="sell"  width="120">' . floatval($v['nums']) . '</td><td style="width: 100px;"><span class="sellSpan" style="width: ' . ((($maxNums < $v['nums'] ? $maxNums : $v['nums']) / $maxNums) * 100) . 'px;" ></span></td></tr>';
			}
		}
		Vendor("XssFilter.XssFilter","",".php");
		$data=\XssFilter::xss_clean($data);
		return $data;
	}

	protected function getTradeLog($market)
	{
		// 过滤非法字符----------------S
		if (checkstr($market)) {
			$this->error('您输入的信息有误！');
		}
		// 过滤非法字符----------------E

		$log = M('TradeLog')->where(array('status' => 1, 'market' => $market))->order('id desc')->limit(100)->select();
		$data = '';

		if ($log) {
			foreach ($log as $k => $v) {
				if ($v['type'] == 1) {
					$type = 'buy';
				} else {
					$type = 'sell';
				}

				$data .= '<tr><td class="' . $type . '"  width="70">' . date('H:i:s', $v['addtime']) . '</td><td class="' . $type . '"  width="70">' . floatval($v['price']) . '</td><td class="' . $type . '"  width="100">' . floatval($v['num']) . '</td><td class="' . $type . '">' . floatval($v['mum']) . '</td></tr>';
			}
		}
		Vendor("XssFilter.XssFilter","",".php");
		$data=\XssFilter::xss_clean($data);
		return $data;
	}

	public function trend()
	{
		// TODO: SEPARATE
		$input = I('get.');
		$market = (is_array(C('market')[$input['market']]) ? trim($input['market']) : C('market_mr'));
		$this->assign('market', $market);
		$this->display();
	}

	public function getMarketTrendJson()
	{
		// TODO: SEPARATE
		$input = I('get.');
		$market = (is_array(C('market')[$input['market']]) ? trim($input['market']) : C('market_mr'));
		$data = (APP_DEBUG ? null : S('ChartgetMarketTrendJson' . $market));

		if (!$data) {
			$data = M('TradeLog')->where(array(
				'market'  => $market,
				'addtime' => array('gt', time() - (60 * 60 * 24 * 30 * 2))
				))->select();
			S('ChartgetMarketTrendJson' . $market, $data);
		}
		$json_data=array();
		foreach ($data as $k => $v) {
			$json_data[$k][0] = intval($v['addtime']);
			$json_data[$k][1] = floatval($v['price']);
		}
		header("Content-type:application/json");
		header('X-Frame-Options: SAMEORIGIN');
		exit(json_encode($json_data));
	}

	public function ordinary()
	{
		// TODO: SEPARATE
		$input = I('get.');
		$market = (is_array(C('market')[$input['market']]) ? trim($input['market']) : C('market_mr'));
		$this->assign('market', $market);
		$this->display();
	}

	public function getMarketOrdinaryJson()
	{
		$input = I("get.");

		$market = (is_array(C("market")[$input["market"]]) ? trim($input["market"]) : C("market_mr"));
		$timearr = array(1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080);


        krsort($tradeJson);
        foreach ($tradeJson as $k => $v) {
            $json_data[] = json_decode($v["data"], true);
        }

		if (in_array($input["time"], $timearr)) {
			$time = $input["time"];
		} else {
			$time = 5;
		}

		$timeaa = (APP_DEBUG ? null : S("ChartgetMarketOrdinaryJsontime" . $market . $time));

		if (($timeaa + 60) < time()) {
			S("ChartgetMarketOrdinaryJson" . $market . $time, null);
			S("ChartgetMarketOrdinaryJsontime" . $market . $time, time());
		}

		$tradeJson = (APP_DEBUG ? null : S("ChartgetMarketOrdinaryJson" . $market . $time));
        $where =[
           "market" =>$market,
           "type"   => $time,
           "data"   => array("neq", "")
       ];
    /*   $where =[
            //"market" => $market,
            "market" =>"rt_cnc",
            "type"   => $time,
            "data"   => array("neq", "")
        ];*/
		if (!$tradeJson) {
			$tradeJson = M("TradeJson")->where($where)->order("id desc")->limit(100)->select();

			S("ChartgetMarketOrdinaryJson" . $market . $time, $tradeJson);
		}

		krsort($tradeJson);

		foreach ($tradeJson as $k => $v) {


			$json_data[] = json_decode($v["data"],true);

             /* foreach (json_de    code($v["data"]) as $var){

                var_dump(number_format($var,8));
              }
		    exit;*/
		}
      //  $json_data[]=['key'=>'K'];
        //var_dump(floor( $v["data"] * 100000000) / 100000000);
        //var_dump(sprintf("%.8f",substr(sprintf("%.3f",0.315456456 ), 0, -8)));

        //$url = $_SERVER['SERVER_NAME'].":8082?type=publish&content=".'js123456'."&to=".$uid;
        /* ignore_user_abort();//关掉浏览器，PHP脚本也可以继续执行.
         set_time_limit(3000);// 通过set_time_limit(0)可以让程序无限制的执行下去
         $interval=2;// 每隔2s运行*/
    //    $uid = session('userId');
       // $url = $_SERVER['SERVER_NAME'].":8082?type=publish&content=".json_encode($json_data)."&to=".$uid;



      //  $this->carriedApi($url);
        //var_dump(json_encode($json_data));
       /* $this->getmicrotime();
        for($i=0;$i<=5;$i++){

            echo '测试'.time().'<br/>';
            sleep($interval);// 等待2s

        }*/
		exit(json_encode($json_data));
	}
    public function getmicrotime(){
        return time();
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
	public function specialty()
	{
		// TODO: SEPARATE
		$input = I('get.');
		$market = (is_array(C('market')[$input['market']]) ? trim($input['market']) : C('market_mr'));
		$this->assign('market', $market);
		$this->display();
	}

	public function getMarketSpecialtyJson()
	{
		// TODO: SEPARATE
		$input = I('get.');
		$market = (is_array(C('market')[$input['market']]) ? trim($input['market']) : C('market_mr'));
		
		$timearr = array(1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080);
		if (in_array($input['step'] / 60, $timearr)) {
			$time = floatval($input['step'] / 60);
		} else {
			$time = 5;
		}

		$timeaa = (APP_DEBUG ? null : S('ChartgetMarketSpecialtyJsontime' . $market . $time));
		if (($timeaa + 60) < time()) {
			S('ChartgetMarketSpecialtyJson' . $market . $time, null);
			S('ChartgetMarketSpecialtyJsontime' . $market . $time, time());
		}

		$tradeJson = (APP_DEBUG ? null : S('ChartgetMarketSpecialtyJson' . $market . $time));
		if (!$tradeJson) {
			$tradeJson = M('TradeJson')->where(array('type' => $time, 'market' => $market))->order('id asc')->limit(1000)->select();
			S('ChartgetMarketSpecialtyJson' . $market . $time, $tradeJson);
		}
		
		$json_data=array();
		Vendor("XssFilter.XssFilter","",".php");
		foreach ($tradeJson as $k => $v) {
			$v['data']=\XssFilter::xss_clean($v['data']);
			$json_data[] = json_decode($v['data'], true);
		}

		foreach ($json_data as $k => $v) {
			$data[$k][0] = $v[0];
			$data[$k][1] = 0;
			$data[$k][2] = 0;
			$data[$k][3] = $v[2];
			$data[$k][4] = $v[5];
			$data[$k][5] = $v[3];
			$data[$k][6] = $v[4];
			$data[$k][7] = $v[1];
		}

		header("Content-type:application/json");
		header('X-Frame-Options: SAMEORIGIN');
		exit(json_encode($data));
	}

	public function getSpecialtyTrades()
	{
		$input = I('get.');

		// 过滤非法字符----------------S
		if (checkstr($input['since']) || checkstr($input['market'])) {
			$this->error('您输入的信息有误！');
		}
		// 过滤非法字符----------------E

		$json_data=array();
		if (!$input['since']) {
			$tradeLog = M('TradeLog')->where(array('market' => $input['market']))->order('id desc')->find();
			$json_data[] = array('tid' => intval($tradeLog['id']), 'date' => intval($tradeLog['addtime']), 'price' => floatval($tradeLog['price']), 'amount' => floatval($tradeLog['num']), 'trade_type' => $tradeLog['type'] == 1 ? 'bid' : 'ask');
			header("Content-type:application/json");
			header('X-Frame-Options: SAMEORIGIN');
			exit(json_encode($json_data));
		} else {
			$tradeLog = M('TradeLog')->where(array(
				'market' => $input['market'],
				'id'     => array('gt', $input['since'])
				))->order('id desc')->select();

			foreach ($tradeLog as $k => $v) {
				$json_data[] = array('tid' => intval($v['id']), 'date' => intval($v['addtime']), 'price' => floatval($v['price']), 'amount' => floatval($v['num']), 'trade_type' => $v['type'] == 1 ? 'bid' : 'ask');
			}
			header("Content-type:application/json");
			header('X-Frame-Options: SAMEORIGIN');
			exit(json_encode($json_data));
		}
	}
	
/*	public function getSpecialtyTrades()
	{
		$input = I('get.');

		// 过滤非法字符----------------S
		if (checkstr($input['since']) || checkstr($input['market'])) {
			$this->error('您输入的信息有误！');
		}
		// 过滤非法字符----------------E

		$json_data=array();
		if (!$input['since']) {
			$tradeLog = M('TradeLog')->where(array('market' => $input['market']))->order('id desc')->find();
			$json_data[] = array('tid' => intval($tradeLog['id']), 'date' => intval($tradeLog['addtime']), 'price' => floatval($tradeLog['price']), 'amount' => floatval($tradeLog['num']), 'trade_type' => $tradeLog['type'] == 1 ? 'bid' : 'ask');
			header("Content-type:application/json");
			header('X-Frame-Options: SAMEORIGIN');
			exit(json_encode($json_data));
		}
		else {
			$tradeLog = M('TradeLog')->where(array(
				'market' => $input['market'],
				'id'     => array('gt', $input['since'])
				))->order('id desc')->select();

			foreach ($tradeLog as $k => $v) {
				$json_data[] = array('tid' => intval($v['id']), 'date' => intval($v['addtime']), 'price' => floatval($v['price']), 'amount' => floatval($v['num']), 'trade_type' => $v['type'] == 1 ? 'bid' : 'ask');
			}
			header("Content-type:application/json");
			header('X-Frame-Options: SAMEORIGIN');
			exit(json_encode($json_data));
		}
	}*/
}

?>