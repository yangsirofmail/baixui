<?php


// 个人中心服务器代码  也就是ajax请求的地址



// 这里面有个关键问题 就是文件权限问题 有时候很多文件没有读写导致的操作失败 
// 做项目是要先把 大框架画图实现 然后TODO 每个大概步骤  全部写好大框架后、在每个todo内实现具体细节 不至于猴子掰玉米
function f1()
{
  // 目标 将用户文件上传到服务器中 
  // 步骤 三部曲
// 接收文件 
// 保存文件
// 返回这个文件的访问url







  if (empty($_FILES['avatar'])) {
    exit('上传文件为空');
  }
  $avatar = $_FILES['avatar'];

  if (!($avatar['error'] === UPLOAD_ERR_OK)) {
    exit('上传文件失败cc');

    return;
  }
  // 文件上传成功后校验文件
  if ($avatar['size' ]> 10 * 1024 * 1024) {
    exit('文件过大');

    return;
  }
  if ($avatar['size'] < 1 * 1024 ) {
    exit('文件过小');

    return;
  }

//   第一种
    // 校验文件类型  必须是指定的jpeg 或者png  gif等文件类型才行
    // $allays1=array('image/jpeg','image/png','image/gif');
    // // in_array 数组比较 判断上传文件类型和自定义的数组内的文件类型是否一致  、相同为真 取反写提示 
    // if(!in_array($imgs['type'][$i],$allays1))
    // {
    //   $massage = '不支持这个图片格式';
    //   return;
    // }

if (!(strpos($avatar['type'], 'image/') === 0)) {
    exit('不支持这个图片格式');

    return;
  }


  // 校验当前服务器是否有对应到目录    最大的问题就是权限不够用   方法 给当前目录 777权限 最省事
//   if (!is_dir('/uploads/image/')) {  
//     // 创建存放永久文件的目录
//     $des = mkdir('-r','/uploads/image/');
//     if (!isset($des)) {
//     exit('目录创建失败');
//       return;
//     }
//   }

//   获取文件后缀
  $ext=pathinfo($avatar['name'],PATHINFO_EXTENSION);
//   指定目标路径
//   $target= '../../uploads/image/'. uniqid() .'.'.$ext;
  $target= '../../uploads/image/'.uniqid().'.'.$ext;



  //将上传后的文件从临时路径移动到目标路径\返回一个布尔值 、是否移动成功
  $mov = move_uploaded_file( $avatar['tmp_name'], $target);
  //移动失败为真、取反后就会进入内容提示
  if (!$mov) {
    echo   $target;
    // 如果报这个移动错误有可能是因为 文件创建失败了 需要给其当前文件的上一级添加文件权限
    exit('上传文件失败222');
    
    return;
  }
 




// 返回移动成功后的路径url  后面的5表示移除前面5个字也就是前面的../..   因为是相对路径所以要去掉点  如果是绝对路径又太长去掉的更多
echo  substr($target,5);
// echo $target;

}




// 判断是否已post提交表单
if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
  // 只有在post方式执行这个函数
  f1();
}
