<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/12
 * Time: 12:16
 */
namespace Common\Behavior;

/**
 * 根据Thinkphp自带的CronRunBehavior修改
 * 主要修改内容：TP3.2自带的Cron默认只能加载php文件，但是
 * 这就导致无法使用TP框架本身的给你，所以这里改成 Autoload
 * 加载class，调用class的method执行。
 * @author 小滕
 * @date 2017/6/27
 */

use Think\Log;

class CronRunBehavior
{

    public function run(&$params)
    {

        // 锁定自动执行
        $lockfile    =   RUNTIME_PATH.'cron.lock';
        if(is_writable($lockfile) && filemtime($lockfile) > $_SERVER['REQUEST_TIME'] - C('CRON_MAX_TIME',null,60)) {
            return ;
        } else {
            touch($lockfile);
        }

        set_time_limit(1000);
        ignore_user_abort(true);

        // 载入cron配置文件
        // 格式 return array(
        // 'TaskName' => array(
        //     'class' => 'Home\Task\DemoTask',
        //     'method' => 'run',
        //     'cycle'      => 1, // 单位：秒
        // ),
        // );
        if(is_file(RUNTIME_PATH.'~crons.php')) {
            $crons  =   include RUNTIME_PATH.'~crons.php';
        }elseif(is_file(COMMON_PATH.'Conf/crons.php')){
            $crons  =   include COMMON_PATH.'Conf/crons.php';
        }
        if(isset($crons) && is_array($crons)) {
            $update  =   false;
            $log    =   array();
            foreach ($crons as $key=>$cron){
                if(empty($cron['next_runtime']) || $_SERVER['REQUEST_TIME']>=$cron['next_runtime']) {
                    // 到达时间 执行cron文件
                    G('cronStart');

                    /**  检测类是否存在 */
                    if (! class_exists($cron['class'])) {
                        $this->log('TaskName:'.$key.',ErrMsg:The class not exists.', $cron);
                        continue;
                    }
                    /** 检测方法是否存在 */
                    $object = new $cron['class'];
                    if (! method_exists($object, $cron['method'])) {
                        $this->log('TaskName:'.$key.',ErrMsg:The method not exists.', $cron);
                        continue;
                    }
                    /** 开始运行 */
                    try {
                        $object->$cron['method']();
                    } catch (\Exception $e) {
                        $this->log('TaskName:'.$key.',ErrMsg:' . $e->getMessage() . '.', $cron);
                    }

                    G('cronEnd');
                    $_useTime    =   G('cronStart','cronEnd', 6);
                    // 更新cron记录
                    $cron['next_runtime']   =   $_SERVER['REQUEST_TIME']+$cron['cycle'];
                    $crons[$key]    =   $cron;
                    $log[] = "Cron:$key Runat ".date('Y-m-d H:i:s')." Use $_useTime s\n";
                    $update  =   true;
                }
            }
            if($update) {
                // 记录Cron执行日志
                Log::write(implode('',$log));
                // 更新cron文件
                $content  = "<?php\nreturn ".var_export($crons,true).";\n?>";
                file_put_contents(RUNTIME_PATH.'~crons.php',$content);
            }
        }
        // 解除锁定
        unlink($lockfile);
        return ;
    }

    /**
     * 错误日志记录
     * @param string $message 错误信息
     * @return void
     */
    protected function log($message, $data = [])
    {
        Log::write('CronTask Error.' . $message.'Data:' . json_encode($data));
    }

}