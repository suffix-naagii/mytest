<?php
include("search.php");
include("../getroot.php");

function connecting(){
	$time=time();
	$session_id = session_id();
	
	$sql = "DELETE FROM connection WHERE ($time-times)>600";
	mysql_query($sql) or die(mysql_error());
	
	$sql = 'SELECT * FROM connection WHERE s_id="'.session_id().'"';
	$res = mysql_query($sql) or die(mysql_error());
	if(mysql_num_rows($res)>0){
		$sql = 'UPDATE connection SET times='.$time.' WHERE s_id="'.session_id().'"';
		mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_object($res);
		
		$_SESSION['user'] = $row->u_id;
		$_SESSION['priv'] = $row->priv;
	} else {
		$browser = getenv('HTTP_USER_AGENT');
		$ip = getenv('REMOTE_ADDR');
		$sql = "INSERT INTO connection VALUES ('','$session_id', 0, 0, $time, '$ip', '$browser')";
		car_analytics();
		mysql_query($sql) or die(mysql_error());
	}
}

function topmenu(){
	/* ---------------------------------------------- */
	$ba_sql = 'SELECT * FROM barilga_buleg ORDER BY id';
	$ba_res = mysql_query($ba_sql) or die(mysql_error());
	$ba = '<ul>';
	while($ba_row = mysql_fetch_array($ba_res)){
		$ba .= '<li><a href="http://'.getRoot("barilga").'/list1ii'.$ba_row['id'].'iii">'.$ba_row['value'].'</a></li>';
	}
	$ba .= '</ul>';
	/* ---------------------------------------------- */
	$jo_sql = 'SELECT * FROM job_buleg ORDER BY id';
	$jo_res = mysql_query($jo_sql) or die(mysql_error());
	$jo = '<ul>';
	while($jo_row = mysql_fetch_array($jo_res)){
		$jo .= '<li><a href="http://'.getRoot("job").'/list1ii'.$jo_row['id'].'iii">'.$jo_row['value'].'</a></li>';
	}
	$jo .= '</ul>';
	/* ---------------------------------------------- */
	$ma_sql = 'SELECT * FROM market_buleg ORDER BY id';
	$ma_res = mysql_query($ma_sql) or die(mysql_error());
	$ma = '<ul>';
	while($ma_row = mysql_fetch_array($ma_res)){
		$ma .= '<li><a href="http://'.getRoot("market").'/list1ii'.$ma_row['id'].'iii">'.$ma_row['value'].'</a></li>';
	}
	$ma .= '</ul>';
	/* ---------------------------------------------- */
	$se_sql = 'SELECT * FROM service_buleg ORDER BY id';
	$se_res = mysql_query($se_sql) or die(mysql_error());
	$se = '<ul>';
	while($se_row = mysql_fetch_array($se_res)){
		$se .= '<li><a href="http://'.getRoot("service").'/list1ii'.$se_row['id'].'iii">'.$se_row['value'].'</a></li>';
	}
	$se .= '</ul>';
	/* ---------------------------------------------- */
	$st_sql = 'SELECT * FROM study_buleg ORDER BY id';
	$st_res = mysql_query($st_sql) or die(mysql_error());
	$st = '<ul>';
	while($st_row = mysql_fetch_array($st_res)){
		$st .= '<li><a href="http://'.getRoot("study").'/list1ii'.$st_row['id'].'iii">'.$st_row['value'].'</a></li>';
	}
	$st .= '</ul>';
	/* ---------------------------------------------- */
	$txt='
    <div id="menu">
    	<div class="menunav">
        	<ul><li><a href="home.html">нүүр</a></li>
                <li><a href="add.html" title="зар нэмэх"><b>+зар нэмэх (үнэгүй)</b></a></li>
                <li><a href="http://'.getRoot("barilga").'">Үл хөдлөх</a>'.$ba.'</li>
                <li><a href="http://'.getRoot("auto").'">Автомашин</a></li>
                <li><a href="http://'.getRoot("job").'">Ажил</a>'.$jo.'</li>
                <li><a href="http://'.getRoot("service").'">Үйлчилгээ</a>'.$se.'</li>
                <li><a href="http://'.getRoot("study").'">Сургалт</a>'.$st.'</li>
                <li><a href="http://'.getRoot("market").'">Худалдаа</a>'.$ma.'</li>
            </ul>
        </div>
        <div class="social">
        	<ul><li class="facebook" onclick="window.open(\'http://facebook.com/cityzarmn\',\'_blank\')"></li>
				<li class="twitter" onclick="window.open(\'http://twitter.com/cityzarmn\',\'_blank\')"></li>
			</ul>
		</div>       
    </div>';
	return $txt;
}

function footermenu(){
	$txt='
	<div class="fmenu">
		<ul><li><a href="page1.html">Үйлчилгээний нөхцөл</a></li>
			<li><a href="page2.html"><font color="#333">Онцлох машин</font></a></li>
			<li><a href="page3.html"><font color="#333">Сурталчилгаа байршуулах</font></a></li>
			<li><a href="page4.html">Вэб хийх</a></li>
			<li><a href="page5.html">Ажлын байр</a></li>
			<li><a href="page6.html">Холбоо барих</a></li>
		</ul>
	</div>';
	return $txt;
}

