<?php

namespace app\controller;

use think\facade\Db;

class System
{


    public function seting()
    {
        $method = request()->method();
        switch ($method) {
            case 'GET':
                $seting = Db::table('system')->where('id', 1)->find();
                return json($seting);
                break;
            case 'POST':
                $data = request()->post();
                Db::name('system')->where('id', 1)->update($data);
                return json([
                    'code' => 0
                ]);
                break;
            default:
                # code...
                break;
        }
        

    }

    public function menus()
    {
        $library = Db::table('library')->select();

        $menus = [
            [
                'title' => '媒体中心',
                'jump'  => '/'
            ]
        ];

        foreach ($library as $value) {
            $info = [
                'id' => $value['id'],
                'name' => $value['name']
            ];
            $info_base64 = str_replace('=','',base64_encode(json_encode($info)));
            if($value['type'] == 1){
                $type = 'movie';
            }elseif($value['type'] == 2){
                $type = 'tv';
            }
            $menus[] = [
                'title' => $value['name'],
                'jump' => $type.'/info='. $info_base64
            ];
        }


        $set = [
            'title' => '系统设置',
            'icon' => 'layui-icon-set',
            'jump'  => '/set'
        ];
        $menus[] = $set;


        $home = [[
            'name' => 'home',
            'title' => '控制台',
            'icon' => 'layui-icon-home',
            'spread' => true,
            'list' => $menus
        ]];



        

        $data = [
            'code' => 0,
            'data' => $home
        ];

        return json($data);
    }

    public function set()
    {

    }
}
