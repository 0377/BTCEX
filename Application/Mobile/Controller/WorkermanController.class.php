<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/9
 * Time: 15:11
 */

namespace Mobile\Controller;


//ini_set('include_path','/usr/local/php/bin/php');

require_once APP_REALPATH.'/GatewayWorker/GatewayClient/Gateway.php';
//require_once APP_REALPATH.'/GatewayWorker/vendor/workerman/workerman/Autoloader.php';
use GatewayClient\Gateway;
//use Workerman\Worker;
//use Workerman\Lib\Timer;
//use GatewayWorker\Protocols\GatewayProtocol;



class WorkermanController extends MobileController
{

    /**
     * 当有人打开这个 Wokerman/index页面的时候
     * 发送一个ajax到jaxs这个方法里 然后 进行匹配账号 接着推送给页面
     */
   /* public function ajaxs()
    {
       Gateway::$registerAddress='127.0.0.1:1238';
       $uid = session('userId');
       $client_id = $_POST['client_id'];
       Gateway::bindUid($client_id,$uid);
        $message = json_encode([
            'type'=>'say',
            'msg'=>'Hello '.$uid
        ]);
        if($uid>0){
            //当有人打开页面时 连接成功时只给当前连接用户推送一条消息
            Gateway::sendToUid($uid, $message);
        }

    }*/
    public function getNewToken()
    {
        Gateway::$registerAddress='127.0.0.1:1238';
        $uid = session('userId');
        $getMyInfro =array(
            'type'=>'say',
            'tokens'=>creatToken()
        );
        $message= json_encode($getMyInfro);
        Gateway::sendToUid($uid, $message);
    }

