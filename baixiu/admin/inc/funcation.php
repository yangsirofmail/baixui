<?php
/**
 *封装公用的函数 
 * 在这里定义公共函数 要注意起名、防止自定义名和php1000多个全局函数一样 导致重复定义错误
 * 解决办法：加前缀或者加后缀或则用特点
 * 
 * 如何判断函数是否被定义
 * typeof  fn === 'funcation'    其中fn就是函数名  这是js的做法
 * function_exist('fn')     其中fn就是函数名  这是php的做法  
 * 
 */ 
// 只有这种所谓的导入文件才能从根目录开始 
require_once '/usr/local/httpd/baixiu/config.php';



//开启session  校验当前访问用户是不是正规登陆的有没有对应的登录标识
session_start();
// 获取用户名的函数 如果没有获取则自动跳转到登录页
function get_fn_user()
{
// 如果为空 表示非法登录 没有登录标识
if(empty($_SESSION['current_login_user']))
{
// 没有登录标识、将其跳转到登录页面
header('Location: /admin/login.php');
// 跳转到登陆也其他代码不执行
exit();
}
//否则 就返回登录信息的标识
return $_SESSION['current_login_user'];
}



/**
 *通过下面的函数获取数据库 查询多条数据 返回结果集 
 *返回索引数组 套关联数组
 */ 
function get_mysql_query_all($sql)
{
    // 链接数据库
$conn=mysql_connect(DB_HOST,DB_USER,DB_PASS);
if(!$conn)
{
    exit('数据库链接失败');
}
// 选择数据库
mysql_select_db(DB_NAME,$conn)or die('数据库链接失败');
// 设置字符集
mysql_query("set names 'utf8'");
// 查询数据库
$query=mysql_query($sql);
if(!$query)
{
    // 查询失败 返回false 
return -1;
}

 
$result=array();
// 查询成功遍历结果集  给一个数组
while($row=mysql_fetch_assoc($query))
{
$result[]=$row;
}
// 返回数组
return $result;
// 关闭数据库连接释放数据库查询资源
mysql_free_result($result);
mysql_close($conn);
}
// 只查询一条数据 利用函数嵌套机制 不重复写代码  只返回一条数据
// 返回关联数组
function  get_mysql_query_one($sql)
{
    // 调用查询语句返回结果进行判断 
     $des = get_mysql_query_all($sql);
    //  如果第9条有数据 就返回第0条数据 否则就返回null 
    return isset($des[0])?$des[0]:null;
}


/**
 * 封装一个增删改语句
 * */ 

 function get_mysql_in_up_de($sql)
{
    // 链接数据库
    $conn=mysql_connect(DB_HOST,DB_USER,DB_PASS);
    if(!$conn)
    {
        exit('数据库链接失败');
    }
    // 选择数据库
    mysql_select_db(DB_NAME,$conn)or die('数据库链接失败');
    // 设置字符集
    mysql_query("set names 'utf8'");
    // 查询数据库
    $query=mysql_query($sql);
    if(!$query)
    {
        // 查询失败 返回false 
    return false;
    }
    //查询受影响行数
    $result=mysql_affected_rows($conn);
    // 返回受影响函数
    return $result;
    // 关闭数据库连接
    mysql_close($conn);


}

/**
 * 获取当前登录用户的信息
 * 如果没有获取到的话则跳转到登录页
 * 也可以通过全局变量访问返回结果
 * @return array 包含用户信息的关联数组
 */
function xiu_get_current_user () {
    if (isset($GLOBALS['current_user'])) {
      // 已经执行过了（重复调用导致）
      return $GLOBALS['current_user'];
    }
  
    // 启动会话
    session_start();
  
    if (empty($_SESSION['current_logged_in_user_id']) || !is_numeric($_SESSION['current_logged_in_user_id'])) {
      // 没有登录标识就代表没有登录
      // 跳转到登录页
      header('Location: /admin/login.php');
      exit; // 结束代码继续执行
    }
  
    // 根据 ID 获取当前登录用户信息（定义成全局的，方便后续使用）
    $GLOBALS['current_user'] = xiu_query(sprintf('select * from users where id = %d limit 1', intval($_SESSION['current_logged_in_user_id'])))[0];
  
    return $GLOBALS['current_user'];
  }
  
/**
 * 输出分页链接
 * @param  integer $page    当前页码
 * @param  integer $total   总页数
 * @param  string  $format  链接模板，%d 会被替换为具体页数
 * @param  integer $visible 可见页码数量（可选参数，默认为 5）
 * @example
 *   <?php xiu_pagination(2, 10, '/list.php?page=%d', 5); ?>
  */
 function xiu_paginations ($page, $total, $format, $visible = 5) {
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
     //   <li<?php echo $i===$page?' class="active"':''; ? href="?page=<?php echo $i .$search; ??php echo $i; ? </a></li>
        
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
  