function czslider(){
	mysql_query('DELETE FROM car_sponsor WHERE '.time().'>end_time') or die(mysql_error());
	$sql = '
	SELECT G.*, H.firm, H.mark FROM 
	(	SELECT C.*, D.url_path FROM 
		(	SELECT A.*, B.car_type_id, B.price, B.version FROM car_sponsor A
			INNER JOIN car_list B
			ON A.car_list_id = B.id
		) C
		INNER JOIN car_images D
		ON C.car_list_id = D.car_list_id
		WHERE D.size = "small"
	) G
	INNER JOIN 
	(	SELECT F.id, E.ner as firm, F.ner as mark FROM car_type E
		INNER JOIN car_type F
		ON E.id= F.parent_id
	) H 
	ON G.car_type_id = H.id
	ORDER BY G.begin_time DESC';
	$res = mysql_query($sql) or die(mysql_error());
	$all = mysql_num_rows($res);
	$i = 0;
	$data = '';
	
	if($_SERVER['SERVER_ADDR']=='127.0.0.1'){ $path='http://'.getRoot("auto").'/'; }
	else { $path=''; }
	
	while($row = mysql_fetch_array($res)){
		if($row['mark']!='Бусад') $ner = $row['mark'];
		else $ner = $row['firm'].' '.$row['version'];
		if(strlen($ner)>21){
			$ner = substr($ner,0,18).'...';
		}
		$data .= '["'.$path.$row['url_path'].'", "'.$row['car_list_id'].'.car", "'.$ner.'", "'.rp($row['price']).'"]';
		if( ($i++) == ($all-1) ) $data .= '';
		else $data .= ',';
	}

	if($all<4){
		$sql = '
			SELECT E.*, F.url_path FROM 
			(	SELECT A.*, B.firm, B.mark FROM 
				(	SELECT id, car_type_id, added_date, price, version, made_date
					FROM car_list
				) A
				INNER JOIN 
				(	SELECT D.id, C.ner AS firm, D.ner AS mark
					FROM car_type C
					INNER JOIN car_type D ON C.id = D.parent_id
				) B
				ON A.car_type_id = B.id
			) E	
			LEFT JOIN car_images F 
			ON E.id = F.car_list_id
			WHERE F.size = "small" AND LEFT(F.url_path,4)="data" AND E.id NOT IN (SELECT id FROM car_sponsor)
			ORDER BY rand()
			LIMIT 0,'.(4-$all);


		$res = mysql_query($sql) or die(mysql_error());
		while($row = mysql_fetch_array($res)){
			$price = rp($row['prices']);
			switch($row['turul']){
				case 1:{ $zar_turul = 'zar_zarna'; } break;
				case 2:{ $zar_turul = 'zar_avna'; } break;
				case 3:{ $zar_turul = 'zar_tureesluulne'; } break;
				case 4:{ $zar_turul = 'zar_tureeslene'; } break;
			}
			
			if($row['mark']!='Бусад') $ner = $row['mark'];
			else $ner = $row['firm'].' '.$row['version'];
			if(strlen($ner)>21){
				$ner = substr($ner,0,18).'...';
			}
			
			$data .= '["'.$path.$row['url_path'].'", "'.$row['id'].'.car", "'.$ner.'", "'.rp($row['price']).'"]';
			if( ($i++) == 3 ) $data .= '';
			else $data .= ',';
		}
	}

	if($all>=16) $size = 8;
	else $size = 4;
	$txt = '
	<script type="text/javascript">
		var slider1 = new cz_slider();
		slider1.data = [
			'.$data.'
		];
		slider1.id = "slider1";
		slider1.size = '.$size.';
		slider1.title = "онцлох машин";
	</script>
	<div class="slider" id="slider1"></div>
    <script type="text/javascript">slider1.init()</script>';
	return $txt;
}

function latest($cnt){
	$txt = '';
	$cnt = isset($cnt)?$cnt:1;
	$sql = '
		SELECT E.*, F.url_path FROM 
		(	SELECT A.*, B.firm, B.mark FROM 
			(	SELECT id, car_type_id, added_date, price, version, made_date
				FROM car_list
			) A
			INNER JOIN 
			(	SELECT D.id, C.ner AS firm, D.ner AS mark
				FROM car_type C
				INNER JOIN car_type D ON C.id = D.parent_id
			) B
			ON A.car_type_id = B.id
		) E	
		LEFT JOIN car_images F 
		ON E.id = F.car_list_id
		WHERE F.size = "small" AND LEFT(F.url_path,4)="data"
		ORDER BY E.added_date DESC
		LIMIT 0,'.$cnt;
	
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res)){
		if($row['mark']!='Бусад') $ner = $row['mark'];
		else $ner = $row['firm'].' '.$row['version'];
		if(strlen($ner)>21){
			$ner = substr($ner,0,18).'...';
		}
		
		if($_SERVER['SERVER_ADDR']=='127.0.0.1'){ $path='http://'.getRoot("auto").'/'; }
		else { $path=''; }
		
		$txt .= '
		<div class="carbox" onclick="window.open(\''.$row['id'].'.car\',\'_blank\')">
			<img src="'.$path.$row['url_path'].'" class="m" alt="'.$row['made_date'].' '.$ner.' - '.getRoot("barilga").'" onerror="noimage(this)" />
			<span class="firm">'.$ner.'</span>
			<span class="price">'.rp($row['price']).'</span>
			<div class="carbox_hover"></div>
		</div>';
	}
	
	$txt='
	<div class="latest">
		<div class="top">
			<div class="title">Шинээр нэмэгдсэн машинууд</div>
			<div class="other"><a href="list1ii">бусад</a></div>
		</div>
		'.$txt.'
		<div class="clear"></div>
	</div>';
	
	//----------------------- Зураггүй 
	
	$sql_noimage = '
		SELECT E.*, F.url_path FROM 
		(	SELECT A.*, B.firm, B.mark FROM 
			(	SELECT id, car_type_id, added_date, price, version, engine, made_date
				FROM car_list
			) A
			INNER JOIN 
			(	SELECT D.id, C.ner AS firm, D.ner AS mark
				FROM car_type C
				INNER JOIN car_type D ON C.id = D.parent_id
			) B
			ON A.car_type_id = B.id
		) E	
		LEFT JOIN car_images F 
		ON E.id = F.car_list_id
		WHERE F.size = "small" AND LEFT(F.url_path,4)="reso"
		ORDER BY E.added_date DESC
		LIMIT 0,10';
	
	$res = mysql_query($sql_noimage);
	$line = '';
	$cnt = 0;
	while($row = mysql_fetch_array($res)){
		if($row['mark']!='Бусад') $ner = $row['mark'];
		else $ner = $row['firm'].' '.$row['version'];
		$line .= '
			<tr class="'.(($cnt++)%2==1?'odd':'even').'" onclick="window.open(\''.$row['id'].'.car\',\'_blank\')">
				<td style="padding-left:10px"><span>'.$ner.'</span></td>
				<td>'.$row['made_date'].' он</td>
				<td>'.$row['engine'].' cc</td>
				<td>'.rp($row['price']).'</td>
			</tr>
		';
	}
	
	$txt .= '
	<table class="carlist">
		<tr class="header">
			<td style="padding-left:10px">Фирм марк</td>
			<td>Он</td>
			<td>Хөдөлгүүр</td>
			<td>Үнэ</td>
		</tr>
		'.$line.'
	</table>';
	return $txt;
}

function bannerA(){
	$sql = 'SELECT * FROM car_widget WHERE position="A" AND isActive="1" LIMIT 0,1';
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	return $row['content'];
}

function bannerB1(){
	$sql = 'SELECT * FROM car_widget WHERE position="B1" AND isActive="1" LIMIT 0,1';
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	return $row['content'];
}
function bannerB2(){
	$sql = 'SELECT * FROM car_widget WHERE position="B2" AND isActive="1" LIMIT 0,1';
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	return $row['content'];
}

