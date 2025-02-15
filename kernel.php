<?
function makelink($in) {
	$makeHttpLinkTmpl="\\1<a target=_blank href=\"\\2\">\\2</a>";
	$makeFtpLinkTmpl="\\1<a target=_blank href=\"\\2\">\\2</a>";
	$makeEmailLinkTmpl="\\2<a href=\"mailto:\\3\">\\3</a>";

	$in=preg_replace("/(\s|^)(http:\/\/\S+)/i",$makeHttpLinkTmpl,$in);
	$in=preg_replace("/(\s|^)(ftp:\/\/\S+)/i",$makeFtpLinkTmpl,$in);
	$in=preg_replace("/((\s|^)([a-z0-9\-_\.]+@[a-z0-9\-_\.]+))/i",$makeEmailLinkTmpl,$in);
	return $in;
}

function CheckGallery($table,$id) {
	$g_path='/files/pictures-attach/';

	$query="select g_id from gallery_attach where g_att_table='".$table."' and g_att_id=".$id.";";
	$rez=mysql_query($query);
	if($rez and mysql_num_rows($rez)) {
		list($g_id)=mysql_fetch_row($rez);
?>
<script language=javascript>
function pcShowPhoto(imgCaption, imgName, imgWidth, imgHeight) {
var pcWnd = window.open('', Math.round(Math.random()*10), 'toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0,width='+imgWidth+',height='+imgHeight);
	pcWnd.document.writeln('<html><head><title>'+imgCaption+'</title></head>');
	pcWnd.document.writeln('<body bgcolor=#FFFFFF topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0">');
	pcWnd.document.writeln('<table width=100% height=100% cellpadding=0 cellspacing=0 border=0><tr><td align=center valign=middle><a href="javascript: window.close()"><img src="'+imgName+'" border=0 alt="'+imgCaption+'"></a></td></tr></table>');
	pcWnd.document.writeln('</body></html>');
	pcWnd.focus();
}
</script>
<?
		$query="select p_id,p_name from pictures_attach where p_g_id=".$g_id.";";
		$rez=mysql_query($query);
		if($rez and @mysql_num_rows($rez)) {
?><table border=0 cellspacing=0 cellpadding=4><?
			list($p_id,$p_name)=@mysql_fetch_row($rez);
			while($p_id) {
?><tr><?
				for($i=0;$i<4;$i++) {
					if($p_id) {
						list($p_width,$p_height)=@getimagesize('.'.$g_path.'big/'.$p_id.'.jpg');
?><td width=108 height=108 align=center valign=middle><a href="javascript:pcShowPhoto('<?=$p_name?>','<?=$g_path?>big/<?=$p_id?>.jpg',<?=$p_width?>,<?=$p_height?>)" title="<?=$p_name?>"><img src="<?=$g_path?>small/<?=$p_id?>.jpg" border=0></a></td><?
					} else {
?><td>&nbsp;</td><?
					}
					list($p_id,$p_name)=@mysql_fetch_row($rez);
				}
?></tr><?
   	   }
?></table><?
		}
	}
}
?>
