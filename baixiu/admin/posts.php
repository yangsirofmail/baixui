<!-- 验证代码 写在开头  -->
<?php 
require_once 'inc/funcation.php' ;
require_once '/usr/local/httpd/baixiu/config.php';
get_fn_user();

/**
 * 转换状态显示 作用是将 数据库存储的因为表示 转换成对应的汉字
 * $status 英文状态   字符串类型   
 * 参数 published 、drafted、trashed
 * 
 *convert_status() 转换函数
* $status 中文状态   字符串类型   
 * 参数 published=已发布 、drafted=草稿、trashed=回收站
 * html的的复杂的逻辑代码在这里抽象成函数进行使用
 * */
function convert_status($status)
{
  # code...
  // 方式2 这个更优化 友好
  //将状态作为参数传递 根据状态不同 返回对应的汉字  
  $dict=array('published'=>'已发布','drafted'=>'草稿','trashed'=>'回收站');
  return isset($dict[$status])?$dict[$status]:'未知';//通过$status 这个键拿到dict中的值 进行返回

}

// 把时间格式进行转换 为年月日
function convert_data($created)
{
 //对日期格式化其他格式  让保存这个格式的'2017-10-22 12:33:21'时间变成'2017年10月22 日12时33分21秒
 // 把已有的时间格式 转换成时间戳
 $ss=strtotime($created);
 // 根据转换后的时间戳 从新生成时间格式
 return date('Y年m月d日<b\r> H:i:s',$ss);
 //  关键在于 br 中的r要取消特殊含义 让其组成br换行标签   双引号 是字符串本身使用 有特殊含义的就解析这里不能用双引号 、单引号是给date内用的
//  总结一句话  \ 是取消字母特殊含义的  双引号不能用在data中 会造成 很多字母本身的特殊意义被使用 造成混乱 只能使用单引号

}




// 下面两种都是每次单独链接数据库 查询 如果数据多了 会影响 效率 所以需要使用关联查询
// 这个是根据id查询获取对应的数据库分类
// function get_category_id($category_id)
// {
//   // 注意防止注入 根据传递进来的id查找categories中的name
//   $rsutl=get_mysql_query_one("SELECT name FROM categories WHERE id = {$category_id}")['name'];

//   return $rsutl;
// }

// // 这个是根据id查询获取对应的数据库用户id名
// function get_user_id($user_id)
// {
//   // 注意防止注入 根据传递进来的id查找categories中的name
// return get_mysql_query_one("select nickname from users where id={$user_id}")['nickname'];

// }


//记录状态 防止出现 筛选后点击分页乱了   每完全解决    点击上下页是还是会乱
$search='';
// 接收分类筛选参数 ==============\
$where='1=1'; //保证 有这个where条件  默认1=1是占位置的 and 是追加where 条件的 最后都要拼接到where

// 接收状态筛选参数
if(isset($_GET['status'])&& $_GET['status']!=='all')
{
  // 这里的 .=   是一个拼接符
  $where .=" and posts.status='{$_GET['status']}'";

  // 用$search 保存每次的url参数
  $search.='&status=' .$_GET['status'];
}
if(isset($_GET['category'])&& $_GET['category']!=='all')
{

  // 这里的 .=   是一个拼接符   数据库内的id和选择的id 进行筛选 如果指定的不存在 会返回null 
  $where .=' and posts.category_id=' .$_GET['category'];

// 用$search 保存每次的url参数
  $search.='&category=' .$_GET['category'];
}
// 执行到这里   $search里面 是&category=分类id &status='状态'  在下面的遍历中.$search 追加保存的参数




// 页数
$size=20;
//处理分页
// 获取分页参数 没有或传过来的不是数字的话默认为 1
// $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
// 判断传递参数是否有没有 就是默认的1 有就用传递过来的数据   
$page=empty($_GET['page'])&& is_numeric($_GET['page']) ?1:(int)$_GET['page']; //page 要从第几条显示数据
// page=具体值   也就是要显示的数据条数
// // 判断是否非法跳转
// $page=$page<1?1:$page;
if($page<=0 )//
{
    // 页码小于 1 没有任何意义，则跳转到第一页
  header('Location: /admin/posts.php?page=1' .$search);
  // header('Location: ' .$search);
}