/* ======================================================================================== */
function sektodate($now, $past){
	$yg_hezee = $now - $past;
	if($yg_hezee>(86400*30)){
		$yg_hezee = date('Y-m-d', $past);
	} elseif($yg_hezee>604800){
		$yg_hezee = (int)($yg_hezee/604800).' долоо хоногийн өмнө';
	} elseif($yg_hezee>86400){
		$yg_hezee = (int)($yg_hezee/86400).' хоногийн өмнө';
	} elseif($yg_hezee>3600){
		$yg_hezee = (int)($yg_hezee/3600).' цагийн өмнө';
	} elseif($yg_hezee>60){
		$yg_hezee = (int)($yg_hezee/60).' минутын өмнө';
	} else {
		$yg_hezee = $yg_hezee.' секундын өмнө';
	}
	return $yg_hezee;
}

function detail($id){
	if(mysql_num_rows(mysql_query('SELECT id FROM car_list WHERE id="'.$id.'"'))==0){
		return '
		<div class="box">
			<div class="error">
				<p>Бүртгэлгүй эсвэл устсан зар байна.</p>
			</div>
		</div>';
	}
	//----- info
	
	$sql = '
	SELECT Q.id, Q.made_date, Q.version, Q.import_date, Q.price, Q.km, Q.about, Q.drive_pos, Q.engine, Q.cylinder, Q.phone, Q.email, Q.website, 
	Q.viewed, Q.firm, Q.mark, Q.facade, Q.category, Q.speedbox, Q.benzin, Q.most, Q.color_out, R.value as color_in, Q.added_date, Q.added_date_str
	FROM (
		SELECT O.*, P.value as color_out FROM (
			SELECT M.*, N.value as most FROM (
				SELECT K.*, L.value as benzin FROM (
					SELECT I.*, J.value as speedbox FROM (
						SELECT G.*, H.value as category FROM (
							SELECT E.*, F.value as facade FROM 
							(	SELECT A.*,B.firm, B.mark FROM car_list A
								INNER JOIN (
									SELECT D.id, C.ner AS firm, D.ner AS mark
									FROM car_type C
									INNER JOIN car_type D ON C.id = D.parent_id
								) B ON A.car_type_id = B.id
							) E	
							LEFT JOIN car_facade F ON E.facade_id = F.id
						) G	LEFT JOIN car_category H ON G.category_id=H.id
					) I	LEFT JOIN car_speedbox J ON I.speedbox_id=J.id
				) K LEFT JOIN car_benzin L ON K.benzin_id=L.id
			) M LEFT JOIN car_most N ON M.most_id = N.id
		) O LEFT JOIN car_colors P ON O.out_color_id=P.id
	) Q LEFT JOIN car_colors R ON Q.in_color_id=R.id
	WHERE Q.id="'.$id.'" AND Q.isActive=1';
	$res = mysql_query($sql);
	$row = mysql_fetch_object($res);
	
	//----- add view counter
	$sql = 'UPDATE car_list SET viewed=viewed+1 WHERE id="'.$id.'"';
	mysql_query($sql) or die(mysql_error());
	
	//----- title & description
	if($row->mark!='Бусад')	$tmp_ner = $row->mark.' '.$row->version;
	else $tmp_ner = $row->firm.' '.$row->version;
	$GLOBALS['title_auto'] = $row->made_date.' '.$tmp_ner.' - '.getRoot("auto");
	$GLOBALS['description_auto'] .= $row->about;
	
	//----- images
	$pic_b = '';
	$pic_s = '';
	$sql = 'SELECT * FROM car_images WHERE car_list_id="'.$id.'" AND size="big" ORDER BY id';
	$res = mysql_query($sql);
	$t=0;
	while($roww=mysql_fetch_array($res)){
		if($t==0) $pic_b = '<img src="'.$roww['url_path'].'" data-zoomsrc="'.$roww['url_path'].'" alt="'.$GLOBALS['title_auto'].'" class="b" onerror="noimage(this)" />';
		$pic_s .= '
		<li onclick="cz_gallery(\''.$roww['url_path'].'\')">
			<img src="'.$roww['url_path'].'" alt="'.$GLOBALS['title_auto'].'" class="s" onerror="noimage(this)" />
			<div class="look"></div>
		</li>';
		$t++;
	}
	if($t<9){
		for($i=$t; $i<9; $i++)
			$pic_s .= '<li><img src="resources/images/noimage.png" alt="" class="s" /></li>';
	}
	
	//----- print
	$txt='
	<link type="text/css" href="resources/plugin/mojozoom/mojozoom.css" rel="stylesheet" />
    <script type="text/javascript" src="resources/plugin/mojozoom/mojozoom.js"></script>
	<div class="gallery">
		<div class="top">
			<div class="title">'.($row->firm).(($row->mark)=='Бусад'?'&nbsp;':' ('.($row->mark).') ').$row->version.'</div>
			<div class="date">'.sektodate(time(),($row->added_date)).'</div>
		</div>
		<div class="pic_cont">
			<div class="pic_b">
				<div id="viewer">'.$pic_b.'</div>
				<span class="price">Үнэ: '.rp($row->price).'</span>
			</div>
			<div class="pic_s">
				<ul>'.$pic_s.'</ul>
			</div>
			<div class="clear"></div>
			
			<div class="date_icon">
				<div class="year">'.date('Y',$row->added_date).'</div>
				<div class="month">'.date('m.',$row->added_date).date('d',$row->added_date).'</div>
			</div>
		</div>
	</div>
	
	<div class="info">
		<table>
			<tr class="even"><td>Зарын дугаар: <b>'.substr($row->id,-6).'</b></td></tr>
			<tr class="odd"><td>Үйлдвэрлэсэн он: <b>'.$row->made_date.'</b></td></tr>
			<tr class="even"><td>Фирм: <b>'.$row->firm.'</b></td></tr>
			<tr class="odd"><td>Марк: <b>'.$row->mark.'</b></td></tr>
			<tr class="even"><td>Хувилбар: <b>'.$row->version.'</b></td></tr>
			<tr class="odd"><td>Орж ирсэн он: <b>'.$row->import_date.'</b></td></tr>
			<tr class="even"><td>Их бие: <b>'.$row->category.'</b></td></tr>
			<tr class="odd"><td>Харагдах байдал: <b>'.$row->facade.'</b></td></tr>
			<tr class="even"><td>Өнгө: <b>'.$row->color_out.'</b></td></tr>
			<tr class="odd"><td>Салоны өнгө: <b>'.$row->color_in.'</b></td></tr>
			<tr class="even"><td>Таны автомашиныг <b>'.$row->viewed.'</b> удаа үзсэн байна.</td></tr>
		</table>
		<table>
			<tr class="even"><td>Үнэ: <b style="color:red">'.rp($row->price).'</b></td></tr>
			<tr class="odd"><td>Гүйлт: <b>'.($row->km!=''?$row->km.' км':'').'</b></td></tr>
			<tr class="even"><td>Жолооны хүрд: <b>'.(($row->drive_pos)==1?'Зөв талдаа':'Буруу талдаа').'</b></td></tr>
			<tr class="odd"><td>Хөдөлгүүр: <b>'.$row->engine.' cc</b></td></tr>
			<tr class="even"><td>Цлиндр: <b>'.$row->cylinder.'</b></td></tr>
			<tr class="odd"><td>Хурдны хайрцаг: <b>'.$row->speedbox.'</b></td></tr>
			<tr class="even"><td>Шатахуун: <b>'.$row->benzin.'</b></td></tr>
			<tr class="odd"><td>Хөтлөгч: <b>'.$row->most.'</b></td></tr>
			<tr class="even"><td>Утас: <b>'.$row->phone.'</b></td></tr>
			<tr class="odd"><td>И-Мэйл: <b><a href="mailto:'.$row->email.'">'.$row->email.'</a></b></td></tr>
			<tr class="even"><td>Вэбсайт: <b><a href="'.$row->website.'" target="_blank">'.$row->website.'</a></b></td></tr>
		</table>
		<table class="nemelt">
			<tr class="even"><td><b>Нэмэлт мэдээлэл:</b> '.$row->about.'</td></tr>
		</table>
	</div>
	
	<div class="ontsloh">
		<span>Энэ машиныг олон мянган хүнд зэрэг хүргэхийг хүсвэл</span>
		<input type="button" class="ontsloh" onclick="location.href=\'page2.html\'" />
		<span>болгоорой</span>
	</div>
	
	<div class="d_bottom">
		<input type="button" class="ustgah" onclick="_popup.html=document.getElementById(\'frm_deletepre\').innerHTML;_popup.open()" />
		<input type="button" class="shinechleh" onclick="_popup.html=document.getElementById(\'frm_regen\').innerHTML;_popup.open()" />
		<input type="button" class="zasah" onclick="_popup.html=document.getElementById(\'frm_editpre\').innerHTML;_popup.open()" />
		<div class="socials" >
			<div style="display:inline-block;float:left;">
				<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://'.getRoot("auto").'/'.$row->id.'.car" data-via="cityzarmn" data-lang="en">Tweet</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			</div>
			<div style="display:inline-block;float:left;">
				<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
				<g:plus action="share" annotation="bubble" href="http://'.getRoot("auto").'/'.$row->id.'.car"></g:plus>
			</div>
			<div style="display:inline-block;float:left; margin-left:29px;">
				<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com/cityzarmn&amp;send=false&amp;layout=button_count&amp;width=73&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:73px; height:21px;" allowTransparency="true"></iframe>
			</div>
		</div>
    	<div class="clear"></div>
	</div>';
	$txt .= '
		<div class="hiddenbox" id="frm_editpre">'.editpre($id).'</div>
		<div class="hiddenbox" id="frm_deletepre">'.deletepre($id).'</div>
		<div class="hiddenbox" id="frm_regen">'.regen($id).'</div>';
	return $txt;
}

