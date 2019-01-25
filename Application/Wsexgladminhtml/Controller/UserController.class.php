<?php
namespace Wsexgladminhtml\Controller;

class UserController extends AdminController
{
	protected function _initialize()
	{
		parent::_initialize();	$allow_action=array("index","createUser","edit","edit2","status","admin","adminEdit","adminStatus","auth","authEdit","authStatus","authStart","authAccess","updateRules","authAccessUp","authUser","authUserAdd","authUserRemove","log","logEdit","logStatus","qianbao","qianbaoEdit","qianbaoStatus","bank","bankEdit","bankStatus","coin","coinEdit","coinFreeze","coinLog","goods","goodsEdit","goodsStatus","setpwd","amountlog","userExcel","loginadmin");
		if(!in_array(ACTION_NAME,$allow_action)){
			$this->error("页面不存在！");
		}
	}

	public function index($name=NULL, $field=NULL, $status=NULL, $idstate=NULL,$date_start=NULL,$date_end=NULL)
	{
	   /* var_dump($date_start);
        var_dump($date_end);*/
		$where = array();
		if ($field && $name) {
			$where[$field] = $name;
		}
		if ($status) {
			$where['status'] = $status - 1;
		}
		/* 状态--条件 */
		if ($idstate) {
			$where['idstate'] = $idstate - 1;
		}
		/* 时间搜索*/
        if (!empty($date_start) && empty($date_end)) {
            $date_start = strtotime($date_start);
            $where['addtime'] = array('EGT',$date_start);

        } else if (empty($date_start) && !empty($date_end)) {
            $date_end = strtotime($date_end);
            $where['addtime'] = array('ELT',$date_end);

        } else if (!empty($date_start) && !empty($date_end)) {
            $date_start = strtotime($date_start);
            $date_end = strtotime($date_end);
            $where['addtime'] =  array(array('EGT',$date_start),array('ELT',$date_end));
            if ($date_start == $date_end){
                $where['addtime'] =  array('EGT',$date_end);
            }
        }
        $where['status'] = array("EGT",0);
        
		// 统计
		$tongji['dsh'] = M('User')->where(array('idstate'=>1))->count();
		$this->assign('tongji', $tongji);
		
		$count = M('User')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();

//		dump($idstate);exit;
		if ($idstate == 2) {
			$list = M('User')->where($where)->order('kyc_lv,id asc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		} else {
			//$list = M('User')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $list = M('User')->where($where)->order("field(idstate,1,8,0,2),addtime desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
            //var_dump($list);
		}

		foreach ($list as $k => $v) {
			$list[$k]['invit_1'] = M('User')->where(array('id' => $v['invit_1']))->getField('username');
			$list[$k]['invit_2'] = M('User')->where(array('id' => $v['invit_2']))->getField('username');
			$list[$k]['invit_3'] = M('User')->where(array('id' => $v['invit_3']))->getField('username');
			//$user_login_state=M('user_log')->where(array('userid'=>$v['id'],'type' => 'login'))->order('id desc')->find();
			//$list[$k]['state']=$user_login_state['state']; // user_log 没有state字段

		}
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	// 新增用户信息
    public function createUser()
    {
        if (empty($_POST)) {
            if (empty($id)) {
                $this->data = array('is_generalize' => 1);
            } else {
                $this->data = M('User')->where(array('id' => trim($id)))->find();
            }

            $areas = M('area')->select();
            $this->assign('areas', $areas);
            $this->display();
        } else {
            if (APP_DEMO) {
                $this->error('测试站暂时不能修改！');
            }

            if ($_POST['paypassword'] != "" && $_POST['password'] == $_POST['paypassword'] && $_POST['password'] !== "") {
                $this->error('交易密码和登录密码一致！');
            }
            if ($_POST['password']) {
                $_POST['password'] = user_md($_POST['password']);
            } else {
                unset($_POST['password']);
            }
            if ($_POST['paypassword']) {
                $_POST['paypassword'] = md5($_POST['paypassword']);
            } else {
                unset($_POST['paypassword']);
            }

            $_POST['mobiletime'] = strtotime($_POST['mobiletime']);
          // $result = M('User')->where(array('username'=>$_POST['id']))->find();
            if(empty($_POST['mobiletime']) && empty($_POST['username'])){
                $this->error('参数不能为空！');
            }
            $_POST['addtime'] = time();
            $mo = M();
            $mo->execute('set autocommit=0');
//                $mo->execute('lock tables tw_user write , tw_user_coin write ');

            $rs = array();
            $rs[] = $mo->table('tw_user')->add($_POST);
            $rs[] = $mo->table('tw_user_coin')->add(array('userid' => $rs[0]));
            $rs[] = crateRecord($rs[0],$coinname='',$type=11,$address='',$remark="后台新建用户");
            if(check_arr($rs)){
                $mo->execute('commit');
//                    $mo->execute('unlock tables');
                $this->success('编辑成功！',U('User/index'));
            } else {
                $mo->execute('rollback');
                $this->error('编辑失败！');
            }

        }
    }




    // 修改
	public function edit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = array('is_generalize'=>1);
			} else {
				$this->data = M('User')->where(array('id' => trim($id)))->find();
			}

            $areas = M('area')->select();
            $this->assign('areas',$areas);
			$this->display();
		} else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			if ($_POST['password']) {
				$_POST['password'] = user_md($_POST['password']);
			} else {
				unset($_POST['password']);
			}
			if ($_POST['paypassword']) {
				$_POST['paypassword'] = md5($_POST['paypassword']);
			} else {
                unset($_POST['paypassword']);
            }

			$_POST['mobiletime'] = strtotime($_POST['mobiletime']);

