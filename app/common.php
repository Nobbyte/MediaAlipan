<?php
// 应用公共文件

function check_ext($ext){
    $videoExtensions = [
        'mp4', 'webm', 'mkv', 'avi', 'mov', 'flv', 'wmv', 'ogv', 'mpeg', 'mpg', 'm4v', '3gp', '3g2', 'ts', 'mts', 'm2ts', 'vob', 'divx', 'asf', 'rm', 'rmvb', 'ogg', 'ogv'
    ];
    $extension = strtolower($ext);

    return in_array($extension, $videoExtensions);

}