// 查最大页数   跟数据库数据数量有关  如果有101条数据  但是每次只展示10条  那么最多展示11页  
//  $total_page=ceil($total_count/$size)    公式：总页数除以展示页数 等于最大页数 需要向上取整 用ceil  目前展示页数是10
//联合查询求数据库符合条件的数据 计算最大页数  加一个别名num方便去返回的结果内的值 
// 注意：所有的数据库返回结果都是字符串、需要注意
$total_count=(int)get_mysql_query_one("SELECT COUNT(1) as num
FROM posts 
INNER JOIN users ON posts.user_id=users.id
INNER JOIN categories ON posts.category_id = categories.id
where {$where}"
)['num'];
if($total_count==0 )
{

  $GLOBALS['massage']='没有对应的标签数据';
  header("Location:".$_SERVER['HTTP_REFERER'] );
return;
}


// 拿到总条数 求的最大页数total_page
$total_page=(int)ceil($total_count/$size);



// // 判断是否非法跳转
// $page=$page>$total_page?$total_page:$page;
if($page>$total_page)//这两种都行
{
  // header("Location:".$_SERVER['HTTP_REFERER'].$search );
  // exit('非法参数');
  header("Location:/admin/posts.php?page=".$total_page .$search);
}





// 偏移量  也就是要越过多少条数据 从哪里开始显示数据
$offset=($page-1)*$size;

 /**
 * 输出分页链接
 * @param  integer $page    当前页码
 * @param  integer $total   总页数
 * @param  string  $format  链接模板，%d 会被替换为具体页数
 * @param  integer $visible 可见页码数量（可选参数，默认为 5）
 * @example
 *   <?php xiu_pagination(2, 10, '/list.php?page=%d', 5); ?>
 */
function xiu_pagination ($page, $total, $format, $visible = 5) {
  // 计算起始页码
  // 当前页左侧应有几个页码数，如果一共是 5 个，则左边是 2 个，右边是两个
  $left = floor($visible / 2);
  // 开始页码
  $begin = $page - $left;
  // 确保开始不能小于 1
  $begin = $begin < 1 ? 1 : $begin;
  // 结束页码
  $end = $begin + $visible - 1;
  // 确保结束不能大于最大值 $total
  $end = $end > $total ? $total : $end;
  // 如果 $end 变了，$begin 也要跟着一起变
  $begin = $end - $visible + 1;
  // 确保开始不能小于 1
  $begin = $begin < 1 ? 1 : $begin;

  // 上一页
  if ($page - 1 > 0) {
    printf('<li><a href="%s">&laquo;</a></li>', sprintf($format, $page - 1));
  }

  // 省略号
  if ($begin > 1) {
    print('<li class="disabled"><span>···</span></li>');
  }

  // 数字页码
  for ($i = $begin; $i <= $end; $i++) {
    // 经过以上的计算 $i 的类型可能是 float 类型，所以此处用 == 比较合适
       
    $activeClass = $i == $page ? ' class="active"' : '';
    printf('<li%s><a href="%s">%d</a></li>', $activeClass, sprintf($format, $i), $i);
  }

  // 省略号
  if ($end < $total) {
    print('<li class="disabled"><span>···</span></li>');
  }

  // 下一页
  if ($page + 1 <= $total) {
    printf('<li><a href="%s">&raquo;</a></li>', sprintf($format, $page + 1));
  }


}






