<?php

namespace app\controller;

use think\facade\Db;

class Medias
{
    private $db;

    public function __construct() {
        $this->db = Db::name('medias');
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

    public function create_get_id()
    {
        $data = request()->post();
        $ret = $this->db->strict(false)->insertGetId($data);
        $data = [
            'code' => 0,
            'id' => $ret
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
       // 检查是否有 'library_id' 参数
       $query = $this->db;
       if (request()->has('library_id')) {
        $library_id = request()->param('library_id');
        $query->where('l.library_id', $library_id); // 使用 'l.id' 而不是 'library_id'
    }

    $query->alias('l')
          ->leftJoin('files f', 'l.id = f.media_id') // 使用正确的字段名称
          ->group('l.id')
          ->field('
              l.*,
              COUNT(f.id) AS file_count
          ');
        $list = $query->select();
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
