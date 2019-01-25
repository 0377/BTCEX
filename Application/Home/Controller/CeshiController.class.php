<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/11
 * Time: 17:01
 */
namespace Home\Controller;


require_once APP_REALPATH.'/webms/GatewayClient/Gateway.php';
require_once APP_REALPATH.'/webms/Autoloader.php';
use Workerman\Worker;
class CeshiController extends HomeController
{
    public function index()
    {
        echo 'ceshishuchu';
    }
}