                if (M('User')->save($_POST)) {
                    $this->success('编辑成功！',U('User/index'));
                } else {
                    $this->error('编辑失败！');
                }
            }

			/*if (empty($result)) {
				$_POST['addtime'] = time();
				$mo = M();
				$mo->execute('set autocommit=0');
				$mo->execute('lock tables tw_user write , tw_user_coin write ');
				$rs = array();
				$rs[] = $mo->table('tw_user')->add($_POST);
				$rs[] = $mo->table('tw_user_coin')->add(array('userid' => $rs[0]));
				if(check_arr($rs)){
					$mo->execute('commit');
					$mo->execute('unlock tables');
					$this->success('新增成功！',U('User/index'));
				} else {
                    $mo->execute('rollback');
					$this->error('新增败！');
				}
			} else {
				if (M('User')->save($_POST)) {
					$this->success('编辑成功！',U('User/index'));
				} else {
					$this->error('编辑失败！');
				}
			}*/

	}

	// 修改用户实名信息
	public function edit2($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = array('is_generalize'=>1);
			} else {
				$this->data = M('User')->where(array('id' => trim($id)))->find();
			}

            $areas = M('area')->select();
            $this->assign('areas',$areas);
			$this->display();
		} else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$_POST['mobiletime'] = strtotime($_POST['mobiletime']);
			
			$_POST['endtime'] = time();
            $time = strtotime('2019-1-7 00:00:00');

            $configs = M('config')->where(array('id' => 1))->find();
            $user_coin = M('UserCoin')->where(array('userid'=>$_POST['id']))->find();
            $user = M('User')->where(array('id'=>$_POST['id']))->find();

            // 开启注册赠送币
            if ($configs['give_type']) {

                // （初级认证通过赠送）
                if($_POST['kyc_lv'] == 1 && $_POST['idstate'] == 2 && $user['addtime'] >$time){

                    $mo = M();
                    $mo->startTrans();
                    $rs = array();
                    try{
                        $coin_name = C('xnb_mr_song'); //赠送币种类型
                        $give_num = C('xnb_mr_song_num'); // 赠送注册用户币种数量

                        $rs[] = M('UserCoin')->where(array('userid'=>$_POST['id']))->setInc($coin_name, $give_num); // 修改金额
                        $sum_num = $user_coin[$coin_name] + $give_num; // 更新后的金额

                        // optype=27 注册赠送类型 'cointype' => 9资金类型-赠送的hrc 'plusminus' => 1增加类型
                        $rs[] =  M('FinanceLog')->add(array('username' => $user['username'], 'adminname' => session('admin_username'), 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $give_num, 'optype' => 27, 'cointype' => 9, 'old_amount' => $user_coin[$coin_name], 'new_amount' => $sum_num, 'userid' => $_POST['id'], 'adminid' => session('admin_id'),'addip'=>get_client_ip()));

                        $invit_1 = $user['invit_1'];
                        $invit_2 = $user['invit_2'];
                        $invit_3 = $user['invit_3'];

                        if(C('song_num_1') > 0 && $invit_1 > 0) {
                             $coin_num_1 = C('song_num_1'); // 赠送数量
                             $rs[] = $mo->table('tw_invit')->add(array('userid' =>$invit_1, 'invit' => $rs[0], 'name' => '一代注册赠送', 'type' => '注册赠送'.strtoupper($coin_name), 'num' => 0, 'mum' => 0, 'fee' => $coin_num_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name)));

                             $user_info = M('User')->where(array('id' => $invit_1))->find();
                             $user_coinInfo = M('UserCoin')->where(array('userid' => $invit_1))->find();

                             $sum_money[$coin_name] = $user_coinInfo[$coin_name] + $coin_num_1;
                             $rs[] = $mo->table('tw_user_coin')->where(array('userid'=>$invit_1))->save($sum_money);
                             // optype=30 一代注册赠送类型 'cointype' => 9资金类型-赠送的hrc 'plusminus' => 1增加类型
                             $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_info['username'], 'adminname' => session('admin_username'), 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $coin_num_1, 'optype' => 30, 'cointype' => 9, 'old_amount' => $user_coinInfo[$coin_name], 'new_amount' => $sum_money[$coin_name], 'userid' => $user_coin, 'adminid' => session('admin_id'),'addip'=>get_client_ip()));

                        }
                        if(C('song_num_2') > 0 && $invit_2 > 0) {
                            $coin_num_1 = C('song_num_2'); // 赠送数量
                            $rs[] = $mo->table('tw_invit')->add(array('userid' =>$invit_1, 'invit' => $rs[0], 'name' => '二代注册赠送', 'type' => '注册赠送'.strtoupper($coin_name), 'num' => 0, 'mum' => 0, 'fee' => $coin_num_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name)));

                            $user_info = M('User')->where(array('id' => $invit_2))->find();
                            $user_coinInfo = M('UserCoin')->where(array('userid' => $invit_2))->find();

                            $sum_money[$coin_name] = $user_coinInfo[$coin_name] + $coin_num_1;
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid'=>$invit_2))->save($sum_money);
                            // optype=30 一代注册赠送类型 'cointype' => 9资金类型-赠送的hrc 'plusminus' => 1增加类型
                            $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_info['username'], 'adminname' => session('admin_username'), 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $coin_num_1, 'optype' => 31, 'cointype' => 9, 'old_amount' => $user_coinInfo[$coin_name], 'new_amount' => $sum_money[$coin_name], 'userid' => $user_coin, 'adminid' => session('admin_id'),'addip'=>get_client_ip()));

                        }
                        if(C('song_num_3') > 0 && $invit_3 > 0) {
                            $coin_num_1 = C('song_num_3'); // 赠送数量
                            $rs[] = $mo->table('tw_invit')->add(array('userid' =>$invit_3, 'invit' => $rs[0], 'name' => '三代注册赠送', 'type' => '注册赠送'.strtoupper($coin_name), 'num' => 0, 'mum' => 0, 'fee' => $coin_num_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name)));

                            $user_info = M('User')->where(array('id' => $invit_3))->find();
                            $user_coinInfo = M('UserCoin')->where(array('userid' => $invit_3))->find();

                            $sum_money[$coin_name] = $user_coinInfo[$coin_name] + $coin_num_1;
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid'=>$invit_3))->save($sum_money);
                            // optype=30 一代注册赠送类型 'cointype' => 9资金类型-赠送的hrc 'plusminus' => 1增加类型
                            $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_info['username'], 'adminname' => session('admin_username'), 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $coin_num_1, 'optype' => 32, 'cointype' => 9, 'old_amount' => $user_coinInfo[$coin_name], 'new_amount' => $sum_money[$coin_name], 'userid' => $user_coin, 'adminid' => session('admin_id'),'addip'=>get_client_ip()));

                        }

                        if (check_arr($rs)) {
                            $mo->commit();
                            if (M('User')->save($_POST)) {
                                $this->success('编辑成功！',U('User/index'));
                            } else {
                                $this->error('编辑失败！');
                            }

                        } else {
                            $mo->rollback();
                            $this->error('数据写入失败！');

                        }

                    }catch(\Think\Exception $e){
                        $mo->rollback();
                        $this->error('数据写入失败！');
                    }

                 }
            }

            if (M('User')->save($_POST)) {
                $this->success('编辑成功！',U('User/index'));
            } else {
                $this->error('编辑失败！');
            }

		}



	}


	public function status($id = NULL, $type = NULL, $mobile = 'User')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}
		if (empty($type)) {
			$this->error('参数错误1！');
		}
		
		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);
		$where1['userid'] = array('in', $id);
		$mobile_coin = $mobile.'_coin';
		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;
				
		case 'idauth':
			$data = array('idstate' => 2, 'idcardinfo' => '', 'endtime' => time());
			break;

		case 'notidauth':
			$data = array('idstate' => 8, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			if (M($mobile)->where($where)->delete()&&M($mobile_coin)->where($where1)->delete()) {
				$this->success('操作成功！');
			} else {
				$this->error('操作失败！');
			}
			break;

		default:
			$this->error('操作失败！');
		}
		if($type == 'idauth'){

            $time = strtotime('2019-1-7 00:00:00');
            $configs = M('config')->where(array('id' => 1))->find();
            $user_coin = M('UserCoin')->where($where1)->find();
            $user = M('User')->where($where)->find();

            // 开启注册赠送币
            if ($configs['give_type'] && $user['kyc_lv']<=1) {
                // （初级认证通过赠送）
                    $mo = M();
                    $mo->startTrans();
                    $rs = array();
                    try{
                        $coin_name = C('xnb_mr_song'); //赠送币种类型
                        $give_num = C('xnb_mr_song_num'); // 赠送注册用户币种数量

                        $rs[] = M('UserCoin')->where($where1)->setInc($coin_name, $give_num); // 修改金额
                        $sum_num = $user_coin[$coin_name] + $give_num; // 更新后的金额

                        // optype=27 注册赠送类型 'cointype' => 9资金类型-赠送的hrc 'plusminus' => 1增加类型
                        $rs[] =  M('FinanceLog')->add(array('username' => $user['username'], 'adminname' => session('admin_username'), 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $give_num, 'optype' => 27, 'cointype' => 9, 'old_amount' => $user_coin[$coin_name], 'new_amount' => $sum_num, 'userid' => $_POST['id'], 'adminid' => session('admin_id'),'addip'=>get_client_ip()));

                        $invit_1 = $user['invit_1'];
                        $invit_2 = $user['invit_2'];
                        $invit_3 = $user['invit_3'];

                        if(C('song_num_1') > 0 && $invit_1 > 0) {
                            $coin_num_1 = C('song_num_1'); // 赠送数量
                            $rs[] = $mo->table('tw_invit')->add(array('userid' =>$invit_1, 'invit' => $rs[0], 'name' => '一代注册赠送', 'type' => '注册赠送'.strtoupper($coin_name), 'num' => 0, 'mum' => 0, 'fee' => $coin_num_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name)));

                            $user_info = M('User')->where(array('id' => $invit_1))->find();
                            $user_coinInfo = M('UserCoin')->where(array('userid' => $invit_1))->find();

                            $sum_money[$coin_name] = $user_coinInfo[$coin_name] + $coin_num_1;
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid'=>$invit_1))->save($sum_money);

                            // optype=30 一代注册赠送类型 'cointype' => 9资金类型-赠送的hrc 'plusminus' => 1增加类型
                            $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_info['username'], 'adminname' => session('admin_username'), 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $coin_num_1, 'optype' => 30, 'cointype' => 9, 'old_amount' => $user_coinInfo[$coin_name], 'new_amount' => $sum_money[$coin_name], 'userid' => $user_coin, 'adminid' => session('admin_id'),'addip'=>get_client_ip()));

//                            $rs[] = $mo->table('tw_user')->where($where)->save($data);
                        }
                        if(C('song_num_2') > 0 && $invit_2 > 0) {
                            $coin_num_1 = C('song_num_2'); // 赠送数量
                            $rs[] = $mo->table('tw_invit')->add(array('userid' =>$invit_2, 'invit' => $rs[0], 'name' => '二代注册赠送', 'type' => '注册赠送'.strtoupper($coin_name), 'num' => 0, 'mum' => 0, 'fee' => $coin_num_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name)));

                            $user_info = M('User')->where(array('id' => $invit_2))->find();
                            $user_coinInfo = M('UserCoin')->where(array('userid' => $invit_2))->find();

                            $sum_money[$coin_name] = $user_coinInfo[$coin_name] + $coin_num_1;
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid'=>$invit_2))->save($sum_money);

                            // optype=30 一代注册赠送类型 'cointype' => 9资金类型-赠送的hrc 'plusminus' => 1增加类型
                            $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_info['username'], 'adminname' => session('admin_username'), 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $coin_num_1, 'optype' => 30, 'cointype' => 9, 'old_amount' => $user_coinInfo[$coin_name], 'new_amount' => $sum_money[$coin_name], 'userid' => $user_coin, 'adminid' => session('admin_id'),'addip'=>get_client_ip()));

                        }
                        if(C('song_num_3') > 0 && $invit_3 > 0) {
                            $coin_num_1 = C('song_num_2'); // 赠送数量
                            $rs[] = $mo->table('tw_invit')->add(array('userid' =>$invit_3, 'invit' => $rs[0], 'name' => '二代注册赠送', 'type' => '注册赠送'.strtoupper($coin_name), 'num' => 0, 'mum' => 0, 'fee' => $coin_num_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name)));

                            $user_info = M('User')->where(array('id' => $invit_3))->find();
                            $user_coinInfo = M('UserCoin')->where(array('userid' => $invit_3))->find();

                            $sum_money[$coin_name] = $user_coinInfo[$coin_name] + $coin_num_1;
                            $rs[] = $mo->table('tw_user_coin')->where(array('userid'=>$invit_3))->save($sum_money);
