<?php
namespace Mobile\Controller;

class MobileController extends \Think\Controller
{
	protected function _initialize()
	{
		$allow_controller=array("Ajax","Api","Article","Chart","Finance","Index","Login","Pay","Queue","Trade","User","Issue",  "Morefind","Financing","Exchange","Workerman");
		if(!in_array(CONTROLLER_NAME,$allow_controller)){
			$this->error("非法操作");
		}
		defined('APP_DEMO') || define('APP_DEMO', 0);


		if (!session('userId')) {
			session('userId', 0);
		} else if (CONTROLLER_NAME != 'Login') {
/*			$user = D('user')->where('id = ' . session('userId'))->find();
			if (!$user['paypassword']) {
				redirect('/Login/register2');
			}*/

			// if (!$user['truename']) {
			// 	redirect('/Login/register3');
			// }
			// if(!session('loginTime')){
			// 	redirect('/Login/loginout');
			// }else{
			// 	if(is_file("login/".session('userId').".txt")){
			// 		$xx=file_get_contents("login/".session('userId').".txt");
			// 	}else{
			// 		$xx='';
			// 	}
			// 	if((!empty($xx)&&$xx!=session('loginTime'))){
			// 		file_put_contents("login/".session('userId').".txt",'');
			// 		redirect('/Login/loginout');
			// 	}
			// }
		}

		if (userid()) {
			$userCoin_top = M('UserCoin')->where(array('userid' => userid()))->find();
			$userCoin_top['cny'] = round($userCoin_top['cny'], 2);
			$userCoin_top['cnyd'] = round($userCoin_top['cnyd'], 2);
			$this->assign('userCoin_top', $userCoin_top);
		}

		if (isset($_GET['invit'])) {
			session('invit', $_GET['invit']);
		}

		$config = (APP_DEBUG ? null : S('home_config'));
		if (!$config) {
			$config = M('Config')->where(array('id' => 1))->find();
			S('home_config', $config);
		}

		if (!session('web_close')) {
			if (!$config['web_close']) {
				exit($config['web_close_cause']);
			}
		}

		C($config);
		C('contact_qq', explode('|', C('contact_qq')));
		C('contact_qqun', explode('|', C('contact_qqun')));
		C('contact_bank', explode('|', C('contact_bank')));
		$coin = (APP_DEBUG ? null : S('home_coin'));

		if (!$coin) {
			$coin = M('Coin')->where(array('status' => 1))->select();
			S('home_coin', $coin);
		}

		$coinList = array();
		foreach ($coin as $k => $v) {
			$coinList['coin'][$v['name']] = $v;
			if ($v['name'] != 'cny') {
				$coinList['coin_list'][$v['name']] = $v;
			}
			if ($v['type'] == 'rmb') {
				$coinList['rmb_list'][$v['name']] = $v;
			} else {
				$coinList['xnb_list'][$v['name']] = $v;
			}
			if ($v['type'] == 'rgb') {
				$coinList['rgb_list'][$v['name']] = $v;
			}
			if ($v['type'] == 'qbb') {
				$coinList['qbb_list'][$v['name']] = $v;
			}
		}

		C($coinList);
		$market = (APP_DEBUG ? null : S('home_market'));
		if (!$market) {
			$market = M('Market')->where(array('status' => 1))->select();
			S('home_market', $market);
		}

		foreach ($market as $k => $v) {
			$v['new_price'] = round($v['new_price'], $v['round']);
			$v['buy_price'] = round($v['buy_price'], $v['round']);
			$v['sell_price'] = round($v['sell_price'], $v['round']);
			$v['min_price'] = round($v['min_price'], $v['round']);
			$v['max_price'] = round($v['max_price'], $v['round']);
			$v['xnb'] = explode('_', $v['name'])[0];
			$v['rmb'] = explode('_', $v['name'])[1];
			$v['xnbimg'] = C('coin')[$v['xnb']]['img'];
			$v['rmbimg'] = C('coin')[$v['rmb']]['img'];
			$v['volume'] = $v['volume'] * 1;
			$v['change'] = $v['change'] * 1;
			$v['title'] = C('coin')[$v['xnb']]['title'] . '(' . strtoupper($v['xnb']) . '/' . strtoupper($v['rmb']) . ')';

			$v['title_n'] = C('coin')[$v['xnb']]['title'];
			$v['title_ns'] = '(' . strtoupper($v['xnb']) . '/' . strtoupper($v['rmb']) . ')';
			$v['title_nsm'] = strtoupper($v['xnb']);

			$marketList['market'][$v['name']] = $v;
		}

		C($marketList);
		$C = C();
		foreach ($C as $k => $v) {
			$C[strtolower($k)] = $v;
		}

		$this->assign('C', $C);
		$this->kefu = './Application/Home/View/Kefu/' . $C['kefu'] . '/index.html';
		if (!S('daohang_aa')) {
			$tables = M()->query('show tables');
			$tableMap = array();

			foreach ($tables as $table) {
				$tableMap[reset($table)] = 1;
			}
			
/*			if (!isset($tableMap['tw_daohang'])) {
				M()->execute("\r\n" . '                    CREATE TABLE `tw_daohang` (' . "\r\n" . '                        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT \'自增id\',' . "\r\n" . '                        `name` VARCHAR(255) NOT NULL COMMENT \'名称\',' . "\r\n" . '                         `title` VARCHAR(255) NOT NULL COMMENT \'名称\',' . "\r\n" . '                        `url` VARCHAR(255) NOT NULL COMMENT \'url\',' . "\r\n" . '                        `sort` INT(11) UNSIGNED NOT NULL COMMENT \'排序\',' . "\r\n" . '                        `addtime` INT(11) UNSIGNED NOT NULL COMMENT \'添加时间\',' . "\r\n" . '                        `endtime` INT(11) UNSIGNED NOT NULL COMMENT \'编辑时间\',' . "\r\n" . '                        `status` TINYINT(4)  NOT NULL COMMENT \'状态\',' . "\r\n" . '                        PRIMARY KEY (`id`)' . "\r\n\r\n" . '                  )' . "\r\n" . 'COLLATE=\'gbk_chinese_ci\'' . "\r\n" . 'ENGINE=MyISAM' . "\r\n" . 'AUTO_INCREMENT=1' . "\r\n" . ';' . "\r\n\r\n\r\n\r\n" . 'INSERT INTO `tw_daohang` (`name`,`title`, `url`, `sort`, `status`) VALUES (\'finance\',\'财务中心\', \'Finance/index\', 1, 1);' . "\r\n" . 'INSERT INTO `tw_daohang` (`name`,`title`, `url`, `sort`, `status`) VALUES (\'user\',\'安全中心\', \'User/index\', 2, 1);' . "\r\n" . 'INSERT INTO `tw_daohang` (`name`, `title`,`url`, `sort`, `status`) VALUES (\'game\',\'应用中心\', \'Game/index\', 3, 1);' . "\r\n" . 'INSERT INTO `tw_daohang` (`name`, `title`,`url`, `sort`, `status`) VALUES (\'article\',\'帮助中心\', \'Article/index\', 4, 1);' . "\r\n\r\n\r\n" . '                ');
			}*/
	
			S('daohang_aa', 1);
		}

		if (!S('daohang')) {
			$this->daohang = M('Daohang')->where(array('status' => 1))->order('sort asc')->select();
			S('daohang', $this->daohang);
		} else {
			$this->daohang = S('daohang');
		}

		$footerArticleType = (APP_DEBUG ? null : S('footer_indexArticleType'));
		if (!$footerArticleType) {
			$footerArticleType = M('ArticleType')->where(array('status' => 1, 'footer' => 1, 'shang' => ''))->order('sort asc ,id desc')->limit(3)->select();
			S('footer_indexArticleType', $footerArticleType);
		}

		$this->assign('footerArticleType', $footerArticleType);
		$footerArticle = (APP_DEBUG ? null : S('footer_indexArticle'));
		if (!$footerArticle) {
			foreach ($footerArticleType as $k => $v) {
				$footerArticle[$v['name']] = M('ArticleType')->where(array('shang' => $v['name'], 'footer' => 1, 'status' => 1))->order('id asc')->limit(4)->select();
			}

			S('footer_indexArticle', $footerArticle);
		}

		// 底部友情链接--------------------S
		$footerindexLink = (APP_DEBUG ? null : S('index_indexLink'));
		if (!$footerindexLink) {
			$footerindexLink = M('Link')->where(array('status' => 1,'look_type'=>1))->order('sort asc ,id desc')->select();
		}

		$this->assign('footerindexLink', $footerindexLink);
		// 底部友情链接--------------------E

		// qq--------------------S
		$qqs = C('contact_qqun');
		foreach ($qqs as $k => $v) {
			$ss = $k + 1;
			$qqs[$k] = '会员'.$ss.'群：'.$v.'　';
		}
		$this->assign('qqs', $qqs);
		// qq--------------------E

		// 交易币种列表--------------------S
		$data = array();
		foreach (C('market') as $k => $v) {
			$v['xnb'] = explode('_', $v['name'])[0];
			$v['rmb'] = explode('_', $v['name'])[1];
			$data[$k]['name'] = $v['name'];
			$data[$k]['img'] = $v['xnbimg'];
			$data[$k]['title'] = $v['title'];
		}
		$this->assign('market_ss', $data);
		// 交易币种列表--------------------E
		
		//$notice_info = M('Article')->where(array('type' => 'aaa', 'status' => 1, 'index' => 1))->order('id desc')->find();
		$notice_info = M('Article')->where(array('type' =>array('like','notice_%'),'lang'=>LANG_SET, 'status' => 1, 'index' => 1))->order('id desc')->find();
		if(!$notice_info){
			$notice_info['id'] = 0;
			$notice_info['title'] = L('暂无公告');
			$notice_info['content'] = L('暂无信息');
		}
		
		//注册协议
		//$this->assign('registerAgreement',((LANG_SET=='zh-cn')?'/Article/detail/id/54.html':'/Article/detail/id/150.html'));
		$this->assign('registerAgreement','/Support/index/articles/cid/7/id/18.html');
		
		// 踢出内容中的标签
		$notice_info['content'] = strip_tags($notice_info['content']);
		$notice_type = M('ArticleType')->where(array('name' => 'aaa'))->find();
		$this->assign('notice_info', $notice_info);
		$this->assign('footerArticle', $footerArticle);
	}
}

?>