<?php
header("Content-Type: text/html; charset=UTF-8");
function http_gets($url) {
	$oCurl = curl_init();
	if (stripos($url,"https://")!==FALSE) {
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
	}
	curl_setopt($oCurl, CURLOPT_URL, $url);
	curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
	$sContent = curl_exec($oCurl);
	$aStatus = curl_getinfo($oCurl);
	curl_close($oCurl);
	if (intval($aStatus["http_code"])==200) {
		return true;
	} else {
		return false;
	}
}

$domain = "http://cs.mochain.co:804";//填写网站域名
$queues = array(
	'Home/Queue/ethcovera99b88c77d66e55/',      //ETH汇总
/*	'Home/Queue/etccovera99b88c77d66e55/',      //ETC汇总
	'Home/Queue/tokencovera88b77c11d0a9d/coin/suf',      //代币汇总
	'Home/Queue/tokencovera88b77c11d0a9d/coin/cw',       //代币汇总
	'Home/Queue/tokencovera88b77c11d0a9d/coin/fff',      //代币汇总
*/
);

$fp = fopen("locktoken.txt", "w+");
if (flock($fp,LOCK_EX | LOCK_NB)) {
	for ($i=0;$i<count($queues);$i++) {
		http_gets("{$domain}/{$queues[$i]}");
	}
	flock($fp,LOCK_UN);
}
fclose($fp);
echo "run successfully";
?>