<?php

require_once __DIR__ . '/../autoload.php';

use \Qiniu\Cdn\DomainManager;

$accessKey = 'uTvRfBkoiKSbooWgpKwoLv4vlYiyA17IoOnFC9T4';
$secretKey = 'Xx6xpexImznFzDkrHn4-P7w-tGuif5PzAg2rpn-p';
$domainName = 'blog.qcgzxw.cn';// 域名
$ca = '/var/cert/1_blog.qcgzxw.cn_bundle.crt'; // 证书地址
$pi = '/var/cert/2_blog.qcgzxw.cn.key'; // 秘钥地址
$auth = new Qiniu\Auth($accessKey, $secretKey);
$domainManager = new DomainManager($auth);
$caStr = file_get_contents($ca);
$piStr = file_get_contents($pi);
$res = $domainManager->createSslcert($domainName . date('Y-m-d', time()), $domainName, $caStr, $piStr);
$certid=$res[0]['certID'];
$result = $domainManager->httpsConf($domainName, $certid, false);
print_r($result);