    public function getInfo()
    {

        Gateway::$registerAddress='127.0.0.1:1238';
        $uid = session('userId');

        $client_id = $_POST['client_id'];
        if(!$uid){
            $uid = 0;
            Gateway::joinGroup($client_id,$_POST['market']);
        }else{
            Gateway::bindUid($client_id,$uid);
            //加入交易对群组
            Gateway::joinGroup($client_id,$_POST['market']);
        }
        if(isset($_POST['market']) && isset($_POST['trade_moshi'])){
            //涨跌幅
           $arr =  $this->getJsonTops($_POST['market']);
           //交易买卖委托信息
           $getDepth = $this->getDepths($_POST['market'],$_POST['trade_moshi']);
           //最新交易记录
           $getTradelog = $this->getTradelogs($_POST['market']);
           //交易中心币种
            $getJsonTop2=$this->getJsonTop2s($_POST['trade_qu_id']);
            //k线
           // $getMarketOrdinaryJson=$this->getMarketOrdinaryJson(['market'=>$_POST['market'],'time'=>$_POST['time']]);
           if($uid>0){
               //我的委托 我的财产
              $myorder =  $this->getEntrustAndUsercoins($_POST['market']);
               $getMyInfro =array(
                   'type'=>'getJsonTop',
                   'getJsonTop'=>$arr,
                   'getEntrustAndUsercoin'=>$myorder,
                   'getDepth'=>$getDepth,
                   'getTradelog'=>$getTradelog,
                   'getJsonTop2'=>$getJsonTop2,

                 );
           }else{
               $getMyInfro =array(
                   'type'=>'getJsonTop',
                   'getJsonTop'=>$arr,
                   'getDepth'=>$getDepth,
                   'getTradelog'=>$getTradelog,
                   'getJsonTop2'=>$getJsonTop2,
               );
           }
            $getMyInfroTwo['type']='getJsonTop';
            //交易买卖委托信息
            $getMyInfroTwo['getDepth']=$getDepth;
            //最新交易记录
            $getMyInfroTwo['getTradelog']=$getTradelog;
            //交易中心币种
            $getMyInfroTwo['getJsonTop2']=$getJsonTop2;
            //K线头部行情
            $getMyInfroTwo['getJsonTop']=$arr;
            $messageTwo = json_encode($getMyInfroTwo);
            $message = json_encode($getMyInfro);
            Gateway::sendToUid($uid,$message);
           // Gateway::sendToAll($messageTwo);

            Gateway::sendToGroup($_POST['market'],$messageTwo);
           // Gateway::sendToAll($messageTwo);
          // Gateway::sendToUid($uid, $message);
        }
    }
    public function weituo($uid,$market)
    {
        $myorder =  $this->getEntrustAndUsercoins($market,$uid);
        $arr['type']='getJsonTop';
        $arr['getEntrustAndUsercoin']=$myorder;
        $message = json_encode($arr);
        Gateway::sendToUid($uid, $message);
    }
    public function getUserInfo($uid,$market,$trade_qu_id=1,$trade_moshi=1)
    {
        Gateway::$registerAddress='127.0.0.1:1238';
        //未知
        $arr =  $this->getJsonTops($market);

        //我的委托 我的财产
        $myorder =  $this->getEntrustAndUsercoins($market);
        //交易买卖委托信息
        $getDepth = $this->getDepths($market,$trade_moshi);

        //最新交易记录
        $getTradelog = $this->getTradelogs($market);
        //交易中心币种
        $getJsonTop2=$this->getJsonTop2s($trade_qu_id);

        $getMyInfro =array(
            'type'=>'getJsonTop',
            'getJsonTop'=>$arr,
            'getEntrustAndUsercoin'=>$myorder,
            'getDepth'=>$getDepth,
            'getTradelog'=>$getTradelog,
            'getJsonTop2'=>$getJsonTop2,
            // 'getMarketOrdinaryJson'=>$getMarketOrdinaryJson
        );
        $getMyInfroTwo=json_encode($getMyInfro);
        //最新交易记录
        Gateway::sendToUid($uid, $getMyInfroTwo);

    }
    /**
     * 获取交易区信息 当订单完成时调用
     * 给当前交易对所在的所有用户推送消息
     */
    public function getDeal($market,$trade_qu_id=1)
    {
        $getMyInfroTwo['type']='getJsonTop';
        //$getMyInfroTwo['getEntrustAndUsercoin']=$getMyInfro['getEntrustAndUsercoin'];
        //交易买卖委托信息
        $getMyInfroTwo['getDepth']=$this->getTradelogs($market);
        //最新交易记录
        $getMyInfroTwo['getTradelog']=$this->getTradelogs($market);
        /*//交易中心币种
        $getMyInfroTwo['getJsonTop2']=getJsonTop2s($trade_qu_id);*/
        //K线头部涨跌幅
        $getMyInfroTwo['getJsonTop']= $this->getJsonTops($market);
        $messageTwo = json_encode($getMyInfroTwo);
        Gateway::sendToGroup($_POST['market'],$messageTwo);
    }