function regen($id){
	$txt = '
	<form action="home.html" method="post">
		<label for="upass">Та зарыг шинэчлэхийн тулд<br/> нууц үгээ оруулна уу.</label><br/><br/>
		<input type="password" name="upass" />
		<input type="submit" class="shinechleh" />
		<input type="hidden" name="action" value="regen" />
		<input type="hidden" name="id" value="'.$id.'" />
	</form>';
	return $txt;
}

function editpre($id){
	$txt = '
	<form action="home.html" method="post">
		<label for="upass">Та зарын нууц үгээ оруулна уу. </label><br/><br/>
		<input type="password" name="upass" />
		<input type="submit" class="ilgeeh" />
		<input type="hidden" name="action" value="editcar_pre" />
		<input type="hidden" name="id" value="'.$id.'" />
	</form>';
	return $txt;
}

function deletepre($id){
	$txt = '
	<form action="home.html" method="post">
		<label for="upass">Та зарын нууц үгээ оруулна уу.<br /> Нууц үг зөв тохиолдолд зар устах болно. </label><br/><br/>
		<input type="password" name="upass" />
		<input type="submit" class="ilgeeh" />
		<input type="hidden" name="action" value="deletecar_pre" />
		<input type="hidden" name="id" value="'.$id.'" />
	</form>';
	return $txt;
}

function delete_car($id){
	$sql = 'DELETE FROM car_list WHERE id="'.$id.'"';
	if(mysql_query($sql)){
		$sql = 'SELECT * FROM car_images WHERE car_list_id="'.$id.'"';
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res)){
			try {
				if(substr($row['url_path'],0,3)!='res')	unlink('../auto/'.$row['url_path']);
			} 
			catch(Exception $e){
				echo 'file cannot delete or not found';
			}
		}
		mysql_query('DELETE FROM car_images WHERE car_list_id="'.$id.'"') or die(mysql_error());
		mysql_query('DELETE FROM car_sponsor WHERE car_list_id="'.$id.'"') or die(mysql_error());
		return true;
	} else return false;
}

