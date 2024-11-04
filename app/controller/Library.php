<?php

namespace app\controller;

use think\facade\Db;

class Library
{
    private $db;

    public function __construct() {
        $this->db = Db::name('library');
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

        $this->db->alias('l')
        ->leftJoin('medias f', 'l.id = f.library_id') // 使用正确的字段名称
        ->group('l.id')
        ->field('
            l.*,
            COUNT(f.id) AS media_count
        ');
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
