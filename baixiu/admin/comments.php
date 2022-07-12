<?php
// <!-- 验证代码 写在开头  -->
require_once 'inc/funcation.php';
get_fn_user();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="../assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="../assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <script src="../assets/vendors/nprogress/nprogress.js"></script>
  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>


  <style>
    #loading {
      display: flex;
      align-items: center;
      justify-content: center;
      position: fixed;
      left: 0;
      top: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, .7);
      z-index: 999;
    }

    .flip-txt-loading {
      font: 26px Monospace;
      letter-spacing: 5px;
      color: #fff;
    }

    .flip-txt-loading>span {
      animation: flip-txt 2s infinite;
      display: inline-block;
      transform-origin: 50% 50% -10%;
      transform-style: preserve-3d;
      ;
    }

    .flip-txt-loading>span:nth-child(1) {
      -webkit-animation-delay: 0.10s;
      animation-delay: 0.10s;
    }

    .flip-txt-loading>span:nth-child(2) {
      -webkit-animation-delay: 0.20s;
      animation-delay: 0.20s;
    }

    .flip-txt-loading>span:nth-child(3) {
      -webkit-animation-delay: 0.30s;
      animation-delay: 0.30s;
    }

    .flip-txt-loading>span:nth-child(4) {
      -webkit-animation-delay: 0.40s;
      animation-delay: 0.40s;
    }

    .flip-txt-loading>span:nth-child(5) {
      -webkit-animation-delay: 0.50s;
      animation-delay: 0.50s;
    }

    .flip-txt-loading>span:nth-child(6) {
      -webkit-animation-delay: 0.60s;
      animation-delay: 0.60s;
    }

    .flip-txt-loading>span:nth-child(7) {
      -webkit-animation-delay: 0.70s;
      animation-delay: 0.70s;
    }

    @keyframes flip-txt {
      to {
        -webkit-transform: rotateX(1turn);
        transform: rotateX(1turn);
      }

    }
  </style>

</head>

<body>
  <script>
    NProgress.start()
  </script>
  <div class="main">
    <?php include 'inc/navbar.php' ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
          <!-- 分页就下面一共ul实现了 -->
        </div>
        <ul id="pagination-dome" class="pagination pagination-sm pull-right"></ul>
      </div>
      <!-- ------------------------- -->
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th class="text-center" width="60">作者</th>
            <th>评论</th>
            <th class="text-center" width="150">评论在</th>
            <th class="text-center" width="150">提交于</th>
            <th class="text-center" width="60">状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>



  <!-- 公共样式 -->
  <?php $current_page = 'comments'; ?>
  <?php include 'inc/sidebar.php' ?>

  <!-- -----------加载loading提示------------------------------ -->
  <div id="loading" style="display:flex">
    <div class="flip-txt-loading">
      <span>L</span><span>o</span><span>a</span><span>d</span><span>i</span><span>n</span><span>g</span>
    </div>
  </div>
  <!-- ----------------------------------------- -->

  <!-- 使用步骤：
导入头文件
写模板
拿到服务器响应的数据
将数据和模板混编
输出混编后的数据 -->

  <!-- jquery 的库 搭配twbs-pagination实现分页 -->
  <script src="../assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <!--第一步导入 js模板引擎 -->
  <script src="../assets/vendors/jsrender/jsrender.js"></script>
  <!-- 用script写模板 好处是浏览器不会显示  前提是设置type为自定义的模板名 而不是javascript-->
  <!-- 第二步创建模板--------四种颜色 批准 info  允许 success   警告 warning 拒绝danger---->
  <script id="comments_temp" type="text/x-jsrender">
    {{for comments}}
    {{!-- 这里模板注释    --}}
    <tr {{if status=='held'}} class="warning" {{else status =='rejected'}}class="danger" {{else status =='approved'}}class="success" {{/if}} data-id="{{:id}}"">
      <td class=" text-center"><input type="checkbox"></td>
      <td>{{:author}}</td>
      <td>{{:content}}</td>
      <td>{{:post_title}}</td>
      <td>{{:created}}</td>
      <td>{{if status=='held'}} 未审核
        {{else status =='rejected'}} 未批准
          {{else status =='approved'}} 已批准
            {{/if}}
      </td>
      <td class="text-center">
        {{if status=='held'}}
        <a href="javascript:;" class="btn btn-success btn-xs">批准</a>
        <a href="post-add.html" class="btn btn-warning btn-xs">驳回</a>
        {{/if}}
        <a href="javascript:;" class="btn btn-danger btn-xs  btn-delete">删除</a>
      </td>
    </tr>
    {{/for}}
  </script>
  <!-- ----------------------------------------- -->
  <!--第三步 发送ajax请求 获取静态页所需数据 将数据渲染到页面 通过append 这种dom方法 追加到静态页面 -->
  <script>
    // --------------上面5行代码就是进度条提示 开始和结束的进度条------------------------------
    //
    //nprogress  用的是链式编程  获取当前dom 开始是让nprogess开始 结束是让nprogress结束 如果不写这个就是没有进度条提示
    $(document)
      .ajaxStart(function() {
        NProgress.start()
        // 显示loading
        // 这种flex 也能实现  css样式也是flex
        $("#loading").css('display', 'flex');

      })
      .ajaxStop(function() {
        NProgress.done()
        // 隐藏loading
        // 这种flex 也能实现
        $("#loading").css('display', 'none');

      })





    //---------------------------------------------------------
    // 保存当前页状态
    var currentpage = 1;
    // ajax请求数据局部刷新