function result($size, $pageInd, $where){
	$pageInd = isset($pageInd)?$pageInd:1;
	$pageInd = ($pageInd!='')?$pageInd:1;
	$sql = '
	SELECT I.*, J.begin_time FROM 
	(
		SELECT G.*, H.url_path FROM 
		(	SELECT E.id, E.firm, E.mark, E.firm_id, E.mark_id, E.viewed, E.added_date, E.added_date_str, E.made_date, E.version, E.engine, E.isActive, F.value as speedbox, E.price, E.benzin_id, E.speedbox_id, E.drive_pos FROM 
			(	SELECT C.id, D.firm, D.firm_id, D.mark, D.id as mark_id, C.viewed, C.added_date, C.added_date_str, C.made_date, C.speedbox_id, C.engine, C.benzin_id, C.drive_pos, C.price, C.isActive, C.version
				FROM car_list C
				INNER JOIN 
				(	SELECT A.id as firm_id, A.ner as firm, B.id, B.ner as mark FROM car_type A
					INNER JOIN car_type B
					ON A.id = B.parent_id
				) D
				ON C.car_type_id = D.id
			) E 
			LEFT JOIN car_speedbox F
			ON E.speedbox_id = F.id
		) G 
		INNER JOIN car_images H	ON G.id = H.car_list_id
		WHERE H.size = "small" AND G.isActive=1
	) I 
	LEFT JOIN car_sponsor J ON I.id = J.car_list_id
	WHERE '.decode_search($where).' 1=1
	ORDER BY J.begin_time DESC, I.added_date DESC
	LIMIT '.($pageInd-1)*$size.','.$size;
	
	$res = mysql_query($sql);
	$line = '';
	$cnt = 0;
	while($row = mysql_fetch_array($res)){
		if($row['mark']!='Бусад') $ner = $row['mark'];
		else $ner = $row['firm'].' '.$row['version'];
		$line .= '
			<tr class="'.(($cnt++)%2==1?'odd':'even').' '.(isset($row['begin_time'])?'current':'').'" onclick="window.open(\''.$row['id'].'.car\',\'_blank\')">
				<td><div class="cont">
						<img src="'.$row['url_path'].'" alt="" class="s" onerror="noimage(this)" />
						'.(isset($row['begin_time'])?'<div class="star"></div>':'').'
					</div>
				</td>
				<td><span>'.$ner.'</span><br />
					<span>'.date('Y-m-d',$row['added_date']).'</span><br />
					<span>'.$row['viewed'].' үзсэн</span></td>
				<td>'.$row['made_date'].' он</td>
				<td>'.$row['speedbox'].'</td>
				<td>'.$row['engine'].' cc</td>
				<td>'.rp($row['price']).'</td>
			</tr>
		';
	}
	$sql = '
	SELECT id FROM car_list A
	INNER JOIN 
	(	SELECT A.id as firm_id, A.ner as firm, B.id as mark_id, B.ner as mark FROM car_type A
		INNER JOIN car_type B
		ON A.id = B.parent_id
	) B
	ON A.car_type_id = B.mark_id WHERE '.decode_search($where).' 1=1';
	$sum = mysql_num_rows(mysql_query($sql));
	$all = $sum;
	$sum = ceil($sum/$size);
	$pg = '';
	if($sum>9){
		if( ($pageInd-4)<1 ){
			for($i=1; $i<=9; $i++){
				$pg .= '<li class="num'.($i==$pageInd?' current':'').'"><a href="list'.$i.'i'.($where).'">'.$i.'</a></li>';
			}
		} elseif( ($pageInd+4)>$sum ){
			for($i=($sum-8); $i<=$sum; $i++){
				$pg .= '<li class="num'.($i==$pageInd?' current':'').'"><a href="list'.$i.'i'.($where).'">'.$i.'</a></li>';
			}
		} else {
			for($i=($pageInd-4); $i<=($pageInd+4); $i++){
				$pg .= '<li class="num'.($i==$pageInd?' current':'').'"><a href="list'.$i.'i'.($where).'">'.$i.'</a></li>';
			}
		}
	} else {
		for($i=1; $i<=$sum; $i++){
			$pg .= '<li class="num'.($i==$pageInd?' current':'').'"><a href="list'.$i.'i'.($where).'">'.$i.'</a></li>';
		}
	}
	
	$txt='
	<table class="carlist" border="1px">
		<tr class="header">
			<td style="width:100px">&nbsp;</td>
			<td style="width:200px">Марк</td>
			<td>Он</td>
			<td>Кроп</td>
			<td>Хөдөлгүүр</td>
			<td>Үнэ(₮)</td>
		</tr>
		'.$line.'
	</table>
	<div class="pager">
		<ul><!--<li class="num"><a href="javascript:void(0)">Нийт: '.$all.'</a></li>-->
			<li class="arrow_first" onclick="location.href=\'list1i'.($where).'\'"></li>
			<li class="arrow_left" '.($pageInd>1?'onclick="location.href=\'list'.($pageInd-1).'i'.($where).'\'"':'').'></li>
			'.$pg.'
			<li class="arrow_right" '.($pageInd<$sum?'onclick="location.href=\'list'.($pageInd+1).'i'.($where).'\'"':'').'></li>
			<li class="arrow_last" onclick="location.href=\'list'.$sum.'i'.($where).'\'"></li>
		</ul>
	</div>';
	return $txt;
}

function decode_search($str){
	$str = explode('i',$str);
	$where = '';
	for($i=0;$i<count($str)-1;$i++){
		if($str[$i]!=''){
			switch($i){
				case 0: $where .= 'firm_id='.$str[$i].' AND '; break;
				case 1: $where .= 'mark_id='.$str[$i].' AND '; break;
				case 2: $where .= 'made_date>='.$str[$i].' AND '; break;
				case 3: $where .= 'price<='.$str[$i].' AND '; break;
				
				case 4: $where .= 'engine<='.$str[$i].' AND '; break;
				case 5: $where .= 'benzin_id='.$str[$i].' AND '; break;
				case 6: $where .= 'speedbox_id='.$str[$i].' AND '; break;
				case 7: $where .= 'drive_pos="'.$str[$i].'" AND '; break;
			}
		}
	}
	return $where;
}

function searchbox(){
	return $txt='
	<div class="rbox">
		<div class="brow_search"><span>хайлт</span></div>
		<div class="inside" id="searchbox">'.search_mini().'</div>
	</div>';
}

