<?php
namespace Mobile\Controller;

class IssueController extends MobileController
{
	public function index()
	{
		if (!userid()) {
			redirect('/#login');
		}

		$where['status'] = array('neq', 0);
		$Model = M('Issue');
		$count = $Model->where($where)->count();
		$Page = new \Think\Page($count, 2);
		$show = $Page->show();

		$list = $Model->where($where)->order('tuijian asc,paixu desc,addtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		$tuijian = $Model->where(array("tuijian"=>1))->order("addtime desc")->limit(1)->find();
		if($tuijian){

			$tuijian['coinname'] = C('coin')[$tuijian['coinname']]['title'];
			// var_dump($tuijian);die;
			$tuijian['buycoin']  = C('coin')[$tuijian['buycoin']]['title'];
			// var_dump($tuijian);die;
			$tuijian['bili']     = round(($tuijian['deal'] / $tuijian['num']) * 100, 2);
			// var_dump($tuijian);die;
			$tuijian['content']  = mb_substr(clear_html($tuijian['content']),0,350);
			// var_dump($tuijian);die;

			$end_ms = strtotime($tuijian['time'])+$tuijian['tian']*3600*24;
			$begin_ms = strtotime($tuijian['time']);

			$tuijian['beginTime'] = date("Y-m-d H:i:s",$begin_ms);
			$tuijian['endTime']   = date("Y-m-d H:i:s",$end_ms);
			$dhbli=1/$tuijian['price'];
			$tuijian['duihuan'] = '1'.' '.strtoupper($tuijian['buycoin']).'='.$dhbli.' '.strtoupper($tuijian['coinname']);
			$tuijian['zhuangtai'] = "进行中" ;

			if($begin_ms>time()){
				$tuijian['zhuangtai'] = "尚未开始";//未开始
			}

			if($tuijian['num']<=$tuijian['deal']){
				$tuijian['zhuangtai'] =  "已结束";//已结束
			}

			if($end_ms<time()){
				$tuijian['zhuangtai'] = "已结束";//已结束
			}

			$tuijian['rengou']="";
			if($tuijian['zhuangtai'] == "进行中"){
				$tuijian['rengou']="<a href='/Issue/buy/id/".$tuijian['id'].".html'>立即认购</a>";
			}
		}

		// die;
		if($list){
			$this->assign('prompt_text', D('Text')->get_content('game_issue'));
		}else{
			$this->assign('prompt_text', '');
		}


		$list_jinxing = array();
		$list_yure	  = array();
		$list_jieshu  = array();


		foreach ($list as $k => $v) {
			//$list[$k]['img'] = M('Coin')->where(array('name' => $v['coinname']))->getField('img');

			$list[$k]['bili'] = round(($v['deal'] / $v['num']) * 100, 2);
			$list[$k]['endtime'] = date("Y-m-d H:i:s",strtotime($v['time'])+$v['tian']*3600*24);

			$list[$k]['coinname'] = C('coin')[$v['coinname']]['title'];
			$list[$k]['buycoin']  = C('coin')[$v['buycoin']]['title'];
			$list[$k]['bili']     = round(($v['deal'] / $v['num']) * 100, 2);
			// $list[$k]['content']  = mb_substr(clear_html($v['content']),0,350,'utf-8');
			$list[$k]['content']  = mb_substr(clear_html($v['content']),0,350,'utf-8');
			$list[$k]['content2']  = mb_substr(clear_html($v['content']),0,50,'utf-8');


			$end_ms = strtotime($v['time'])+$v['tian']*3600*24;
			$begin_ms = strtotime($v['time']);


			$list[$k]['beginTime'] = date("Y-m-d H:i:s",$begin_ms);
			$list[$k]['endTime']   = date("Y-m-d H:i:s",$end_ms);

			$list[$k]['zhuangtai'] = "进行中" ;
			$list[$k]['statuss'] =1;//进行中

			if($begin_ms>time()){
				$list[$k]['zhuangtai'] = "尚未开始";//未开始
				$list[$k]['statuss'] =0;//尚未开始
			}



			if($list[$k]['num']<=$list[$k]['deal']){
				$list[$k]['zhuangtai'] =  "已结束";//已结束
				$list[$k]['statuss'] =2;//已结束
			}

			if($end_ms<time()){
				$list[$k]['zhuangtai'] = "已结束";//已结束
				$list[$k]['statuss'] =2;//已结束
			}

			switch($list[$k]['zhuangtai']){
				case "尚未开始":
					$list_yure[] = $list[$k];
					break;
				case "进行中":
					$list_jinxing[] = $list[$k];
					break;
				case "已结束":
					$list_jieshu[] = $list[$k];
					break;
			}
		}

		//var_dump($list_jieshu);
		if(!$tuijian){
				$show=0;
			}else{
				$show=1;
			}
			$this->assign('show', $show);
		$this->assign('tuijian', $tuijian);
		$this->assign('list_yure', $list_yure);
		$this->assign('list_jinxing', $list_jinxing);
		$this->assign('list_jieshu', $list_jieshu);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
public function index2222()
	{
		if (!userid()) {
			redirect('/#login');
		}

		$this->assign('prompt_text', D('Text')->get_content('game_issue'));
		$where['status'] = array('egt', 0);
		$Model = M('Issue');
		$count = $Model->where($where)->count();
		$Page = new \Think\Page($count, 5);
		$show = $Page->show();
		$list = $Model->where($where)->order('addtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['img'] = M('Coin')->where(array('name' => $v['coinname']))->getField('img');
			$list[$k]['bili'] = round(($v['deal'] / $v['num']) * 100, 2);
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function buy($id = 1)

	{
		// 过滤非法字符----------------S
		if (checkstr($id)) {
			$this->error(L('您输入的信息有误！'));
		}
		// 过滤非法字符----------------E
		if (!userid()) {
			redirect('/Login/index');
		}
		// $this->assign('prompt_text', D('Text')->get_content('game_issue_buy'));

		if (!check($id, 'd')) {
			$this->error(L('参数错误！'));
		}

		$Issue = M('Issue')->where(array('id' => $id))->find();
		$Issue['min'] = ($Issue['min'] ? $Issue['min'] : 1);

		$Issue['max'] = ($Issue['max'] ? $Issue['max'] : '不设');

		$Issue['bili'] = round(($Issue['deal'] / $Issue['num']) * 100, 2);

		$user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
		$user_coin['cny']=sprintf("%.2f",substr(sprintf("%.4f", $user_coin['cny']), 0, -2));
		$user_coin['shouyi_num']= sprintf("%.2f",substr(sprintf("%.4f", $user_coin['shouyi_num']), 0, -2));

		$this->assign('user_coin', $user_coin);

		if (!$Issue) {
			$this->error(L('认购错误！'));
		}

		$Issue['img'] = M('Coin')->where(array('name' => $Issue['coinname']))->getField('img');

		$this->assign('issue', $Issue);

		$this->display();

	}



	public function log()

	{

		if (!userid()) {

			redirect('/Login/index');

		}



		$this->assign('prompt_text', D('Text')->get_content('game_issue_log'));

		$where['status'] = array('egt', 0);

		$where['userid'] = userid();

		$IssueLog = M('IssueLog');

		$count = $IssueLog->where($where)->count();


		$Page = new \Think\Page1($count, 10);

		$show = $Page->show();

		$list = $IssueLog->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();



		foreach ($list as $k => $v) {

			$list[$k]['shen'] = round((($v['ci'] - $v['unlock']) * $v['num']) / $v['ci'], 6);

		}



		$this->assign('list', $list);

		$this->assign('page', $show);

		$this->display();

	}


	// 认购中心列表
	public function buy_list($p_nums = null)

	{


		// 过滤非法字符----------------S

		if (checkstr($p_nums)) {
			$this->error(L('您输入的信息有误！'));
		}

		// 过滤非法字符----------------E



		if (!userid()) {
			redirect('/Login/index');
		}

		$this->assign('prompt_text', D('Text')->get_content('game_issue'));
		$where['status'] = array('egt', 0);
		$Model = M('Issue');
		$count = $Model->where($where)->count();

		$Page = new \Think\Page1($count, 10);

		$show = $Page->show();

		$list = $Model->where($where)->order('addtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();



		foreach ($list as $k => $v) {

			$list[$k]['img'] = M('Coin')->where(array('name' => $v['coinname']))->getField('img');

			$list[$k]['bili'] = round(($v['deal'] / $v['num']) * 100, 2);
			if (time() > strtotime($v['time'])+$v['tian']*24*3600) {
				$list[$k]['status']=0;
			}
				if (time() < strtotime($v['time'])) {
				$list[$k]['status']=2;
			}
		}



		$this->assign('list', $list);

		$this->assign('page', $show);

		$this->display();

	}

	// 认购中心详情页
	public function buy_detail($id = null)

	{


		// 过滤非法字符----------------S

		if (checkstr($id)) {
			$this->error('您输入的信息有误！');
		}

		// 过滤非法字符----------------E

		if (!userid()) {

			redirect('/Login/index');

		}

		$this->assign('id', $id);

		$where['status'] = array('egt', 0);
		$where['id'] = $id;

		$Model = M('Issue');

		$list = $Model->where($where)->find();

		$list['img'] = M('Coin')->where(array('name' => $list['coinname']))->getField('img');
		$list['ctitle'] = M('Coin')->where(array('name' => $list['coinname']))->getField('title');
		$list['bili'] = round(($list['deal'] / $list['num']) * 100, 2);

		// $list['status'] = 1 ;
			if($list['time']>time()){
				$list['status'] = 2;//未开始
			}
			if($list['num']==$Issue['deal']){
				$list['status'] = 0;//已结束
			}
			if($list['endtime']<time()){
				$list['status'] = 0;//已结束
			}
		// if (time() > strtotime($list['time'])+$list['tian']*24*3600) {

		// 	$list['status']=0;

		// }

		$this->assign('list', $list);

		$this->display();

	}

	// 认购中心详情页
	public function buy_detail_article($id = null)

	{
		// 过滤非法字符----------------S

		if (checkstr($id)) {
			$this->error('您输入的信息有误！');
		}

		// 过滤非法字符----------------E
		if (!userid()) {

			redirect('/Login/index');

		}

		$this->assign('id', $id);

		$where['status'] = array('egt', 0);
		$where['id'] = $id;

		$Model = M('Issue');

		$list = $Model->where($where)->find();

		$list['img'] = M('Coin')->where(array('name' => $list['coinname']))->getField('img');
		$list['bili'] = round(($list['deal'] / $list['num']) * 100, 2);


		$this->assign('list', $list);

		$this->display();

	}

	// 认购中心详情页获取信息
	public function issue_list()

	{

		if (!userid()) {

			redirect('/Login/index');

		}

		$this->display();

	}

	// 认购记录详情页获取数据
	public function ajax_buy_detail($id = null)

	{
		// 过滤非法字符----------------S

		if (checkstr($id)) {
			$this->error('您输入的信息有误！');
		}

		// 过滤非法字符----------------E

		if (!userid()) {

			redirect('/Login/index');

		}

		if(empty($_POST['id'])){

			$this->error('参数错误！');
		}

		$this->assign('id', $id);

		$where['status'] = array('egt', 0);
		$where['id'] = $id;

		$list = M('Issue')->where($where)->find();

		$list['coinname'] = strtoupper($list['coinname']);

		$list['img'] = M('Coin')->where(array('name' => $list['coinname']))->getField('img');

		$list['bili'] = round(($list['deal'] / $list['num']) * 100, 2);
		if (time() > strtotime($list['time'])+$list['tian']*24*3600) {

			$list['status']=0;

		}

		$user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
		$user_coin[$list['buycoin']]=sprintf("%.2f",substr(sprintf("%.4f", ($user_coin[$list['buycoin']]-$user_coin['shouyi_num'])), 0, -2));
		$user_coin['shouyi_num']= sprintf("%.2f",substr(sprintf("%.4f", $user_coin['shouyi_num']), 0, -2));

		$list['cny'] = $user_coin[$list['buycoin']];
		$list['buyname'] = M('coin')->where(array('name'=>$list['buycoin']))->getField('title');
		$list['coinm'] = M('coin')->where(array('name'=>$list['coinname']))->getField('title');
		$list['shouyi_num'] = $user_coin['shouyi_num'];

		exit(json_encode($list));

	}


	// 认购记录详情页
	public function log_detail($id = null)

	{

		// 过滤非法字符----------------S

		if (checkstr($id)) {
			$this->error('您输入的信息有误！');
		}

		// 过滤非法字符----------------E

		if (!userid()) {

			redirect('/Login/index');

		}

		$this->assign('id', $id);

		$this->display();

	}

	// 认购记录详情页获取数据
	public function ajax_log_detail($id = null)

	{

		// 过滤非法字符----------------S

		if (checkstr($id)) {
			$this->error('您输入的信息有误！');
		}

		// 过滤非法字符----------------E

		if (!userid()) {

			redirect('/Login/index');

		}

		if(empty($_POST['id'])){

			$this->error('参数错误！');
		}

		$this->assign('id', $id);

		$is_info = M('IssueLog')->where(array('id'=>$id))->find();

		$is_info['price'] = $is_info['price']*1;
		$is_info['mum'] = $is_info['mum']*1;
		$is_info['addtime'] = date('Y-m-d',$is_info['addtime']);
		$is_info['endtime'] = date('Y-m-d',$is_info['endtime']);

		if($is_info['status']){
			$is_info['status'] = true;
		}else{
			$is_info['status'] = false;
		}

		$is_info['shen'] = round((($is_info['ci'] - $is_info['unlock']) * $is_info['num']) / $is_info['ci'], 6);

		exit(json_encode($is_info));

	}

public function upbuy($id, $num, $paypassword)
	{
		if (!userid()) {
			redirect('/#login');
		}

		if (!check($id, 'd')) {
			$this->error(L('参数错误！'));
		}

		if (!check($num, 'd')) {
			$this->error(L('认购数量格式错误！'));
		}

		if (!check($paypassword, 'password')) {
			$this->error(L('交易密码格式错误！'));
		}

		$User = M('User')->where(array('id' => userid()))->find();

		if (!$User['paypassword']) {
			$this->error(L('交易密码非法！'));
		}

		if (md5($paypassword) != $User['paypassword']) {
			$this->error(L('交易密码错误！'));
		}

		$Issue = M('Issue')->where(array('id' => $id))->find();

		if (!$Issue) {
			$this->error(L('认购错误！'));
		}

		if (time() < strtotime($Issue['time'])) {
			$this->error(L('当前认购还未开始！'));
		}

		if (!$Issue['status']) {
			$this->error(L('当前认购已经结束！'));
		}


		$end_ms = strtotime($Issue['time'])+$Issue['tian']*3600*24;
			/* 		$begin_ms = strtotime($Issue['time']);
				if($begin_ms<time()){
					$Issue['status'] = 2;//未开始
				} */

		if($end_ms<time()){
			$this->error(L('当前认购已经结束！'));
		}

		$issue_min = ($Issue['min'] ? $Issue['min'] : 9.9999999999999995E-7);
		$issue_max = ($Issue['max'] ? $Issue['max'] : 100000000);

		if ($num < $issue_min) {
			$this->error(L('单次认购数量不得少于系统设置') . $issue_min . L('个'));
		}

		if ($issue_max < $num) {
			$this->error(L('单次认购数量不得大于系统设置') . $issue_max . L('个'));
		}

		if (($Issue['num'] - $Issue['deal']) < $num) {
			$this->error(L('认购数量超过当前剩余量！'));
		}

		$mum = round($Issue['price'] * $num, 6);

		if (!$mum) {
			$this->error(L('认购总额错误'));
		}

		$buycoin = M('UserCoin')->where(array('userid' => userid()))->getField($Issue['buycoin']);

		if ($buycoin < $mum) {
			$this->error('可用' . C('coin')[$Issue['buycoin']]['title'] . '余额不足');
		}

		$issueLog = M('IssueLog')->where(array('userid' => userid(), 'coinname' => $Issue['coinname']))->sum('num');

		if ($Issue['limit'] < ($issueLog + $num)) {
			$this->error(L('认购总数量超过最大限制') . $Issue['limit']);
		}

		if ($Issue['ci']) {
			$jd_num = round($num / $Issue['ci'], 6);
		}
		else {
			$jd_num = $num;
		}

		if (!$jd_num) {
			$this->error(L('认购解冻数量错误'));
		}

		$mo = M();
		$mo->execute('set autocommit=0');
		$mo->execute('lock tables tw_invit write ,  tw_user_coin write  , tw_issue write  , tw_issue_log  write ,tw_finance write');
		$rs = array();
		$finance = $mo->table('tw_finance')->where(array('userid' => userid()))->order('id desc')->find();
		$finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => userid()))->find();
		$rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setDec($Issue['buycoin'], $mum);
		// $rs[] = $finance_nameid = $mo->table('tw_issue_log')->add(array('userid' => userid(), 'coinname' => $Issue['coinname'], 'buycoin' => $Issue['buycoin'], 'name' => $Issue['name'], 'price' => $Issue['price'], 'num' => $num, 'mum' => $mum, 'ci' => $Issue['ci'], 'jian' => $Issue['jian'], 'unlock' => 1, 'addtime' => time(), 'endtime' => time(), 'status' => $Issue['ci'] == 1 ? 1 : 0));

		$rs[] = $finance_nameid = $mo->table('tw_issue_log')->add(array('userid' => userid(), 'coinname' => $Issue['coinname'], 'buycoin' => $Issue['buycoin'], 'name' => $Issue['name'], 'price' => $Issue['price'], 'num' => $num, 'mum' => $mum, 'ci' => $Issue['ci'], 'jian' => $Issue['jian'], 'unlock' => 0, 'addtime' => time(), 'endtime' => ($Issue['endtime']+ (60 * 60 * $Issue['jian'])), 'status' => 0));

		$finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => userid()))->find();
		$finance_hash = md5(userid() . $finance_num_user_coin['cny'] . $finance_num_user_coin['cnyd'] . $mum . $finance_mum_user_coin['cny'] . $finance_mum_user_coin['cnyd'] . MSCODE . 'auth.xxx.com');
		$rs[] = $mo->table('tw_finance')->add(array('userid' => userid(), 'coinname' => 'cny', 'num_a' => $finance_num_user_coin['cny'], 'num_b' => $finance_num_user_coin['cnyd'], 'num' => $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'], 'fee' => $mum, 'type' => 2, 'name' => 'issue', 'nameid' => $finance_nameid, 'remark' => '认购中心-立即认购', 'mum_a' => $finance_mum_user_coin['cny'], 'mum_b' => $finance_mum_user_coin['cnyd'], 'mum' => $finance_mum_user_coin['cny'] + $finance_mum_user_coin['cnyd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance['mum'] != $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'] ? 0 : 1));
		// $rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setInc($Issue['coinname'], $jd_num);
		$rs[] = $mo->table('tw_issue')->where(array('id' => $id))->setInc('deal', $num);

		if ($Issue['num'] <= $Issue['deal']) {
			$rs[] = $mo->table('tw_issue')->where(array('id' => $id))->setField('status', 0);
		}

		if ($User['invit_1'] && $Issue['invit_1']) {
			$invit_num_1 = round(($mum / 100) * $Issue['invit_1'], 6);

			if ($invit_num_1) {
				$rs[] = $mo->table('tw_user_coin')->where(array('userid' => $User['invit_1']))->setInc($Issue['invit_coin'], $invit_num_1);
				$rs[] = $mo->table('tw_invit')->add(array('userid' => $User['invit_1'], 'invit' => userid(), 'name' => $Issue['name'], 'type' => '一代认购赠送', 'num' => $num, 'mum' => $mum, 'fee' => $invit_num_1, 'addtime' => time(), 'status' => 1));
			}
		}

		if ($User['invit_2'] && $Issue['invit_2']) {
			$invit_num_2 = round(($mum / 100) * $Issue['invit_2'], 6);

			if ($invit_num_2) {
				$rs[] = $mo->table('tw_user_coin')->where(array('userid' => $User['invit_2']))->setInc($Issue['invit_coin'], $invit_num_2);
				$rs[] = $mo->table('tw_invit')->add(array('userid' => $User['invit_2'], 'invit' => userid(), 'name' => $Issue['name'], 'type' => '二代认购赠送', 'num' => $num, 'mum' => $mum, 'fee' => $invit_num_2, 'addtime' => time(), 'status' => 1));
			}
		}

		if ($User['invit_3'] && $Issue['invit_3']) {
			$invit_num_3 = round(($mum / 100) * $Issue['invit_3'], 6);

			if ($invit_num_3) {
				$rs[] = $mo->table('tw_user_coin')->where(array('userid' => $User['invit_3']))->setInc($Issue['invit_coin'], $invit_num_3);
				$rs[] = $mo->table('tw_invit')->add(array('userid' => $User['invit_3'], 'invit' => userid(), 'name' => $Issue['name'], 'type' => '三代认购赠送', 'num' => $num, 'mum' => $mum, 'fee' => $invit_num_3, 'addtime' => time(), 'status' => 1));
			}
		}

		if ($mo->execute('commit')>=0) {
			$mo->execute('unlock tables');
			$this->success(L('购买成功！'));
		}
		else {
			$mo->execute('rollback');
			$this->error(L('购买失败!'));
		}
	}

	public function upbuyooo221($id, $num, $paypassword, $check_s)

	{


		// 过滤非法字符----------------S

		if (checkstr($id) || checkstr($num) || checkstr($paypassword) || checkstr($check_s)) {
			$this->error('您输入的信息有误！');
		}

		// 过滤非法字符----------------E

		if (!userid()) {

			redirect('/Login/index');

		}

		if (!check($id, 'd')) {

			$this->error('参数错误！');

		}



		if (!check($num, 'd')) {

			$this->error(L('认购数量格式错误！'));

		}



		if (!check($paypassword, 'password')) {

			$this->error(L('交易密码格式错误！'));

		}



		$User = M('User')->where(array('id' => userid()))->find();



		if (!$User['paypassword']) {

			$this->error(L('交易密码非法！'));

		}



		if (md5($paypassword) != $User['paypassword']) {

			$this->error(L('交易密码错误！'));

		}



		$Issue = M('Issue')->where(array('id' => $id))->find();



		if (!$Issue) {

			$this->error(L('认购错误！'));

		}



		if (time() < strtotime($Issue['time'])) {

			$this->error(L('当前认购还未开始！'));

		}
		if (time() > strtotime($Issue['time'])+$Issue['tian']*24*3600) {

			$this->error(L('当前认购已经结束！'));

		}


		if (!$Issue['status']) {

			$this->error(L('当前认购已经结束！'));

		}



		$issue_min = ($Issue['min'] ? $Issue['min'] : 9.9999999999999995E-7);

		$issue_max = ($Issue['max'] ? $Issue['max'] : 100000000);



		if ($num < $issue_min) {

			$this->error(L('单次认购数量不得少于系统设置') . $issue_min .L('个'));

		}



		if ($issue_max < $num) {

			$this->error(L('单次认购数量不得大于系统设置') . $issue_max . L('个'));

		}



		if (($Issue['num'] - $Issue['deal']) < $num) {

			$this->error(L('认购数量超过当前剩余量！'));

		}



		$mum = round($Issue['price'] * $num, 6);


		//判断是否新注册 是否推荐了别人注册 做出认购限制 开始

		// $Userss = M('User')->where(array('invit_1' => userid()))->select();

		// $cur_rg_max = 0 ;

		// if(!$Userss){

		// 	$cur_rg_max = C('new_max_rg');

		// 	if($mum > $cur_rg_max){

		// 			$this->error('新注册最高认购 '. $cur_rg_max .' 元');
		// 		}

		// }else{

		// 	$cur_rg_max = intval(C('new_tui_add_rg')) * count($Userss);

		// 	if($mum > $cur_rg_max){

		// 			$this->error('您最高可认购 '. $cur_rg_max .' 元');
		// 		}

		// }

		//判断是否新注册 是否推荐了别人注册 做出认购限制 结束





		if (!$mum) {

			$this->error(L('认购总额错误'));

		}


		// 获取后台设置的使用品种还是人民币账户
		$buycoin = M('UserCoin')->where(array('userid' => userid()))->getField($Issue['buycoin']);
		// 获取该用户收益账户
		$buy_shouyi_num = M('UserCoin')->where(array('userid' => userid()))->getField('shouyi_num');

		if ($buycoin < $mum) {
			$this->error('可用' . C('coin')[$Issue['buycoin']]['title'] . '余额不足');
		}



		// 处理收益-----------E
		if($check_s == 'yes'){
			if ($buycoin < $mum) {

				$this->error('可用' . C('coin')[$Issue['buycoin']]['title'] . '与收益账户总额余额不足');

			}
		}else{
			if (($buycoin - $buy_shouyi_num) < $mum) {

				$this->error('可用' . C('coin')[$Issue['buycoin']]['title'] . '余额不足');

			}
		}

		// 处理收益-----------S





		$issueLog = M('IssueLog')->where(array('userid' => userid(), 'coinname' => $Issue['coinname'],'from'=>$Issue['id']))->sum('num');



		if ($Issue['limit'] < ($issueLog + $num)) {

			$this->error(L('认购总数量超过最大限制') . $Issue['limit']);

		}



		if ($Issue['ci']) {

			$jd_num = round($num / $Issue['ci'], 6);

		}

		else {

			$jd_num = $num;

		}



		if (!$jd_num) {

			$this->error(L('认购解冻数量错误'));

		}



		$mo = M();

		$mo->execute('set autocommit=0');

		$mo->execute('lock tables tw_invit write ,  tw_user_coin write  , tw_issue write  , tw_issue_log  write ,tw_finance write');

		$rs = array();

		$finance = $mo->table('tw_finance')->where(array('userid' => userid()))->order('id desc')->find();

		$finance_num_user_coin = $mo->table('tw_user_coin')->where(array('userid' => userid()))->find();







		// 处理收益-------------E
		if($check_s == 'yes'){
			if($buy_shouyi_num > 0){
				if($buy_shouyi_num >= $mum){
					// 收益的余额大于等于 购买的金额
					$rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setDec('shouyi_num', $mum);
				}else{
					$rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setDec('shouyi_num', $buy_shouyi_num);
				}
			}
		}
		// 处理收益-------------S



		// 该用户账户人民币账户 减
		$rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setDec($Issue['buycoin'], $mum);




		if($Issue['jd_starttime']!=0&&$Issue['jd_starttime']!=''&&$Issue['jd_starttime']!=NULL&&time()>$Issue['jd_starttime']){

			$rs[] = $finance_nameid = $mo->table('tw_issue_log')->add(array('userid' => userid(), 'coinname' => $Issue['coinname'], 'buycoin' => $Issue['buycoin'], 'name' => $Issue['name'], 'price' => $Issue['price'], 'num' => $num, 'mum' => $mum, 'ci' => $Issue['ci'], 'jian' => $Issue['jian'], 'unlock' => 1, 'addtime' => time(), 'endtime' => time(), 'status' => $Issue['ci'] == 1 ? 1 : 0,'jd_starttime'=>$Issue['jd_starttime'],'from'=>$Issue['id']));
		}else{
			$rs[] = $finance_nameid = $mo->table('tw_issue_log')->add(array('userid' => userid(), 'coinname' => $Issue['coinname'], 'buycoin' => $Issue['buycoin'], 'name' => $Issue['name'], 'price' => $Issue['price'], 'num' => $num, 'mum' => $mum, 'ci' => $Issue['ci'], 'jian' => $Issue['jian'], 'unlock' => 0, 'addtime' => time(), 'endtime' => $Issue['jd_starttime'], 'status' => 0,'jd_starttime'=>$Issue['jd_starttime'],'from'=>$Issue['id']));
		}
		$finance_mum_user_coin = $mo->table('tw_user_coin')->where(array('userid' => userid()))->find();

		$finance_hash = md5(userid() . $finance_num_user_coin['cny'] . $finance_num_user_coin['cnyd'] . $mum . $finance_mum_user_coin['cny'] . $finance_mum_user_coin['cnyd'] . MSCODE . 'auth.movesay.com');



		$rs[] = $mo->table('tw_finance')->add(array('userid' => userid(), 'coinname' => 'cny', 'num_a' => $finance_num_user_coin['cny'], 'num_b' => $finance_num_user_coin['cnyd'], 'num' => $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'], 'fee' => $num, 'type' => 2, 'name' => 'issue', 'nameid' => $finance_nameid, 'remark' => '认购中心-立即认购', 'mum_a' => $finance_mum_user_coin['cny'], 'mum_b' => $finance_mum_user_coin['cnyd'], 'mum' => $finance_mum_user_coin['cny'] + $finance_mum_user_coin['cnyd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance['mum'] != $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'] ? 0 : 1));
		if($Issue['jd_starttime']!=0&&$Issue['jd_starttime']!=''&&$Issue['jd_starttime']!=NULL&&time()>$Issue['jd_starttime']){
		// 宏信币数量添加
			$rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setInc($Issue['coinname'], $jd_num);
		}

		$rs[] = $mo->table('tw_issue')->where(array('id' => $id))->setInc('deal', $num);



		if ($Issue['num'] <= $Issue['deal']) {

			$rs[] = $mo->table('tw_issue')->where(array('id' => $id))->setField('status', 0);

		}



		if (check_arr($rs)) {

			$mo->execute('commit');

			$mo->execute('unlock tables');

			$this->success(L('购买成功！'));

		}

		else {

			$mo->execute('rollback');

			$this->error(L('购买失败!'));

		}

	}



	public function unlockmobile($id)

	{
		// 过滤非法字符----------------S
		if (checkstr($id)) {
			$this->error(L('您输入的信息有误！'));
		}
		// 过滤非法字符----------------E
		if (!userid()) {
			redirect('/Login/index');
		}
		if (!check($id, 'd')) {
			$this->error(L('请选择解冻项！'));
		}
		$IssueLog = M('IssueLog')->where(array('id' => $id))->find();
		if (!$IssueLog) {
			$this->error(L('参数错误！'));
		}
		if ($IssueLog['status']) {
			$this->error(L('当前解冻已结束！'));
		}
		if (time() < $IssueLog['jd_starttime']) {
			// $this->error('1');
			$this->error('未到开始解冻时间<br>【' . addtime($IssueLog['jd_starttime']) . '】');
		}

		if ($IssueLog['ci'] <= $IssueLog['unlock']) {
			$this->error(L('非法访问！'));
		}
		if (!$IssueLog['jd_starttime']) {
			$this->error(L('开始解冻时间暂未设定！'));
		}

		//$tm = $IssueLog['jd_starttime'] + (60 * 60 * $IssueLog['jian']);

		// 处理解冻次数----------E
		// 获取第一次解冻时间（也就是该记录的添加时间）
		$new_times = time() - $IssueLog['jd_starttime'];

		if(!$IssueLog['jian']){
			$jie_numbers = 1;
			$IssueLog['jian'] = 0;
		}else{
			$jian_time = 60 * 60 * $IssueLog['jian'];
			$jie_numbers = floor($new_times/$jian_time);
			$jie_n = floor($new_times/$jian_time);
		}



		$tm = $IssueLog['jd_starttime'] + $jie_n * (60 * 60 * $IssueLog['jian']);

		if (!$IssueLog['unlock']) {

			$IssueLog['unlock'] = 0;

		}

		// 计算该次解冻次数（加1是因为添加记录时已经解冻一次）
		$able_nums = $jie_numbers - $IssueLog['unlock'];

		if(time() < $IssueLog['jd_starttime']){

			$tm = $IssueLog['jd_starttime'];

		}

		if(time() > $IssueLog['jd_starttime']){

			$able_nums += 1;

		}


		if($able_nums <= 0){
			$this->error('尚未到解冻时间!<br>请在【' . date('Y-m-d',($tm)) . '】<br>之后再次操作');
			// $this->error('解冻时间还没有到,请在<br>【' . addtime($tm) . '】<br>之后再次操作');

		}

		if ($IssueLog['ci'] <= $IssueLog['unlock'] + $able_nums) {

			$able_nums = $IssueLog['ci'] - $IssueLog['unlock'];

		}
		// 处理解冻次数----------S

		if($IssueLog['unlock']!=0){
			if (time() < $tm) {
				$this->error('尚未到解冻时间!<br>请在【' . date('Y-m-d',($tm)) . '】<br>之后再次操作');
				// $this->error('解冻时间还没有到,请在<br>【' . addtime($tm) . '】<br>之后再次操作');

			}
		}



		if ($IssueLog['userid'] != userid()) {

			$this->error('非法访问');

		}



		// $jd_num = round($IssueLog['num'] / $IssueLog['ci'], 6);
		// 处理解冻次数----------E

		$jd_num = round(($IssueLog['num'] / $IssueLog['ci'])*$able_nums, 6);

		// 处理解冻次数----------S


		$mo = M();

		$mo->execute('set autocommit=0');

		$mo->execute('lock tables tw_user_coin write  , tw_issue_log write ');

		$rs = array();

		$rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setInc($IssueLog['coinname'], $jd_num);

		// $rs[] = $mo->table('tw_issue_log')->where(array('id' => $IssueLog['id']))->save(array('unlock' => $IssueLog['unlock'] + 1, 'endtime' => time()));
		// if ($IssueLog['ci'] <= $IssueLog['unlock'] + 1) {

		// 	$rs[] = $mo->table('tw_issue_log')->where(array('id' => $IssueLog['id']))->save(array('status' => 1));

		// }
		// 处理解冻次数----------E

		$rs[] = $mo->table('tw_issue_log')->where(array('id' => $IssueLog['id']))->save(array('unlock' => $IssueLog['unlock'] + $able_nums, 'endtime' => time()));

		if ($IssueLog['ci'] <= $IssueLog['unlock'] + $able_nums) {

			$rs[] = $mo->table('tw_issue_log')->where(array('id' => $IssueLog['id']))->save(array('status' => 1));

		}

		// 处理解冻次数----------S

		if (check_arr($rs)) {

			$mo->execute('commit');

			$mo->execute('unlock tables');

			$this->success('解冻成功！');

		}

		else {

			$mo->execute('rollback');

			$this->error('解冻失败！');

		}

	}

public function unlock($id)

	{
		header('Content-Type: text/html; charset=utf-8');
		// 过滤非法字符----------------S
		if (checkstr($id)) {
			$this->error(L('您输入的信息有误！'));
		}
		// 过滤非法字符----------------E
		if (!userid()) {
			redirect('/#login');
		}

		if (!check($id, 'd')) {
			$this->error(L('请选择解冻项！'));
		}

		$IssueLog = M('IssueLog')->where(array('id' => $id))->find();

		if (!$IssueLog) {

			$this->error(L('参数错误！'));

		}
		if ($IssueLog['status']) {
			$this->error(L('当前解冻已完成！'));
		}
		if ($IssueLog['ci'] <= $IssueLog['unlock']) {
			$this->error(L('非法访问！'));
		}
		$tm = $IssueLog['endtime'];

		if (time() < $tm) {


			 $this->error('尚未到解冻时间!<br>请在【' . date('Y-m-d',($tm)) . '】之后再次操作!');
			// $this->error('尚未到解冻时间!<br>请在【' . addtime($tm) . '】<br>之后再次操作');
		}

		if ($IssueLog['userid'] != userid()) {

			$this->error('非法访问');

		}

		$jd_num = round($IssueLog['num'] / $IssueLog['ci'], 6);

		$mo = M();

		$mo->execute('set autocommit=0');

		$mo->execute('lock tables tw_user_coin write  , tw_issue_log write ');

		$rs = array();

		$rs[] = $mo->table('tw_user_coin')->where(array('userid' => userid()))->setInc($IssueLog['coinname'], $jd_num);

		$rs[] = $mo->table('tw_issue_log')->where(array('id' => $IssueLog['id']))->save(array('unlock' => $IssueLog['unlock'] + 1, 'endtime' => $tm));//下次解冻时间endtime修改成+间隔
		// $rs[] = $mo->table('tw_issue_log')->where(array('id' => $IssueLog['id']))->save(array('unlock' => $IssueLog['unlock'] + 1, 'endtime' => time()));



		if ($IssueLog['ci'] <= $IssueLog['unlock'] + 1) {

			$rs[] = $mo->table('tw_issue_log')->where(array('id' => $IssueLog['id']))->save(array('status' => 1));

		}

		if (check_arr($rs)) {

			$mo->execute('commit');

			$mo->execute('unlock tables');

			$this->success('解冻成功！');

		}

		else {

			$mo->execute('rollback');

			$this->error('解冻失败！');

		}
	}


}



?>