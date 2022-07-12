<!-- 这个是最后追加的 引用豆瓣的接口 实现ajax跨域调用  自己用jsonp 
fontawesome.io  是字体图标  list-alt  需要导入对应的库 font-awesome-->
<!-- 验证代码 写在开头  -->
<?php 
require_once 'inc/funcation.php' ;
get_fn_user();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Navigation menus &laquo; Admin</title>
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
        <h1>电影推荐</h1>
      </div>
  
      
    </div>
  </div>

  <?php $current_page='douban'; ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>

<!-- 豆瓣接口
正式请求链接
developer.douban.com

http://api.douban.com/v2/movie/search?tag=foo

-->
<script>
    // 域名不一样 不同源 默认不允许 jsonp需要服务器配合
// $.get('http://api.douban.com/v2/movie/in_theaters',{},function(res)
// {
//     console.log(res)
// })



// 现在不能用了 豆瓣的api不能让个人调用了草
$.ajax(
             {
                // 设置跨域的url
                url:'http://api.douban.com/v2/movie/in_theaters',               
                dataType: 'jsonp',//method的 请求方式 jsonp类型
            //    设置回调函数
                success: function (res) {
            
                    $(res.subjects).each(function(i,item)
                    {
                        $('#movies').append(`<li>${item.title}</li`)
                    })
                }

            })
</script>
<!-- //----------------------------------------------- -->


  <script>NProgress.done()</script>
</body>
</html>
