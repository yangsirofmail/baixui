<!-- 客户端先发一个请求到服务器的要一个空的表单
填写空的表单在提交  服务器 拿到有数据的表单进行验证 返回对应的响应结果-->
<?php
// 引入配置文件
require_once '../config.php';

// 开启session  给用户找一个箱子、 如果之前有就给之前的 、没有就给新的 并把箱子的钥匙发送给客户端
session_start();

function f1()
{
  // 判断是否为空
  if (empty($_POST['email'])) {
    $GLOBALS['massage'] = '请填写邮箱';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['massage'] = '请填写密码';
    return;
  }
  // 接收数据
  $email = $_POST['email'];
  $password = $_POST['password'];

  // 上面的是表单的校验  属于本地的 最起码校验 也就是不能输入为空 下面的假数据校验或者数据库校验属于服务器校验 属于业务校验

  // 假数据校验 成功之后在开始 获取数据库内数据进行校验 
  // if($email!=='yangsir@qq.com')
  // {
  //   $GLOBALS['massage']='邮箱错误';
  //   return;
  // }
  // if($password!=='yangsir')
  // {
  //   $GLOBALS['massage']='密码错误';
  //   return;
  // }
  // -------------验证成功 假数据换成真数据 从数据库拿到数据再次验证------------

  // 持久化
  // 下面爆红线也没事 php有病 

  //建立数据库链接 成功返回接收 失败die提示错误
  $conn = @mysql_connect(DB_HOST, DB_USER, DB_PASS);
  if (!$conn) {
    exit('<h1>连接数据库失败</h1>');
  }
  //选择某个数据库
  mysql_select_db(DB_NAME, $conn) or die("数据库链接错误");
  //   设置字符集
  mysql_query("set names 'utf8'");

  // 直接将用户提交过来的数据 拼接到查询语句 送到mysql中进行查询 limit 1 表示只要有一条符合就返回 不用查找全部  并且返回的是包括账号密码 的单个人的所有的信息、 而不是将数据库所有信息拿出来跟用户提交数据对比 
  $query = mysql_query("select * from users where email='{$email}' limit 1 ;");
  // 如果为空表示查询失败 没有这个数据
  if (!$query) {
    $GLOBALS['massage'] = '登录失败，请重试';
    return;
  }
  //为真表示查询成功有这个数据  因为账号唯一性 所以不需要while遍历 
  $user = mysql_fetch_assoc($query);
  // $user为空表示需要的数据 不存在 因为是通过email匹配的不存在就表示用户名没有
  if (!$user) {
    // 用户名不存在
    $GLOBALS['massage'] = '账号和密码不匹配';
    return;
  }
  // 注意：因为MD5不安全所以需要将密码增加数字或者字符计算后在加密 防止破解
  //将密码通过MD5加密后在于数据库返回的信息内的密码进行对比  数据库存储的密码也是MD5加密后的才行 
  // -- 存储是需要经过MD5加密 验证是也要通过md5加密 默认php下的MD5加密是32位   测试用户 admin@zce.me  密码wanglei  被md532位加密后存储在数据库 
  // UPDATE users SET  `password`=' 1f64f7f3d94a6ea252fd016577dd7992' WHERE id=1;
  // if ($user['password'] !== md5($password)) {
    if ($user['password'] !== ($password)) {
    // 密码不存在
    $GLOBALS['massage'] = '账号和密码不匹配';
    return;
  }

  // 登录成功设置session

  // 存一个登录标识  此session中的current_login_user就是键 $user就是值  这个是将用户名作为值进行保存
  $_SESSION['current_login_user'] = $user;



  // 回复响应
  // 验证成功跳转到网站根目录  admin/ 表示管理员页面
  header('Location: /admin/');
}
// 判断post请求是否为空
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  f1();
}