//
                            // optype=30 一代注册赠送类型 'cointype' => 9资金类型-赠送的hrc 'plusminus' => 1增加类型
                            $rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_info['username'], 'adminname' => session('admin_username'), 'addtime' => time(), 'plusminus' => 1, 'amount' =>  $coin_num_1, 'optype' => 30, 'cointype' => 9, 'old_amount' => $user_coinInfo[$coin_name], 'new_amount' => $sum_money[$coin_name], 'userid' => $user_coin, 'adminid' => session('admin_id'),'addip'=>get_client_ip()));
                        }
                        if (check_arr($rs)) {
                            $rs[] = $mo->table('tw_user')->where($where)->save($data);
                            $mo->commit();
                            $this->success('操作成功！');

                        } else {
                            $mo->rollback();
                            $this->error('数据写入失败！');

                        }

                    }catch(\Think\Exception $e){
                        $mo->rollback();
                        $this->error('数据写入失败！');
                    }


            }
        }
		
		/*if ($type == 'idauth') {
			// 注册奖励模块
			$datas = M('User')->where($where)->find();
			$configs = M('config')->where(array('id' => 1))->find();
			
			$ids = $datas['id'];
			$invit_1 = $datas['invit_1'];
			$invit_2 = $datas['invit_2'];
			$invit_3 = $datas['invit_3'];
			
			if($datas['idstate'] == 8){}
			else
			{
				if($datas['kyc_lv'] == 2 || $datas['idstate'] == 2){}
				else if($datas['kyc_lv'] == 0 || $datas['kyc_lv'] == 1)
				{
					//注册赠送币
					if ($configs['give_type'] == 1) {

						$mo = M();
						$mo->execute('set autocommit=0');
//						$mo->execute('lock tables tw_user write, tw_user_coin write, tw_invit write, tw_finance_log write');

						$rs = array();

						// 数据未处理时的查询（原数据）
						$finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $ids))->find();
						// 用户账户数据处理
						$coin_name = $configs['xnb_mr_song']; //赠送币种
						$song_num =  $configs['xnb_mr_song_num']; //赠送数量
                        $result = queryUserCoin($userid=$where1,$coin_name);
						$rs[] = $mo->table('tw_user_coin')->where($where1)->setInc($coin_name, $song_num); // 修改金额
                        // 查询未操作的记录
                        $info = queryUserCoin($userid=$where1,$coin_name);
                        //user_coin 操作写入表
//                        $rs[] = crateRecord($userid=$where1,$type=13,$coin_name,$address='',$remark="注册赠送币",$result[$coin_name],$info[$coin_name]);
						// 数据处理完的查询（新数据）
						$finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => $ids))->find();

						// optype=1 充值类型 'cointype' => 1人民币类型 'plusminus' => 1增加类型
						$rs[] = $mo->table('tw_finance_log')->add(array('username' => $datas['username'], 'adminname' => session('admin_username'), 'addtime' => time(), 'plusminus' => 1, 'amount' => $song_num, 'description' => '注册赠送', 'optype' => 27, 'cointype' => 3, 'old_amount' => $finance_num_user_coin[$coin_name], 'new_amount' => $finance_mum_user_coin[$coin_name], 'userid' => $datas['id'], 'adminid' => session('admin_id'),'addip'=>get_client_ip()));


						// 赠送邀请人邀请奖励
						if($configs['song_num_1'] > 0 && $invit_1 > 0){
							$coin_num_1 = $configs['song_num_1'];
							$rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_1, 'invit' => $ids, 'name' => '一代注册赠送', 'type' => '注册赠送'.strtoupper($coin_name), 'num' => 0, 'mum' => 0, 'fee' => $coin_num_1, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name)));
						}
						if($configs['song_num_2'] > 0 && $invit_2 > 0){
							$coin_num_2 = $configs['song_num_2'];
							$rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_2, 'invit' => $ids, 'name' => '二代注册赠送', 'type' => '注册赠送'.strtoupper($coin_name), 'num' => 0, 'mum' => 0, 'fee' => $coin_num_2, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name)));
						}
						if($configs['song_num_3'] > 0 && $invit_3 > 0){
							$coin_num_3 = $configs['song_num_3'];
							$rs[] = $mo->table('tw_invit')->add(array('userid' => $invit_3, 'invit' => $ids, 'name' => '三代注册赠送', 'type' => '注册赠送'.strtoupper($coin_name), 'num' => 0, 'mum' => 0, 'fee' => $coin_num_3, 'addtime' => time(), 'status' => 0,'coin'=>strtoupper($coin_name)));
						}

						$rs[] = $mo->table('tw_user')->where($where)->save($data);

						if (check_arr($rs)) {
							$mo->execute('commit');
//							$mo->execute('unlock tables');
							return $this->success('操作成功！');
						} else {
							$mo->execute('rollback');
							return $this->error('操作失败！');
						}
					}
				}
			}
		}*/
		if (M($mobile)->where($where)->save($data)) {
			$this->success('操作成功！');
		} else {
			$this->error('操作失败！');
		}
	}

	public function admin($name = NULL, $field = NULL, $status = NULL)
	{
		$DbFields = M('Admin')->getDbFields();


		if (!in_array('email', $DbFields)) {
			M()->execute('ALTER TABLE `tw_admin` ADD COLUMN `email` VARCHAR(200)  NOT NULL   COMMENT \'\' AFTER `id`;');
		}

		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			} else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}
        $where['status'] = array("EGT",0);
		$count = M('Admin')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('Admin')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	// 修改管理员信息
	public function adminEdit()
	{
		if (empty($_POST)) {
			if (empty($_GET['id'])) {
				$this->data = null;
			} else {
				$this->data = M('Admin')->where(array('id' => trim($_GET['id'])))->find();
			}

			$this->display();
		} else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$input = I('post.');

			if (!check($input['username'], 'username')) {
				//$this->error('用户名格式错误！');
			}
			if ($input['nickname'] && !check($input['nickname'], 'A')) {
				$this->error('昵称格式错误！');
			}
			if ($input['password'] && !check($input['password'], 'password')) {
				$this->error('登录密码格式错误！');
			}
			if ($input['mobile'] && !check($input['mobile'], 'mobile')) {
				$this->error('手机号码格式错误！');
			}
			if ($input['email'] && !check($input['email'], 'email')) {
				$this->error('邮箱格式错误！');
			}

			if ($input['password']) {
				$input['password'] = admin_md($input['password']);
			} else {
				unset($input['password']);
			}

			if ($_POST['id']) {
				$rs = M('Admin')->save($input);
			} else {
				$_POST['addtime'] = time();
				$rs = M('Admin')->add($input);
			}

			if ($rs) {
				$this->success('编辑成功！',U('User/admin'));
			} else {
				$this->error('编辑失败！');
			}
		}
	}

	public function adminStatus($id = NULL, $type = NULL, $mobile = 'Admin')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}
		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			/*if (M($mobile)->where($where)->delete()) {
				$this->success('操作成功！');
			} else {
				$this->error('操作失败！');
			}*/
			$data = array('status' => -1);
			break;

		default:
			$this->error('操作失败！');
		}

		if (M($mobile)->where($where)->save($data)) {
			$this->success('操作成功！');
		} else {
			$this->error('操作失败！');
		}
	}

	public function auth()
	{
		$list = $this->lists('AuthGroup', array('module' => 'admin'), 'id asc');
		$list = int_to_string($list);
		$this->assign('_list', $list);
		$this->assign('_use_tip', true);
		$this->meta_title = '权限管理';
		$this->display();
	}

	public function authEdit()
	{
		if (empty($_POST)) {
			if (empty($_GET['id'])) {
				$this->data = null;
			} else {
				$this->data = M('AuthGroup')->where(array('module' => 'admin', 'type' => \Common\Model\AuthGroupModel::TYPE_ADMIN))->find((int) $_GET['id']);
			}

			$this->display();
		} else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			if (isset($_POST['rules'])) {
				sort($_POST['rules']);
				$_POST['rules'] = implode(',', array_unique($_POST['rules']));
			}

			$_POST['module'] = 'admin';
			$_POST['type'] = \Common\Model\AuthGroupModel::TYPE_ADMIN;
			$AuthGroup = D('AuthGroup');
			$data = $AuthGroup->create();

			if ($data) {
				if (empty($data['id'])) {
					$r = $AuthGroup->add();
				} else {
					$r = $AuthGroup->save();
				}

				if ($r === false) {
					$this->error('操作失败' . $AuthGroup->getError());
				} else {
					$this->success('操作成功!',U('User/auth'));
				}
			} else {
				$this->error('操作失败' . $AuthGroup->getError());
			}
		}
	}

	public function authStatus($id = NULL, $type = NULL, $mobile = 'AuthGroup')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}
		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			if (M($mobile)->where($where)->delete()) {
				$this->success('操作成功！');
			} else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('操作失败！');
		}

		if (M($mobile)->where($where)->save($data)) {
			$this->success('操作成功！');
		} else {
			$this->error('操作失败！');
		}
	}

	public function authStart()
	{
		if (M('AuthRule')->where(array('status' => 1))->delete()) {
			$this->success('操作成功！');
		} else {
			$this->error('操作失败！');
		}
	}

	public function authAccess()
	{
		$this->updateRules();
		$auth_group = M('AuthGroup')->where(array(
			'status' => array('egt', '0'),
			'module' => 'admin',
			'type'   => \Common\Model\AuthGroupModel::TYPE_ADMIN
		))->getfield('id,id,title,rules');
		
		$node_list = $this->returnNodes();
		$map = array('module' => 'admin', 'type' => \Common\Model\AuthRuleModel::RULE_MAIN, 'status' => 1);
		$main_rules = M('AuthRule')->where($map)->getField('name,id');
		$map = array('module' => 'admin', 'type' => \Common\Model\AuthRuleModel::RULE_URL, 'status' => 1);
		$child_rules = M('AuthRule')->where($map)->getField('name,id');
		$this->assign('main_rules', $main_rules);
		$this->assign('auth_rules', $child_rules);
		$this->assign('node_list', $node_list);
		$this->assign('auth_group', $auth_group);
		$this->assign('this_group', $auth_group[(int) $_GET['group_id']]);
		$this->meta_title = '访问授权';
		$this->display();
	}

	protected function updateRules()
	{
		$nodes = $this->returnNodes(false);
		$AuthRule = M('AuthRule');
		$map = array(
			'module' => 'admin',
			'type'   => array('in', '1,2')
		);
		$rules = $AuthRule->where($map)->order('name')->select();
		$data = array();

		foreach ($nodes as $value) {
			$temp['name'] = $value['url'];
			$temp['title'] = $value['title'];
			$temp['module'] = 'admin';

			if (0 < $value['pid']) {
				$temp['type'] = \Common\Model\AuthRuleModel::RULE_URL;
			} else {
				$temp['type'] = \Common\Model\AuthRuleModel::RULE_MAIN;
			}

			$temp['status'] = 1;
			$data[strtolower($temp['name'] . $temp['module'] . $temp['type'])] = $temp;
		}

		$update = array();
		$ids = array();

		foreach ($rules as $index => $rule) {
			$key = strtolower($rule['name'] . $rule['module'] . $rule['type']);
			if (isset($data[$key])) {
				$data[$key]['id'] = $rule['id'];
				$update[] = $data[$key];
				unset($data[$key]);
				unset($rules[$index]);
				unset($rule['condition']);
				$diff[$rule['id']] = $rule;
			} else if ($rule['status'] == 1) {
				$ids[] = $rule['id'];
			}
		}

		if (count($update)) {
			foreach ($update as $k => $row) {
				if ($row != $diff[$row['id']]) {
					$AuthRule->where(array('id' => $row['id']))->save($row);
				}
			}
		}

		if (count($ids)) {
			$AuthRule->where(array(
				'id' => array('IN', implode(',', $ids))
			))->save(array('status' => -1));
		}

		if (count($data)) {
			$AuthRule->addAll(array_values($data));
		}

		if ($AuthRule->getDbError()) {
			trace('[' . 'Admin\\Controller\\UserController::updateRules' . ']:' . $AuthRule->getDbError());
			return false;
		} else {
			return true;
		}
	}

	public function authAccessUp()
	{
		if (isset($_POST['rules'])) {
			sort($_POST['rules']);
			$_POST['rules'] = implode(',', array_unique($_POST['rules']));
		}

		$_POST['module'] = 'admin';
		$_POST['type'] = \Common\Model\AuthGroupModel::TYPE_ADMIN;
		$AuthGroup = D('AuthGroup');
		$data = $AuthGroup->create();

		if ($data) {
			if (empty($data['id'])) {
				$r = $AuthGroup->add();
			} else {
				$r = $AuthGroup->save();
			}
			if ($r === false) {
				$this->error('操作失败' . $AuthGroup->getError());
			} else {
				$this->success('操作成功!');
			}
		} else {
			$this->error('操作失败' . $AuthGroup->getError());
		}
	}

	public function authUser($group_id)
	{
		if (empty($group_id)) {
			$this->error('参数错误');
		}

		$auth_group = M('AuthGroup')->where(array(
			'status' => array('egt', '0'),
			'module' => 'admin',
			'type'   => \Common\Model\AuthGroupModel::TYPE_ADMIN
		))->getfield('id,id,title,rules');
		$prefix = C('DB_PREFIX');
		$l_table = $prefix . \Common\Model\AuthGroupModel::MEMBER;
		$r_table = $prefix . \Common\Model\AuthGroupModel::AUTH_GROUP_ACCESS;
		$model = M()->table($l_table . ' m')->join($r_table . ' a ON m.id=a.uid');
		$_REQUEST = array();
		$list = $this->lists($model, array(
			'a.group_id' => $group_id,
			'm.status'   => array('egt', 0)
			), 'm.id asc', null, 'm.id,m.username,m.nickname,m.last_login_time,m.last_login_ip,m.status');
		int_to_string($list);
		$this->assign('_list', $list);
		$this->assign('auth_group', $auth_group);
		$this->assign('this_group', $auth_group[(int) $_GET['group_id']]);
		$this->meta_title = '成员授权';
		$this->display();
	}

	public function authUserAdd()
	{
		$uid = I('uid');

		if (empty($uid)) {
			$this->error('请输入后台成员信息');
		}

		if (!check($uid, 'd')) {
			$user = M('Admin')->where(array('username' => $uid))->find();
			if (!$user) {
				$user = M('Admin')->where(array('nickname' => $uid))->find();
			}
			if (!$user) {
				$user = M('Admin')->where(array('mobile' => $uid))->find();
			}
			if (!$user) {
				$this->error('用户不存在(id 用户名 昵称 手机号均可)');
			}
			$uid = $user['id'];
		}

		$gid = I('group_id');

		if ($res = M('AuthGroupAccess')->where(array('uid' => $uid))->find()) {
			if ($res['group_id'] == $gid) {
				$this->error('已经存在,请勿重复添加');
			} else {
				$res = M('AuthGroup')->where(array('id' => $gid))->find();
				if (!$res) {
					$this->error('当前组不存在');
				}
				$this->error('已经存在[' . $res['title'] . ']组,不可重复添加');
			}
		}

		$AuthGroup = D('AuthGroup');

		if (is_numeric($uid)) {
			if (is_administrator($uid)) {
				$this->error('该用户为超级管理员');
			}
			if (!M('Admin')->where(array('id' => $uid))->find()) {
				$this->error('管理员用户不存在');
			}
		}

		if ($gid && !$AuthGroup->checkGroupId($gid)) {
			$this->error($AuthGroup->error);
		}
		if ($AuthGroup->addToGroup($uid, $gid)) {
			$this->success('操作成功');
		} else {
			$this->error($AuthGroup->getError());
		}
	}

	public function authUserRemove()
	{
		$uid = I('uid');
		$gid = I('group_id');

		if ($uid == UID) {
			$this->error('不允许解除自身授权');
		}
		if (empty($uid) || empty($gid)) {
			$this->error('参数有误');
		}

		$AuthGroup = D('AuthGroup');
		if (!$AuthGroup->find($gid)) {
			$this->error('用户组不存在');
		}

		if ($AuthGroup->removeFromGroup($uid, $gid)) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	public function log($name = NULL, $field = NULL, $status = NULL)
	{
		$where = array();
		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			} else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		$count = M('UserLog')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserLog')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function logEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			} else {
				$this->data = M('UserLog')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		} else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$_POST['addtime'] = strtotime($_POST['addtime']);
			$res = M('UserLog')->save($_POST);
			if ($res) {
				$this->success('编辑成功！');
			} else {
                $result = M('UserLog')->add($_POST);
                if($result){
                    $this->success('新增成功！');
                }else{
                    $this->error('新增失败！');
                }
				$this->error('编辑失败！');
			}
		}
	}

	public function logStatus($id = NULL, $type = NULL, $mobile = 'UserLog')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}
		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			if (M($mobile)->where($where)->delete()) {
				$this->success('操作成功！');
			}
			else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('操作失败！');
		}

		if (M($mobile)->where($where)->save($data)) {
			$this->success('操作成功！');
		} else {
			$this->error('操作失败！');
		}
	}

	public function qianbao($name = NULL, $field = NULL, $coinname = NULL, $status = NULL)
	{
		$where = array();
        $where['status'] = array("EGT",0);
		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			} else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}
		if ($coinname) {
			$where['coinname'] = trim($coinname);
		}

		$count = M('UserQianbao')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserQianbao')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	// 用户钱包管理
	public function qianbaoEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			} else {
				$this->data = M('UserQianbao')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		} else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}
			$_POST['addtime'] = strtotime($_POST['addtime']);

			if (M('UserQianbao')->save($_POST)) {
				$this->success('编辑成功！',U('User/qianbao'));
			} else {
                if( $result = M('UserQianbao')->add($_POST)){
                    $this->success('新增成功！',U('User/qianbao'));
                }else{
                    $this->error('新增失败！');
                }
				$this->error('编辑失败！');
			}
		}
	}

	public function qianbaoStatus($id = NULL, $type = NULL, $mobile = 'UserQianbao')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}
		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			/*if (M($mobile)->where($where)->delete()) {
				$this->success('操作成功！');
			} else {
				$this->error('操作失败！');
			}*/
            $data = array('status' => -1);
			break;

		default:
			$this->error('操作失败！');
		}

		if (M($mobile)->where($where)->save($data)) {
			$this->success('操作成功！');
		} else {
			$this->error('操作失败！');
		}
	}

	public function bank($name = NULL, $field = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			} else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}
        $where['status'] = array("EGT",0);
		$count = M('UserBank')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserBank')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function bankEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			} else {
				$this->data = M('UserBank')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		} else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$_POST['addtime'] = strtotime($_POST['addtime']);
          $res =  M('UserBank')->save($_POST);
           if($res){
               $this->success('编辑成功！',U('User/bank'));
           }else{
               $result =  M('UserBank')->add($_POST);
               if($result){
                   $this->success('新增成功！',U('User/bank'));
               }else{
                   $this->error('新增失败！');
               }
               $this->error('编辑失败！');
            }
			/*if (M('UserBank')->save($_POST)) {
				$this->success('编辑成功！');
			} else {
				$this->error('编辑失败！');
			}*/
		}
	}

	public function bankStatus($id = NULL, $type = NULL, $mobile = 'UserBank')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}
		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'del':
			$data = array('status' => -1);
			break;

		case 'delete':
			if (M($mobile)->where($where)->delete()) {
				$this->success('操作成功！');
			} else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('操作失败！');
		}

		if (M($mobile)->where($where)->save($data)) {
			$this->success('操作成功！');
		} else {
			$this->error('操作失败！');
		}
	}

	public function coin($name = NULL, $field = NULL)
	{

		$where = array();
		$coin_type = I('coin_type');
		$coin_type_val = I('coin_type_val');
		$num_start = I('num_start');
		$num_stop = I('num_stop');
		if($coin_type_val=='sum'){
            $is_sum = true;
            $coin_typed = $coin_type.'d';
        }elseif($coin_type_val=='disable'){
            $coin_type = $coin_type.'d';
        }
        $where = 'id>0';
		if ($field && $name) {
			if ($field == 'username') {
//				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
                $userid = M('User')->where(array('username' => $name))->getField('id');
				$where .= " and userid=$userid";
			} else {
//				$where[$field] = $name;
                $where .= " and $field = $name";
			}
		}
		if($coin_type && ($num_start || $num_stop)){
		    $num_start = $num_start!=''?$num_start:0;
		    $num_stop = $num_stop!=''?$num_stop:0;
//            $where[$coin_type][] = array('gt',$num_start);

            if($num_stop && !$is_sum) {
//                $where[$coin_type][] = array('lt',$num_stop);
                $where .= " and $coin_type>$num_start";
                $where .= " and $coin_type<$num_stop";
            }elseif (!$num_stop && !$is_sum){
                $where .= " and $coin_type>$num_start";
            }

            if($is_sum && $num_stop){
//                unset($where[$coin_type]);
                $where .= " and $coin_type+$coin_typed > $num_start";
                $where .= " AND $coin_type+$coin_typed < $num_stop";
//                $where1[$coin_type.'+'.$coin_typed][] = array('lt',$num_stop);
            }elseif($is_sum && !$num_stop){
                $where .= " and $coin_type+$coin_typed > $num_start";
            }
        }
		$count = M('UserCoin')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserCoin')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
//		if($is_sum){
//            $count = M('UserCoin')->where($where1)->count();
//            $list = M('UserCoin')->where($where1)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
//        }
//dump(M()->getLastSql());exit;
		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	// 编辑用户财产
	public function coinEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			} else {
				$this->data = M('UserCoin')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		} else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			try{

				$mo = M();
				$mo->execute('set autocommit=0');
//				$mo->execute('lock tables tw_user_coin write ,tw_finance_log write ,tw_coin read ,tw_user read');

				// 获取该用户信息
				$user_coin_info = $mo->table('tw_user_coin')->where(array('id' => $_POST['id']))->find();
				$user_info = $mo->table('tw_user')->where(array('id' => $user_coin_info['userid']))->find();
				$coin_list = $mo->table('tw_coin')->where(array('status' => 1))->select();

				$rs = array();

				foreach ($coin_list as $k => $v) {
					// 判断那些币种账户发生变化
					if($user_coin_info[$v['name']] != $_POST[$v['name']]){
						// 账户数目减少---0减少1增加
						if($user_coin_info[$v['name']] > $_POST[$v['name']]){
							$plusminus = 0;
						} else {
							$plusminus = 1;
						}

						$amount = abs($user_coin_info[$v['name']] - $_POST[$v['name']]);

						$rs[] = $mo->table('tw_finance_log')->add(array('username' => $user_info['username'], 'adminname' => session('admin_username'), 'addtime' => time(), 'plusminus' => $plusminus, 'amount' => $amount, 'optype' => 3, 'cointype' => $v['id'], 'old_amount' => $user_coin_info[$v['name']], 'new_amount' => $_POST[$v['name']], 'userid' => $user_info['id'], 'adminid' => session('admin_id'),'addip'=>get_client_ip()));
						// user_coin 操作记录
                        $rs[] = crateRecord($user_coin_info['userid'],$v['name'],12,$address='',$remark='修改用户金额',$_POST[$v['name']],$user_coin_info[$v['name']]);
					}

				}

				// 更新用户账户数据
				$rs[] = $mo->table('tw_user_coin')->save($_POST);
				if (check_arr($rs)) {
					$mo->execute('commit');
//					$mo->execute('unlock tables');
				} else {
					throw new \Think\Exception('编辑失败！');
				}
				$this->success('编辑成功！',U('User/coin'));

			} catch(\Think\Exception $e) {
				$mo->execute('rollback');
//				$mo->execute('unlock tables');
				$this->error('编辑失败！');
			}
			// if (M('UserCoin')->save($_POST)) {
			// 	$this->success('编辑成功！');
			// }
			// else {
			// 	$this->error('编辑失败！');
			// }
		}
	}
	
    public function coinFreeze($id = NULL)
    {
        if (empty($_POST)) {
            if (empty($id)) {
                $this->data = null;
            } else {
                $this->data = M('UserCoin')->where(array('id' => trim($id)))->find();
            }
            $this->display();
        } else {
            if (APP_DEMO) {
                $this->error('测试站暂时不能修改！');
            }
            try{
                $mo = M();
                $mo->execute('set autocommit=0');
//                $mo->execute('lock tables tw_user_coin write ,tw_finance_log write ,tw_coin read ,tw_user read');
                // 获取该用户信息
                $user_coin_info = $mo->table('tw_user_coin')->where(array('id' => $_POST['id']))->find();
                $user_info = $mo->table('tw_user')->where(array('id' => $user_coin_info['userid']))->find();
                $coin_list = $mo->table('tw_coin')->where(array('status' => 1))->select();
                $rs = array();
                $data = array('id'=>$_POST['id']);
                foreach ($coin_list as $k => $v) {
                    // 判断那些币种账户发生变化
                    if($_POST[$v['name']]!=0){
						// 账户数目减少---0减少1增加
                        if($user_coin_info[$v['name']] > $_POST[$v['name']]){
                            $plusminus = 0;
                        } else {
                            $plusminus = 1;
                        }
                        $data[$v['name']] = $user_coin_info[$v['name']]-$_POST[$v['name']];
                        $data[$v['name'].'d'] = $user_coin_info[$v['name'].'d']+$_POST[$v['name']];
                        $amount = abs($_POST[$v['name']]);
                        $rs[] = $mo->table('tw_finance_log')->add(array(
                            'username' => $user_info['username'],
                            'adminname' => session('admin_username'),
                            'addtime' => time(),
                            'plusminus' => $plusminus,
                            'description'=>'管理手动'.($_POST[$v['name']]>0?'冻结':'解冻'),
                            'amount' => $amount,
                            'optype' => 3,
                            'cointype' => $v['id'],
                            'old_amount' => $user_coin_info[$v['name']],
                            'new_amount' => $data[$v['name']],
                            'userid' => $user_info['id'],
                            'adminid' => session('admin_id'),
                            'addip'=>get_client_ip()));
                    }
                    // user_coin 操作记录
                    $rs[] = crateRecord($user_info['id'],$v['name'],14,$address='',$remark='冻结或解冻用户账户',$data[$v['name']],$user_coin_info[$v['name']]);
                }

                // 更新用户账户数据
                $rs[] = $mo->table('tw_user_coin')->save($data);
                if (check_arr($rs)) {
                    $mo->execute('commit');
//                    $mo->execute('unlock tables');
                } else {
                    throw new \Think\Exception('编辑失败！');
                }
                $this->success('编辑成功！');
            }catch(\Think\Exception $e){
                $mo->execute('rollback');
//                $mo->execute('unlock tables');
                $this->error('编辑失败！');
            }
        }
    }

	public function coinLog($userid = NULL, $coinname = NULL)
	{
		$data['userid'] = $userid;
		$data['username'] = M('User')->where(array('id' => $userid))->getField('username');
		$data['coinname'] = $coinname;
		$data['zhengcheng'] = M('UserCoin')->where(array('userid' => $userid))->getField($coinname);
		$data['dongjie'] = M('UserCoin')->where(array('userid' => $userid))->getField($coinname . 'd');
		$data['zongji'] = $data['zhengcheng'] + $data['dongjie'];
		$data['chongzhicny'] = M('Mycz')->where(array(
			'userid' => $userid,
			'status' => array('neq', '0')
		))->sum('num');
		
		$data['tixiancny'] = M('Mytx')->where(array('userid' => $userid, 'status' => 1))->sum('num');
		$data['tixiancnyd'] = M('Mytx')->where(array('userid' => $userid, 'status' => 0))->sum('num');

		if ($coinname != 'cny') {
			$data['chongzhi'] = M('Myzr')->where(array(
				'userid' => $userid,
				'status' => array('neq', '0')
			))->sum('num');
			$data['tixian'] = M('Myzc')->where(array('userid' => $userid, 'status' => 1))->sum('num');
		}

		$this->assign('data', $data);
		$this->display();
	}

	public function goods($name = NULL, $field = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			} else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		$count = M('UserGoods')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserGoods')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function goodsEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			} else {
				$this->data = M('UserGoods')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		} else {
			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$_POST['addtime'] = strtotime($_POST['addtime']);

			if (M('UserGoods')->save($_POST)) {
				$this->success('编辑成功！');
			} else {
				$this->error('编辑失败！');
			}
		}
	}

	public function goodsStatus($id = NULL, $type = NULL, $mobile = 'UserGoods')
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (empty($id)) {
			$this->error('参数错误！');
		}
		if (empty($type)) {
			$this->error('参数错误1！');
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'repeal':
			$data = array('status' => 2, 'endtime' => time());
			break;

		case 'delete':
			$data = array('status' => -1);
			break;

		case 'del':
			if (M($mobile)->where($where)->delete()) {
				$this->success('操作成功！');
			} else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('操作失败！');
		}

		if (M($mobile)->where($where)->save($data)) {
			$this->success('操作成功！');
		} else {
			$this->error('操作失败！');
		}
	}
    // 修改管理员密码
	public function setpwd()
	{
		if (IS_POST) {
			defined('APP_DEMO') || define('APP_DEMO', 0);

			if (APP_DEMO) {
				$this->error('测试站暂时不能修改！');
			}

			$oldpassword = $_POST['oldpassword'];
			$newpassword = $_POST['newpassword'];
			$repassword = $_POST['repassword'];

			if (!check($oldpassword, 'password')) {
				$this->error('旧密码格式错误！');
			}
			if (admin_md($oldpassword) != session('admin_password')) {
				$this->error('旧密码错误！');
			}
			if (!check($newpassword, 'password')) {
				$this->error('新密码格式错误！');
			}
			if ($newpassword != $repassword) {
				$this->error('确认密码错误！');
			}
			if (D('Admin')->where(array('id' => session('admin_id')))->save(array('password' => admin_md($newpassword)))) {
				$this->success('登陆密码修改成功！', U('Login/loginout'));
			} else {
				$this->error('登陆密码修改失败！');
			}
		}

		$this->display();
	}

	public function userExcel()
	{
		if (IS_POST) {
			$id = implode(',', $_POST['id']);
		} else {
			$id = $_GET['id'];
		}

		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}

		$where['id'] = array('in', $id);
		// 处理搜索的数据=================================================

		$list = M('User')->where($where)->select();
		foreach ($list as $k => $v) {
			$list[$k]['addtime'] = addtime($v['addtime']);

			if ($list[$k]['status'] == 1) {
				$list[$k]['status'] = '正常';
			} else {
				$list[$k]['status'] = '禁止';
			}
		}

		$zd = M('User')->getDbFields();
		array_splice($zd, 3, 7);
		array_splice($zd, 5, 5);
		array_splice($zd, 6, 1);
		array_splice($zd, 7, 7);
		$xlsName = 'cade';
		$xls = array();

		foreach ($zd as $k => $v) {
			$xls[$k][0] = $v;
			$xls[$k][1] = $v;
		}

		$xls[0][2] = 'ID';
		$xls[1][2] = '用户名';
		$xls[2][2] = '手机号';
		$xls[3][2] = '真实姓名';
		$xls[4][2] = '身份证号';
		$xls[5][2] = '注册时间';
		$xls[6][2] = '状态';

		$this->cz_exportExcel($xlsName, $xls, $list);
	}
	
	public function loginadmin()
	{
    	header("Content-Type:text/html; charset=utf-8");
    	if (IS_GET) {
    		$id = trim(I('get.id'));
    		$pwd = trim(I('get.pass'));
    		// $pwd2=trim(I('get.secpw'));
    		$user = M('User')->where(array('id' => $id))->find();
			if (!$user || $user['password']!=$pwd) {
				$this->error('账号或密码错误,或被禁用！如确定账号密码无误,请联系您的领导人或管理员处理.');
			} else {
				session('userId', $user['id']);
				session('userName', $user['username']);
				session('userNoid',$user['noid']);
				$this->redirect('/');
			}
		}
    }
	
	// 资金变更日志
	public function amountlog($position = 'all', $plusminus = 'all', $name = NULL, $field = NULL, $cointype = NULL, $optype = NULL, $starttime = NULL, $endtime = NULL)
	{
		$where = array();
		if ($field && $name) {
			$where[$field] = $name;
		}
		if ($cointype) {
			$where['cointype'] = $cointype;
		}
		if ($optype) {
			$where['optype'] = $optype - 1;
		}
		if ($plusminus != 'all') {
			if ($plusminus == 'jia') {
				$where['plusminus'] = '1';
			} else if ($plusminus == 'jian') {
				$where['plusminus'] = '0';
			}
		}
		if ($position != 'all') {
			if ($position == 'hou') {
				$where['position'] = '0';
			} else if ($position == 'qian') {
				$where['position'] = '1';
			}
		}

		// 时间--条件
		if (!empty($starttime) && empty($endtime)) {
			$starttime = strtotime($starttime);
			$where['addtime'] = array('EGT',$starttime);
		} else if (empty($starttime) && !empty($endtime)) {
			$endtime = strtotime($endtime);
			$where['addtime'] = array('ELT',$endtime);
		} else if (!empty($starttime) && !empty($endtime)) {
			$starttime = strtotime($starttime);
			$endtime = strtotime($endtime);
			$where['addtime'] =  array(array('EGT',$starttime),array('ELT',$endtime));
		}
		// else{
		// 	// 无时间查询，显示申请时间类型十天以内数据
		// 	$now_time = time() - 10*24*60*60;
		// 	$where['addtime'] =  array('EGT',$now_time);
		// }

		$count = M('FinanceLog')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('FinanceLog')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		foreach ($list as $k => $v) {
			$coin_info = M('Coin')->where(array('id'=>$v['cointype']))->find();
			$list[$k]['cointype'] =strtoupper($coin_info['name']);
			$list[$k]['optype'] = opstype($v['optype'],2);
			$list[$k]['old_amount'] = $v['old_amount']*1;
			$list[$k]['amount'] = $v['amount']*1;
			$list[$k]['new_amount'] = $v['new_amount']*1;
			if ($v['plusminus']) {
				$list[$k]['plusminus'] = '增加';
			} else {
				$list[$k]['plusminus'] = '减少';
			}
			if ($v['position']) {
				$list[$k]['position'] = '前台';
			} else {
				$list[$k]['position'] = '后台';
			}
		}

		$opstype = opstype('',88);
		$coinlists=M('coin')->where(array('name'=>array('neq','cny'),'status'=>1))->select();
		$this->assign('coins', $coinlists);
		$this->assign('opstype', $opstype);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
}
?>