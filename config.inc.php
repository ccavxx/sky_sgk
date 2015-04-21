<?php
/**
 * Created by PhpStorm.
 * User: Esky
 * Date: 2014/12/8
 * Time: 14:08
 */
header("Content-Type: text/html; charset=utf-8");
define('DB_HOST','10.211.55.14');
define('DB_USER','root');
define('DB_PASS','25251325');
define('DB_DATABASE','sgk');
$con=mysql_connect(DB_HOST,DB_USER,DB_PASS);
if (!$con)
{
    die('Could not connect: ' . mysql_error());
}
mysql_select_db(DB_DATABASE,$con);
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER_SET_CLIENT=utf8");
mysql_query("SET CHARACTER_SET_RESULTS=utf8");
