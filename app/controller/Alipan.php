<?php

namespace app\controller;

use think\facade\Db;
use GuzzleHttp\Client;

class Alipan
{
    private $access_token;
    
    private $auth_header;
    
    private $client;

    private $seting;
    
    public function __construct() {
        $this->seting = Db::table('system')->where('id', 1)->find();
        $this->client = new Client([
            'http_errors' => false,
            'verify' => false,
        ]);
    }

    public function player()
    {
        $id = request()->param('id');
        $data = Db::table('files')->where('id', $id)->find();
        if (!empty($data['download_url']) || !empty($data['expire_time'])) {
            $current_time = time();
            $expire_time = strtotime($data['expire_time']);
            if($current_time < $expire_time){
                return redirect($data['download_url']);
            }
        }
        $share_id = $data['share_id'];
        $file_id = $data['file_id'];
        $share_pwd = $data['share_pwd'];
        if($share_id == '' || $file_id == ''){
            return 'Invalid Parameter';
        }
        $direct_link = $this->save_share_file($share_id, $file_id, $share_pwd, $id);
        Db::table('files')->where('id', $id)->update([
            'download_url' => $direct_link,
            'expire_time' => date('Y-m-d H:i:s', time() + 12600)
        ]);

        return redirect($direct_link);
    }
    
    public function demo()
    {
        return $this->get_web_access_token();
        $data = [
            [
                'share_id' => 'hkDrkhaS3Jk',
                'file_id' => '64fe19a007fe2c91b019424ba748ba625c339de8',
                'name' => 'となりのトトロ(1988) - BD.mkv',
                'size' => 26403553885,
                'resolution' => '1080P',
                'updated_at' => '2023-09-10T19:31:44.518Z'
            ]
        ];
        return json($data);
    }

    public function get_all_share_files()
    {
        $url = request()->param('url');
        $string = str_replace(['https:','http:'],['',''], $url);
        $share_arr = explode(':',$string);
        $share_url = $share_arr[0];
        $share_pwd = $share_arr[1] ?? '';
        preg_match('/s\/([^\/]+)/', $share_url, $matchesS);
        preg_match('/folder\/([^:]+)/', $share_url, $matchesFolder);
        $share_id = isset($matchesS[1]) ? $matchesS[1] : null;
        $file_id = isset($matchesFolder[1]) ? $matchesFolder[1] : null;
        if($file_id == null){
            $root = $this->get_share_by_anonymous($share_id);
            $file_infos = $root['file_infos'] ?? [];
            if(count($file_infos) >= 1){
                $name = $file_infos[0]['file_name'];
                $file_id = $file_infos[0]['file_id'];
            }else{
                $name = $root['display_name'] ?? null;
                if($name == null){
                    return json([
                        'code' => -1,
                        'name' => '无任何文件',
                        'list' => []
                    ]);
                }
                $file_id = 'root';
            }
        }else{
            $data = $this->get_by_share($share_id, $file_id, $share_pwd);
            $name = $data['name'];
        }
        
        $media['name'] = $name;
        $arr = $this->core_get_all_share_files($share_id, $file_id, $share_pwd);
        $list = [];
        foreach ($arr as $item) {
            if(check_ext($item['file_extension'])){
                $item['resolution'] = $item['video_media_metadata']['height'] . 'P';
                $list[] = $item;
            }
        }
        $media['list'] = $list;
        return json($media);
    }

    public function core_get_all_share_files($share_id, $file_id, $share_pwd='')
    {
        $data = $this->list_by_share($share_id, $file_id, $share_pwd);
        $arr = [];
        foreach ($data as $item) {
            if($item['type'] == 'folder'){
                $folderName = $item['name'];
                $newData = $this->core_get_all_share_files($item['share_id'], $item['file_id'], $share_pwd);
                foreach($newData as $newItem) {
                    $newItem['season'] = $folderName;
                    $arr[] = $newItem;
                }
            }else{
                $arr[] = $item;
            }
        }
        return $arr;
    }

    public function get_share_files()
    {
        //https://www.alipan.com/s/hkDrkhaS3Jk/folder/64fe199e8efd1f34bca84f4aba190f90dec47ef4
        $url = request()->param('url');
        $string = str_replace(['https:','http:'],['',''], $url);
        $share_arr = explode(':',$string);
        $share_url = $share_arr[0];
        $share_pwd = $share_arr[1] ?? '';
        preg_match('/s\/([^\/]+)/', $share_url, $matchesS);
        preg_match('/folder\/([^:]+)/', $share_url, $matchesFolder);
        $share_id = isset($matchesS[1]) ? $matchesS[1] : null;
        $file_id = isset($matchesFolder[1]) ? $matchesFolder[1] : null;
        $data = $this->list_by_share($share_id, $file_id, $share_pwd);

        $arr = [];

        foreach ($data as $item) {
            if($item['type'] == 'file'){
                if(check_ext($item['file_extension'])){
                    $item['resolution'] = $item['video_media_metadata']['height'] . 'P';
                    $arr[] = $item;
                }
            }

        }
        return json($arr);
    }

    private function get_share_by_anonymous($share_id)
    {
        $api = 'https://api.aliyundrive.com/adrive/v3/share_link/get_share_by_anonymous';
        $data = [
            'share_id' => $share_id,
        ];

        $response = $this->client->post($api, [
            'json' => $data
        ]);

        $responseBody = (string) $response->getBody();

        $array = json_decode($responseBody, true);

        return $array;
    }