    public  function getNewMarkets($uid,$market,$trade_qu_id=1,$trade_moshi=1)
    {
        if(isset($market) && !empty($market)){
            //未知
            Gateway::$registerAddress='127.0.0.1:1238';
            $arr =  $this->getJsonTops($market);

            //交易买卖委托信息
           $getDepth = $this->getDepths($market,$trade_moshi);
            //最新交易记录
            $getTradelog =  $this->getTradelogs($_POST['market']);
            //交易中心币种
            $getJsonTop2=$this->getJsonTop2s($trade_qu_id);
            //k线
            //$getMarketOrdinaryJson=$this->getMarketOrdinaryJson(['market'=>$_POST['market'],'time'=>$_POST['time']]);
            if($uid>0){
                //我的委托 我的财产
                $myorder =  $this->getEntrustAndUsercoins($market);
                $getMyInfro =array(
                    'type'=>'getJsonTop',
                    'getJsonTop'=>$arr,
                    'getEntrustAndUsercoin'=>$myorder,
                    'getDepth'=>$getDepth,
                    'getTradelog'=>$getTradelog,
                    'getJsonTop2'=>$getJsonTop2,
                 //   'getMarketOrdinaryJson'=>$getMarketOrdinaryJson
                );
            }else{
                $getMyInfro =array(
                    'type'=>'getJsonTop',
                    'getJsonTop'=>$arr,
                    'getDepth'=>$getDepth,
                    'getTradelog'=>$getTradelog,
                    'getJsonTop2'=>$getJsonTop2,
                   // 'getMarketOrdinaryJson'=>$getMarketOrdinaryJson
                );
            }

            $getMyInfroTwo['type']='getJsonTop';
            //$getMyInfroTwo['getEntrustAndUsercoin']=$getMyInfro['getEntrustAndUsercoin'];
            //交易买卖委托信息
             $getMyInfroTwo['getDepth']=$getMyInfro['getDepth'];
            //最新交易记录
            $getMyInfroTwo['getTradelog']=$getMyInfro['getTradelog'];
            //交易中心币种
            $getMyInfroTwo['getJsonTop2']=$getMyInfro['getJsonTop2'];
            //K线头部涨跌幅
            $getMyInfroTwo['getJsonTop']=$arr;
            $messageTwo = json_encode($getMyInfroTwo);
            $message = json_encode($getMyInfro);
            Gateway::sendToUid($uid, $message);
            Gateway::sendToGroup($_POST['market'],$messageTwo);
           // Gateway::sendToAll($messageTwo);
          //  $getMyInfro['jishiqiceshixxxx']=1;
            /*$message = json_encode($getMyInfro);
           // $messageTwo = json_encode();
            Gateway::sendToUid($uid, $message);*/
         //   Gateway::sendToAll($messageTwo);
         //   sleep(2);sendToAll
        }
    }


    /** 交易中心-币种列表  改.HAOMA20181101 **/
    public function getJsonTop2s($id=1, $ajax = 'json')
    {
        /*if (!$data) {
            var_dump(1111);
            exit;*/
        $trandata_data['info'] = "数据正常";
        $trandata_data['status'] = 1;
        $trandata_data['url'] = "";
        if(!session('C_market')){
            $this->error('系统错误，请联系管理员');
        }
       // $arr = session('C_market');

        $markets = M('market')->where(array('status'=>1))->order("sort desc")->select();

        //$markets = M('market')->order("sort desc")->select();

        foreach ($markets as $key => $val){
            foreach (C("market") as $k => $v){
                if($val['name'] == $k){
                    $markets[$key]['xnbimg'] = $v['xnbimg'];
                    $markets[$key]['xnb'] = $v['xnb'];
                    $markets[$key]['title'] = $v['title'];
                }
            }
        }

        //foreach (C("market") as $k => $v) {
        foreach ($markets as $k => $v) {
            if ($v['jiaoyiqu'] == $id) {
                if($v['show_status'] == 1){
                    $trandata_data["list"][$k]["name"] = $v["name"];
                    $trandata_data["list"][$k]["img"] = $v["xnbimg"];
                    $trandata_data["list"][$k]["title"] = $v["title"];
                    $trandata_data["list"][$k]["new_price"] = $v["new_price"];
                    $trandata_data["list"][$k]["change"] = $v["change"];
                    $trandata_data["list"][$k]['coin_name'] = strtoupper($v["xnb"]);
                }

            }
        }
        /*}*/
        return $trandata_data;
        /*if ($ajax) {
            return $trandata_data;
        } else {
            return $trandata_data;
        }*/
    }


    /**
     * @param null $market
     * @param string $ajax
     * @return mixed|null|string|void
     * 最新交易记录
     */
    public function getTradelogs($market = NULL, $ajax = 'json')
    {
        // 过滤非法字符----------------S
        if (checkstr($market)) {
            $this->error('您输入的信息有误！');
        }
        // 过滤非法字符----------------E

        $data = (APP_DEBUG ? null : S('getTradelog' . $market));

        if (!$data) {
            //$suo = M()->query("unlock tables");
            $tradeLog =  M('TradeLog')->where(array('status' => 1, 'market' => $market))->order('id desc')->limit(10)->select();
           // $tradeLog =  M()->query("UNLOCK TABLES SELECT * FROM `tw_trade_log` WHERE `status` = 1 AND `market` = 'hrc_usdt' ORDER BY id desc LIMIT 10");
           // var_dump(M('TradeLog')->_sql());
            if ($tradeLog) {
                foreach ($tradeLog as $k => $v) {
                    $data['tradelog'][$k]['addtime'] = date('H:i:s', $v['addtime']);
                    $data['tradelog'][$k]['type'] = $v['type'];
                    $data['tradelog'][$k]['price'] = $v['price'] * 1;
                    $data['tradelog'][$k]['num'] = round($v['num'], 6);
                    $data['tradelog'][$k]['mum'] = round($v['mum'], 6);
                }
                S('getTradelog' . $market, $data);
            }
        }

        if ($ajax) {
            return  $data;
        } else {
            return $data;
        }
    }
    public function jiekou()
    {
        $this->display();
    }


