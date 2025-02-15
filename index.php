<?
include("config.php");
include("kernel.php");

function makeTree() {
	$tree=array();
	$query="select s_id,s_up_id,s_level,s_name,s_title,s_order,s_hidden from ".$GLOBALS['structure_table'].";";
	$rez=mysql_query($query);
	if($rez and mysql_num_rows($rez)) {
		while($row=mysql_fetch_row($rez)) {
			$s_id=$row[0];
			$s_up_id=$row[1];
			$s_level=$row[2];
			$s_name=$row[3];
			$s_title=$row[4];
			$s_order=$row[5];
			$s_hidden=$row[6];

			$tree[$s_level][$s_up_id][$s_order]['s_id']=$s_id;
			$tree[$s_level][$s_up_id][$s_order]['s_name']=$s_name;
			$tree[$s_level][$s_up_id][$s_order]['s_title']=stripslashes($s_title);
			$tree[$s_level][$s_up_id][$s_order]['s_hidden']=$s_hidden;
		}
	}
	return $tree;
}

function arsearch($needle,$array) {
	$out=0;
	while(list($key,$val)=@each($array) and !$out) {
		if($val==$needle and $key=='s_name') {
			$out=1;
		}
		if(is_array($val)) $out=arsearch($needle,$val);
	}
	return $out;
}

function getRealParam($params,$tree) {
	$out=array();
	if(is_array($params)) {
		while(list($key,$val)=each($params) and arsearch($val,$tree[$key])) {
			$out[$key]=$params[$key];
		}
	}
	return $out;
}

function getVirtualParam($params,$rParam) {
	$out=$params;
	if(is_array($params)) {
		while(list($key,$val)=each($params) and $val==$rParam[$key]) {
//			unset($params[$key]);
			array_shift($out);
		}
	}
	return $out;
}
/*
function getVirtualParam($params,$tree) {
	$out=$params;
	if(is_array($params)) {
		while(list($key,$val)=each($params) and arsearch($val,$tree[$key])) {
//			unset($params[$key]);
			array_shift($out);
		}
	}
	return $out;
}
*/
function showPath($path) {
	$p_count=count($path);
	for($i=0;$i<$p_count;$i++) {
		list(,$data)=each($path);
		$s_name.=$data['s_name'].'/';
		if($i==$p_count-1) {
			?>/ <?=$data['s_title']?><?
		} else {
			?>/ <a href="<?=$GLOBALS['index']?><?=$s_name?>"><?=$data['s_title']?></a> <?
		}
	}
}

function showAllTitle($path) {
	$p_count=count($path);
	for($i=0;$i<$p_count;$i++) {
		list(,$data)=each($path);
		?>| <?=$data['s_title']?> <?
	}
}


function lastMod($path) {
	$s_time=time();
	if(!$path[count($path)-1]['s_module']) {
		$s_id=$path[count($path)-1]['s_id'];
		$s_up_id=$path[count($path)-1]['s_up_id'];
		$query="select s_time from ".$GLOBALS['structure_table']." where s_id=".$s_id.";";
		$rez=mysql_query($query,$GLOBALS['link']);
		if($rez and mysql_num_rows($rez)) {
			list($s_time)=mysql_fetch_row($rez);
		}
	}
	header("Last-Modified: ".gmdate("D, d M Y H:i:s",$s_time)." GMT");
}


function showPageTitle($path) {
	$s_title=$path[count($path)-1]['s_title'];
	?><?=$s_title?><?
}

function showContent($path,$num) {
	$s_id=$path[count($path)-1]['s_id'];
	$s_up_id=$path[count($path)-1]['s_up_id'];
	if($num==1) $s_text='s_text';
	if($num==2) $s_text='s_text2';
	$query="select ".$s_text.",s_tags from ".$GLOBALS['structure_table']." where s_id=".$s_id.";";
	$rez=mysql_query($query,$GLOBALS['link']);
	if($rez and mysql_num_rows($rez)) {
		$row=mysql_fetch_row($rez);
//		$s_text=$row[1]?$row[0]:nl2br($row[0]);
		$s_text=$row[0];
		?><?=stripslashes($s_text)?><?
	}
}

function showModule($path) {
	$s_id=$path[count($path)-1]['s_id'];
	$s_up_id=$path[count($path)-1]['s_up_id'];
	$query="select s_module from ".$GLOBALS['structure_table']." where s_id=".$s_id.";";
	$rez=mysql_query($query,$GLOBALS['link']);
	if($rez and mysql_num_rows($rez)) {
		$row=mysql_fetch_row($rez);
		list($s_module,$s_module_param)=explode(';',$row[0]);
		if(is_file($s_module)) include($s_module);
	}
}

function showModule2($path) {
	$s_id=$path[count($path)-1]['s_id'];
	$s_up_id=$path[count($path)-1]['s_up_id'];
	$query="select s_module2 from ".$GLOBALS['structure_table']." where s_id=".$s_id.";";
	$rez=mysql_query($query,$GLOBALS['link']);
	if($rez and mysql_num_rows($rez)) {
		$row=mysql_fetch_row($rez);
		list($s_module,$s_module_param)=explode(';',$row[0]);
		if(is_file($s_module)) include($s_module);
	}
}

