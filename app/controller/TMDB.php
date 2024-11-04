<?php

namespace app\controller;

use think\facade\Db;
use GuzzleHttp\Client;

class TMDB
{

    private $api_key;

    public function __construct() {
        $system = Db::table('system')->where('id', 1)->find();
        $this->api_key = $system['tmdb_api_key'];
    }

    function search_movie() {
        $query = request()->param('query');
        $query = str_replace(['（','）'],['(',')'],$query);
        $pattern = '/\([0-9]{4}\)/';
        $year = '';
        if (preg_match($pattern, $query, $matches)) {
            $yearStr = $matches[0];
            $year = substr($yearStr, 1, -1);
            
            // 检查括号内的数字是否为有效的年份
            if (checkdate(1, 1, $year)) {
                // 删除整个括号部分
                $query = preg_replace($pattern, '', $query);
            }
        }


        $client = new Client(); // 创建Guzzle HTTP客户端
        $base_url = "http://api.tmdb.org/3/search/movie";
        $query_params = [
            'api_key' => $this->api_key,
            'query' => $query,
            'language' => 'zh-CN', // 设置语言参数为简体中文
            'year' => $year
        ];
    
            // 发送GET请求
            $response = $client->request('GET', $base_url, [
                'query' => $query_params
            ]);
    
            // 解析响应内容
            $result = json_decode((string)$response->getBody(), true);
            return $response->getBody();
    }

    function search_tv() {
        $query = request()->param('query');
        $query = str_replace(['（','）'],['(',')'],$query);
        $pattern = '/\([0-9]{4}\)/';
        $year = '';
        if (preg_match($pattern, $query, $matches)) {
            $yearStr = $matches[0];
            $year = substr($yearStr, 1, -1);
            if (checkdate(1, 1, $year)) {
                $query = preg_replace($pattern, '', $query);
            }
        }

        $client = new Client(); // 创建Guzzle HTTP客户端
        $base_url = "http://api.tmdb.org/3/search/tv";
        $query_params = [
            'api_key' => $this->api_key,
            'query' => $query,
            'language' => 'zh-CN', // 设置语言参数为简体中文
            'year' => $year
        ];
    
            // 发送GET请求
            $response = $client->request('GET', $base_url, [
                'query' => $query_params
            ]);
    
            // 解析响应内容
            $result = json_decode((string)$response->getBody(), true);
            return $response->getBody();
    }
}
