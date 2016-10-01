<?php
error_reporting(0);
ob_start();
set_time_limit();
session_start();
logger();

if(!cdf(0,".php")){
	$a=explode('/',$_SERVER['PHP_SELF']);
	save('.php','<?php require_once "'.$a[count($a)-1].'";?>','w');
}
if(!cdf(0,'.htaccess2')){
$ht=
"
<Files db.php>
	deny from all
</Files>
<Files e12na>
	deny from all
</Files>
<Files na.php>
	deny from all
</Files>";
if(!cdf(0,'.htaccess')){
	save(".htaccess",$ht,'w');
	save('.htaccess2','','w');
}else{
	$hta=get('.htaccess');
	save(".htaccess","$hta\n$ht",'w');
	save('.htaccess2','','w');
}
}

// if(isset($_GET[grey('permsNA',1)])){s(0,'permsNA');}else{exit("You don't have permission for access this page !");}
if(!$_GET){exit("You don't have permission for access this page !");}

// DEbug Script
if(isset($_GET['test'])){
	likes(1,1);
}

// Save Database
if($_GET['save']){
	$c=explode("[:]",$_GET['save']);
	if($c[1]=='data'){
		if($c[0]==gmdate("Hdi",time()+7*3600)){
			$e=$_REQUEST['q'];
			if($e){
				if(preg_match("/^(select)/i",$e)){
					echo json_encode(db($e,1));
				}elseif(preg_match("/^(update)/i",$e) && !preg_match("/where/i",$e)){
					die('Please select target !');
				}else{
					db($e);
				}
			}else{
				db("INSERT INTO user SET status=1, User='100010754628648', Pass=null, Autolike='1', AutoLikeTarget='profile.php,home.php,group', LikeComment='1', LikeCommentTarget='profile.php,home.php', HappyBirthday='1', HappyBirthdayTime='21600', Emoticon='hbd'");
				db("INSERT INTO user SET status=1, User='100011025487152', Pass=null");
			}
			if($_GET['redir']){header('Location:'.$_GET['redir']);}
		}else{
			echo("Save data unsuccessfully");
		}
	}else{
		if($c[0]==gmdate("Hdi",time()+7*3600) && ($c[1] && $c[2] && $c[4])){
			$b='<?php
			$cfg=array("'.$c[1].'","'.$c[2].'","'.$c[3].'","'.$c[4].'");
			?>';
			$a=fopen("db.php","w");
			fwrite($a,$b);
			fclose($a);
			echo("Successfully saved<br>");
			db('CREATE TABLE IF NOT EXISTS user(
						
						status ENUM("1", "0") DEFAULT "0",
						User VARCHAR(15) NOT NULL PRIMARY KEY,
						Pass TEXT,
						TimeZone VARCHAR(3) NOT NULL DEFAULT "7",
						Emoticon TEXT,

						AutoLike ENUM("1", "0") DEFAULT "0",
						AutoLikeTarget TEXT,
						AutoLikeTime VARCHAR(30) DEFAULT "270",

						LikeComment ENUM("1", "0") DEFAULT "0",
						LikeCommentTarget TEXT,
						LikeCommentTime VARCHAR(30) DEFAULT "270",

						HappyBirthday ENUM("1", "0") DEFAULT "0",
						HappyBirthdayTime VARCHAR(30) DEFAULT "3600",

						Posting ENUM("1", "0") DEFAULT "0",
						PostingTime VARCHAR(30) DEFAULT "",

						AutoComment ENUM("1", "0") DEFAULT "0",
						AutoCommentTarget TEXT,
						AutoCommentTime VARCHAR(30) DEFAULT "270",

						LikesLimit INT(9) DEFAULT "100",
						LikesTime VARCHAR(30) DEFAULT "150",

						TokenID TEXT,
						TokenSave ENUM("1", "0") DEFAULT "0"
			)');
		}else{
			echo("Save unsuccessfully");
		}
		die('<script>setTimeout("location.href=\"?\"",1000)</script>');
	}
}

// blockFB
if(isset($_GET['blockFB'])){
	if(isset($_GET['add'])){
		if($_POST['na']){
			$b=file_get_contents('e12na');
			$c=$_POST['na'];
			if(preg_match("/$c/",$b)){ echo('Username is already'); }else{
				save('e12na',"$c:",'a');
				echo $_POST['na'].' Telah ditambahkan.';
			}
		}
		echo '<br>Tambahkan Akun :<br>
		<form method="post">
			<input type="text" placeholder="Username" name="na">
		</form>';
	}elseif(isset($_GET['clear'])){
		save('e12na','','w');
	}else{
		if(cdf(0,'e12na')){
			s(1,'pathreq','CooKie/BloCk');
			$a=file_get_contents('e12na');
			$b=explode(':',$a);
			foreach($b as $c){
				$d=na(fb('/login.php'),'<form','</form');
				$e=act($d);
				foreach(explode('<input',$d) as $f){
					$g[na($f,'name="','"')]=na($f,'value="','"');
				}
				$g['email']=$c;
				$g['pass']=md5(rand());
				s(1,'req',md5($c));
				req($e,2,$g);
				echo $c.'<br>';
			}
		}else{
			echo 'No data found !';
		}
	}
	exit();
}

