<?php /*a:1:{s:49:"C:\Users\YANG\Desktop\media\view\index\index.html";i:1729986981;}*/ ?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Media Alipan</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/static/res/layui/css/layui.css" rel="stylesheet">
</head>
<style>
  .layui-input:focus,
  .layui-textarea:focus {
    border-color: #1e9fff !important;
    box-shadow: none;
  }

  .layui-form-select dl dd.layui-this {
    background-color: #f8f8f8;
    color: #1e9fff;
    font-weight: 700;
  }

  .layui-table-view .layui-table td[data-edit]:hover:after {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    border: 1px solid #1e9fff;
    pointer-events: none;
    content: "";
  }
</style>

<body>
  <div id="LAY_app"></div>
  <script src="/static/res/layui/layui.js"></script>
  <script>
    layui.config({
      base: '/static/res/',
      version: new Date().getTime()
    }).use('index', function () {
      var layer = layui.layer;
      var admin = layui.admin;

    });
  </script>
</body>

</html>