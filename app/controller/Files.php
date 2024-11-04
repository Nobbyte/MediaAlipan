<?php

namespace app\controller;

use think\facade\Db;

class Files
{
    private $db;

    public function __construct() {
        $this->db = Db::table('files');
    }

    public function change_season()
    {
        $data = request()->post();
        foreach ($data as $item) {
            Db::table('files')->where('id', $item['id'])->update(['season' => $item['season']]);
        }
        $data = [
            'code' => 0,
        ];
        return json($data);
        
    }

    public function insertAll()
    {
        $data = request()->post();
        $data = array_map(function ($item) {
            $date = new \DateTime($item['updated_at']);
            return array_merge($item, ['updated_at' => $date->format('Y-m-d H:i:s')]);
        }, $data);
        $ret = $this->db->strict(false)->insertAll($data);
        $data = [
            'code' => 0,
            'ret' => $ret
        ];
        return json($data);
    }

    public function create()
    {
        $data = request()->post();
        $ret = $this->db->strict(false)->insert($data);
        $data = [
            'code' => 0,
            'ret' => $ret
        ];
        return json($data);
    }

    public function delete()
    {
        $id = request()->post('id');
        
        $ret = $this->db->where('id', $id)->delete();
        $data = [
            'code' => 0,
            'ret' => $ret
        ];
        return json($data);
    }

    public function list()
    {
        if(request()->has('media_id')){
            $media_id = request()->param('media_id');
            $this->db->where('media_id', $media_id);
        }
        $list = $this->db->select();
        $data = [
            'code' => 0,
            'data' => $list
        ];
        return json($data);
    }

    public function edit()
    {
        $data = request()->post();
        $ret = $this->db->strict(false)->update($data);
        $data = [
            'code' => 0,
            'ret' => $ret
        ];
        return json($data);
    }
}