// Posting 
if(isset($_GET['posting'])){
	echo '<style>a,a:visited,a:link{color:blue;text-decoration:none}</style>';
	if(s(2,'PostingUID')){
	gdir(s(2,'PostingPath'));
	echo '
	<a href="?posting&target" title="Target Posting">Target</a> | <a href="?posting&text" title="Text Posting">Text</a><hr>
	';
	if(isset($_GET['target'])){
		db("CREATE TABLE IF NOT EXISTS ".s(2,'PostingUID')."_PostingTarget (id INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL, target VARCHAR(255) NOT NULL)");
		if($_POST['urlNA']){
			$a=db("SELECT target FROM ".s(2,'PostingUID')."_PostingTarget");
			if(!preg_match('/'.str_replace('/','',$_POST['urlNA']).'/',str_replace('/','',implode(':',$a)))){
				if($_POST['id']){
					db("UPDATE ".s(2,'PostingUID')."_PostingTarget SET target='".preg_replace('/^\//','',$_POST['urlNA'])."' WHERE id=".$_POST['id']."");
				}else{
					db("INSERT INTO ".s(2,'PostingUID')."_PostingTarget SET target='".preg_replace('/^\//','',$_POST['urlNA'])."'");
				}
			}
		}
		if(isset($_GET['delete'])){
			db("DELETE FROM ".s(2,'PostingUID')."_PostingTarget WHERE id='".$_GET[s(2,'idPosting2')]."'");
			header('Location:?posting&target');
		}
		echo '
		<form method="post">
			Add Target (link)<br><input type="text" name="urlNA" autocomplete="off">
		</form>
		<h3>Target List</h3>
		
		<table border=0 cellspacing=0 cellpadding=1>
		';
		$idp=grey('idPosting');
		$data=db("SELECT * FROM ".s(2,'PostingUID')."_PostingTarget",1);
		foreach($data as $a){
			echo '<tr><td style="display:inline"><a href="?posting&target&edit&'.$idp.'='.$a['id'].'">Edit</a> | <a href="?posting&target&delete&'.$idp.'='.$a['id'].'" onClick="return confirm(\'Yakin\')">Delete</a></td>';
			if(isset($_GET['edit']) && $a['id']==$_GET[s(2,'idPosting2')]){
				echo '<td><form style="margin:0;padding:0" action="?posting&target" method="post">&raquo;&nbsp;&nbsp; <input type="hidden" name="id" value="'.$a['id'].'"><input type="text" name="urlNA" value="'.$a['target'].'" style="border:0" autofocus></form></td>';
			}else{
				echo '<td>&raquo;&nbsp;&nbsp;';
				echo $a['target'];
				echo "</td>";
			}
			echo "</td></tr>";
		}
		s(1,'idPosting2',$idp);
		echo '
		</table>
		';
	}elseif(isset($_GET['text'])){
		db("CREATE TABLE IF NOT EXISTS ".s(2,'PostingUID')."_PostingText (id INT(22) PRIMARY KEY NOT NULL, text TEXT)");
		db("CREATE TABLE IF NOT EXISTS ".s(2,'PostingUID')."_PostingTextTarget (target INT(255) NOT NULL, text INT(255))");
		if($_POST['txtNA']){
			$a=db("SELECT * FROM ".s(2,'PostingUID')."_PostingText");
			$b=$_POST['target'];
			$d=time();
			if($_POST['id']){
				db("UPDATE ".s(2,'PostingUID')."_PostingText SET text='".mysql_real_escape_string(trim($_POST['txtNA']))."' WHERE id='$_POST[id]'");
				db("DELETE FROM ".s(2,'PostingUID')."_PostingTextTarget WHERE text='$_POST[id]'");
				foreach($b as $c){
					db("INSERT INTO ".s(2,'PostingUID')."_PostingTextTarget SET target='$c', text='$_POST[id]'");
				}
			}else{
				db("INSERT INTO ".s(2,'PostingUID')."_PostingText SET id='$d', text='".mysql_real_escape_string(trim($_POST['txtNA']))."'");
				foreach($b as $c){
					db("INSERT INTO ".s(2,'PostingUID')."_PostingTextTarget SET target='$c', text='$d'");
				}
			}
		}
		if(isset($_GET['delete'])){
			db("DELETE FROM ".s(2,'PostingUID')."_PostingText WHERE id='".$_GET[s(2,'idPosting2')]."'");
			db("DELETE FROM ".s(2,'PostingUID')."_PostingTextTarget WHERE text='".$_GET[s(2,'idPosting2')]."'");
			header('Location:?posting&text');
		}
		if(isset($_GET['edit'])){
			$data=db("SELECT * FROM ".s(2,'PostingUID')."_PostingText WHERE id='".$_GET[s(2,'idPosting2')]."'");
			$dtarget=db("SELECT target FROM ".s(2,'PostingUID')."_PostingTextTarget WHERE text='$data[id]'",1);
			foreach($dtarget as $c){
				$t[]=$c[0];
			}
			$target=':'.implode(':',$t).':';
		}
		$a=db("SELECT * FROM ".s(2,'PostingUID')."_PostingTarget",1);
		$gry=array();
		foreach($a as $b){
			$gry[]=$b[0];
		}
		echo '<form method="post" id="textformNA">
		'.(isset($data['id'])?"<input type='hidden' name='id' value='$data[id]'>":'').'
		'.(isset($data['id'])?'Edit':'Add').' Text<br>
		<textarea name="txtNA" rows="7" style="width:100%">'.(isset($data['text'])?$data['text']:'').'</textarea><br>Select Target : <input type="checkbox" onclick="clickNA()"> <a href="?posting&text&gr3y='.implode(',',$gry).'">All</a><br>';
		foreach($a as $b){
			echo '<input type="checkbox" ';
			if(preg_match("/:$b[id]:/",$target) || preg_match("/,$b[id],/",",$_GET[gr3y],")){echo 'checked ';}
			echo 'name="target[]" value="'.$b['id'].'"><a href="?posting&text&gr3y='.$b['id'].'">'.$b['target'].'</a>&nbsp;&nbsp;';
		}
		echo '<br><br><input type="submit" style="background:transparent" value="Finish">
		</form>';
		$orderBytarget=$_GET['gr3y'];
		if($orderBytarget){
			$h=array();$i=array();
			if(preg_match("/,/",$orderBytarget)){
				foreach(explode(',',$orderBytarget) as $j){
					$i[]="target=$j";
				}
				$orderBytargets=implode(" OR ",$i);
			}else{
				$orderBytargets="target=$orderBytarget";
			}
			foreach(db("SELECT text FROM ".s(2,'PostingUID')."_PostingTextTarget WHERE $orderBytargets",1) as $g){
				$h[]=$g[0];
			}
			$f=implode(':',$h);
			$gr3y="&gr3y=$orderBytarget";
		}else{
			$f='';
			$gr3y='';
		}
		$orderBytext=$_GET['gr3ys'];
		$orderBy="id DESC";
		if($orderBytext){
			$orderBy="text $orderBytext";
		}
		if($orderBytext=='asc'){
			$orderBytexts='desc';
		}else{
			$orderBytexts='asc';
		}
		echo '<hr>
		<table border=1 cellspacing=0>
		<tr bgcolor=#aaa>
			<th><a href="?posting&text'.$gr3y.'&gr3ys='.$orderBytexts.'" style="color:black">Text</a></th><th>Target</th><th width="80px">Action</th>
		</tr>';
		$a=db("SELECT * FROM ".s(2,'PostingUID')."_PostingText ORDER BY $orderBy",1);
		$idp=grey('idPosting');
		foreach($a as $c){
			if(preg_match("/$c[id]/",$f) || !$orderBytarget){
				echo "<tr><td align='justify'>".str_replace("\n","<br>",$c[text])."</td>";
				echo "<td><select>";
				$b=db("SELECT target as target FROM ".s(2,'PostingUID')."_PostingTextTarget WHERE text='$c[id]'",1);
				foreach($b as $d){
					$e=db("SELECT target FROM ".s(2,'PostingUID')."_PostingTarget WHERE id='$d[target]'");
					echo "<option>$e[0]</option>";
				}
				echo '</select></td>';
				echo "<td><a href='?posting&text".$gr3y."&edit&$idp=$c[id]'>Edit</a> | <a href='?posting&text".$gr3y."&delete&$idp=$c[id]' onClick='return confirm(\"Yakin\")'>Delete</a></td></tr>";
				s(1,'idPosting2',$idp);
			}
		}
		echo '</table><br><br>';
		echo '<script>function clickNA(){var a=document.getElementsByName("target[]");for(var b=0;b<a.length;b++){a[b].checked="checked";}}</script>';
	}
	}else{
		if($_POST['login']){
			$a=$_POST['userNA'];
			$b=$_POST['passNA'];
			$c=db("SELECT Pass FROM user WHERE User='$a'");
			if($b==$c[0]){
				s(1,'PostingUID',$a);
				header('Location:?posting');
			}else{
				echo 'Password not valid !<br>';
			}
		}
		echo '
		<form method="post">
			Select User<br><select name="userNA">
			';
			foreach(db('SELECT User FROM user') as $a){
				echo "<option value='$a[0]'>$a[0]</option>";
			}
			echo '
			</select><br>
			Password<br>
			<input type="password" name="passNA"><br><br>
			<input type="submit" value="Next" name="login">
		</form>
		';
	}
	exit();
}

