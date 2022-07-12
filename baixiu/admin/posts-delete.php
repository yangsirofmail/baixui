<?php 

/**
 * 跟去客户端传递过来的数据删除对应的数据库的数据
 * 
 * */ 
// 导入头文件
require_once 'inc/funcation.php';

if(empty($_GET['id']))
{
    exit('缺少必要参数111');
}

// 临时保存数据  判断是否是合法数据而不是sql注入
// sql注入 id=1  or 1=1 这是一个bug 解决方法 强转为int  $id=（int）$_GET['id']; 
// 或者使用is_numeric() 函数解决
if(is_numeric($id))
{
    
    exit('输入违法');
}else
{   
    $id=$_GET['id'];

    // 删除语句  delete from categories where id in('.$id.);   
    // 删除语句  delete from categories where id in('.1,3,4,5.);   
    $row=get_mysql_in_up_de('delete from posts where id in('.$id.');');

    // http中referer用来标识当前请求的来源 图片防盗链可以使用这个参数实现 但是需要穿很多参数
    // header("Location:".$_SERVER['HTTP_REFERER']);
    header("Location:".$_SERVER['HTTP_REFERER']);

    // 成功跳转界面
}

?>