<!-- 验证代码 写在开头  -->
<?php
require_once 'inc/funcation.php';
// 这个是判断登录标识 如果正常登录返回登录的用户信息 如果非正常登录 跳转到用户登录页面
get_fn_user();

// 只有正确登录了才能执行接下来的代码


// 将重复的查询函数封装起来 调用 返回的是数组 as是给返回的字段取别名  第0个字段的属性为num的值  因为封装了 所以不要[0]
$posts_count = get_mysql_query_one('select count(1) as num from posts')['num'];
// 判断是否拿到数据 如果拿到的数据为空 表示查询失败
if (!isset($posts_count)) {
  exit('查询失败1');
}
$posts_drafted = get_mysql_query_one("SELECT COUNT(1) as num FROM posts WHERE STATUS='drafted';")['num'];
// 判断是否拿到数据 如果拿到的数据为空 表示查询失败
if (!isset($posts_drafted)) {
  exit('查询失败2');
}
$categories_count = get_mysql_query_one("SELECT  COUNT(1) as num FROM categories;")['num'];
// 判断是否拿到数据 如果拿到的数据为空 表示查询失败
if (!isset($categories_count)) {
  exit('查询失败3');
}
$comments_count = get_mysql_query_one("SELECT COUNT(1) as num FROM comments;")['num'];
// 判断是否拿到数据 如果拿到的数据为空 表示查询失败
if (!isset($comments_count)) {
  exit('查询失败4');
}
$comments_held = get_mysql_query_one("SELECT COUNT(1) as num FROM comments WHERE STATUS='held';")['num'];
// 判断是否拿到数据 如果拿到的数据为空 表示查询失败 最大的可能就是查询语句写错了
if (!isset($comments_held)) {
  exit('查询失败5');
}

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
  <script>
    NProgress.start()
  </script>

  <div class="main">

    <?php include 'inc/navbar.php' ?>

    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.html" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $posts_count ?></strong>篇文章（<strong><?php echo $posts_drafted ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong> <?php echo $categories_count ?> </strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $comments_count ?></strong>条评论（<strong><?php echo $comments_held ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <!-- 第二列 -->
        <div class="col-md-4">
          <canvas id="myChart" width="850" height="400"></canvas>
        </div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

  <!-- <div class="aside">
    <div class="profile">
      <img class="avatar" src="../uploads/avatar.jpg">
      <h3 class="name">布头儿</h3>
    </div>
    <ul class="nav">
      <li class="active">
        <a href="index.html"><i class="fa fa-dashboard"></i>仪表盘</a>
      </li>
      <li>
        <a href="#menu-posts" class="collapsed" data-toggle="collapse">
          <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-posts" class="collapse">
          <li><a href="posts.html">所有文章</a></li>
          <li><a href="post-add.html">写文章</a></li>
          <li><a href="categories.html">分类目录</a></li>
        </ul>
      </li>
      <li>
        <a href="comments.html"><i class="fa fa-comments"></i>评论</a>
      </li>
      <li>
        <a href="users.html"><i class="fa fa-users"></i>用户</a>
      </li>
      <li>
        <a href="#menu-settings" class="collapsed" data-toggle="collapse">
          <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-settings" class="collapse">
          <li><a href="nav-menus.html">导航菜单</a></li>
          <li><a href="slides.html">图片轮播</a></li>
          <li><a href="settings.html">网站设置</a></li>
        </ul>
      </li>
    </ul>
  </div> -->

  <?php $current_page = 'index'; ?>
  <?php include 'inc/sidebar.php' ?>


  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    NProgress.done()
  </script>
  <!-- 导入char.js -->
  <script src="../assets/vendors/char/dist/Chart.js"></script>
  <!-- 实现图表数据展示 -->
  <script>
    // 获取元素标签
    const ctx = document.getElementById('myChart').getContext('2d');
    // 创建实例画布  ctx就是画布  参2就是传入画布参数对象即可
    const myChart = new Chart(ctx, {
      type: 'pie',//类型是pie 也就是源
      data: {  
        labels: ['文章', '草稿', '分类', '评论', '审核'],//标题 
        datasets: [//datasets内部存储的是具体值和颜色          
          {//data里面有几个 圆就被累加和后均分几分
            data: [<?php echo $posts_count ?>, <?php echo $posts_drafted ?>, <?php echo $categories_count ?>, <?php echo $comments_count ?>, <?php echo $comments_held ?>],
            backgroundColor: [//这个是每块颜色的设置 字母 十六进制 rgba都可以
              'rgba(255, 99, 132, 0.2)',
              'rgba(54, 162, 2, 0.2)',
              'rgba(255, 206, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(153, 102, 255, 0.2)',
              'rgba(115, 119, 119, 0.8)'

            ],
            borderWidth: 1,//设置边宽
          },
          {//这个是内环圆
            data: [<?php echo $posts_count ?>, <?php echo $posts_drafted ?>, <?php echo $categories_count ?>, <?php echo $comments_count ?>, <?php echo $comments_held ?>],
            backgroundColor: [

              // 不管什么颜色进制都可以
              'red',
              'pink',
              'hotpink',
              'deeppink',
              'palevioletred',

            ],
            borderWidth: 1,

          }
        ]
      }
    });
  </script>



</body>

</html>