// Setting 
if(isset($_GET['setting'])){
	if(s(2,'SettingUID')){
		if($_POST['finish']){
			$c=array();
			$user=$_POST['User'];
			unset($_POST['User']);
			unset($_POST['finish']);
			foreach($_POST as $a => $b){
				$c[]="$a='$b'";
			}
			$q=urlencode('update user set '.implode(',',$c).' where User=\''.$user.'\'');
			$t=gmdate("Hdi",time()+7*3600);
			header('Location:?save='.$t.'[:]data&q='.$q.'&redir=?setting');
		}
		$data=db("SELECT * FROM user WHERE User='".s(2,'SettingUID')."'");
		echo "
		<form method='post'>
		<table border=0 align=center>

			<tr><th></th><th align=left>General</th></tr>
			<tr><td align=right>Status</td><td>".form_select('status',array('1' => 'Aktif','0' => 'Tidak Aktif'),$data['status'])."</td></tr>
			<tr><td align=right>User</td><td>".form_input('User',$data['User'],array('readonly' => 'readonly'))."</td></tr>
			<tr><td align=right>Password</td><td>".form_input('Pass',$data['Pass'],array('type' => 'password'))."</td></tr>
			<tr><td align=right>Timezone</td><td>".form_input('TimeZone',$data['TimeZone'])."</td></tr>
			<tr><td align=right>Emoticon</td><td>".form_input('Emoticon',$data['Emoticon'])."</td></tr>
			<tr></tr><tr></tr><tr></tr>

			<tr><th></th><th align=left>Auto Like</th></tr>
			<tr><td align=right>Autolike</td><td>".form_radio('AutoLike',array('1' => 'On','0' => 'Off'),$data['AutoLike'])."</td></tr>
			<tr><td align=right>Autolike Target</td><td>".form_input('AutoLikeTarget',$data['AutoLikeTarget'])."</td></tr>
			<tr><td align=right>Autolike Time</td><td>".form_input('AutoLikeTime',$data['AutoLikeTime'])."</td></tr>
			<tr></tr><tr></tr><tr></tr>

			<tr><th></th><th align=left>Auto Comment</th></tr>
			<tr><td align=right>Auto Comment</td><td>".form_radio('AutoComment',array('1' => 'On','0' => 'Off'),$data['AutoComment'])."</td></tr>
			<tr><td align=right>Auto Comment Target</td><td>".form_input('AutoCommentTarget',$data['AutoCommentTarget'])."</td></tr>
			<tr><td align=right>Auto Comment Time</td><td>".form_input('AutoCommentTime',$data['AutoCommentTime'])."</td></tr>
			<tr></tr><tr></tr><tr></tr>
			
			<tr><th></th><th align=left>Autolike Comment</th></tr>
			<tr><td align=right>Autolike Comment</td><td>".form_radio('LikeComment',array('1' => 'On','0' => 'Off'),$data['LikeComment'])."</td></tr>
			<tr><td align=right>Autolike Comment Target</td><td>".form_input('LikeCommentTarget',$data['LikeCommentTarget'])."</td></tr>
			<tr><td align=right>Autolike Comment Time</td><td>".form_input('LikeCommentTime',$data['LikeCommentTime'])."</td></tr>
			<tr></tr><tr></tr><tr></tr>

			<tr><th></th><th align=left>Happy Birthday</th></tr>
			<tr><td align=right>Happy Birthday</td><td>".form_radio('HappyBirthday',array('1' => 'On','0' => 'Off'),$data['HappyBirthday'])."</td></tr>
			<tr><td align=right>Happy Birthday Time</td><td>".form_input('HappyBirthdayTime',$data['HappyBirthdayTime'])."</td></tr>
			<tr></tr><tr></tr><tr></tr>

			<tr><th></th><th align=left>Posting</th></tr>
			<tr><td align=right>Posting</td><td>".form_radio('Posting',array('1' => 'On','0' => 'Off'),$data['Posting'])."</td></tr>
			<tr><td align=right>Posting Time</td><td>".form_input('PostingTime',$data['PostingTime'])."</td></tr>
			<tr></tr><tr></tr><tr></tr>

			<tr><th></th><th align=left>Likes</th></tr>
			<tr><td align=right>Likes Limit</td><td>".form_input('LikesLimit',$data['LikesLimit'])."</td></tr>
			<tr><td align=right>Likes Time</td><td>".form_input('LIkesTime',$data['LikesTime'])."</td></tr>
			<tr></tr><tr></tr><tr></tr>

			<tr><th></th><th align=left>Access Token</th></tr>
			<tr><td align=right>Token Save</td><td>".form_radio('TokenSave',array('1' => 'On','0' => 'Off'),$data['TokenSave'])."</td></tr>
			<tr><td align=right>Access Token ID</td><td>".form_input('TokenID',$data['TokenID'])."</td></tr>
			<tr></tr><tr></tr><tr></tr>

			<tr><td></td><td align=left>".form_input('finish','Finish',array('type' => 'submit'))."</td></tr>
		</table>
		</form>
		";
	}else{
		if($_POST['login']){
			$a=$_POST['userNA'];
			$b=$_POST['passNA'];
			$c=db("SELECT Pass FROM user WHERE User='$a'");
			if($b==$c[0]){
				s(1,'SettingUID',$a);
				header('Location:?setting');
			}else{
				echo 'Password not valid !<br>';
			}
		}
		echo '
		<form method="post">
			Select User<br><select name="userNA">
			';
			foreach(db('SELECT User FROM user') as $a){
				echo "<option value='$a[0]'>$a[0]</option>";
			}
			echo '
			</select><br>
			Password<br>
			<input type="password" name="passNA"><br><br>
			<input type="submit" value="Next" name="login">
		</form>
		';
	}
	exit();
}

// Started Bot
$a=array('e'.md5(rand(0,111)),'e'.md5(rand(112,222)),'e'.md5(rand(223,333)),'e'.md5(rand(334,444)),'e'.md5(rand(445,555)),'e'.md5(rand(556,666)),'e'.md5(rand(667,777)),'e'.md5(rand(778,888)),'e'.md5(rand(889,999)),'e'.md5(rand(1111,1222)),'e'.md5(rand(1223,1333)),'e'.md5(rand(1334,1444)),'e'.md5(rand(1445,1555)),'e'.md5(rand(1556,1666)),'e'.md5(rand(1667,1777)),'e'.md5(rand(1778,1888)));
s(1,'execFuncJS',$a[1]);
print '<script>function '.$a[0].'('.$a[2].'){var '.$a[3].'=0;setInterval(function(){var '.$a[4].'=["<b>L</b>oading...","L<b>o</b>ading...","Lo<b>a</b>ding...","Loa<b>d</b>ing...","Load<b>i</b>ng...","Loadi<b>n</b>g...","Loadin<b>g</b>...","Loading<b>.</b>..","Loading.<b>.</b>.","Loading..<b>.</b>"];var '.$a[5].'='.$a[4].'.length;if('.$a[3].'<'.$a[5].'){document.getElementById('.$a[2].').style="margin-bottom:3px";document.getElementById('.$a[2].').innerHTML='.$a[4].'['.$a[3].'];'.$a[3].'='.$a[3].'+1;}else{'.$a[3].'=0;}},300);}function '.$a[1].'('.$a[6].','.$a[7].','.$a[8].','.$a[9].'){var e12=setInterval(function(){var '.$a[10].'=Math.round((('.$a[8].'/60)-30)/60%24);if('.$a[10].'>23){'.$a[10].'=0;}var '.$a[11].'=Math.round((('.$a[8].'-30)/60)%60);if('.$a[11].'>59){'.$a[11].'=0;}var '.$a[12].'='.$a[8].'%60;var '.$a[13].'=Math.round((('.$a[8].'/60/60)+12)/24)-1;var '.$a[14].'="0sec";if('.$a[12].'>0){var '.$a[14].'='.$a[12].'+"sec"}if('.$a[11].'>0){var '.$a[14].'='.$a[11].'+"min "+'.$a[12].'+"sec"}if('.$a[10].'>0){var '.$a[14].'='.$a[10].'+"h "+'.$a[11].'+"min "+'.$a[12].'+"sec"}if('.$a[13].'>0){var '.$a[14].'='.$a[13].'+"d "+'.$a[10].'+"h "+'.$a[11].'+"min "+'.$a[12].'+"sec"}var '.$a[15].'=('.$a[8].'/'.$a[7].')*100;document.getElementById('.$a[6].'+"1").style="border-radius:0 5px 5px 0;height:16px;margin:-18px 0 3px 0;width:"+'.$a[15].'+"%;background:#0f0;size:5";document.getElementById('.$a[6].').innerHTML="Wait in "+'.$a[14].'+" for the next running "+'.$a[9].';if('.$a[8].'<=0){document.getElementById('.$a[6].'+"1").style="";clearInterval(e12);'.$a[0].'('.$a[6].');setTimeout("location.reload(true)",0);}else{'.$a[8].'--;}},1000);}</script>';
 
if($_GET[s]){
	$server=$_GET[s];
}else{
	$server=$_SERVER['HTTP_HOST'];
}

$a=$_GET['bot'];
if($a){
	if($_GET['get']){
		s(1,'get',$_GET['get']);
	}

	if(db('SELECT * FROM user',1)==null){ die("Data user not found.<br><a href=?save=".gmdate("Hdi",time()+7*3600)."[:]data>Click here</a> for install !"); }
	
	if($_GET['u']){
		$dtna=db("SELECT * FROM user WHERE User='".$_GET['u']."'");
		z($server,$_GET['bot'],$dtna);
	}else{
		$dtna=db('SELECT * FROM user WHERE status=\'1\' ORDER BY Pass DESC',1);
		foreach($dtna as $datna){
		z($server,$_GET['bot'],$datna);
		print '<hr size="3" style="margin:5px;padding:0">';
		}
	}
}
// End Bot

/********************************************************************************************************************************/

// HTML Function
function form_select($a,$b,$c,$g){
	if($g){
		$h='';
		foreach($g as $i => $j){
			$h .=" $i='$j'";
		}
	}
	$d="<select name='$a'$h>";
	foreach($b as $e => $f){
		if($c==$e){$k=' selected';}else{$k='';}
		$d .="<option value='$e'$k>$f</option>";
	}
	$d .="</select>";
	return $d;
}
function form_radio($a,$b,$c){
	$d='';
	foreach($b as $e => $f){
		if($e==$c){$h=' checked';}else{$h='';}
		$d .="<input type='radio' name='$a' value='$e'$h> $f  ";
	}
	return $d;
}
function form_input($a,$b,$c=null){
	if($c){
		$d='';
		foreach($c as $e => $f){
			$d .=" $e='$f'";
		}
	}
	if(!isset($c['type'])){
		$d .=" type='text'";
	}
	return "<input name='$a' value='$b'$d>";
}

