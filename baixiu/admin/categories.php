<!-- 验证代码 写在开头    -->
<?php
require_once 'inc/funcation.php';
get_fn_user();


// 添加功能
function insert_fn()
{

  // 这个是新增功能
  // 验证 任意为空则提示并返回
  if (empty($_POST['name']) || empty($_POST['slug'])) {
    $GLOBALS['massage'] = '请填写完整表单';
    $GLOBALS['success'] = false;
    return;
  }
  $user_name = trim($_POST['name']);
  $user_slug = trim($_POST['slug']);
  // 验证是否为合法输入a-zA-Z0-9  这个也行 但是不个输入中文
  if (!preg_match("/^[x00-\xff]{3,}$/", $user_name)) {
    $GLOBALS['massage'] = '请填写合法表单';
    $GLOBALS['success'] = false;
    return;
  }
  if (!preg_match("/^[x00-\xff]{3,}$/", $user_slug)) {
    $GLOBALS['massage'] = '请填写合法表单';
    $GLOBALS['success'] = false;
    return;
  }

  // $user_name = $_POST['name'];
  // $user_slug = $_POST['slug'];
  // 回复响应
  //链接数据库 增加数据  insert into categories values( null,'{$user_name}','{$user_slug}');
  $resu = get_mysql_in_up_de("insert into categories values( null,'{$user_name}','{$user_slug}');");



  $GLOBALS['success'] =  $resu > 0;

  $GLOBALS['massage'] = $resu <= 0 ? '添加失败' : '添加成功';
}


// 编辑提交功能
function edit_fn()
{
  // 别的函数声明的变量 这里要用在这里使用全局global声明一下
  global $curreent_edit_categor;


  // 编辑功能不需要判断是否为空 在下面直接用三元表达式判断、如果值为false直接等于原有的值的值 
  $user_name = empty($_POST['name']) ? $curreent_edit_categor['name'] : $_POST['name'];
  $curreent_edit_categor['name'] = $user_name;
  $user_slug = empty($_POST['slug']) ? $curreent_edit_categor['slug'] : $_POST['slug'];
  $curreent_edit_categor['slug'] = $user_slug;


  // 验证是否为合法输入a-zA-Z0-9  这个也行 但是不个输入中文
  if (!preg_match("/^[x00-\xff]{3,}$/", $user_name)) {
    $GLOBALS['massage'] = '请填写合法表单';
    $GLOBALS['success'] = false;
    return;
  }
  if (!preg_match("/^[x00-\xff]{3,}$/", $user_slug)) {
    $GLOBALS['massage'] = '请填写合法表单';
    $GLOBALS['success'] = false;
    return;
  }
  // 获取修改后的id
  $id = $curreent_edit_categor['id'];
  // 根据id查找对用的数据库内容 更新数据
  $resu = get_mysql_in_up_de("update categories set slug='{$user_slug}',name='{$user_name}' where id='{$id}';");
  // 接收返回值
  $GLOBALS['success'] =  $resu > 0;
  $GLOBALS['massage'] = $resu <= 0 ? '更新失败' : '更新成功';
}

//查看id是否存在 为空表示不存在 那就是主线   有id表示修改那就是支线
if (empty($_GET['id'])) {
  // 判断表单是否以post提交   是表示 主线 添加数据
  if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
    // 如果表单以ost提交并且没有通过？传递id就是新增 
    insert_fn();
  }
} 
else {
  // 查询有数据的表单 根据id获取到当前要修改的数据 、 注意注入问题 
  $curreent_edit_categor = get_mysql_query_one('select *from categories where id=' . $_GET['id']);
  //如果表单以post提交并且通过？传递id 就修改
  if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
    edit_fn();
  }
}

