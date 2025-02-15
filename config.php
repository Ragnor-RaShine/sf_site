<?
$CORP_NAMES['corp_site']['name']='Глобальная космическая корпорация<br>Астарта';
$CORP_NAMES['corp_site']['title']='ГМК Астарта';
$CORPL_NAMES['corp_site']['email']='Astarta@gmail.com';
setlocale(LC_ALL, "ru_SU.CP1251");
error_reporting(4+2+1);
$root="/var/www/corp_site/data/www/";
$tmpl=$root."tmpl/";
$docroot=$root."html/";

//---MySQL's connect---

$user='corp_site';
$pass='r4aHg';
$server='localhost';
$dbase='corp_site';
$link=mysql_connect($server,$user,$pass);
mysql_select_db($dbase,$link);mysql_query("set character_set_client=cp1251");mysql_query("set character_set_connection=cp1251");mysql_query("set character_set_results=cp1251");

$mpp=2;
$npp=10;
$tpp=20;

$newsimagedir[0]="i/news/";

function p4f($in) {
	return htmlspecialchars(stripslashes($in),ENT_QUOTES,'cp1251');
}

$structure_table="structure";
$news_table[0]="news";

//$request=$QUERY_STRING;
$request=substr($_SERVER['REQUEST_URI'],1);
$index='/';
//$index='index.php?';
$request=trim(str_replace('/',' ',$request));
$params=explode(' ',$request);
$allParams="'".implode("','",$params)."'";
?>
