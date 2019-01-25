<?php

return [
    'TMPL_PARSE_STRING' => ['__UPLOAD__' => __ROOT__ . '/Upload', '__PUBLIC__' => __ROOT__ . '/Public',
	'__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
	'__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
	'__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
	'__WEBTITLE__' => "管理中心 -ADMIN EX"],
	
	'TMPL_ACTION_ERROR' => './Public/admin_error.html', //默认错误跳转对应的模板文件
	'TMPL_ACTION_SUCCESS' => './Public/admin_success.html', //默认成功跳转对应的模板文件
    'url' => '47.244.52.38:8802/api/queryAddress', // 转出查询
    'trade_url' => '47.244.52.38:8802/api/trade', // 转出交易
//    'URL_ROUTER_ON'   => true,
//    'URL_ROUTE_RULES'=>array(
//        'admin/1' => 'index/index',
//        'news/:id'               => 'News/read',
//        'news/read/:id'          => '/news/:1',
//    ),
];
?>