/**
 * get 请求 没传递 id 就是获取空表单、展示已有数据  就是点击文字的分类目录展示的页面 属于默认页 也叫静态页  上面的判断只有post不符合会直接跳过之下下面的html 也就是默认的静态页
 * post请求 没传递id  就是添加数据到表单 、并展示已有数据    点击默认页的添加就是触发点
 * get请求 传递id 获取 有数据的表单、展示已有数据    点击默认页的修改就是触发点  
 * post请求 传递id数据   更新已有的数据 并展示已有的数据   点击修改页的 保存修改就是触发点
 * 
 * 点击删除触发删除这个也属于当前功能、但是没写在当前页面 用的是get获取id的形式进行删除的 
 * 如果也写在这个页面会显得混乱 现在删除在别的页面 增加 修改 查询在当前页面 
 * */ 


// 查询功能  负责展示已有的数据 
// 如果查询和其他操作一起、那么先做其他后查询 增加时效性
$categories = get_mysql_query_all('select *from categories;');
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
      <div class="page-title">
        <h1>分类目录</h1>
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

      <div class="row">
        <div class="col-md-4">
          <?php if (isset($curreent_edit_categor)) : ?>
            <!-- 在post提交时后面也可以使用？传递需要的id参数 -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $curreent_edit_categor['id']; ?>" method='post'>
              <h2>编辑《<?php echo $curreent_edit_categor['name']; ?>》</h2>
              <div class="form-group">
                <label for="name">名称</label>
                <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $curreent_edit_categor['name']; ?>">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $curreent_edit_categor['slug']; ?>">
                <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">保存修改</button>
              </div>
            </form>
          <?php else : ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
              <h2>添加新分类目录</h2>
            
              <div class="form-group">
                <label for="slug">名称</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
                <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
              </div>
              <div class="form-group">
                <label for="name">别名</label>
                <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">添加</button>
              </div>
            </form>
          <?php endif ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/categories-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item) : ?>
                <tr>
                  <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                  <td><?php echo $item['name']; ?></td>
                  <td><?php echo $item['slug']; ?></td>
                  <td class="text-center">
                    <a href="/admin/categories.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs" id>编辑</a>
                    <a href="/admin/categories-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
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
          <li><a href="posts.html">所有文章</a></li>
          <li><a href="post-add.html">写文章</a></li>
          <li class="active"><a href="categories.html">分类目录</a></li>
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
  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php' ?>


  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <!-- 全选 客户端代码 上面有写好的 全选按钮 默认是隐藏的 每个创建的信息前也有checkbox 只要选中就让隐藏按钮显示 -->
  <script>
    // 知识点： 1、不要重复使用获取元素操作、要把操作元素本地化、存储到变量中、使用变量 而不是每次使用元素操作
    // 如下面 $(tobody input) 这个元素操作 不用重复使用 应该使用变量接收 使用变量增加效率
    // 知识点：2、attr只获取元素属性也就是人为写的属性 prop可以获取元素本身被dom封装后的属性 
    // 也就是attr只能获取 声明并使用的属性、能够拿到其的值 但是  没声明的这个标签本身的dom属性的内容拿不到
    // prop可以获取即使没声明也没使用、但是本身自带的属性并且不是获取的值、而是获取的dom抽象话后的值
    // 总结就是 attr只能拿表面的html代码有的  prop可以拿 这个标签被抽象为对象后所有的属性  、使用时如果html没变化用prop 如果html有变化用attr
    // checkbox 点击是html代码没有变化 所以有 prop  不知道用啥就用prop
    // 知识点：设置自定义属性必须加data-  这个前缀 然后就可以通过$（）这函数将标签转换为jquery对象 使用各种方法 attr和prop在此时没区别
    // 并且设置自定义属性后 在用$()转换为jquey后 内部有一个dataset 里面存储的是设置的值 可以通过jquery.data方法拿到这个值 或者通过attr和prop也可以拿到 
    // 在dataset中 data-不显示 显示格式为如果写data-sb=123  那么 存储的是sb=123
    // 知识点 $(0) 是jquery的对象 通过console.dir可以打印jquery对象
    $(function($) {

      // 通过id拿到删除的dom对象 
      var $btndelete = $('#btn_delete')
      // 通过子类选择器拿到input返回的dom对象 是个数组
      var $tbodyCheckboxs = $('tbody input')
      // ################33version 2.0 这个叼 并且这次通过的方法 可以为后面批量删除提供要删除数组
      // 创建存储多个要删除的id 的数组
      var allcheckeds = []
      // 创建改变状态就触发的事件
      $tbodyCheckboxs.on('change', function() {
        // 上面141行 指向的html中有一个自定义属性  data-id="?php echo $item['id']; ?>"
        // 设置完毕后当前的data-id=当前获取到的动态id  
        // this在$() 就会被转换为jquery对象 是一个伪数组 然后同attr就可以访问被php获取到的data-id的id
        // console.log($(this).attr('data-id'))//第一种方式通过jquery的attr获取自定义属性拿到真实的数据库的id
        // console.log(this.dataset['id'])//第二种方式通过原生js的dom对象通过dateset内自定义属性拿到真实的数据库的id
        var id = $(this).data('id') //第三种方式通过jquery的封装函数通过自定义属性拿到真实的数据库的id 、data函数是jquery封装的dom对象 html5中可以拿到自定义属性的值  也就是sb=1  拿到1 
        // 第四种通过prop拿到当前checked属性判断是否为真
        if ($(this).prop('checked')) {
          // 为真就将第三种获取到的id添加到数组中  
          // 或者调用函数includes 判断是否有重复成员 为真就不添加了  但是这个有兼容问题 
          allcheckeds.includes(id)|| allcheckeds.push(id)
        } else {
          // 为假 就通过id找到当前元素在数组中的下标 并删除数组内元素
          allcheckeds.splice(allcheckeds.indexOf(id), 1)
        }
        console.log(allcheckeds)
        // 如果数组长度大于0就显示多选按钮 小于0就隐藏
        allcheckeds.length ? $btndelete.fadeIn() : $btndelete.fadeOut()

        //通过attr设置全选删除按钮 设置href属性跳转到指定页面 执行删除操作后返回
        // 知识点 ：数组可以可url直接进行拼接么 可以 并且可以使用search的属性在html链接后面追加id=数组这样的形式 追加参数
        $btndelete.prop('search', '?id=' + allcheckeds)
      })



      //#########version 1.0  实现不友好且多次遍历 每次发生状态变化就要遍历 dom操作效率不高数据多了 就受影响  ##############################################
      //   // 注册input中任意一个checbbox选中时 状态发生变化 就出发的事件
      //   $tbodyCheckboxs.on('change',function()
      //   {
      //       // 创建一个开关  表示默认是关闭的
      //   var flag=false
      //   // 遍历上面返回的input的dom数组 、内部有任意一个 checkbox 选中就显示 反转没有显示就按照上面默认的隐藏
      //     $tbodyCheckboxs.each( function (i,item)
      //     {
      //         // console.log(item)
      //         // 判断 复选框是否返回true   返回就让flag为ture
      //         if($(item).prop('checked'))
      //         {
      //           flag=true;
      //         }
      //     })
      // // 遍历完flag不为true就让其隐藏 如果有true、就让其显示
      // // 在外面判断 如果flag为true 通过dome调用显示批量按钮 如果为false就通过dome调用隐藏 批量按钮
      //       flag?$btndelete.fadeIn():$btndelete.fadeOut()
      //   })

        
      var $theadCheckboxs = $('thead input')
      // 全选和全不选  找到一个合适的时机 做一个合适的事情
      $('thead input').on('change',function(){
        // 获取当前选中状态 
        var checked=$(this).prop('checked')
       
          // 使用trigger触发change事件 显示 删除按钮
            $tbodyCheckboxs.prop('checked',checked).trigger('change')
          
      })
    })
  </script>

  <script>
    NProgress.done()
  </script>
</body>

</html>