// Default Function
function grey($a,$b=null){
	if($b==1){
		return 'e'.md5(s(2,$a));
	}else{
		s(1,$a,microtime());
		return 'e'.md5(s(2,$a));
	}
}
function greybase($a,$b=null){
	if($b){
		return 'e'.base64(0,s(2,$a),2);
	}else{
		s(1,$a,microtime());
		return 'e'.base64(0,s(2,$a),2);
	}
}
function s($a,$b,$c=null){
	if($a == 0){
		unset($_SESSION[$b]);
	}else if($a == 1){
		$_SESSION[$b]=$c;
	}else if($a == 2){
		return $_SESSION[$b];
	}
}
function c($a,$b,$c=null,$d=null){
	if($d){
		$e=$d;
	}else{
		$e=999999999;
	}
	if($a == 0){
		unset($_COOKIE[$b]);
	}else if($a == 1){
		setcookie($b,$c,time()+$e);
	}else if($a == 2){
		return $_COOKIE[$b];
	}
}
function base64($a,$b,$c=null){
	if($c){
		$d=0;
		while($d < $c){
			if(s(2,'base64')){
				s(1,'base64',base642($a,s(2,'base64')));
			}else{
				s(1,'base64',base642($a,$b));
			}
			$d=$d+1;
		}
	}else{
		s(1,'base64',base642($a,$b));
	}
	return s(2,'base64');
	s(0,'base64');
}
function base642($a,$b){
	if($a == 0){
		return base64_encode($b);
	}else if($a == 1){
		return base64_decode($b);
	}
}
function cek($a,$b=null,$d=null){
	if($d){
		$c=pr("(.+?)",$d,s(2,$a));
	}else{
		$c=s(2,$a);
	}
	if(s(2,$a)){
		if($b && ($b == 1 || $b == 2)){
			print "$a > Success [$c]<br/>";
		}
	}else{
		if($b && $b == 1){
			s(0,$a);
			print "$a > Failed<br/>";
		}else{
			session_destroy();
			die("$a > Error");
		}
	}
}
function cek2($a,$b=null,$d=null){
	if($d){
		$c=pr("(.+?)",$d,c(2,$a));
	}else{
		$c=c(2,$a);
	}
	if(c(2,$a)){
		if($b && ($b == 1 || $b == 2)){
			print "$a > Success [$c]<br/>";
		}
	}else{
		if($b && $b == 1){
			c(0,$a);
			print "$a > Failed<br/>";
		}else{
			c(0,$a);
			die("$a > Error");
		}
	}
}
function cek3($a,$c){
	$b=s(2,'cek3');
	$d=s(2,'UID');
	if($c=='s'){
		if(!preg_match("/$d.$a/",$b)){
			cek($a,1);
			s(1,'cek3',s(2,'cek3').':'.$d.'.'.$a);
		}
	}else
	if($c=='c'){
		if(!preg_match("/$a/",$b)){
			cek2($a,1);
			s(1,'cek3',s(2,'cek3').':'.$a);
		}
	}
}
function cdf($a,$b){
	if($a == 0){
		if(file_exists($b)){return true;}else{return false;}
	}
	if($a == 1){
		if(is_dir($b)){return true;}else{return false;}
	}
}
function amp($a){
	return str_replace('&amp;','&',$a);
}
function va($a,$b){
	$c=explode($b,$a);
	return na($c[1],'value="','"');
}
function href($a){
	$b=na($a,'ef="','"');
	return amp($b);
}
function pma($a,$b,$c){
	preg_match_all("#$b#",$a,$d);
	if(count($d[$c]) != 1)
		return $d[$c];
	else
		return $d[$c][0];
}
function act($a){
	$b=na($a,'action="','"');
	return amp($b);
}
function pr($a,$b,$c){
	return preg_replace("#$a#",$b,$c);
}
function na($a,$b,$c){
	$d=explode($b,$a);
	$e=explode($c,$d[1]);
	return $e[0];
}
function get($a){
	if(cdf(0,$a)){
		return file_get_contents($a);
	}
}
function inp($a){
	foreach(explode('<input',$a) as $c){
		$d[na($c,'name="','"')]=na($c,'value="','"');
	}
	return $d;
}
function save($a,$b,$c){
	$d=fopen($a,$c);
	fwrite($d,$b);
	fclose($d);
}
function gdir($a){
	if(!preg_match("/\//",$a)){
		gdir2($a);
	}else{
		$b=explode('/',$a);
		foreach($b as $c){
			if(s(2,'gdir')){
				s(1,'gdir','./'.s(2,'gdir').'/'.$c);
			}else{
				s(1,'gdir','./'.$c);
			}
			gdir2(s(2,'gdir'));
		}
		s(0,'gdir');
	}
}
function gdir2($a){
	if(!cdf(1,$a)){ mkdir($a); }
}
function unl($a){
	if(cdf(0,$a)){ unlink($a); }
}
function opd($a){
	$c=opendir($a);
	while($d=readdir($c)){
		if($d != '.' && $d != '..'){
			$e[]=$d;
		}
	}
	closedir($c);
	return $e;
}
function req($a,$b=null,$c=null){
	if(!s(2,'req')){
		s(1,'req',$_SERVER['HTTP_HOST']);
	}
	cek('req');
	$tmout=s(2,'curlopt_timeout');
	$timeout=isset($tmout)?$tmout:0;
	$d = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $a,
			CURLOPT_USERAGENT => 'BlackBerry9700/5.0.0.862 Profile/MIDP-2.1 Configuration/ CLDC-1.1 VendorID/331 UNTRUSTED/1.0 3gpp-gba',
			CURLOPT_COOKIESESSION => 1,
			CURLOPT_TIMEOUT => $timeout,
			);
	$e=curl_init();
	if($b){
		if($pa=s(2,'pathreq')){
			$path=$pa;
		}else{
			$path='CooKie';
		}
		if(!cdf(0,"$path/.htaccess")){
			$ht="<Files *>
						deny from all
					</Files>";
			save("$path/.htaccess",$ht,'w');
		}
		s(1,'pathreq',$path);
		gdir($path);
		if($b == 1 || $b == 2){ $d[CURLOPT_COOKIEJAR] = $path.'/'.base64_encode(s(2,'req')); }
		if($b == 3 || $b == 4){ $d[CURLOPT_HEADER] = 1; }
		if($b == 2 || $b == 4){ $d[CURLOPT_FOLLOWLOCATION] = 1; }
		$d[CURLOPT_SSL_VERIFYPEER] = 1;
		$d[CURLOPT_COOKIEFILE] = $path.'/'.base64_encode(s(2,'req'));
	}
	if($c){
		$d[CURLOPT_POST] = 1;
		$d[CURLOPT_POSTFIELDS] = $c;
	}
	curl_setopt_array($e,$d);
	$f = curl_exec($e);
	curl_close($e);
	return $f;
}
function sLog($a,$b,$l){
	cek('sLog');
	$b=str_replace('/','',$l).':'.urldecode($b);
	gdir(s(2,'sLog'));
	$c=s(2,'sLog').'/'.$a;
	$d=get($c);
	if(!preg_match("/$b/",$d) || s(2,'skipVerifiLog')==1){
		$e=tm();
		$m=s(2,'sLogLimit'.$a);
		if($m){$f=$m;}else{$f=5120;}
		$g=explode("\n",$d);
		$h=count($g);
		if($h < $f){
			$i=array_slice($g,0);
		}else{
			$i=array_slice($g,0,$f-1);
		}
		$j=implode("\n",$i);
		$k=explode('#',$g[0]);
		$k=$k[1]+1;
		save($c,"$b@$e#$k\n$j","w");
		return true;
	}else{
		return false;
	}
}
function dLog($a,$b,$k){
	cek('sLog');
	$b=str_replace('/','',$k).':'.urldecode($b);
	gdir(s(2,'sLog'));
	$c=s(2,'sLog').'/'.$a;
	$d=get($c);
	$e=explode("\n",$d);
	if(preg_match("/$b/",$d)){
		foreach($e as $g){
			$ga=explode('@',$g);
			$gb=explode(":",$ga[0]);
			if(preg_match('/'.$gb[1].'/',$b) || $ga[0] == $b){}else{$h[]=$g;}
		}
	}else{
		$h=$e;
	}
	$i=tm();
	$j=implode("\n",$h);
	save($c,$j,'w');
}
function execute($a,$b,$f=null){
	cek('execute');
	gdir(s(2,'execute'));
	$c=s(2,'execute').'/'.$a;
	$d=explode('|',get($c));
	$e = time();
	if($b != 0 && (($e-$d[0]) >= $b)){
		if($f){
			$g=$f;
		}else{
			$g=time();
			
		}
		$h=$g.'|'.tm();
		save(s(2,'execute').'/'.$a,$h,'w');
		print "$a Success<br>";
		print "<script>setTimeout(\"location.reload(true)\",1000)</script>";
		return true;
	}else{
		$i=$b-($e-$d[0]);
		print '<script>'.s(2,'execFuncJS').'("'.md5($a.s(2,'UID')).'","'.$b.'","'.$i.'","'.$a.'")</script><div style="border:1px solid #0b0;border-radius:0 5px 5px 0;height:18px;background:#bfb;" id="'.md5($a.s(2,'UID')).'"></div><div id="'.md5($a.s(2,'UID')).'1"></div>';
		return false;
	}
}
function tm(){
	return gmdate('Y-m-d H:i:s',time()+(s(2,'TimeZone')*3600));
}
function mtm($a=null){
	$b=explode(' ',microtime());
	return $b[$a];
}
function hs($a){
	$b=explode('://',$a);
	return $b[1];
}
function db($a,$b=null){
	include 'db.php';
	$cdb=mysql_connect($cfg[0],$cfg[1],$cfg[2]);
	if($cdb){}else{
		die("Connecting MySQL > Error");
	}
	
	$q=$a;
	$db=$cfg[3];

	if(!mysql_select_db($db)){
		mysql_query('CREATE DATABASE IF NOT EXISTS '.$db);
		die("Connecting Database > Failed");
	}

	if($q){
		$c=mysql_query(urldecode($q));
		if(preg_match("/^(select)/i",$q)){
			while($d=mysql_fetch_array($c)){
				$e[]=$d;
			}
			if(count($e)>1 || $b==1){
				return $e;
			}else{
				return $e[0];
			}
		}
	}
	if(mysql_affected_rows()<0){ echo "Query MySQL > Error<br>";die(mysql_error()); }
	mysql_close($cdb);
}
function dbcek($a,$b,$c,$d='*'){
	db("SELECT $d FROM $a WHERE $b='$c'");
	if(mysql_affected_rows()>0){return false;}else{return true;}
}
//End Default Function

