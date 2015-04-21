<?php
header("Content-Type: text/html; charset=utf-8");
include "config.inc.php";
require('./sphinxapi.php');
$mod = $_GET["mode"];
$md5 = $_GET["she"];
if ($md5 == "搜") {
    $keyToSearch = $_GET["key"];
}
if ($md5 == "MD5^16") {
    $keyToSearch = substr(md5($_GET["key"]), 8, 16);
}
if ($md5 == "MD5^32") {
    $keyToSearch = md5($_GET["key"]);
}
switch($_GET['mode']){
    case 1:
        $mode='SPH_MATCH_FULLSCAN';
        break;
    case 2:
        $mode='SPH_MATCH_EXTENDED2';
        break;
    case 3:
        $mode='SPH_MATCH_BOOLEAN';
        break;
    case 4:
        $mode='SPH_MATCH_PHRASE';
        break;
    case 5:
        $mode='SPH_MATCH_ANY';
        break;
    case 6:
        $mode='SPH_MATCH_ALL';
        break;
}
$p=$_GET['page'];
$p = ($p<1) ? 1 : $p ;
$cl = new SphinxClient();
$cl->SetServer('10.211.55.14', 9312);
$cl->SetArrayResult(true);                                  //设置 显示结果集方式
$cl->SetLimits($p*20,20);                           //同sql语句中的LIMIT
$cl->SetSortMode(SPH_SORT_RELEVANCE);                       //设置默认按照相关性排序
$cl->SetMatchMode($mode);
if ($keyToSearch != " ")                   // 如果关键字为空 不执行 否则程序出错
    $result = $cl->Query($keyToSearch, "*");                 //执行搜索

//算最大值，等同于统计共有多少数据
$count=mysql_fetch_assoc(mysql_query('select max(id) from shegongku'));


$fy=$result['total'];
$pn=(ceil($fy / 20));
function dis_td($sql)
{

    $result = mysql_query($sql);
    while ($row = mysql_fetch_assoc($result)) {
        echo "<tr><td>" . $row["source"] .
            "</td> <td>" . $row["username"] . "</td> <td>" . $row["password"] . "</td> <td>" . $row["email"] .
            "</td> <td>" . $row["others"] . " </td> </tr>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>信息查询库</title>
    <meta content="no" http-equiv="imagetoolbar">
    <meta content="width=device-width,initial-scale=1" name="viewport">
    <link type="image/x-icon" href="assets/ico/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" href="assets/css/quick.css">
    <link rel="stylesheet" href="css/addons.css">
    <link rel="stylesheet" href="css/sgk.css">
    <script src="assets/js/sgk.js"></script>
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/quick.js"></script>
</head>
<body>
<div class="masthead">
    <div class="lead text-red">信息查询库</div>
    <h2 class="text-green">本站现有数据量为<abbr class="text-blue"><?php echo $count['max(id)'] ?></abbr>条</h2>
</div>
<div>
    <form method="get" action="">
        <span class="icon icon-fire " id="tuBiao"></span>
        <select id='searchMode' name="mode" class="selectpicker">
            <option value='1'>完整扫描</option>
            <option value='2'>扩展匹配模式(V2)</option>
            <option value='3'>布尔查询</option>
            <option value='4'>短语匹配</option>
            <option value='5'>任意查询词</option>
            <option value='6'>所有查询词</option>
        </select>
        <span class="icon icon-user" id="tuBiao"></span>
        <input id="keyword" name="key" type="text" placeholder="请输入姓名&QQ&电话&身份证&用户名&邮箱"
               class="border border-green text-green padding-small"/>
        <input type="submit" id="she" name="she" value="搜" class="button bg-red bg-inverse"/>
        <input type="submit" id="she16" name="she" value="MD5^16" class="button bg-blue bg-inverse"/>
        <input type="submit" id="she32" name="she" value="MD5^32" class="button bg-green bg-inverse"/>
    </form>
</div>
<?php
if (!empty($_GET['key'])) {//判断是否输入关键字
    echo "
            <table  class=\"table table-bordered\" >
             <tr>
            <th style=\"text-align: center\"> 来源</th>
            <th style=\"text-align: center\">用户名</th>
            <th style=\"text-align: center\">密码</th>
            <th style=\"text-align: center\">邮箱</th>
            <th style=\"text-align: center\">其它</th>
            </tr><br>" .
        "<div class=\"alert success\">
            <strong>查询<span class=\"text-yellow\">" . $_GET['key'] . "</span>的结果: 查询到" . $result["total_found"] . "条结果,显示前1000条
            <br>
            <strong>查询耗时: 本次查询耗时" . $result["time"] . "秒</strong>
            </div>";

//print_r($cl);   //显示cl对象状态 使用原始数据
//print_r($result); //显示结果
    $arr = $result["matches"];
    $sql_id = array();
    foreach ($arr as $k => $v) {
        $sql_id[$i] = $v["id"];
        $i++;
    }
    $sql_query = array();
    foreach ($sql_id as $id) {
        $sql = "select * from shegongku where id =" . $id;
        $sql_query[$i] = $sql;
        $i++;
    }
//print_r($sql_query);   //显示原始的sql语句
    foreach ($sql_query as $sql) {
        dis_td($sql);
    }

    $ln='?mode='.$_GET['mode'].'&key='.$keyToSearch.'&she='.$md5.'&page=';
    $w=$pn-1;
    echo "    </table>
<div class=\"pager\">
    <a href=\"{$ln}\">首页</a>";
    for($i=1;$i<$pn;$i++){
        echo "<a href=\"{$ln}$i\">$i</a>";
    }

    echo "<a href=\"{$ln}$w\">尾页</a>
</div>";
}

mysql_close();

?>
<?php

?>

<div style="display: none" id="goTopBtn"><img border=0 src="assets/imgs/go_tu_top.png"></div>
<script type=text/javascript>goTopEx();</script>
</body>
</html>