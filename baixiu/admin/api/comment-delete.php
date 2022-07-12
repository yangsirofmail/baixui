<?php

/**
 * 根据客户端传递过来的id删除对应的数据
*/
// 第一步导入函数的头文件
require_once '../inc/funcation.php';

// 第二步判空
if(empty($_GET['id']))
{
    exit('缺少参数');
}
//第三步 保存id  
$id=$_GET['id'];
// 第四步根据查询mysql的数据 这样要需要注意防止sql注入 也就是id= 1 or  1=1 
$sql=sprintf('delete from comments where id in (%d);',$id);
// $sql=sprintf('delete from comments where id in ('.$id.');');
// 上面的sql语句不知道写到对不对 先这样试试
$row=get_mysql_in_up_de($sql);

header('Content-Type:appliction/json');
// 如果返回结果大于0表示修改成功 要么是true要么是false
echo json_encode($row>0);
// header('Location:/admin/comments.php'); 因为是ajax请求的所有不需要跳转页面