<?php
namespace Wsexgladminhtml\Controller;

class IssueController extends AdminController
{
	public function index($name = NULL, $field = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else if ($field == 'name') {
				$where['name'] = array('like', '%' . $name . '%');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		$count = M('Issue')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('Issue')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		
		$this->display();
	}
		public function issueimage()
			{
				$upload = new \Think\Upload();
				$upload->maxSize = 3145728;
                $dir = './Upload/issue/';
                if(!is_dir($dir)){
                    mkdir($dir,0777,true);
                }
				$upload->exts = array('jpg', 'gif', 'png', 'jpeg');
				$upload->rootPath = $dir;
				$upload->autoSub = false;
				$info = $upload->upload();

				foreach ($info as $k => $v) {
					$path = $v['savepath'] . $v['savename'];
					echo $path;
					exit();
				}
			}

	// 认购配置编辑页
	public function edit()
	{
		if (empty($_GET['id'])) {
			$this->data = false;
		}
		else {
			$this->data = M('Issue')->where(array('id' => trim($_GET['id'])))->find();
		}
		
		$this->display();
	}

    // 认购配置 - 提交编辑
	public function save()
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}
       /* if(empty($_POST['content']) ){
            $this->error('认购说明没有填写！');
        }*/
		$_POST['addtime'] = time();

		if (strtotime($_POST['time']) != strtotime(addtime(strtotime($_POST['time'])))) {
			$this->error('开启时间格式错误！');
		}
		// var_dump($_POST['id']);die;
		if($_POST['tuijian']==1){
			//推荐的话 先把其它的推荐修改成不推荐
			M('Issue')-> where('tuijian=1')->setField('tuijian','2');
		}
		$_POST['endtime']=strtotime($_POST['time'])+86400*$_POST['tian'];
		if ($_POST['id']) {
			$rs = M('Issue')->save($_POST);
		}
		else {
			$rs = M('Issue')->add($_POST);

		}

		if ($rs) {
			$this->success('操作成功！');
		}
		else {
			$this->error('操作失败！');
		}
	}

	public function status()
	{
		if (APP_DEMO) {
			$this->error('测试站暂时不能修改！');
		}

		if (IS_POST) {
			$id = array();
			$id = implode(',', $_POST['id']);
		}
		else {
			$id = $_GET['id'];
		}

		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}

		$where['id'] = array('in', $id);
		$method = $_GET['method'];

		switch (strtolower($method)) {
		case 'forbid':
			$data = array('status' => 0);
			break;

		case 'resume':
			$data = array('status' => 1);
			break;

		case 'del':
			if (M('Issue')->where($where)->delete()) {
				$this->success('操作成功！');
			}
			else {
				$this->error('操作失败！');
			}

			break;

		default:
			$this->error('参数非法');
		}

		if (M('Issue')->where($where)->save($data)) {
			$this->success('操作成功！');
		}
		else {
			$this->error('操作失败！');
		}
	}

	public function log($name = NULL)
	{
		if ($name && check($name, 'username')) {
			$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
		}
		else {
			$where = array();
		}

		$count = M('IssueLog')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('IssueLog')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
}

?>