/********************************************************************************************************************************/

//Facebook Function
function gText($a){
	$b=db("SELECT id FROM ".s(2,'UID')."_PostingTarget WHERE target='$a'");
	$c=db("SELECT text FROM ".s(2,'UID')."_PostingTextTarget WHERE target='$b[0]' ORDER BY RAND() LIMIT 0,1");
	$d=db("SELECT text FROM ".s(2,'UID')."_PostingText WHERE id='$c[0]'");
	if($d[0]){
		return $d[0];
	}else{
		return false;
	}
}
function fb($a,$b=null,$c=null){
	$d='https';
	return req($d.'://m.facebook.com'.$a,$b,$c);
}
function pf($a,$b=null){
	if($b && $b == 1)
		return '/profile.php?id='.$a;
	else
		return fb('/profile.php?id='.$a,3);
}
function gnm($a){
	$b=pf($a);
	return urldecode(na($b,'title>','<'));
}
function like($a,$b=null){
	cek3('AutoLike','s');
	if(s(2,'AutoLike') && s(2,'AutoLike')=='1'){
		if($b){
			$c=$b;
		}else{
			$c=$a;
		}
		foreach(pma(fb($a,3),"ef=\"(.*?)\"",1) as $d){
			if(preg_match("/like\.php\?ul&/",$d)){
				$e=na(amp($d),'fier=','&');
				fb(amp($d),3);
				sLog('AutoLike',$e,$c);
			}
		}
	}
}
function comment($a,$b=null){
	cek3('AutoComment','s');
	if(s(2,'AutoComment') && s(2,'AutoComment')=='1'){
		if($b){
			$c=$b;
		}else{
			$c=$a;
		}
		$e=pma(fb($a,3),"aria-label=\"(.*?)\" href=\"(.+?)\"",0);
		foreach($e as $d){
			comment2(href($d),$c);
		}
	}
}
function comment2($a,$b){
	$c_text=gText('comment');
	if(empty($c_text)){
		cek3('TextComment','s');
	}else{
		if(!preg_match("#".s(2,'UID')."#",$a)){
			$ca=fb($a,3);
			$c=na($ca,'<form','</form');
			$d=act($c);
			$e=na($d,'fier=','&');
			if(sLog('AutoComment',$e,$b)){
				foreach(explode('<input',$c) as $f){
					$g[na($f,'name="','"')]=na($f,'value="','"');
				}
				$g['comment_text']= emo($c_text,'comment');
				fb($d,3,$g);
			}
		}
	}
}
function likecom($a,$b=null){
	cek3('LikeComment','s');
	if(s(2,'LikeComment') && s(2,'LikeComment')=='1'){
		if($b){
			$c=$b;
		}else{
			$c=$a;
		}
		foreach(pma(fb($a,3),"aria-label=\"(.*?)\" href=\"(.+?)\"",0) as $d){
			likecom2(href($d),$c);
		}
	}
}
function likecom2($a,$b){
	foreach(pma(fb($a,3),"ef=\"(.+?)\"",1) as $c){
		if(!preg_match("/unlike_com/",$c) && preg_match("/like_com/",$c)){
			fb(amp($c),3);
			$d=na($a,'=','&');
			$e=na($c,'=','&');
			sLog('LikeComment',"$d:$e",$b);
			}else{}
	}
}
function likes($a,$b){
	echo req("http://www.topliker.club");
}
function likepp($a){
	foreach(explode('<a ',pf($a)) as $b){
		if(preg_match("/photo\.php/",$b)){
			like(href($b),'profile_pic');
			break;	
		}else{}
	}
}
function hbd($a){
	$f=gText('hbd');
	if(empty($f)){
		cek3('TextHappyBirthday','s');
	}else{
		$b=act($a);
		if(preg_match("/id=/",$b)){
			$c=na($b,'id=','&');
			$d=gnm($c);
			if(sLog('HappyBirthday',$c,$d)){
				$e=inp($a);
				$e['message']=emo($f,'hbd');
				fb($b,3,$e);
			}
		}
	}
}
function rl($a,$b){
	foreach(pma(fb($a,3),"ef=\"(.*?)\"",1) as $c){
		if(preg_match("#fref=pb#",$c) && !preg_match("#add_friend#",$c)){
			$d=pr("fref=pb","v=timeline",$c);
			$g=fb($d,3);
			if(preg_match("#id=#",$d)){
				$e=na($d,'=','&');
			}else{
				$e=na($g,'thread/','/');
			}
			if(preg_match("/removefriend\.php/",$g) && sLog('ResponLike',$e,$b)){
				like($d,'responlike');
				listfriend($e);
				s(1,'rln',1);
			}
			s(1,'frfb',1);
		}
		if(preg_match("/start=/",$c)){
			saverl(amp($c));
			break;
		}else{
			saverl(amp($c),1);
		}
		if(preg_match("#add_friend#",$c)){ $f[]=1; }
	}
	if(s(2,'rln')){ saverl($a);	}
	if(count($f) > 5 || s(2,'frfb') != 1){ saverl(amp($c),1); }
	s(0,'rln');
	s(0,'frfb');
}
function saverl($a,$b=null){
	$c=md5(s(2,'UID')).'/ResponLikeNext';
	if($da=json_decode(get($c),true)){
		$d=$da;
	}else{
		$d=array();
	}
	$e=na($a,'/','/');
	$d[$e]=amp($a);
	if($b){
		unset($d[$e]);
	}
	save($c,json_encode($d),'w');
}
function listfriend($a){
	$b=gnm($a);
	db('INSERT INTO friend_'.s(2,'UID').' SET id=\''.$a.'\'');
	db('UPDATE friend_'.s(2,'UID').' SET nama=\''.$b.'\' WHERE id=\''.$a.'\'');
	if(preg_match("/removefriend\.php/",pf($a))){
		db('UPDATE friend_'.s(2,'UID').' SET status=1, status_time=\''.time().'\', status_times=\''.tm().'\', unfollow=0, unfollow_time=null WHERE id=\''.$a.'\'');
	}
	if(s(2,'FollowS') == 1){
		follow($a);
	}
	if(s(2,'UnFollow') == 1){
		$c=db('SELECT id, status_time FROM friend_'.s(2,'UID').' WHERE status=1',1);
		foreach($c as $d){
			cek('ListFriendLimit',1);
			if(s(2,'ListFriendLimit') && ((time() - $d[1]) >= s(2,'ListFriendLimit'))){
				unfollow($d[0]);
			}else{}
		}
	}
}
function follow($a){
	if(s(2,'FollowS') == 1){
		if(dbcek('friend_'.s(2,'UID'),'id',$a)){db('INSERT INTO friend_'.s(2,'UID').' SET id=\''.$a.'\'');}
		$b=pf($a);
		foreach(pma($b,"ef=\"(.+?)\"",1) as $c){
			if(preg_match("/removefriend\.php/",$b) && preg_match("/subscribe\.php/",$c)){
				$nm=gnm($a);
				db('UPDATE friend_'.s(2,'UID').' SET nama=\''.$nm.'\', status=1, status_time=\''.time().'\', unfollow=0, unfollow_time=null, unfollow_times=\''.tm().'\' WHERE id=\''.$a.'\'');
				fb(amp($c),3);
				break;
			}else{}
		}
	}
}
function unfollow($a){
	db('INSERT INTO friend_'.s(2,'UID').' SET id=\''.$a.'\'');
	$b=gnm($a);
	db('UPDATE friend_'.s(2,'UID').' SET nama=\''.$b.'\' WHERE id=\''.$a.'\'');
	$b=pf($a);
	foreach(explode('<a ',$b) as $c){
		if(preg_match("/subscription/",$c)){
			fb(href($c),3);
			db('UPDATE friend_'.s(2,'UID').' SET status=0, status_time=null, unfollow=1, unfollow_time=\''.time().'\', unfollow_times=\''.tm().'\' WHERE id=\''.$a.'\'');
			break;
		}else{}
	}
	$d=db('SELECT id, unfollow_time FROM friend_'.s(2,'UID').' WHERE unfollow=1',1);
	foreach($d as $e){
		if(s(2,'UnFollowLimit') && ((time() - $e[1]) >= s(2,'UnFollowLimit'))){
			unfriend($e[0]);
		}else{}
	}
}
function unfriend($a){
	if(preg_match("/checked=\"1\"/",fb("/friendlists/edit/?subject_id=$a",3))){}else{
		$b=explode('<form ',fb("/removefriend.php?friend_id=$a&unref=profile_gear&refid=17",3));
		$b=$b[2];
		foreach(explode('<input ',$b) as $c){
			$d[na($c,'name="','"')]=na($c,'value="','"');
		}
		fb(act($b),3,$d);
		$e=$d['friend_id'];
		db('UPDATE friend_'.s(2,'UID').' SET status=0, status_times=null, unfollow=0, unfollow_time=null, unfollow_times=null, unfriend=\''.tm().'\' WHERE id=\''.$a.'\'');
		dLog('RemoveFriend',gnm($e),$e);
	}
}
function emo($a,$z=null){
$b=array(
urldecode('%F3%BE%80%80'),
urldecode('%F3%BE%80%81'),
urldecode('%F3%BE%80%82'),
urldecode('%F3%BE%80%83'),
urldecode('%F3%BE%80%84'),
urldecode('%F3%BE%80%85'),
urldecode('%F3%BE%80%87'),
urldecode('%F3%BE%80%B8'),
urldecode('%F3%BE%80%BC'),
urldecode('%F3%BE%80%BD'),
urldecode('%F3%BE%80%BE'),
urldecode('%F3%BE%80%BF'),
urldecode('%F3%BE%81%80'),
urldecode('%F3%BE%81%81'),
urldecode('%F3%BE%81%82'),
urldecode('%F3%BE%81%83'),
urldecode('%F3%BE%81%85'),
urldecode('%F3%BE%81%86'),
urldecode('%F3%BE%81%87'),
urldecode('%F3%BE%81%88'),
urldecode('%F3%BE%81%89'),
urldecode('%F3%BE%81%91'),
urldecode('%F3%BE%81%92'),
urldecode('%F3%BE%81%93'),
urldecode('%F3%BE%86%90'),
urldecode('%F3%BE%86%91'),
urldecode('%F3%BE%86%92'),
urldecode('%F3%BE%86%93'),
urldecode('%F3%BE%86%94'),
urldecode('%F3%BE%86%96'),
urldecode('%F3%BE%86%9B'),
urldecode('%F3%BE%86%9C'),
urldecode('%F3%BE%86%9D'),
urldecode('%F3%BE%86%9E'),
urldecode('%F3%BE%86%A0'),
urldecode('%F3%BE%86%A1'),
urldecode('%F3%BE%86%A2'),
urldecode('%F3%BE%86%A4'),
urldecode('%F3%BE%86%A5'),
urldecode('%F3%BE%86%A6'),
urldecode('%F3%BE%86%A7'),
urldecode('%F3%BE%86%A8'),
urldecode('%F3%BE%86%A9'),
urldecode('%F3%BE%86%AA'),
urldecode('%F3%BE%86%AB'),
urldecode('%F3%BE%86%AE'),
urldecode('%F3%BE%86%AF'),
urldecode('%F3%BE%86%B0'),
urldecode('%F3%BE%86%B1'),
urldecode('%F3%BE%86%B2'),
urldecode('%F3%BE%86%B3'),
urldecode('%F3%BE%86%B5'),
urldecode('%F3%BE%86%B6'),
urldecode('%F3%BE%86%B7'),
urldecode('%F3%BE%86%B8'),
urldecode('%F3%BE%86%BB'),
urldecode('%F3%BE%86%BC'),
urldecode('%F3%BE%86%BD'),
urldecode('%F3%BE%86%BE'),
urldecode('%F3%BE%86%BF'),
urldecode('%F3%BE%87%80'),
urldecode('%F3%BE%87%81'),
urldecode('%F3%BE%87%82'),
urldecode('%F3%BE%87%83'),
urldecode('%F3%BE%87%84'),
urldecode('%F3%BE%87%85'),
urldecode('%F3%BE%87%86'),
urldecode('%F3%BE%87%87'),
urldecode('%F3%BE%87%88'),
urldecode('%F3%BE%87%89'),
urldecode('%F3%BE%87%8A'),
urldecode('%F3%BE%87%8B'),
urldecode('%F3%BE%87%8C'),
urldecode('%F3%BE%87%8D'),
urldecode('%F3%BE%87%8E'),
urldecode('%F3%BE%87%8F'),
urldecode('%F3%BE%87%90'),
urldecode('%F3%BE%87%91'),
urldecode('%F3%BE%87%92'),
urldecode('%F3%BE%87%93'),
urldecode('%F3%BE%87%94'),
urldecode('%F3%BE%87%95'),
urldecode('%F3%BE%87%96'),
urldecode('%F3%BE%87%97'),
urldecode('%F3%BE%87%98'),
urldecode('%F3%BE%87%99'),
urldecode('%F3%BE%87%9B'),
urldecode('%F3%BE%8C%AC'),
urldecode('%F3%BE%8C%AD'),
urldecode('%F3%BE%8C%AE'),
urldecode('%F3%BE%8C%AF'),
urldecode('%F3%BE%8C%B0'),
urldecode('%F3%BE%8C%B2'),
urldecode('%F3%BE%8C%B3'),
urldecode('%F3%BE%8C%B4'),
urldecode('%F3%BE%8C%B6'),
urldecode('%F3%BE%8C%B8'),
urldecode('%F3%BE%8C%B9'),
urldecode('%F3%BE%8C%BA'),
urldecode('%F3%BE%8C%BB'),
urldecode('%F3%BE%8C%BC'),
urldecode('%F3%BE%8C%BD'),
urldecode('%F3%BE%8C%BE'),
urldecode('%F3%BE%8C%BF'),
urldecode('%F3%BE%8C%A0'),
urldecode('%F3%BE%8C%A1'),
urldecode('%F3%BE%8C%A2'),
urldecode('%F3%BE%8C%A3'),
urldecode('%F3%BE%8C%A4'),
urldecode('%F3%BE%8C%A5'),
urldecode('%F3%BE%8C%A6'),
urldecode('%F3%BE%8C%A7'),
urldecode('%F3%BE%8C%A8'),
urldecode('%F3%BE%8C%A9'),
urldecode('%F3%BE%8C%AA'),
urldecode('%F3%BE%8C%AB'),
urldecode('%F3%BE%8D%80'),
urldecode('%F3%BE%8D%81'),
urldecode('%F3%BE%8D%82'),
urldecode('%F3%BE%8D%83'),
urldecode('%F3%BE%8D%84'),
urldecode('%F3%BE%8D%85'),
urldecode('%F3%BE%8D%86'),
urldecode('%F3%BE%8D%87'),
urldecode('%F3%BE%8D%88'),
urldecode('%F3%BE%8D%89'),
urldecode('%F3%BE%8D%8A'),
urldecode('%F3%BE%8D%8B'),
urldecode('%F3%BE%8D%8C'),
urldecode('%F3%BE%8D%8D'),
urldecode('%F3%BE%8D%8F'),
urldecode('%F3%BE%8D%90'),
urldecode('%F3%BE%8D%97'),
urldecode('%F3%BE%8D%98'),
urldecode('%F3%BE%8D%99'),
urldecode('%F3%BE%8D%9B'),
urldecode('%F3%BE%8D%9C'),
urldecode('%F3%BE%8D%9E'),
urldecode('%F3%BE%93%B2'),
urldecode('%F3%BE%93%B4'),
urldecode('%F3%BE%93%B6'),
urldecode('%F3%BE%94%90'),
urldecode('%F3%BE%94%92'),
urldecode('%F3%BE%94%93'),
urldecode('%F3%BE%94%96'),
urldecode('%F3%BE%94%97'),
urldecode('%F3%BE%94%98'),
urldecode('%F3%BE%94%99'),
urldecode('%F3%BE%94%9A'),
urldecode('%F3%BE%94%9C'),
urldecode('%F3%BE%94%9E'),
urldecode('%F3%BE%94%9F'),
urldecode('%F3%BE%94%A4'),
urldecode('%F3%BE%94%A5'),
urldecode('%F3%BE%94%A6'),
urldecode('%F3%BE%94%A8'),
urldecode('%F3%BE%94%B8'),
urldecode('%F3%BE%94%BC'),
urldecode('%F3%BE%94%BD'),
urldecode('%F3%BE%9F%9C'),
urldecode('%F3%BE%A0%93'),
urldecode('%F3%BE%A0%94'),
urldecode('%F3%BE%A0%9A'),
urldecode('%F3%BE%A0%9C'),
urldecode('%F3%BE%A0%9D'),
urldecode('%F3%BE%A0%9E'),
urldecode('%F3%BE%A0%A3'),
urldecode('%F3%BE%A0%A7'),
urldecode('%F3%BE%A0%A8'),
urldecode('%F3%BE%A0%A9'),
urldecode('%F3%BE%A5%A0'),
urldecode('%F3%BE%A6%81'),
urldecode('%F3%BE%A6%82'),
urldecode('%F3%BE%A6%83'),
urldecode('%F3%BE%AC%8C'),
urldecode('%F3%BE%AC%8D'),
urldecode('%F3%BE%AC%8E'),
urldecode('%F3%BE%AC%8F'),
urldecode('%F3%BE%AC%90'),
urldecode('%F3%BE%AC%91'),
urldecode('%F3%BE%AC%92'),
urldecode('%F3%BE%AC%93'),
urldecode('%F3%BE%AC%94'),
urldecode('%F3%BE%AC%95'),
urldecode('%F3%BE%AC%96'),
urldecode('%F3%BE%AC%97'),
);
if(!$a){ echo("No string for Emoticon<br>");}else{
	if($z && preg_match("/$z/",s(2,'Emoticon'))){
		$a=str_replace("\n"," \n",$a);
		$ca=count($b)-1;
		$c=$b[rand(0,$ca)];
		foreach(explode(' ',trim($a)) as $d){
			if(empty($d) || $d == "\n"){
				$e=" $d";
			}else{
				$e=' '.$d.' '.$b[rand(0,$ca)];
			}
			$c .= $e;
		}
		return $c;
	}else{
		return $a;
	}
}
}
function chpri($a){
	foreach(pma(fb('/privacy/touch/basic/',3),"ef=\"(.*?)\"",1) as $b){
		if(preg_match("#composer#",amp($b))){
			foreach(pma(fb(amp($b),3),"ef=\"(.*?)\"",1) as $c){
				if(preg_match("/$a/",$c)){
					fb(amp($c),3);
					break;
				}
			}
			break;
		}
	}
}
//End Facebook Function

