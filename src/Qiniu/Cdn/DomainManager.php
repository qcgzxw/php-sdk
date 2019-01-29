<?php

namespace Qiniu\Cdn;

use Qiniu\Auth;
use Qiniu\Http\Error;
use Qiniu\Http\Client;

final class DomainManager
{
    private $auth;
    private $server;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $this->server = 'http://api.qiniu.com';
    }

    private function post($url, $body)
    {
        $headers = $this->auth->authorization($url, $body, 'application/json');
        $headers['Content-Type'] = 'application/json';
        $ret = Client::post($url, $body, $headers);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        $r = ($ret->body === null) ? array() : $ret->json();
        return array($r, null);
    }

    private function put($url, $body)
    {
        $headers = $this->auth->authorization($url, $body, 'application/json');
        $headers['Content-Type'] = 'application/json';
        $ret = Client::put($url, $body, $headers);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        $r = ($ret->body === null) ? array() : $ret->json();
        return array($r, null);
    }

    /**
     * 上传SSL证书
     *
     * @param string $name
     * @param string $commonName
     * @param string $ca
     * @param string $pi
     *
     * @return array 证书ID和错误信息
     */
    public function createSslcert($name, $commonName, $ca, $pi)
    {
        $req = array();
        if (!empty($name)) {
            $req['name'] = $name;
        }
        if (!empty($commonName)) {
            $req['common_name'] = $commonName;
        }
        if (!empty($ca)) {
            $req['ca'] = $ca;
        }
        if (!empty($pi)) {
            $req['pri'] = $pi;
        }
        $url = $this->server . '/sslcert';
        $body = json_encode($req, JSON_UNESCAPED_SLASHES);
        return $this->post($url, $body);
    }

    /**
     * 修改证书
     * @param string $name 域名name
     * @param string $certid 证书ID
     * @param bool $forceHttps 强制https
     *
     * @return array 错误信息
     *
     * @url https://developer.qiniu.com/fusion/api/4246/the-domain-name#11
     */
    public function httpsConf($name, $certid, $forceHttps)
    {
        $req = array();
        if (!empty($certid)) {
            $req['certid'] = $certid;
        }
        $req['forceHttps'] = $forceHttps;
        $url = $this->server . '/domain/' . $name . '/httpsconf';
        $body = json_encode($req, JSON_UNESCAPED_SLASHES);
        return $this->put($url, $body);
    }
}
