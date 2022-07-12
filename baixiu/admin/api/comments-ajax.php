<?php 
// 处理客户端comments.php发送过来的ajax请求 并进行查询数据库处理 
// 将处理结果 使用json格式进行返回
// 因为网络之间只能传输 json或者xml之类的字符串 或者二进制 01  但是
// 这里使用的是将数据使用json格  返回个客户端   

//载入公共函数
require_once '../inc/funcation.php';

// 取得客户端传递的分页、的页码数据oage
// 为空就给1 不为空就给转换为int类型后的page
$page=empty($_GET['page'])?1:intval($_GET['page']);

// 确定每页展示页数 
$size=20;
// 确定每页偏移数量
$offset=($page-1)*$size;


// 查询总条数
$total_count=(int)get_mysql_query_one("SELECT COUNT(1) as num FROM comments INNER JOIN posts ON comments.post_id=posts.id")['num'];
//  求的最大页数total_page  向上取整ceil
$total_page=(int)ceil($total_count/$size);


// 编写查询语句 根据 偏移量和页数 返回指定数量的数据
$sql=sprintf('SELECT comments.*,posts.`title` as post_title FROM comments INNER JOIN posts ON comments.post_id=posts.id
order by comments.created desc
limit %d, %d;',$offset,$size);




// 获取传参 有写好的函数直接调用即可   因为用的是拼接后的limit 所以查询的是指定页数的数据
$comments =get_mysql_query_all($sql);

// 将数据序列化为json格式
$json=json_encode(
    // 以关联数组形式返回数据 一个是总页数一个是分页条数
    array(
    'total_page'=>$total_page,
    'comments'=>$comments
));

// 给浏览器说明 服务器响应的是json格式的数据 防止浏览器 以text格式解析
header("Content-Type:application/json");
//将json格式的数据echo 输出 也就是返回到浏览器
echo $json;



