<?php 
//  有输入和输出 用ajax请求服务器获取用户图片

// 这个地方就是服务器返回代码的具体请求

// 使用jquery在文本失去焦点的时候发送ajax请求


// 拿到配置文件
require_once '../../config.php';

// 查询传递过来的邮箱\如果为空表示没有传递过来数据  对面是通过   $.get('/admin/api/avatar.php' 方式请求数据的
if(empty($_GET['email']))
{
    exit('确实必要参数');
}
$email=$_GET['email'];
// 查询对应的头像地址

// echo返回数据


//   //Mysql=连接
//建立数据库链接 成功返回接收 失败die提示错误
$conn =@mysql_connect(DB_HOST,DB_USER,DB_PASS) ;
if(!$conn)
{
  exit('<h1>连接数据库失败</h1>');
}
//选择某个数据库
mysql_select_db(DB_NAME,$conn)or die("数据库链接错误");
//   设置字符集
mysql_query("set names 'utf8'");

//根据邮箱建立查询
$query = mysql_query( "select avatar from users where email='{$email}' limit 1 ;");
// 如果为空表示查询失败 没有这个数据
if (!$query) {
  $GLOBALS['massage']='登录失败，请重试';
  return;
}
//为真表示查询成功有这个数据  返回的是一个关联数组
$user_base=mysql_fetch_assoc($query);
if (!$user_base) {
  // 用户名不存在
  $GLOBALS['massage']='图像查找失败';
  return;
}
// 返回这个查找到的图像地址
echo  $user_base['avatar'];