// // 所以 在展示数据库是直接用联合查询 然后将关联数据全部搜索到即可 直接在显示代码通过foreach遍历获取数据然后展示
// //查询展示功能  使用的是联合查询 // 
// //链接数据库  获取全部数据 并展示到页面
  $current_posts=get_mysql_query_all("SELECT
  posts.id,posts.title,posts.created ,
  posts.status,
  users.nickname AS user_name,
  categories.name AS category_name
  FROM posts
  INNER JOIN users ON posts.user_id=users.id
  INNER JOIN categories ON posts.category_id = categories.id
  where {$where}
  order by posts.created desc
  limit {$offset},{$size};");

// 上面的查询是联合查询 查的是具体的数据 下面的是分类查询 只查询当前categories文件内

// 查询功能  负责展示已有的数据  如果查询和其他操作一起、那么先做其他后查询 增加时效性
$categories = get_mysql_query_all('select *from categories;');





?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
    <!-- 有错误信息时展示 -->
      <!--  判断是否有数据 有就进入判断 没有就不执行-->
      <?php if (isset($massage)) : ?>
        <!-- 如果success为真就表示成功 打印成功信息 -->
        <?php if (($success)) : ?>
          <div class="alert alert-success">
            <strong>成功 ！</strong> <?php echo $massage ?>
          </div>
          <!-- 否则就打印错误信息 -->
        <?php else : ?>
          <div class="alert alert-danger">
            <strong>错误！</strong> <?php echo $massage ?>
          </div>
        <?php endif ?>
      <?php endif ?>


   
        <!-- show when multiple checked -->
        <!-- <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a> -->

        <!-- 分类也需要表单提交 -->
        <form class="form-inline"   action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get"  >
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach($categories as $item): ?>
              <!-- 判断默认值 如果category传递的id存在且跟￥item遍历的id符合就设置为默认selected -->
              <option value="<?php echo $item['id']; ?>"  <?php echo isset($_GET['category'])&& $_GET['category']===$item['id']? 'selected':''; ?>><?php echo $item['name'] ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all"   >所有状态</option>
              <!-- 判断默认值 如果category传递的id存在且跟￥item遍历的id符合就设置为默认selected -->
              <option value="drafted" <?php echo isset($_GET['status'])&& $_GET['status']==='drafted'? 'selected':''; ?>>草稿</option>
            <option value="published"  <?php echo isset($_GET['status'])&& $_GET['status']==='published'? 'selected':''; ?>>已发布</option>
            <option value="trashed"  <?php echo isset($_GET['status'])&& $_GET['status']==='trashed'? 'selected':''; ?>>回收站</option>
      
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
        <?php xiu_pagination($page, $total_page, '? page= %d'.$visiables.$search); ?>
        </ul>
      </div>
      <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/categories-delete.php" style="display: none">批量删除</a>
          </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>

        <!-- 遍历查询到的数据 -->
          <?php foreach((array)$current_posts as $item): ?>
            <tr>
            <td class="text-center"><input type="checkbox"></td>
            <!-- 如果页面的展现代码 逻辑过于复杂不建议混编 而是将逻辑代码和展现代码区分 逻辑代码写到
          逻辑代码写到专门的php代码中、以函数形式保存、然后在展现代码中直接使用韩即可
         而不是直接在展现代码html中写一堆if else 或者for来混编 -->
            <td><?php echo $item['title']; ?></td>   
            <!-- 这些是多次查询的代码
              <td >?php echo get_user_id($item['user_id']); ?></td>
            <td >?php echo get_category_id($item['category_id']); ?></td> -->

            <td ><?php echo $item['user_name']; ?></td>
            <td ><?php echo $item['category_name']; ?></td>
            <td class="text-center"><?php echo convert_data($item['created']); ?></td>
            <td class="text-center"><?php echo convert_status( $item['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/posts-delete.php?id=<?php echo $item['id'];?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
       
        </tbody>
      </table>
    </div>
  </div>

  <!-- <div class="aside">
    <div class="profile">
      <img class="avatar" src="../uploads/avatar.jpg">
      <h3 class="name">布头儿</h3>
    </div>
    <ul class="nav">
      <li>
        <a href="index.html"><i class="fa fa-dashboard"></i>仪表盘</a>
      </li>
      <li class="active">
        <a href="#menu-posts" data-toggle="collapse">
          <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-posts" class="collapse in">
          <li class="active"><a href="posts.html">所有文章</a></li>
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

  <?php $current_page='posts'; ?>
  <?php include 'inc/sidebar.php' ?>


  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>


  <script>NProgress.done()</script>
</body>
</html>