    private function get_by_share($share_id, $file_id, $share_pwd='')
    {
        $api = 'https://api.aliyundrive.com/adrive/v2/file/get_by_share';

        $share_token = $this->get_share_token($share_id, $share_pwd);
        
        $data = [
            'drive_id' => '',
            'fields' => '*',
            'file_id' => $file_id,
            'share_id' => $share_id,
        ];

        $response = $this->client->post($api, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->get_web_access_token(),
                'x-share-token' => $share_token
            ],
            'json' => $data
        ]);

        $responseBody = (string) $response->getBody();

        $array = json_decode($responseBody, true);

        return $array;
    }

    private function list_by_share($share_id, $file_id, $share_pwd='')
    {
        $api = 'https://api.aliyundrive.com/adrive/v2/file/list_by_share';

        $share_token = $this->get_share_token($share_id, $share_pwd);

        $data = [
            'limit' => 100,
            'order_by' => 'name',
            'order_direction' => 'DESC',
            'parent_file_id' => $file_id,
            'share_id' => $share_id,
        ];

        $response = $this->client->post($api, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->get_web_access_token(),
                'x-share-token' => $share_token
            ],
            'json' => $data
        ]);

        $responseBody = (string) $response->getBody();

        $array = json_decode($responseBody, true);

        $items = $array['items'] ?? [];

        return $items;
    }
    

    
    private function get_web_access_token()
    {
        $web_access_token = cache('web_access_token');
        
        if($web_access_token !== null){
            return $web_access_token;
        }else{
            $api = 'https://auth.aliyundrive.com/v2/account/token';
            $web_refresh_token = $this->seting['token'];
            if($web_refresh_token == null){
                return '没有web_refresh_token';
            }
            $data = [
                'refresh_token' => $web_refresh_token,
                'grant_type' => 'refresh_token',
            ];
            
            $response = $this->client->post($api, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $data
            ]);

            $responseBody = (string) $response->getBody();
            $array = json_decode($responseBody, true);
            
            $web_refresh_token = $array['refresh_token'] ?? null;
            $web_access_token = $array['access_token'] ?? null;
            Db::name('system')->where('id', 1)->update(['token' => $web_refresh_token]);
            cache('web_access_token', $web_access_token, 7000);
            
            return $web_access_token;
        }

    }

    private function save_share_file($share_id, $file_id, $share_pwd = '')
    {
        $api = 'https://api.aliyundrive.com/v2/file/copy';
        $share_token = $this->get_share_token($share_id, $share_pwd);
        $web_access_token = $this->get_web_access_token();

        $data = [
            'file_id' => $file_id,
            'share_id' => $share_id,
            'to_drive_id' => $this->seting['drive_id'],
            'to_parent_file_id' => $this->seting['parent_file_id'],
            'auto_rename' => true
        ];

        $response = $this->client->post($api, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->get_web_access_token(),
                'x-share-token' => $share_token
            ],
            'json' => $data
        ]);

        $responseBody = (string) $response->getBody();
        
        $array = json_decode($responseBody, true);
        
        $save_drive_id = $array['drive_id'] ?? null;
        $save_file_id = $array['file_id'] ?? null;
        
        if($save_drive_id !== null && $save_file_id !== null){
            $download_url = $this->get_download_url($save_drive_id, $save_file_id);
            return $download_url;
        }else{
            return 'Error: ' .$responseBody;
        }
    }
    
    private function get_share_token($share_id, $share_pwd = '')
    {
        $api = 'https://api.aliyundrive.com/v2/share_link/get_share_token';
        $data = [
            'share_id'  => $share_id,
            'share_pwd' => $share_pwd
        ];
        
        $response = $this->client->post($api, [
            'json' => $data
        ]);

        $responseBody = (string) $response->getBody();
        $array = json_decode($responseBody, true);
        
        $share_token = $array['share_token'] ?? null;
        return $share_token;        
    }
    
    private function get_access_token()
    {
        $access_token = cache('access_token');
        if($access_token !== null){
            return $access_token;
        }else{
            $api = $this->seting['open_token_url'];
            $refresh_token = $this->seting['open_token'];
            if($refresh_token == null){
                return '没有refresh_token';
            }
            $data = [
                'refresh_token'  => $refresh_token,
            ];
            
            $response = $this->client->post($api, [
                'json' => $data
            ]);
            
            $responseBody = (string) $response->getBody();
            $array = json_decode($responseBody, true);
            
            $refresh_token = $array['refresh_token'] ?? null;
            $access_token = $array['access_token'] ?? null;
            Db::name('system')->where('id', 1)->update(['open_token' => $refresh_token]);
            cache('access_token', $access_token, 7000);
            return $access_token;
        }
    }

    private function get_download_url($drive_id, $file_id)
    {
        $api = 'https://open.aliyundrive.com/adrive/v1.0/openFile/getDownloadUrl';
        $data = [
            'drive_id'  => $drive_id,
            'file_id' => $file_id,
            'expire_sec' => 14400
        ];
        
        $response = $this->client->post($api, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->get_access_token()
            ],
            'json' => $data
        ]);

        $responseBody = (string) $response->getBody();
        
        $array = json_decode($responseBody, true);
        
        $download_url = $array['url'] ?? null;
        return $download_url;
    }

}