    public function config()
    {
        $config = array(
            'supports_search' => true,
            'supports_group_request' => false,
            'supported_resolutions' => ["1", "5", "15", "30", "60", "1D", "1W"],
            'supports_marks' => false,
            'supports_time' => true,
            "exchanges" => [["value" => "", "name" => "xxx", "desc" => ""]]
        );

        exit(json_encode($config));
    }
    public function time()
    {
        echo time();
    }


    public function symbols()
    {
        $input = I('get.');
        $market = strtolower(trim($input['symbol']));

        $data = [];
        if (is_array(C('market')[$market])) {
            $marketData = C('market')[$market];
//            $data['market'] = $marketData;

            $data['description'] = $marketData['title'];
//            $data['exchange-listed'] = '';
//            $data['exchange-traded'] = '';
            $data['has_intraday'] = true;
            $data['has_no_volume'] = false;
            $data['minmov'] = 1;
            $data['minmov2'] = 0;
            // $data['name'] = $marketData['title'];
            $data['name'] = $market;
            $data['pricescale'] = 100;
            $data['session'] = substr(str_replace(':', '', $marketData['begintrade']), 0, 4) . '-' . substr(str_replace(':', '', $marketData['endtrade']), 0, 4) . ':1234567';
            $data['supported_resolutions'] = ["1", "5", "15", "30", "60", "1D", "1W"];
            $data['ticker'] = $market;
            $data['timezone'] = 'Asia/Shanghai';
            $data['type'] = 'stock';
        }

        exit(json_encode($data));
    }

    /**
     * 更新K线数据
     * @param $Trading 交易对 既分组
     * @param $market 交易对  需要查询数据的交易对
     * @param $from //开始时间
     * @param $to //结束时间
     * @param $resolution //类型 1  3  5 15 20 30 60 1440 等K线数据类型
     */
    public function updateKline($Trading,$market, $from, $to, $resolution)
    {
        $updateK =  $this->getMarketOrdinaryJson($market, $from, $to, $resolution);
        $data['type']="lineK";
        $data['method']='KlineUpdata';
        $data['data']=$updateK;
        Gateway::sendToGroup($Trading,$data);
    }
    public function history()
    {
        $input = I('get.');
        $market = (is_array(C('market')[$input['symbol']]) ? trim($input['symbol']) : C('market_mr'));
        $from = intval($input['from']);
        $to = intval($input['to']);
        $resolution = $input['resolution'];
        $allResolutions = array(
            '1' => 1,
            '5' => 5,
            '15' => 15,
            '30' => 30,
            '60' => 60,
            '1D' => 1440,
            '1W' => 10080,
//            '1M' => 302400,
        );
        $key = $market . $resolution . $from . $to;

        $resolution = key_exists($resolution, $allResolutions) ? $allResolutions[$resolution] : 1;

        $lastUpdateTime = (APP_DEBUG ? null : S('UdfHistoryUpdateTime' . $key));

        if (($lastUpdateTime + 60) < time()) {
            S('UdfHistoryData' . $key, null);
            S('UdfHistoryUpdateTime' . $key, time());
        }

        $tradeJson = (APP_DEBUG ? null : S('UdfHistoryData' . $key));

        if (!$tradeJson) {
            $tradeJson = M('TradeJson')->where(array(
                'market' => $market,
                'type' => $resolution,
                'data' => array('neq', ''),
                //'addtime' => array(array('egt', $from), array('elt', $to), 'and'),
            ))->order('id asc')->limit(100)->select();
           // S('UdfHistoryData' . $key, $tradeJson);
        }
        $json_data = array();
        krsort($tradeJson);
        foreach ($tradeJson as $k => $v) {

            $json_data[] = json_decode($v['data'], true);
            $arrJson['s']="ok";
            $arrJson['t'][]=json_decode($v['data'],true)[0];//时间戳
            $arrJson["v"][]=json_decode($v['data'],true)[1];//量
            $arrJson["o"][]=json_decode($v['data'],true)[2];//开盘
            $arrJson['l'][]=json_decode($v['data'],true)[4];//低
            $arrJson['h'][]=json_decode($v['data'],true)[3];//高
            $arrJson['c'][]=json_decode($v['data'],true)[5];//收盘
        }
        if(empty($json_data)){
            $data['s']="no_data";
            $data['nextTime']=1522108800;
            exit(json_encode($data));
        }
 /*       $data = array(
            's' => 'ok',
            't' => array(),
            'c' => array(),//收盘
            'o' => array(),//开盘
            'h' => array(),//高
            'l' => array(),//低
            'v' => array(),//量
        );

        foreach ($json_data as $k => $v) {

        }*/

        exit(json_encode($arrJson));
    }






