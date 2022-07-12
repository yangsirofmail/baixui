<!-- 验证代码 写在开头  -->
<?php 
require_once 'inc/funcation.php' ;
get_fn_user();
?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="../assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="../assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <script src="../assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">

  <?php include 'inc/navbar.php' ?>

    <div class="container-fluid">
      <div class="page-title">
        <h1>我的个人资料</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <form class="form-horizontal">
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="avatar" type="file">
              <img src="../assets/img/default.png">
              <!-- 隐藏的文本框 也叫隐藏域 可以提交一个键值对 -->
              <input type="hidden" name="avatar">
              <i class="mask fa fa-upload"></i>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="w@zce.me" placeholder="邮箱" readonly>
            <p class="help-block">登录邮箱不允许修改</p>
          </div>
        </div>
        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">别名</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type" value="zce" placeholder="slug">
            <p class="help-block">https://zce.me/author/<strong>zce</strong></p>
          </div>
        </div>
        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type" value="汪磊" placeholder="昵称">
            <p class="help-block">限制在 2-16 个字符</p>
          </div>
        </div>
        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" class="form-control" placeholder="Bio" cols="30" rows="6">MAKE IT BETTER!</textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary">更新</button>
            <a class="btn btn-link" href="password-reset.html">修改密码</a>
          </div>
        </div>
      </form>
    </div>
  </div>

 <!-- 侧边栏 -->
  <?php $current_page='profille'; ?>
  <?php include 'inc/sidebar.php' ?>


  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
<!-- 
// 个人中心客户端代码  也就是ajax代码 -->
<script>

$('#avatar').on('change',function()
{
  var $this=$(this);
  // 当文件选择状态发生变化就会执行这个事件处理函数
var des=$(this).prop('files');
  if(!des.length)return;
  // 打印上传的对象  0表示当前文件的信息 有name size  type 等 
  console.log(des);

// 拿到要上传的文件
var file=des[0];
// // formdate是html5中新增的成员、专门配合ajax操作用于在客户端和服务器之间传递二进制数据
var data=new FormData()
// // 将拿到的文件信息 添加到二进制的请求体重
data.append('avatar',file)
// 创建ajax方式的数据请求
var xhr=new XMLHttpRequest()
xhr.open('POST','/admin/api/upload.php')
xhr.send(data)//借助form data 将转换后的二进制数据传递
xhr.onload=function()
{
console.log(this.responseText);
// 拿到返回的保存路径的url 设置下一个元素的src
$this.siblings('img').attr('src',this.responseText)
// 将拿到的图片的url设置为隐藏域的默认value  点击提交可以随着get或者post提交 服务器接收并保存即可
$this.siblings('input').val(this.responseText)

}

})

</script>

  <script>NProgress.done()</script>
</body>
</html>