function add($id,$pass){
	//---- EDIT CAR
	$mark = '<select name="mark"><option class="title" value="">Марк*</option>';
	if(isset($id)){
		$sql = '
		SELECT * FROM car_list C
		INNER JOIN 
		(	SELECT A.id as firm, B.id as mark FROM car_type A
			INNER JOIN car_type B
			ON A.id = B.parent_id
		) D ON C.car_type_id = D.mark WHERE C.id="'.$id.'"';
		$res = mysql_query($sql);
		$car = mysql_fetch_object($res);
		//--- SET MARK
		$sql = "SELECT * FROM car_type WHERE parent_id=".$car->firm;
		$res = mysql_query($sql);
		while( $row = mysql_fetch_array($res) ){
			if($row['id']==$car->mark) $isSelect='selected';
			else $isSelect='';
			$mark .= '<option value="'.$row['id'].'" '.$isSelect.'>'.$row['ner'].'</option>';
		}
	}
	$mark .= '</select>';
	//----------------------------------------------------------------------
	$firm = '<select name="firm" onchange="getmark(this)"><option class="title" value="">Фирм*</option>';
	$sql = 'SELECT * FROM car_type WHERE parent_id=0 AND isActive=1 ORDER BY ner';
	$res = mysql_query($sql);
	while( $row = mysql_fetch_array($res) ){
		if($row['id']==$car->firm) $isSelect='selected';
		else $isSelect='';
		$firm .= '<option value="'.$row['id'].'" '.$isSelect.'>'.$row['ner'].'</option>';
	}
	$firm .= '</select>';
	//----------------------------------------------------------------------
	$date_made = '<select name="date_made"><option value="" class="title">Үйлд/он*</option>';
	$ognoo = '';
	$y = (int)date('Y');
	for( $i=$y; $i>=($y-20); $i-- ){
		if($i==$car->made_date) $isSelect='selected';
		else $isSelect='';
		$ognoo .= '<option value="'.$i.'" '.$isSelect.'>'.$i.' он</option>';
	}
	$ognoo .= '</select>';
	$date_made .= $ognoo;
	//----------------------------------------------------------------------
	$date_import = '<select name="date_import"><option value="" class="title">Орж ирсэн он</option>';
	$ognoo = '';
	$y = (int)date('Y');
	for( $i=$y; $i>=($y-20); $i-- ){
		if($i==$car->import_date) $isSelect='selected';
		else $isSelect='';
		$ognoo .= '<option value="'.$i.'" '.$isSelect.'>'.$i.' он</option>';
	}
	$ognoo .= '</select>';
	$date_import .= $ognoo;
	//----------------------------------------------------------------------
	$category = '<select name="category"><option class="title" value="">Их бие*</option>';
	$sql='SELECT * FROM car_category';
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res)){
		if($row['id']==$car->category_id) $isSelect='selected';
		else $isSelect='';
		$category .= '<option value="'.$row['id'].'" '.$isSelect.'>'.$row['value'].'</option>';
	}
	$category.='</select>';
	//----------------------------------------------------------------------
	$facade = '<select name="facade"><option value="" class="title">Үзэмж</option>';
	$sql = 'SELECT * FROM car_facade';
	$res = mysql_query($sql);
	while($row = mysql_fetch_array($res)){
		if($row['id']==$car->facade_id) $isSelect='selected';
		else $isSelect='';
		$facade .= '<option value="'.$row['id'].'" '.$isSelect.'>'.$row['value'].'</option>';
	}
	$facade .= '</select>';
	//----------------------------------------------------------------------
	$color_out = '<select name="color_out"><option class="title" value="">Машины өнгө</option>';
	$color = '';
	$sql = 'SELECT * FROM car_colors';
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res)){
		if($row['id']==$car->out_color_id) $isSelect='selected';
		else $isSelect='';
		$color .= '<option value="'.$row['id'].'" '.$isSelect.'>'.$row['value'].'</option>';
	}
	$color_out .= $color.'</select>';
	//----------------------------------------------------------------------
	$color_in = '<select name="color_in"><option class="title" value="">Салоны өнгө</option>';
	$color = '';
	$sql = 'SELECT * FROM car_colors';
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res)){
		if($row['id']==$car->in_color_id) $isSelect='selected';
		else $isSelect='';
		$color .= '<option value="'.$row['id'].'" '.$isSelect.'>'.$row['value'].'</option>';
	}
	$color_in .= $color.'</select>';
	//----------------------------------------------------------------------
	$most = '<select name="most"><option class="title" value="">Хөтлөгч</option>';
	$sql = 'SELECT * FROM car_most';
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res)){
		if($row['id']==$car->most_id) $isSelect='selected';
		else $isSelect='';
		$most .= '<option value="'.$row['id'].'" '.$isSelect.'>'.$row['value'].'</option>';
	}
	$most .= '</select>';
	//----------------------------------------------------------------------
	$speedbox = '<select name="speedbox"><option class="title" value="">Хурдны хайрцаг</option>';
	$sql = 'SELECT * FROM car_speedbox';
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res)){
		if($row['id']==$car->speedbox_id) $isSelect='selected';
		else $isSelect='';
		$speedbox .= '<option value="'.$row['id'].'" '.$isSelect.'>'.$row['value'].'</option>';
	}
	$speedbox .= '</select>';
	//----------------------------------------------------------------------
	$benzin = '<select name="benzin"><option class="title" value="">Шатахуун</option>';
	$sql = 'SELECT * FROM car_benzin';
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res)){
		if($row['id']==$car->benzin_id) $isSelect='selected';
		else $isSelect='';
		$benzin .= '<option value="'.$row['id'].'" '.$isSelect.'>'.$row['value'].'</option>';
	}
	$benzin .= '</select>';
	//----------------------------------------------------------------------
	$resample='
		<script type="text/javascript" src="resources/plugin/resample/resample.js"></script>
		<script type="text/javascript" src="resources/plugin/resample/resample_do.js"></script>';
	if(isset($id)){
		$resample = '<input type="hidden" name="id" value="'.$id.'" />';
		$sql = 'SELECT * FROM car_images WHERE car_list_id="'.$id.'" AND size="big"';
		$res = mysql_query($sql);
		$img = 1;
		while($row = mysql_fetch_array($res)){
			$img_array[$img++] = $row['url_path'];
		}
	}
	$big_img_data = '';
	$big_img = '';
	for($i=1; $i<10; $i++){
		$big_img_data .= '<input type="hidden" id="big_img_data_'.$i.'" name="big_img_data_'.$i.'" value="'.((isset($id) && $i==1)?'resources/images/blank_60x60.jpg':'').'" />';
		if(isset($id)){
			$big_img .= '
				<div class="upload_pic_box '.($i==9?'lastbox':'').'">
					<img id="big_img_'.$i.'" src="'.(isset($img_array[$i])?$img_array[$i]:'resources/images/blank_60x60.jpg').'" alt="" class="upload" onerror="noimage60x60(this)" />
				</div>';
		} else {
			$big_img .= '
				<div class="upload_pic_box '.($i==9?'lastbox':'').'" onclick="del_pic('.$i.')">
					<img id="big_img_'.$i.'" src="'.(isset($img_array[$i])?$img_array[$i]:'resources/images/blank_60x60.jpg').'" alt="" class="upload" onerror="noimage60x60(this)" />
					<div class="del_pic"></div>
				</div>';
		}
	}
	//----------------------------------------------------------------------
	$txt='
	<form class="addcar" action="home.html" method="post">
	<div class="box">
		<div class="box_title">
			<div class="title">Ерөнхий мэдээлэл</div>
		</div>
		<div class="warning">
			<ul><li>*-оор тэмдэглэсэн талбаруудыг заавал бөглөнө үү.</li></ul>
		</div>
		<div class="insidebox">
			<div class="col1">
				<div class="selectbox">'.$firm.'</div>
			</div>
			<div class="col2">
				<div class="selectbox" id="mark">'.$mark.'</div>
			</div>
		</div>
		<div class="insidebox lastbox">
			<div class="col1">
				<span>Машины хувилбар</span>
			</div>
			<div class="col2">
				<input type="text" name="version" onkeyup="onlyStr_eng(this)" maxlength="25" value="'.$car->version.'" />
			</div>
		</div>
		<div class="insidebox">
			<div class="col1">
				<div class="selectbox">'.$date_made.'</div>
			</div>
			<div class="col2">
				<div class="selectbox">'.$date_import.'</div>
			</div>
		</div>
		<div class="insidebox lastbox">
			<div class="col1">
				<span>Гүйлт (км)</span>
				<div class="help"><div class="txt">Ж.нь: 10000 , 8000 , 6000 гэх мэт</div></div>
			</div>
			<div class="col2">
				<input type="text" name="km" onkeyup="onlyNum(this)" maxlength="10" value="'.$car->km.'" />
			</div>
		</div>
		<div class="insidebox">
			<div class="col1">
				<div class="selectbox">'.$category.'</div>
			</div>
			<div class="col2">
				<div class="selectbox">'.$facade.'</div>
			</div>
		</div>
		<div class="insidebox lastbox">
			<div class="col1">
				<div class="selectbox">'.$color_out.'</div>
			</div>
			<div class="col2">
				<div class="selectbox">'.$color_in.'</div>
			</div>
		</div>
		<div class="insidebox">
			<div class="col1">
				<span>Хөдөлгүүр* (сс)</span>
				<div class="help"><div class="txt">Ж.нь: 4200 , 2700 гэх мэт</div></div>
			</div>
			<div class="col2">
				<input type="text" name="engine" onkeyup="onlyNum(this)" maxlength="10" value="'.$car->engine.'" />
			</div>
		</div>
		<div class="insidebox lastbox">
			<div class="col1">
				<div class="selectbox">
					<select name="drive_pos">
						<option value="" class="title">Жолооны хүрд</option>
						<option value="1" '.($car->drive_pos==1?'selected':'').'>Зөв талдаа</option>
						<option value="0" '.($car->drive_pos==0?'selected':'').'>Буруу талдаа</option>
					</select>
				</div>
			</div>
			<div class="col2">
				<div class="selectbox">'.$most.'</div>
			</div>
		</div>
		<div class="insidebox">
			<div class="col1">
				<div class="selectbox">'.$speedbox.'</div>
			</div>
			<div class="col2">
				<div class="selectbox">'.$benzin.'</div>
			</div>
		</div>
		<div class="insidebox lastbox">
			<div class="col1">
				<span>Цлиндр</span>
				<div class="help"><div class="txt">Ж.нь: 12 , 8 гэх мэт</div></div>
			</div>
			<div class="col2">
				<input type="text" name="cylinder" onkeyup="onlyNum(this)" maxlength="4" value="'.$car->cylinder.'" />
			</div>
		</div>

		<div class="box_title">
			<div class="title">Холбоо барих мэдээлэл</div>
		</div>
		<div class="insidebox">
			<div class="col1">
				<span>Үнэ* (саяар)</span>
				<div class="help"><div class="txt">Ж.нь: 6.5 , 12.7 гэх мэт саяар тооцож оруулаарай</div></div>
			</div>
			<div class="col2">
				<input type="text" name="price" maxlength="5" onkeyup="_float(this)" value="'.$car->price.'" />
			</div>
		</div>
		<div class="insidebox lastbox">
			<div class="col1">
				<span>Утас *</span>
				<div class="help"><div class="txt">Хэрэв олон утас оруулах бол таслалаар зааглана уу. Ж.нь: 88123456, 99123456, 96123456 г.м</div></div>
			</div>
			<div class="col2">
				<input type="text" name="phone" maxlength="50" value="'.$car->phone.'" />
			</div>
		</div>

		<div class="insidebox">
			<div class="col1">
				<span>И-Мэйл</span>
			</div>
			<div class="col2">
				<input type="text" name="email" value="'.$car->email.'" />
			</div>
		</div>
		<div class="insidebox lastbox">
			<div class="col1">
				<span>Вэбсайт</span>
				<div class="help"><div class="txt">Ж.нь: http://www.'.$GLOBALS['proj_root'].'</div></div>
			</div>
			<div class="col2">
				<input type="text" name="website" value="'.$car->website.'" />
			</div>
		</div>
		
		<div class="warning">
			<ul><li style="color:red">Та нууц кодоо өөрөө зохиож энд оруулна уу. Дараа уг кодоо ашиглан зараа засаж, устгаж болно.</li>
				<li>Хамгийн багадаа 4 тэмдэгт оруулна уу.</li>
			</ul>
		</div>
		<div class="insidebox">
			<div class="col1">
				<span>Нууц үг*</span>
			</div>
			<div class="col2">
				<input type="password" name="password" maxlength="8" value="'.$pass.'" />
			</div>
		</div>
		<div class="insidebox lastbox">
			<div class="col1">
				<span>Нууц үг* (давтана)</span>
			</div>
			<div class="col2">
				<input type="password" name="password_re" maxlength="8" value="'.$pass.'" />
			</div>
		</div>
		<div class="areabox">
			<div class="col1">
				<span>Нэмэлт мэдээлэл</span>
			</div>
			<div class="col2">
				<textarea name="about" onkeyup="onlyStr(this)">'.$car->about.'</textarea>
			</div>
		</div>

		<div class="box_title">
			<div class="title">Фото зураг /ЗААВАЛ ОРУУЛАХ ШААРДЛАГАГҮЙ/</div>
		</div>
		
		<div class="warning">
			<ul '.(isset($id)?'class="hiddenbox"':'').'>
				<li style="color:red">Хэрвээ та зараа зураггүй оруулвал автомашиныг тань хэн ч сонирхохгүй гэдгийг бид судалгаагаар нотолж чадна. Нэг зураг түмэн үгнээс хүчтэй.</li>
				<li>Эхний зураг нь таны автомашиныг төлөөлөх тул сонгохдоо анхаарна уу.</li>
				<li>Таны зарах автомашины бодит зураг байвал сайн.</li>
				<li>Зурагнуудаа нэмэхдээ "СОНГОХ" товч дээр дарж оруулна.</li>
			</ul>
		</div>

		<!--  -->

		<div class="browsebox'.(isset($id)?' hiddenbox':'').'">
			<div class="filebox" onclick="browse(\'upload_1\')">
				<input type="text" id="upload_1_txt" />
			</div>
			
			<div id="message" class="message"></div>
			<input type="file" id="upload_1" />
			<div class="clear"></div>
		</div>

		<input id="big_width" type="hidden" value="600" />
		<input id="big_height" type="hidden" />
		'.$big_img_data.$big_img.'
		<div class="clear"></div>
		<input id="small_width" type="hidden" value="136" />
		<input id="small_height" type="hidden" value="96" />
		
		<input type="hidden" id="small_img_data_1" name="small_img_data_1" />
		<div style="display:none">
			<img id="small_img_1" src="resources/images/blank_60x60.jpg" alt="" class="upload" onerror="noimage60x60(this)" />
			<div class="del_pic"></div>
		</div>
	    '.$resample.'
	</div>

	

	<div class="box">
		<div class="insidebox" style="width:335px">
			<div class="col1" style="width:170px">
				<span>Баталгаажуулах код*</span>
			</div>
			<div class="col2">
				<input type="text" maxlength="10" name="captcha" />
			</div>
		</div>
		<div>
			<iframe id="CA_img" src="image.php" onload="checkCA(this)" width="100" height="50" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>
			<a href="javascript:void(0)" onclick="setCA()"><img src="resources/images/circle-arrow.png" style="background-color:#e7e7e7; padding: 4px;" alt="" title="Баталгаажуулах кодыг өөрчлөх" /></a>
		</div>
	</div>
	<div class="clear"></div>
	<div class="box">
		<div id="error"></div>
	</div>
	<div class="box">
		<input type="hidden" name="action" value="'.(isset($id)?'editcar_post':'addcar_post').'" />
		<input type="button" class="butsah left" onclick="window.history.back()" />
		<input type="button" class="ilgeeh left-10" onclick="_submitForm(this)" />
		<div class="clear"></div>
	</div>
	</form>';
	return $txt;
}

