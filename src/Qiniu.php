<?php

/*
 * This file is part of the laravuel/qiniu.
 *
 * (c) laravuel <45761113@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Laravuel\Qiniu;

use Qiniu\Auth;
use Illuminate\Support\Str;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use Illuminate\Support\Facades\Log;

class Qiniu 
{
    public $config;
    public $auth;
    public $request;

    public function __construct($config) 
    {
        $this->config = $config;
        $this->request = app('request');
        $this->auth = new Auth($this->config['access_key'], $this->config['secret_key']);
        $this->bucketManager = new BucketManager($this->auth);
    }

    public function getBaseUri() 
    {
        return $this->config['base_uri'];
    }

    /**
     * 获取upload token
     */
    public function getToken($policy = [], $callbackBody = [], $expire = 3600, $strictPolicy = true)
    {
        if ($callbackBody 
            && array_key_exists('custom', $callbackBody) 
            && is_array($callbackBody['custom']) 
            && count($callbackBody['custom'])) 
        {
            foreach ($callbackBody['custom'] as $name) {
                $callbackBody[$name] = '$(x:'. $name .')';
            }
            unset($callbackBody['custom']);
        }

        $policy = array_merge([
            'callbackUrl' => $this->config['callback'],
            'callbackHost' => $this->config['callback_host'],
            'insertOnly' => intval($this->config['insert_only']),
            'fileType' => intval($this->config['file_type']),
            'callbackBody' => json_encode(array_merge([
                'key' => '$(key)',
                'size' => '$(fsize)',
                'filename' => '$(fname)',
                'ext' => '$(ext)',
            ], $callbackBody)),
            'callbackBodyType' => 'application/json'
        ], $policy);

        return $this->auth->uploadToken($this->config['bucket'], null, $expire, $policy, $strictPolicy);
    }

    /**
     * 回调验证
     */
    public function verifyCallback($success = null, $fail = null)
    {
        $contentType = 'application/json';
        $authorization = $this->request->headers->get('Authorization');
        Log::info('qiniu callback authorization：'.$authorization);
        $res = $this->auth->verifyCallback($contentType, $authorization, $this->config['callback'], $this->request->all());
        if ($res) {
            return $success instanceof \Closure ? call_user_func($success) : true;
        }
        return $fail instanceof \Closure ? call_user_func($fail) : false;
    }

    /**
     * 下载私有文件
     * @param $key 文件key
     */
    public function privateDownloadUrl($key)
    {
        $baseUrl = "http://{$this->config['domain']}/{$key}";
        return $this->auth->privateDownloadUrl($baseUrl);
    }

    /**
     * 复制资源文件
     * @param $key 原始文件key
     * @param $force 强制
     */
    public function copy($key, $force = true)
    {
        preg_match('/_copy_[\s\S]+?_/', $key, $match);
        if ($match) {
            $newKey = preg_replace('/(_copy_)([\s\S]+?)(_)/', '$1'.Str::random(8).'$3', $key);
        }
        else {
            $newKey = $key.'_copy_'.Str::random(8).'_';
        }
        $res = $this->bucketManager->copy($this->config['bucket'], $key, $this->config['bucket'], $newKey, $force);
        if (!$res) {
            return $newKey;
        }
        Log::error($res);
        return false;
    }

    /**
     * 删除资源文件
     * @param $key 文件key
     */
    public function delete($key)
    {
        $res = $this->bucketManager->delete($this->config['bucket'], $key);
        if (!$res) {
            return true;
        }
        Log::error(print_r($res, true));
        return false;
    }
}