<?php
// <!-- 这里面是公共的样式 这个是侧边栏样式    开始 -->

// 导入文件   __FILE__  获取文件当前的地址   direname是只要路径信息  这个写是即使移动这个所谓的文件也不会出现路径不一致
 require_once dirname(__FILE__).'/funcation.php';
// 输出路径
// echo  dirname(__FILE__).'/funcation.php';
// __FILE__返回当前文件所在的路径包括文件名
// echo  __FILE__;
//dirname只返回当前文件所在路径的路径信息不包括文件名
// echo dirname(__FILE__);


// 提前声明 防止报错
$current_page =isset($current_page)?$current_page:''; 


$current_user=get_fn_user();

// 下面代码被抽象到了funcation.php中
// // 开启session
// session_start();
// // 将session的自定义属性取出来 里面存储的是用户id的信息
// $current_user=$_SESSION['current_login_user'];
?>

<!-- 提前声明防止出现未定义情况  也可以使用$_SERVER['PHP_SELF']取代 $current_page 效果类似-->

<div class="aside">
  <div class="profile">
    <img class="avatar" src="<?php echo $current_user['avatar'] ?>">
    <!-- 拿到session的数据后将用户名的假数据替换为真数据   -->
    <h3 class="name"><?php echo $current_user['nickname'] ?></h3>
  </div>
  <ul class="nav">

    <!-- 仪表盘页面 -->
    <!-- <li class="active"> 去掉了active 换成了php  这是焦点样式 那个样式为active 焦点就会跑到对应的
        作用声明一个变量存储当前页面名 如果点击到当前页面让active为真 否则为空-->
    <li <?php echo $current_page === 'index' ? 'class="active"' : '' ?>>
      <a href="/admin/index.php"><i class="fa fa-dashboard"></i>仪表盘</a>
    </li>

    <!-- 文章页面   如果点击到当前a标签 判断$current_page存储的值 如果跟当前的here对应的文件名的值相同 就让其为calss加上active样式-->
    <!-- 这是一种多层或的写法 <li  ?php echo $current_page ==='post-add' || $current_page ==='posts'||$current_page ==='categories'  ?  'class="active"' : '' ? >> -->
    <!-- 这是用数组存储多个变量 -->
    <?php $munu_posts = array('posts', 'post-add', 'categories'); ?>
    <!-- 用in_array 判断当前变量$current_page和数组元素匹配 如果为真就给class设置active-->
    <li <?php echo in_array($current_page, $munu_posts) ? 'class="active"' : '' ?>>
      <a href="#menu-posts" <?php echo in_array($current_page, $munu_posts) ? '' : 'class="collapsed"' ?>  data-toggle="collapse">
        <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
      </a>
      <!-- 如果符合条件 就给当前collapse追加一个in 让其页面展开 -->
      <ul id="menu-posts" class="collapse  <?php echo in_array($current_page, $munu_posts) ? ' in' : '' ?>">
        <!-- 写三元表达式匹配是否选中 如果选中对应的$current_page会被赋值为当前页面的页面名  在导航页用三元表达式判断当前$current_page存储的导航页名称 为真给赋值class的active样式 -->
        <li <?php echo $current_page === 'post-add' ? 'class="active"' : '' ?>><a href="/admin/post-add.php">写文章</a></li>
        <li <?php echo $current_page === 'posts' ? 'class="active"' : '' ?>><a href="/admin/posts.php">所有文章</a></li>
        <li <?php echo $current_page === 'categories' ? 'class="active"' : '' ?>><a href="/admin/categories.php">分类目录</a></li>
      </ul>
    </li>
    <!-- 评论页面 -->

    <li <?php echo $current_page === 'comments' ? 'class="active"' : '' ?>>
      <a href="/admin/comments.php"><i class="fa fa-comments"></i>评论</a>
    </li>
    <!-- 用户页面 -->
    <li <?php echo $current_page === 'users' ? 'class="active"' : '' ?>>
      <a href="/admin/users.php"><i class="fa fa-users"></i>用户</a>
    </li>

    <!-- 设置页面 -->
<!-- 将页面设置为数组 -->
    <?php $munu_settings = array('nav-menus', 'slides', 'settings'); ?>
    <!-- 通过in array匹配对应数组 为真给active -->
    <li <?php echo in_array($current_page, $munu_settings) ? 'class="active"' : '' ?> >
      <a href="#menu-settings" <?php echo in_array($current_page, $munu_settings) ? '' : 'class="collapsed"' ?> data-toggle="collapse">
        <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
      </a>
      <!-- 匹配对应数组 为真让其保持展开 -->
      <ul id="menu-settings" class="collapse  <?php echo in_array($current_page, $munu_settings) ? ' in' : '' ?>">
      <!-- 匹配对应$current_page 如果存储的数据和当前选中的数据一致 让其active -->
        <li <?php echo $current_page === 'nav-menus' ? 'class="active"' : '' ?>><a href="/admin/nav-menus.php">导航菜单</a></li>
        <li <?php echo $current_page === 'slides' ? 'class="active"' : '' ?>><a href="/admin/slides.php">图片轮播</a></li>
        <li <?php echo $current_page === 'settings' ? 'class="active"' : '' ?>><a href="/admin/settings.php">网站设置</a></li>
      </ul>
    </li>
     <!-- 电影推荐 -->
   <li <?php echo $current_page === 'douban' ? 'class="active"' : '' ?>>
      <a href="/admin/1douban.php"><i class="fa fa-list-alt"></i>电影推荐</a>
    </li>
  </ul>
</div>

<!-- 这里面是公共的样式 这个是侧边栏样式    结束 -->
<!-- 追加的电影推荐 -->
  