function delete_old_cars(){
	$sql = 'SELECT id,end_time FROM car_list WHERE id NOT IN ( SELECT car_list_id FROM car_sponsor ) AND UNIX_TIMESTAMP()>end_time';
	$res = mysql_query($sql) or die(mysql_error());
	while($row = mysql_fetch_array($res)){
		delete_car($row[0]);
	}
}

function car_analytics(){
	$ognoo = date('Y-m-d');
	$sql = 'SELECT dates FROM car_analytics WHERE dates = "'.$ognoo.'"';
	$res = mysql_query($sql) or die(mysql_error());
	if(mysql_num_rows($res)>0){
		$sql = 'UPDATE car_analytics SET value=value+1 WHERE dates="'.$ognoo.'"';
		mysql_query($sql) or die(mysql_error());
	} else {
		$sql = 'INSERT INTO car_analytics VALUES (NULL,"'.$ognoo.'",'.strtotime($ognoo).',1)';
		mysql_query($sql) or die(mysql_error());
	}
}

function analytics($t){
	$ognoo = date('Y-m-d');
	$time = strtotime($ognoo);
	
	switch($t){
		case 'online':{
			$res = mysql_query('SELECT count(id) as sum FROM connection');
			return mysql_fetch_object($res)->sum;
		} break;
		case 'today':{
			$res = mysql_query('SELECT value FROM car_analytics WHERE times='.$time) or die(mysql_error());
			return mysql_fetch_object($res)->value;
		} break;
		case 'yesterday':{
			$time = $time - 86400;
			$res = mysql_query('SELECT value FROM car_analytics WHERE times='.$time) or die(mysql_error());
			if(mysql_num_rows($res)>0)
				return mysql_fetch_object($res)->value;
			else return 0;
		} break;
		case 'week':{
			$time = $time - 86400*7;
			$res = mysql_query('SELECT sum(value) as value FROM car_analytics WHERE times>'.$time) or die(mysql_error());
			return mysql_fetch_object($res)->value;
		} break;
		case 'month':{
			$time = $time - 86400*30;
			$res = mysql_query('SELECT sum(value) as value FROM car_analytics WHERE times>'.$time) or die(mysql_error());
			return mysql_fetch_object($res)->value;
		} break;
		case 'year':{
			$time = $time - 86400*365;
			$res = mysql_query('SELECT sum(value) as value FROM car_analytics WHERE times>'.$time) or die(mysql_error());
			return mysql_fetch_object($res)->value;
		} break;
		case 'all':{
			$res = mysql_query('SELECT sum(value) as value FROM car_analytics') or die(mysql_error());
			return mysql_fetch_object($res)->value;
		} break;
	}
}