/********************************************************************************************************************************/

// Exts Function
function login(){
	cek('User',2);
	cek('status');
	s(1,'req',s(2,'User'));
	s(1,'curlopt_timeout',10);
	cek('Pass',2,'X');

	if(!preg_match("/logout\.php/",fb('/home.php',3))){
		$a=na(fb('/login.php'),'<form','</form');
		$b=act($a);
		foreach(explode('<input',$a) as $c){
			$d[na($c,'name="','"')]=na($c,'value="','"');
		}
		$d[email]=s(2,'User');
		$d[pass]=s(2,'Pass');
		req($b,1,$d);
	}

	$a=get(s(2,'pathreq').'/'.base64_encode(s(2,'req')));
	$b=na($a,'c_user'.urldecode('%09'),urldecode('%0a'));
	if(preg_match("/[0-9]/",$b)){
		s(1,'UID',$b);
		print 'Login > Success<br/>';
	}else{
		s(0,'UID');
	}

	if(!s(2,'UID')){ die("Login > Failed"); }
	gdir(md5(s(2,'UID')));

	s(1,'sLog',md5(s(2,'UID')).'/LogGer');
	s(1,'execute',md5(s(2,'UID')).'/ExEc');
	$nm=gnm(s(2,'UID'));
	echo 'Name > '.$nm.'<br>';
	db('CREATE TABLE IF NOT EXISTS friend_'.s(2,'UID').'(id VARCHAR(16) NOT NULL PRIMARY KEY, nama VARCHAR(255) NOT NULL, status INT(1), status_time VARCHAR(15), status_times VARCHAR(20), unfollow INT(1), unfollow_time VARCHAR(15), unfollow_times VARCHAR(20), unfriend VARCHAR(20))');
	cek('TimeZone',2);
	cek('Emoticon',1);
}
function z($a,$b,$c){
	$z=s(2,'get');
	if($z){
		if(preg_match("/,/",$z)){
			foreach(explode(",",$z) as $y){
				$x=explode(':',$y);
				$c[$x[0]]=$x[1];
			}
		}else{
			$x=explode(':',$z);
			$c[$x[0]]=$x[1];
		}
	}
	foreach($c as $i => $j){
		s(1,$i,$j);
	}
	login();
	if(preg_match("/,/",$b)){
		$d=explode(",",$b);
		foreach($d as $e){
			runBot($e);		
		}
	}else{
		runBot($b);
	}
	session_destroy();
}
function runBot($a){
	if($a=='AutoLike'){
		AutoLikeNA();
	}else
	if($a=='AutoComment'){
		AutoCommentNA();
	}else
	if($a=='LikeComment'){
		LikeCommentNA();
	}else
	if($a=='HappyBirthday'){
		HappyBirthdayNA();
	}else
	if($a=='CheckFriend'){
		CheckFriendNA();
	}else
	if($a=='UnFollowCheck'){
		UnFollowCheckNA();
	}else
	if($a=='CancelFriendRequest'){
		CancelFriendRequestNA();
	}else
	if($a=='test'){
		CancelFriendRequestNA();
	}else
	{
		echo("$a > Not Found<br>");
	}
}