    /**
     * K线图
     */
    public function getMarketOrdinaryJson($market, $from, $to, $resolution)
    {

   /*     $arr=[
            1,2,3
        ];
        //$input
        echo json_encode($arr);
        exit;*/
       // $input = I("get.");
        $market = (is_array(C("market")[$market]) ? trim($market) : C("market_mr"));
        $timearr = array(1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080);
     /*   krsort($tradeJson);
        foreach ($tradeJson as $k => $v) {
            $json_data[] = json_decode($v["data"], true);
        }*/

        if (in_array($resolution, $timearr)) {
            $type = $resolution;
        } else {
            $type = 5;
        }
        $timeaa = (APP_DEBUG ? null : S("ChartgetMarketOrdinaryJsontime" . $market . $type));

        if (($timeaa + 60) < time()) {
            S("ChartgetMarketOrdinaryJson" . $market . $type, null);
            S("ChartgetMarketOrdinaryJsontime" . $market . $type, time());
        }

        $tradeJson = (APP_DEBUG ? null : S("ChartgetMarketOrdinaryJson" . $market . $type));
        $where =[
            "market" =>$market,
            "type"   => $type,
            "data"   => array("neq", ""),
            "addtime"=>[['EGT',$from],['ELT',$to]]
        ];
        /*   $where =[
                //"market" => $market,
                "market" =>"rt_cnc",
                "type"   => $time,
                "data"   => array("neq", "")
            ];*/
        if (!$tradeJson) {
            $tradeJson = M("TradeJson")->where($where)->order("id desc")->limit(100)->select();
            S("ChartgetMarketOrdinaryJson" . $market . $type, $tradeJson);
        }
        krsort($tradeJson);
        foreach ($tradeJson as $k => $v) {

            $json_data[] = json_decode($v["data"],true);
        /*    $arrJson['s']='ok';
            $arrJson['t'][]=json_decode($v['data'],true)[0];//时间戳
            $arrJson["v"][]=json_decode($v['data'],true)[1];//量
            $arrJson["o"][]=json_decode($v['data'],true)[2];//开盘
            $arrJson['l'][]=json_decode($v['data'],true)[4];//低
            $arrJson['h'][]=json_decode($v['data'],true)[3];//高
            $arrJson['c'][]=json_decode($v['data'],true)[5];//收盘*/
        }
        foreach ($json_data as $k => $v) {
            $data[$k]      = array();
            $data[$k]['t'] = $v[0];
            $data[$k]['o'] = floatval($v[2]);
            $data[$k]['c'] = floatval($v[5]);
            $data[$k]['h'] = floatval($v[3]);
            $data[$k]['l'] = floatval($v[4]);
            $data[$k]['v'] = $v[1];
            $data[$k]['s'] = 'ok';
        }
/*        $datas = array(
            's' => 'ok',
            't' => array(),//时间戳
            'c' => array(),//收盘
            'o' => array(),//开盘
            'h' => array(),//高
            'l' => array(),//低
            'v' => array(),//量
        );*/
        echo json_encode($data);
        exit;
        return $json_data;
       // $json_data[]=['key'=>'K'];
        //var_dump(floor( $v["data"] * 100000000) / 100000000);
        //var_dump(sprintf("%.8f",substr(sprintf("%.3f",0.315456456 ), 0, -8)));

        //$url = $_SERVER['SERVER_NAME'].":8082?type=publish&content=".'js123456'."&to=".$uid;
        /* ignore_user_abort();//关掉浏览器，PHP脚本也可以继续执行.
         set_time_limit(3000);// 通过set_time_limit(0)可以让程序无限制的执行下去
         $interval=2;// 每隔2s运行*/
        // $url = $_SERVER['SERVER_NAME'].":8082?type=publish&content=".json_encode($json_data)."&to=".$uid;



        //  $this->carriedApi($url);
        //var_dump(json_encode($json_data));
        /* $this->getmicrotime();
         for($i=0;$i<=5;$i++){

             echo '测试'.time().'<br/>';
             sleep($interval);// 等待2s

         }*/
        //exit(json_encode());
    }
    /**
     * 交易买卖委托信息
     * @param null $market
     * @param int $trade_moshi
     * @param int $limts
     * @param string $ajax
     * @return null
     */
    public function getDepths($market = NULL, $trade_moshi = 1,$limts = 5, $ajax = 'json')
    {
       // 过滤非法字符----------------S
        if (checkstr($market) || checkstr($trade_moshi)) {
            $this->error(L('您输入的信息有误！'));
        }
        // 过滤非法字符----------------E

        /* if (!C('market')[$market]) {
            return null;
        }*/
        $data_getDepth = (APP_DEBUG ? null : S('getDepth'));

        if (!$data_getDepth[$market][$trade_moshi]) {

            if ($trade_moshi == 1) {
                $limt = $limts;
            }
            if (($trade_moshi == 3) || ($trade_moshi == 4)) {
                $limt = $limts;
            }

            $mo = M();
            if ($trade_moshi == 1) {
                $buy = $mo->query('select id,price,sum(num-deal)as nums from tw_trade where status=0 and type=1 and userid>0 and market =\'' . $market . '\' group by price order by price desc limit ' . $limt . ';');
                $sell = array_reverse($mo->query('select id,price,sum(num-deal)as nums from tw_trade where status=0 and type=2 and userid>0 and market =\'' . $market . '\' group by price order by price asc limit ' . $limt . ';'));
            }
            if ($trade_moshi == 3) {
                $buy = $mo->query('select id,price,sum(num-deal)as nums from tw_trade where status=0 and type=1 and userid>0 and market =\'' . $market . '\' group by price order by price desc limit ' . $limt . ';');
                $sell = null;
            }
            if ($trade_moshi == 4) {
                $buy = null;
                $sell = array_reverse($mo->query('select id,price,sum(num-deal)as nums from tw_trade where status=0 and type=2 and userid>0 and market =\'' . $market . '\' group by price order by price asc limit ' . $limt . ';'));
            }

            if ($buy) {
                $maxNums = maxArrayKey($buy, 'nums') / 2;
                foreach ($buy as $k => $v) {
                    $data['depth']['buy'][$k] = array(floatval($v['price'] * 1), floatval($v['nums'] * 1));
                    $data['depth']['buypbar'][$k] = ((($maxNums < $v['nums'] ? $maxNums : $v['nums']) / $maxNums) * 100);
                }
            } else {
                $data['depth']['buy'] = '';
                $data['depth']['buypbar'] = '';
            }

            if ($sell) {
                $maxNums = maxArrayKey($sell, 'nums') / 2;
                foreach ($sell as $k => $v) {
                    $data['depth']['sell'][$k] = array(floatval($v['price'] * 1), floatval($v['nums'] * 1));
                    $data['depth']['sellpbar'][$k] = ((($maxNums < $v['nums'] ? $maxNums : $v['nums']) / $maxNums) * 100);
                }
            } else {
                $data['depth']['sell'] = '';
                $data['depth']['sellpbar'] = '';
            }

            $data_getDepth[$market][$trade_moshi] = $data;
            S('getDepth', $data_getDepth);
        } else {
            $data = $data_getDepth[$market][$trade_moshi];
        }

        if ($ajax) {
            return $data;
        } else {
            return $data;
        }
    }
    /**
     * @param null $market
     * @param string $ajax
     * @return mixed
     * 我的财产我的委托
     */
    public function getEntrustAndUsercoins($market = NULL,$uid= NULL, $ajax = 'json')
    {
        // 过滤非法字符----------------S
        if (checkstr($market)) {
            $this->error('您输入的信息有误！');
        }
        // 过滤非法字符----------------E
        if(empty($uid)){
            if(!session('userId')){
                $this->error('系统错误！');
            }
            $userid = session('userId');
        }else{
            $userid = $uid;
        }




      /*  if (!C('market')[$market]) {
            return 1111;
        }*/

        //$results = M()->query('select id,price,num,deal,mum,type,fee,status,addtime from tw_trade where status=0 and market=\'' . $market . '\' and userid=' . userid() . ' order by id desc limit 10;');
        $where = [
            'status'=>0,
            'market'=>$market,
            'userid'=>$userid
        ];
        $result =  M("trade")->where($where)->order('id desc')->limit(10)->field('id,price,num,deal,mum,type,fee,status,addtime')->select();
        if ($result) {
            foreach ($result as $k => $v) {
                $data['entrust'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
                $data['entrust'][$k]['type'] = $v['type'];
                $data['entrust'][$k]['price'] = $v['price'] * 1;
                $data['entrust'][$k]['num'] = round($v['num'], 6);
                $data['entrust'][$k]['deal'] = round($v['deal'], 6);
                $data['entrust'][$k]['id'] = round($v['id']);
                $data['entrust'][$k]['status'] = $v['status'];
            }
        } else {
            $data['entrust'] = null;
        }

        $userCoin = M('UserCoin')->where(array('userid' => userid()))->find();

        if ($userCoin) {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
            $data['usercoin']['xnb'] = floatval($userCoin[$xnb]);
            $data['usercoin']['xnbd'] = floatval($userCoin[$xnb . 'd']);
            $data['usercoin']['rmb'] = floatval($userCoin[$rmb]);
            $data['usercoin']['rmbd'] = floatval($userCoin[$rmb . 'd']);
        } else {
            $data['usercoin'] = null;
        }
        // 处理开盘闭盘交易时间===开始
        $times = date('G',time());
        $minute = date('i',time());
        $minute = intval($minute);
        $data['time_state'] = 0;
        if(!session('C_market')){
            $this->error('系统错误，请联系管理员');
        }
        $cArr= session('C_market');
        if (($times <= $cArr[$market]['start_time'] && $minute < intval($cArr[$market]['start_minute']))|| ($times > $cArr[$market]['stop_time'] && $minute>= intval($cArr[$market]['stop_minute']))) {
            $data['time_state'] = 1;
        }
        if (($times <$cArr[$market]['start_time'] )|| $times > $cArr[$market]['stop_time']) {
            $data['time_state'] = 1;
        } else {
            if ($times == $cArr[$market]['start_time']) {
                if ($minute< intval($cArr[$market]['start_minute'])) {
                    $data['time_state'] = 1;
                }
            } elseif ($times ==$cArr[$market]['stop_time']) {
                if (( $minute > $cArr[$market]['stop_minute'])) {
                    $data['time_state'] = 1;
                }
            }
        }
        // 处理周六周日是否可交易===开始
        $weeks = date('N',time());
        if(!C('market')[$market]['agree6']){
            if($weeks == 6){
                $data['time_state'] = 1;
            }
        }
        if(!C('market')[$market]['agree7']){
            if($weeks == 7){
                $data['time_state'] = 1;
            }
        }
        //处理周六周日是否可交易===结束
        if ($ajax) {
            return $data;
        } else {
            return $data;
        }
    }
    // 交易中心调用

    /**
     * @param null $market
     * @param string $ajax
     * @return mixed|null|string|void
     * 涨跌幅信息
     */
    public function getJsonTops($market =null, $ajax = 'json')
    {
        // 过滤非法字符----------------S
        if (checkstr($market)) {
            $this->error('您输入的信息有误！');
        }

        // 过滤非法字符----------------E
        $data = (APP_DEBUG ? null : S("getJsonTop" . $market));
        $data=0;

        if (!$data) {
            if ($market) {

                $xnb = explode("_", $market)[0];
                $rmb = explode("_", $market)[1];

                // 24小时 交易量
                $volume_24h = array();
                $volume_24h = M('TradeLog')->where(array(
                    'status' => 1,
                    'market' => $market,
                    'addtime' => array('gt', time() - (60 * 60 * 24))
                ))->sum('num');
                $volume_24h = round($volume_24h, 4);

               /* foreach (C("market") as $k => $v) {
                        $v["xnb"] = explode("_", $v["name"])[0];
                        $v["rmb"] = explode("_", $v["name"])[1];
                        $data["list"][$k]["name"] = $v["name"];
                        $data["list"][$k]["img"] = $v["xnbimg"];
                        $data["list"][$k]["title"] = $v["title"];
                        $data["list"][$k]["new_price"] = $v["new_price"];
                        $data["list"][$k]["change"] = $v["change"];
                        $data["list"][$k]['coin_name'] = strtoupper($v["xnb"]);
                }*/

                $arr = M('market')->where(['name'=>$market])->find();

              /*  var_dump($arr);
                //币种图片
                $data["info"]["img"] = C("market")[$market]["xnbimg"];
                //$data["info"]["title"] = C("market")[$market]["title"];
                //最新价格
                $data["info"]["new_price"] = C("market")[$market]["new_price"];
                //最高价
                $data["info"]["max_price"] = C("market")[$market]["max_price"];
                //最低价
                $data["info"]["min_price"] = C("market")[$market]["min_price"];
                //买一价
                $data["info"]["buy_price"] = C("market")[$market]["buy_price"];
                //卖一价
                $data["info"]["sell_price"] = C("market")[$market]["sell_price"];
                $data["info"]["volume"] = isset($volume_24h) ? $volume_24h : 0;//C("market")[$market]["volume"];
                //涨跌幅
                $data["info"]["change"] = C("market")[$market]["change"];

                //var_dump($a[$market]["change"]);*/
                $data=[];
                $data['info']['img']=C("market")[$market]["xnbimg"];
                //最新价格
                $data["info"]["new_price"] = $arr["new_price"];
                //最高价
                $data["info"]["max_price"] = $arr["max_price"];
                //最低价
                $data["info"]["min_price"] = $arr["min_price"];
                //买一价
                $data["info"]["buy_price"] =$arr["buy_price"];
                //卖一价
                $data["info"]["sell_price"] = $arr["sell_price"];
                $data["info"]["volume"] = isset($volume_24h) ? $volume_24h : 0;//C("market")[$market]["volume"];
                //涨跌幅
                $data["info"]["change"] = $arr["change"];

                S("getJsonTop" . $market, $data);
            }
        }

        if ($ajax) {
            return $data;
            //exit(json_encode($data));
        } else {
            return $data;
        }
    }

    //机器人撮合交易调用
    public function sendToSuccess($market,$trade_moshi=1)
    {
        //交易买卖委托信息
        $getDepth = $this->getDepths($market,$trade_moshi);
        $messageTwo = json_encode($getDepth);
        Gateway::sendToGroup($market,$messageTwo);
    }
}
