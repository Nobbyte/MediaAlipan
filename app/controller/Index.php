<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Index extends BaseController
{
    public function index()
    {
        return view('index');
    }

    public function update()
    {
// 设置根目录
$rootDirectory = 'E:/媒体库';

// 确保根目录存在
if (!is_dir($rootDirectory)) {
    if (!mkdir($rootDirectory, 0777, true)) {
        die("无法创建根目录: $rootDirectory");
    }
} else {
    if (!is_writable($rootDirectory)) {
        die("根目录不可写: $rootDirectory");
    }
}

// 获取所有图书馆数据
$libraries = Db::table('library')->select()->toArray();

// 获取所有媒体数据，并关联图书馆
$medias = Db::table('medias')
              ->alias('m')
              ->join('library l', 'm.library_id = l.id', 'LEFT')
              ->field('m.*, l.name as library_name')
              ->select()
              ->toArray();

// 获取所有文件数据，并关联媒体和season字段
$files = Db::table('files')
             ->alias('f')
             ->join('medias m', 'f.media_id = m.id', 'LEFT')
             ->field('f.*, m.name as media_name, f.season') // 注意这里获取 files 表中的 season 字段
             ->select()
             ->toArray();

// 创建树形结构
foreach ($libraries as $library) {
    $libraryName = trim($library['name']);
    $libraryName = str_replace(':', '﹕', $libraryName); // 替换冒号
    $libraryDir = $rootDirectory . '/' . $libraryName;
    
    // 创建图书馆目录
    if (!is_dir($libraryDir)) {
        echo "Creating library directory: $libraryDir\n";
        if (!mkdir($libraryDir, 0777, true)) {
            die("无法创建图书馆目录: $libraryDir");
        }
    } else {
        if (!is_writable($libraryDir)) {
            die("图书馆目录不可写: $libraryDir");
        }
    }

    foreach ($medias as $media) {
        if ($media['library_id'] === $library['id']) {
            $mediaName = trim($media['name']);
            $mediaName = str_replace(':', '﹕', $mediaName); // 替换冒号
            $mediaDir = $libraryDir . '/' . $mediaName;

            // 创建媒体目录
            if (!is_dir($mediaDir)) {
                echo "Creating media directory: $mediaDir\n";
                if (!mkdir($mediaDir, 0777, true)) {
                    die("无法创建媒体目录: $mediaDir");
                }
            } else {
                if (!is_writable($mediaDir)) {
                    die("媒体目录不可写: $mediaDir");
                }
            }

            foreach ($files as $file) {
                if ($file['media_id'] === $media['id']) {
                    $fileName = trim($file['name']);
                    $fileName = str_replace(':', '﹕', $fileName); // 替换冒号

                    // 检查 season 字段
                    if (!empty($file['season'])) {
                        $seasonName = trim($file['season']);
                        $seasonName = str_replace(':', '﹕', $seasonName); // 替换冒号
                        
                        $seasonDir = $mediaDir . '/' . $seasonName;
                        
                        // 创建季节目录
                        if (!is_dir($seasonDir)) {
                            echo "Creating season directory: $seasonDir\n";
                            if (!mkdir($seasonDir, 0777, true)) {
                                die("无法创建季节目录: $seasonDir");
                            }
                        } else {
                            if (!is_writable($seasonDir)) {
                                die("季节目录不可写: $seasonDir");
                            }
                        }
                        
                        // 将文件写入季节目录
                        $filePath = $seasonDir . '/' . $fileName . '.strm';
                    } else {
                        // 如果 season 字段为空，则直接写入媒体目录
                        $filePath = $mediaDir . '/' . $fileName . '.strm';
                    }
                    
                    // 创建文件并写入内容
                    $content = "http://plex.com/alipan/player/id/{$file['id']}";
                    if (!file_put_contents($filePath, $content)) {
                        die("无法创建文件: $filePath");
                    }
                }
            }
        }
    }
}

echo "文件夹和文件创建完成。\n";
    }
}