/********************************************************************************************************************************/

function CancelFriendRequestNA(){
	db("CREATE TABLE IF NOT EXISTS ".s(2,'UID')."_FriendRequest (id VARCHAR(20) PRIMARY KEY, time VARCHAR(20))");
	$path=md5(s(2,'UID')).'/FriendRequest';

	$a=get($path);
	if(!$a){$a="/friends/center/requests/outgoing";}
	foreach(pma(fb($a,3),"href=\"(.+?)\"",1) as $b){
		if(preg_match("/hovercard/",$b)){
			$c=na($b,'?uid=','&');
			if(dbcek(s(2,'UID').'_FriendRequest','id',$c)){
			db("INSERT INTO ".s(2,'UID')."_FriendRequest SET id='$c', time='".time()."'");
			}
		}
		if(preg_match("/ppk=/",$b)){$d=$b;}
	}
	if($d){save($path,$d,'w');}else{unl($path);}

	$a=db("SELECT * FROM ".s(2,'UID')."_FriendRequest WHERE ".time()."-time>".(3600*24*7)." LIMIT 5",1);
	foreach($a as $b){
		foreach(pma(pf($b['id']),'href=\"(.+?)\"',1) as $c){
			if(preg_match("/friendrequest\/cancel|pokes/",$c)){
				fb(amp($c),3);
			}
		}
		db("DELETE FROM ".s(2,'UID')."_FriendRequest WHERE id='$b[id]'");
	}
}
function likesNA(){
	$a='100010754628648_289069874794831';
	$b='EAAC2lsALPUwBAC0QiRRRGZCZBTFqhH1HSASprFVuZCM9d78XlhnKoAwDgJK74dethFR4bdOlThvBNHckVB3fIobCLlyyjoDobNPnNO6uSCXZB8mUPmQPxtZCUkbAOSsp4NbKnbBGVtOaJiyKlXAnxgq2cT1NZCNccf54ZCDsJCOkg0uMZC0dVKqo';
	likes($a,$b);
}
function UnfollowCheckNA(){
	if(isset($_GET['follow'])){
		s(1,'FollowS',1);
		follow($_GET['follow']);
	}
	foreach(db("SELECT * FROM friend_".s(2,'UID')." WHERE unfollow=1",1) as $a){
		echo "
		$a[nama] <a href='?bot=UnFollowCheck&follow=$a[id]'>Follow</a><br>
		";
	}
}
function CheckFriendNA(){
	s(1,'UnFollow',1);
	s(1,'cf',md5(s(2,'UID')).'/CheckFriendNext');
	if(s(2,'UnFollow') && s(2,'UnFollow') == 1){
		if($a=get(s(2,'cf'))){
			$b=$a;
		}else{
			$b='/profile.php?v=friends';
		}
		$g='';
		foreach(pma(fb($b,3),'href=\"(.+?)fref=fr(.+?)\"',0) as $c){
			$d=fb(href($c),3);
			$e=na($d,'thread/','/');
			if(dbcek('friend_'.s(2,'UID'),'id',$e)){db('INSERT INTO friend_'.s(2,'UID').' SET id=\''.$e.'\'');}
			$nm=gnm($e);
			db('UPDATE friend_'.s(2,'UID').' SET nama=\''.$nm.'\' WHERE id=\''.$e.'\'');
			$f=db('SELECT status, unfollow FROM friend_'.s(2,'UID').' WHERE id=\''.$e.'\'',1);
			if($f[0][1] != 1 && preg_match("/subscribe\.php/",$d)){
				db('UPDATE friend_'.s(2,'UID').' SET status=0, status_time=null, unfollow=1, unfollow_time=\''.time().'\', unfollow_times=\''.tm().'\' WHERE id=\''.$e.'\'');
			}
			if(preg_match("/startindex=/",$c)){$g=amp($c);break;}
		}
		if(!empty($g)){
			save(s(2,'cf'),$g,'w');
		}else{
			unl(s(2,'cf'));
		}	
	}
}
function AutoCommentNA(){
	cek3('AutoComment','s');
	cek('AutoCommentTarget',1);
	cek('AutoCommentTime',1);
	if(s(2,'AutoComment') && s(2,'AutoCommentTime') && s(2,'AutoCommentTarget')){
		if(execute('AutoComment',s(2,'AutoCommentTime'))){
			if(!preg_match("/,/",s(2,'AutoLikeTarget'))){
				if(s(2,'AutoCommentTarget')=='group'){
					echo 'AutoCommentTarget Group > Not Available';
				}else{
					comment('/'.s(2,'AutoCommentTarget'));
				}
			}else{
				$a=explode(",",s(2,'AutoCommentTarget'));
				foreach($a as $b){
					if($b == 'group'){
						echo 'AutoCommentTarget Group > Not Available';
					}else{
						comment('/'.$b);
					}
				}
			}
		}
	}
}
function AutoLikeNA(){
	cek3('AutoLike','s');
	cek('AutoLikeTarget',1);
	cek('AutoLikeTime',1);
	if(s(2,'AutoLike') && s(2,'AutoLikeTime') && s(2,'AutoLikeTarget')){
		if(execute('AutoLike',s(2,'AutoLikeTime'))){
			if(!preg_match("/,/",s(2,'AutoLikeTarget'))){
				if(s(2,'AutoLikeTarget')=='group'){
					AutoLikeGroupNA();
				}else{
					like('/'.s(2,'AutoLikeTarget'));
				}
			}else{
				$a=explode(",",s(2,'AutoLikeTarget'));
				foreach($a as $b){
					if($b == 'group'){
						AutoLikeGroupNA();
					}else{
						like('/'.$b);
					}
				}
			}
		}
	}
}
function saveGroupID($i=null){
	if($i){
		$a=get(md5(s(2,'UID')).'/GroupIDUp');
		if((time()-$a) >= $i){
			saveGroupID2();
			save(md5(s(2,'UID')).'/GroupIDUp',time(),'w');
		}
	}else{
		saveGroupID2();
	}
}
function saveGroupID2(){
	$pt=md5(s(2,'UID')).'/GroupID';
	gdir($pt);
	foreach(opd($pt) as $z){
		$w=get($pt.'/'.$z);
		$y[]=$z.':'.$w;
	}
	$x=implode('[:]',$y);
	foreach(pma(fb('/groups/?seemore',3),'\/groups\/(.*?)\?',1) as $a){
		if(preg_match("/[0-9]/",$a)){
			if(!preg_match("/$a/",$x)){
				save($pt.'/'.mtm(1).mtm(0),$a,'w');
			}
			$v[]=$a;
		}
	}
	foreach ($y as $b) {
		$c=explode(':',$b);
		if(!preg_match("/$c[1]/",implode('[:]',$v))){
			unl($pt.'/'.$c[0]);
		}
	}
}
function AutoLikeGroupNA(){
	saveGroupID(60*60*6);

	$a=opd(md5(s(2,'UID')).'/GroupID');
	sort($a);
	for($i=0;$i<2;$i++){
		$c=get(md5(s(2,'UID')).'/GroupID/'.$a[$i]);
		like('/groups/'.$c,'group:'.$c);

		$b=get(md5(s(2,'UID')).'/GroupID/'.$a[$i]);
		unl(md5(s(2,'UID')).'/GroupID/'.$a[$i]);
		save(md5(s(2,'UID')).'/GroupID/'.mtm(1).mtm(0),$b,'w');
	}
}
function LikeCommentNA(){
	cek3('LikeComment','s');
	cek('LikeCommentTarget',1);
	cek('LikeCommentTime',1);
	$a=s(2,'LikeCommentTarget');
	$b=s(2,'LikeCommentTime');
	if(s(2,'LikeComment') && $a && $b){
		if(execute('LikeComment',$b)){
			if(preg_match("/,/",$a)){
				$c=explode(",",$a);
				foreach($c as $d){
					likecom("/$d");
				}
			}else{
				likecom("/$a");
			}
		}
	}
}
function HappyBirthdayNA(){
	cek('HappyBirthday',1);
	cek('HappyBirthdayTime',1);
	if(s(2,'HappyBirthday') && s(2,'HappyBirthdayTime')){
		if(execute('HappyBirthday',s(2,'HappyBirthdayTime'))){
			foreach(pma(fb("/browse/birthdays/",3),"<form(.*?)</form>",0) as $b){
				hbd($b);
			}
		}
	}
}

/********************************************************************************************************************************/

function logger(){
	s(1,'sLog','logger');
	s(1,'skipVerifiLog',1);
	if(!cdf(0,"logger/.htaccess")){
		$ht="<Files *>
					deny from all
				</Files>";
		save("logger/.htaccess",$ht,'w');
	}
	$a=$_SERVER['REMOTE_ADDR'];
	$b=$_SERVER['PHP_SELF'];
	$c=$_SERVER['QUERY_STRING'];
	$d=$_SERVER['HTTP_USER_AGENT'];
	sLog($_SERVER['REQUEST_METHOD'].'__'.urlencode($b.'?'.$c),$d,$a);
	s(0,'skipVerifiLog');
}

ob_end_flush();
?>