// 判断get请求  判断是否有action 判断action的值是否是logout
if ($_SERVER['REQUEST_METHOD'] === 'GET'&& isset($_GET['action']) &&$_GET['action']==='logout') {

  // 退出 清空掉session的值即可
  unset($_SESSION['current_login_user']);
}

?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
  <link rel="stylesheet" href="/assets/vendors/animate/animate.css">
</head>

<body>
  <div class="login">
    <!-- novalidate 取消浏览器中html5中的type为email的自动校验
        autocomplete  关闭浏览器的自动完成
      shake animated注意这个版本以前的版本 最新版去网站上找并不是这个单词
      因为并不是每次都需要抖动一下 所以增加三元表达式 只有massage错误信息时才会抖动 
      -->
    <form class="login-wrap <?php echo isset($massage) ? ' shake animated' : '' ?> " action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data" novalidate autocomplete="off">
    <!-- ../assets/img/default.png -->
      <img class="avatar" src="../assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($massage)) : ?>
        <div class="alert alert-danger">
          <strong>错误！</strong>
          <?php echo $massage; ?>
        </div>
      <?php endif ?>


      <!-- 这个是正常显示的代码 如果出现错误信息 就把上面的打开 会显示错误信息 相当于把两个页面合并了 正常显示这个错误了显示另一个
    设置状态保持 用了一个三元表达式 、如果 当前邮箱账号为空就让其等于空 否则就等于输入的邮箱账号
    -->
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo empty($_POST['email']) ? '' : $_POST['email'] ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>

  <!-- 导入js文件 -->
  <script src="/assets/vendors/jquery/jquery.js"></script>
  <script>
    // 需求用户输入邮箱后、如果存在拿到用户的图像、否则显示默认图像
    // 实现 ：找一个合适时机 做一个合适的事情
    // 时机：当用户输入邮箱、邮箱文本框失去焦点、并且文本框内有正确的邮箱时、 发送ajax请求、请求数据库内这个邮箱对应的头像地址 赋值到上面展示的img的src
    $(function($) {
      // 单独作用域
      // 保证页面加载过后执行
      // 正则表达式 用来做邮箱验证 可以在tool.oschina.net做测试
      var emailFormat = /^[a-zA-z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/
      // 正则测试用法 emailformat.test('yangsir@qq.com')   true
      // var emailformat=/^([a-zA-z0-9])+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/
      // 正则提取组  emailformat.exec('yangsir@qq.com')  提取后 yangsir@qq.com   yangsir  index  0  yangsir@qq.com

      //失去焦点事件  on blur   当文本框输入文本、选择其他文本框后、当文本框失去焦点触发事件 打印输出当前文本框的value值
      $('#email').on('blur', function() {
        // 失去焦点触发事件 、拿到文本框内容、
        var value = $(this).val()
        // 如果文本框内容为空就返回 如果正则返回false也返回
        if (!value || !emailFormat.test(value)) return
        // 否则就打印输出
        console.log(value)

        // 用户输入了一个合理的邮箱
        // 获取这个邮箱对应的头像的地址、赋值到上面的img的src
        // 因为客户端的js无法直接连接到数据库 应该通过js发送ajax请求告诉服务端的接口php让其获取数据库的图像地址在返回给客户端的就是

        // 让接口获取头像
        // 发送ajax请求  传递对象 email是自定义属性名 value是上面输入的邮箱
        $.get('/admin/api/avatar.php', {email: value }, function(res) {
          // res =>就是服务器返回的响应体内的数据 如果为空就返回
          if (!res) return
            // 将拿到的图像地址通过attr设置img中src这个属性的值更改其图像路径
            // 先执行淡出效果 执行完毕
            $('.avatar').fadeOut(function(){
              // 执行淡出的回调函数 注册on load事件 等待图片完全加载
              $(this).on('load',function(){
                    // 加载完毕后 执行淡入效果  实际上 直接显示了 因为已经完全加载了
                $(this).fadeIn() 
              }).attr('src', res)

            })
        })
      })

    })
  </script>

</body>

</html>