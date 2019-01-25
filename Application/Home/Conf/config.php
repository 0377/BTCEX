<?php
return array(
    'LAYOUT_ON'=>true,
    'LAYOUT_NAME'=>'layout',
	'TMPL_PARSE_STRING' => array('__UPLOAD__' => __ROOT__ . '/Upload', '__PUBLIC__' => __ROOT__ . '/Public', '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images', '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css', '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js'),
    'create_url' => '47.244.52.38:8802/api/createAddr',
    'url' => '47.244.52.38:8802/api/queryAddress', // 转出查询
    'trade_url' => '47.244.52.38:8802/api/trade', // 转出交易
    'center_account' => '1FmexStVZSKdtfJcTknS7B1XmCnVGnpsZ6' //usdt 测试中心账号
);
?>