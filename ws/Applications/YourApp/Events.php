<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;
//use \Home\Controller\WokermanController;
//use \Workerman\Lib\Timer;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        /*// 向当前client_id发送数据 
        Gateway::sendToClient($client_id, "Hello $client_id\r\n");
        // 向所有人发送
        Gateway::sendToAll("$client_id login\r\n");*/
		$resData = [
			'type' => 'init',
			'client_id' => $client_id,
		];
        Gateway::sendToClient($client_id,json_encode($resData));
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message)
   {

		$message = json_decode($message,true);
		if(isset($message)){
          /*  $resData = [
                'type' => 'init',
                'client_id' => $client_id,
            ];
            Gateway::sendToClient($client_id,json_encode($resData));*/
          // $url = $message['data']['host'].'//'.$message['data']['url']."/Wokerman/getMarketOrdinaryJson";
          //  $url ="http://api.wsex.cc/K";
            $url ="http://api.wsex.cc/api/V1/S";

			$where=[
                'market'=>$message['data']['market'],
                'from'=>$message['data']['from'],
                'to'=>$message['data']['to'],
                'resolution'=>$message['data']['resolution']
            ];
           $arr =  self::carriedApi($url,$where);
           $data['type']="lineK";
           $data['method']=$message['method'];
           $data['data']=$arr;
            Gateway::sendToClient($client_id,json_encode($data));
           //  Gateway::sendToClient($client_id,json_encode($data));
		}else{
           $arr=[
             'type'=>2,
             $message=>'数据有误'
           ];
           Gateway::sendToClient($client_id,json_encode($arr));
       }
   }


   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id)
   {
       // 向所有人发送 
	   $arr =[
		'type'=>'break',
		'info'=>$client_id.'断开连接',
	   ];
	   
       GateWay::sendToAll( json_encode($arr));
   }
   /**
     * @param $url
     * @param null $data
     * @return mixed
     * 调用接口方法
     */
    public static function carriedApi($url,$data=null)
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
}