function showMainTree() {
	$tree=$GLOBALS['tree'][0][0];
	ksort($tree);
	while(list(,$data)=each($tree)) {
		if($data['s_hidden']==0) {
			echo $GLOBALS['path'][$level]['s_name'];
			if($data['s_name']==$GLOBALS['path'][0]['s_name']) {
?>
<b><?=$data['s_title']?></b><br>
<?
			} else {
?>
<a href="<?=$GLOBALS['index']?><?=$s_name?><?=$data['s_name']?>"><?=$data['s_title']?></a><br>
<?
			}
		}
//		if(is_array($GLOBALS['tree'][1][$data['s_id']]) and $data['s_name']==$GLOBALS['path'][0]['s_name']) showActiveTree(1,$data['s_id'],$data['s_name'].'/');
	}
}

function showTree($level,$s_id,$s_name) {
	$tree=$GLOBALS['tree'][$level][$s_id];
	ksort($tree);
	?><ul><?
	while(list(,$data)=each($tree)) {
		if($data['s_name']==$GLOBALS['path'][$level]['s_name']) {
			?><li><a href="<?=$GLOBALS['index']?><?=$s_name?><?=$data['s_name']?>"><b><?=$data['s_title']?></b></a></li><?
		} else {
			?><li><a href="<?=$GLOBALS['index']?><?=$s_name?><?=$data['s_name']?>"><?=$data['s_title']?></a></li><?
		}
		if(is_array($GLOBALS['tree'][($level+1)][$data['s_id']])) showTree($level+1,$data['s_id'],$s_name.$data['s_name'].'/');
	}
	?></ul><?
}

function showActiveTree($level,$s_id,$s_name) {
	$tree=$GLOBALS['tree'][$level][$s_id];
	ksort($tree);
	while(list(,$data)=each($tree)) {
		if($data['s_hidden']==0) {
			if($data['s_name']==$GLOBALS['path'][$level]['s_name']) {
				include($GLOBALS['tmpl'].'showActiveTree_item_a');
			} else {
				include($GLOBALS['tmpl'].'showActiveTree_item');
			}
		}
		if(is_array($GLOBALS['tree'][($level+1)][$data['s_id']]) and $data['s_name']==$GLOBALS['path'][$level]['s_name']) showActiveTree($level+1,$data['s_id'],$s_name.$data['s_name'].'/');
	}
}

function showActiveTree2($level,$s_id,$s_name) {
	$tree=$GLOBALS['tree'][$level][$s_id];
	ksort($tree);
	while(list(,$data)=each($tree)) {
		if($data['s_hidden']==0 and $level>0) {
//		if($data['s_hidden']==0) {
			if($data['s_name']==$GLOBALS['path'][$level]['s_name']) {
				?><div class="submenu<?=$level?>_left_a"><?=$data['s_title']?></div><?
			} else {
				?><div class="submenu<?=$level?>_left"><a href="<?=$GLOBALS['index']?><?=$s_name?><?=$data['s_name']?>"><?=$data['s_title']?></a></div><?
			}
		}
		if(is_array($GLOBALS['tree'][($level+1)][$data['s_id']]) and $data['s_name']==$GLOBALS['path'][$level]['s_name']) showActiveTree2($level+1,$data['s_id'],$s_name.$data['s_name'].'/');
	}
}

if($params[0]) {
	$qadd=array();
	for($i=0;$i<count($params);$i++) {
		$qadd[]='(s_name="'.$params[$i].'" and s_level='.$i.')';
	}
	$qadd=implode(' or ',$qadd);
	$query="select s_id,s_up_id,s_name,s_title,s_module,s_hidden from ".$GLOBALS['structure_table']." where ".$qadd." order by s_id asc;";
	$rez=mysql_query($query);
	if($rez and mysql_num_rows($rez)) {
		$i=0;
		while($row=mysql_fetch_row($rez)) {
			$path[$i]['s_id']=$row[0];
			$path[$i]['s_up_id']=$row[1];
			$path[$i]['s_name']=$row[2];
			$path[$i]['s_title']=stripslashes($row[3]);
			$path[$i]['s_module']=trim($row[4]);
			$path[$i]['s_hidden']=$row[5];
			$s_id=$path[$i]['s_id'];
			$i++;
		}
	}

	$query="select s_name from structure where s_up_id=".$s_id." and s_default=1;";
	$rez=mysql_query($query);
	if($rez and mysql_num_rows($rez)) {
		list($s_name)=mysql_fetch_row($rez);
	}

	$template='template';
} else {
	$query="select s_id,s_up_id,s_name,s_title,s_module from ".$GLOBALS['structure_table']." where s_up_id=0 order by s_default desc,s_id asc limit 1;";
	$rez=mysql_query($query);
	if($rez and @mysql_num_rows($rez)==1) {
		$row=mysql_fetch_row($rez);
		$path[0]['s_id']=$row[0];
		$path[0]['s_up_id']=$row[1];
		$path[0]['s_name']=$row[2];
		$path[0]['s_title']=$row[3];
		$path[0]['s_module']=trim($row[4]);
		$s_name=$row[2];
	}
/*
	include('first.php');
	$template='template.first';
*/
}

//echo '<pre>';
$tree=makeTree();
//echo '</pre>';
$rParam=getRealParam($params,$tree);
//$vParam=getVirtualParam($params,$tree);
$vParam=getVirtualParam($params,$rParam);
$rPath=implode('/',$rParam);

//lastMod($path);
if(!$s_name) {
	echo '';
	include($tmpl.$template);
} else {
	$rParam[]=$s_name;
	$rPath=implode('/',$rParam);
	header('Location: '.$index.$rPath);
}
?>