function loadPageDate(page) {
      $('tbody').fadeOut()
      // get请求需要设置服务器响应的header内为json   getJSON请求不需要设置json
      $.get('/admin/api/comments-ajax.php', { page: page}, function(res) // 发送ajax请求 参1 地址  参2 参数  参3 回调
    {
      // 如果分页最大数小于我当前的页数 让这个最大页数等于我当前页数从新在渲染  不执行下面代码
          if (page > res.total_page) {
            loadPageDate(res.total_page)
            return
          }
          //每次调用前销毁 以前的分页组件
          $('#pagination-dome').twbsPagination('destroy')
          //第五步 调用回调函数内 初始化分页 通过拿到的服务器响应 设置总页数和页数通过插件直接操作分页
          $('#pagination-dome').twbsPagination({
            first: '首页',
            prev: '上一页',
            next: '下一页',
            last: '末页',
            startPage: page, //初始化默认page是当前页 但是startPage是1 所以要每次给初始页赋值为当前页
            totalPages: res.total_page, //这个表示总页数 通过查数据库获取到
            visiblePages: 5, // 这个表示分页数
            initiateStartPageClick: false, //初始化点击不需要了
            // 第六步 点击分页页码 创建分页事件 点击一次 触发一次事件 获取当前页数进行加载这里的event表示 事件  page是每次拿到的参数
            onPageClick: function(event, page) {
              // 将拿到的page传递给封装好的page1 让page的值传递给ajax 然后让其通过参数发送给服务端的mysql 通过limit分页查询
              loadPageDate(page);
            }
          })
          // 第四步渲染数据  通过id找到模板 调用模板下面的方法render  将服务器响应的数据作为参数传递到模板进行混编  
          //   var data={};// // 将回调的数据赋值给遍历的模板  data.comments=res;//   //通过id找到模板 调用模板下面的方法 将数据可模板混编  //   var html=$('#comments_temp').render(data)// 上面是一种写法 先赋值后使用 下面也是一种写法  直接传参使用
          var html = $('#comments_temp').render({ comments: res.comments })
          $('tbody').html(html).fadeIn()          // 第五步将混编后的html模板 使用html渲染到浏览器中
          currentpage = page;     // 加载完数据后 更新状态
    })
}
// 加了下面这句代码 下面注释掉代码就不需要了
    loadPageDate(currentpage);

    // //在回调函数内 初始化分页 通过拿到的服务器响应 设置总页数和页数通过插件直接操作分页
    // $('#pagination-dome').twbsPagination({
    //   first: '首页',
    //   prev: '上一页',
    //   next: '下一页',
    //   last: '末页',
    //   // totalPages: res.total_page, //这个表示总页数
    //   totalPages: 100, //这个表示总页数
    //   visiblePages: 5, // 这个表示分页数
    //   // initiateStartPageClick: false,//初始化点击不需要了
    //   // 创建分页事件 点击一次 触发一次事件 获取当前页数进行加载这里的event表示 事件  page是每次拿到的参数
    //   onPageClick: function(event, page) {
    //     // 将拿到的page传递给封装好的page1 让page的值传递给ajax 然后让其通过参数发送给服务端的mysql 通过limit分页查询
    //     loadPageDate(page);
    //   }
    // })



    // ----------------删除功能--------------------------------
    // btn-delete 这个因为删除按钮是动态加载的、而且执行动态按钮在此之后才执行、所以过早的原因 没有注册成功
    // $('.btn-delete').on('click',function(){console.log("111")})

    // 使用委托代理的形式 因为jquery封装的原因 可以直接在原有基础上在其父类添加响应事件即可 多传一个委托参数即可
    // 给tbody注册一个代理事件负责接收btn-delete触发的事件  因为冒泡机制子类btn-delete触发的事件父类也会接收到 
    $('tbody').on('click', '.btn-delete', function() {

      var $tr = $(this).parent().parent()
      // 默认this表示当前  此时的this是a链接的删除按钮  它的父类是td也就是行 在父类也就是爷爷才是设置的id的tr
      var id = $tr.data('id')
      $.get('/admin/api/comment-delete.php', {
        id: id
      }, function(res) {
        // 打印的true 表示删除成功 只是删除了数据库的数据 打印的false表示删除失败取反后进入条件返回即可
        if (!res) return
        // 删除html上面的指定元素标签 否则就需要刷新才行
        // $tr.remove()
        // 注意remove会把当前页面删除完毕 但是后面没有顶替数据

        // 重新载入当前页数据
        loadPageDate(currentpage);
      })
    })
  </script>
  <script>
    NProgress.done()
  </script>
</body>

</html>