function statistic(){
	$sql = "SELECT count(id) cnt FROM car_list";
	$res = mysql_query($sql) or die(mysql_error());
	$cnt = mysql_fetch_object($res)->cnt;
	$txt = '
	<div class="rbox">
	<div class="brow"><span>Вэб статистик</span></div>
	<div class="inside">
		<table border="1" style="">
			<tr><td colspan="2">
					<ul class="statistic">
						<li>Яг одоо '.analytics('online').' зочин онлайн байна.</li>
					</ul></td>
			</tr>
			<tr><td width="150px">
					<ul class="statistic">
						<li>Өнөөдөр: '.analytics('today').'</li>
						<li>Өчигдөр: '.analytics('yesterday').'</li>
						<li>Энэ 7 хоногт: '.analytics('week').'</li>
					</ul></td>
				<td width="120px">
					<ul class="statistic">
						<li>Энэ сард: '.analytics('month').'</li>
						<li>Жилд: '.analytics('year').'</li>
						<li>Нийт: '.analytics('all').'</li>
					</ul>
				</td>
			</tr>
			<tr><td colspan="2">
					<ul class="statistic">
						<li>Зарын санд нийт '.$cnt.' автомашин байна.</li>
					</ul></td>
			</tr>
		</table>
	</div>
	</div>';
	return $txt;
}

function widget($a){
	switch($a){
		case '': 		$a = 1; break;
		case 'add': 
		case 'result': 
		case 'search': 	$a = 2; break;
		case 'page':	$a = 3; break;
	}
	$txt = '';
	$sql = 'SELECT * FROM car_widget WHERE position="C" AND isActive="1" ORDER BY ordering, begin_time';
	$res = mysql_query($sql) or die(mysql_error());
	while($row = mysql_fetch_array($res)){
		if($row['page']==$a || $row['page']==0)
		$txt .= html_entity_decode($row['content']);
	}
	return $txt;
}

function inactive_widgets(){
	$sql = 'SELECT id, types, end_time, isActive FROM car_widget WHERE types="0" AND isActive="1"';
	$res = mysql_query($sql) or die(mysql_errors());
	while($row = mysql_fetch_array($res)){
		if( time() > $row['end_time'] ){
			$usql = 'UPDATE car_widget SET isActive="0" WHERE id='.$row['id'];
			mysql_query($usql) or die(mysql_error());
		}
	}
}

function rp($price){
	if($price==0 || $price>250) { $price = '&nbsp;'; }
	else $price .= ' сая';
	return $price;
}
?>