<?php
namespace Common\Ext;

class AstcCommon
{
    protected $host, $port;
    public $base = 1000000000000000000;//1e18 wei  基本单位

    function __construct($host)
    {
        $this->host = $host;
        //$this->port = $port;

    }
    /**
     * 发送请求
     *
     */
    function request($method, $params = array())
    {
        $data = array();
        $data['params'] = $params;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->host);
        curl_setopt($ch, CURLOPT_PORT, $this->port);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $ret = curl_exec($ch);
        //返回结果
        if ($ret) {
            curl_close($ch);
            return json_decode($ret, true);
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return json_decode('ASTC钱包服务器连接失败', true);
            // throw new Exception("curl出错，错误码:$error");
        }
    }
   /*
    * 创建地址
    * post请求不带参数
    **/
    public function user_createNewAddr()
    {
        $params = '';
        $result = $this->request(__FUNCTION__, $params);
        if ($result['code'] == '200') {
            return $result['data'];//新生成的账号地址
        } else {
            return $result['msg'];
        }
    }
    /*
    * 查询账户余额
    *post 请求带上地址
    **/
    public function astc_getBalance($host,$address)
    {
        if ($address=='') {
            echo '请传入账号地址';
            return false;
        }
        $this->host = $host;
        $data['address'] = $address;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->host);
        curl_setopt($ch, CURLOPT_PORT, $this->port);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $ret = curl_exec($ch);
        //返回结果
        if ($ret) {
            curl_close($ch);
            return json_decode($ret, true);
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return json_decode('ASTC钱包服务器连接失败', true);
            // throw new Exception("curl出错，错误码:$error");
        }

        /*$data = $this->request(__FUNCTION__, $addres);
        if (empty($data['error']) && !empty($data['result'])) {
            // return $this->fromWei($data['result']);//返回eth数量，自己做四舍五入处理
            return $data['result'];//返回eth数量，自己做四舍五入处理
        } else {
            return $data['error']['message'];
        }*/
    }

    /**
     * @author qiuphp2
     * @since 2017-9-21
     * @param $weiNumber 16进制wei单位
     * @return float|int 10进制eth单位【正常单位】
     */
    function fromWei($weiNumber)
    {
        $ethNumber = hexdec($weiNumber) / $this->base;
        return $ethNumber;
    }
    /*
     * 创建交易
     *
     **/
    public function astc_createTrade($host,$data)
    {
        if ($data=='') {
            echo '请传入参数';
            return false;
        }
        $this->host = $host;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->host);
        curl_setopt($ch, CURLOPT_PORT, $this->port);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $ret = curl_exec($ch);
        //返回结果
        if ($ret) {
            curl_close($ch);
            return json_decode($ret, true);
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            //return json_decode('ASTC钱包服务器连接失败', true);
           return false;
            // throw new Exception("curl出错，错误码:$error");
        }
    }

    /*
     * 区块浏览器查询属于平台账号的hight一致的
    */
    public function astc_queryHigInfo($chain)
    {

        if($chain == ''){
            echo '请传入参数';
            return false;
        }
        $data = $chain;
        $this->host = '47.244.52.38:8080/api/V1/OnlyChain';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->host);
        curl_setopt($ch, CURLOPT_PORT, $this->port);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $ret = curl_exec($ch);
        //返回结果
        if ($ret) {
            curl_close($ch);
            return json_decode($ret, true);
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            //return json_decode('ASTC钱包服务器连接失败', true);
            return false;
            // throw new Exception("curl出错，错误码:$error");
        }

    }
}