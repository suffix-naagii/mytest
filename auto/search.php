<?php
function search(){
	//----------------------------------------------------------------------
	$firm = '<select name="firm" onchange="getmark_search(this)"><option class="title" value="">Фирм</option>';
	$sql = 'SELECT * FROM car_type WHERE parent_id=0 AND isActive=1 ORDER BY ner';
	$res = mysql_query($sql);
	while( $row = mysql_fetch_array($res) ){
		$firm .= '<option value="'.$row['id'].'">'.$row['ner'].'</option>';
	}
	$firm .= '</select>';
	//----------------------------------------------------------------------
	$date_made = '<select name="date_made"><option value="" class="title">Үйлд/он</option>';
	$ognoo = '';
	$y = (int)date('Y');
	for( $i=$y; $i>=($y-20); $i-- ){
		$ognoo .= '<option value="'.$i.'">'.$i.' оноос хойш</option>';
	}
	$ognoo .= '</select>';
	$date_made .= $ognoo;
	//----------------------------------------------------------------------
	$price = '<select name="price">
		<option value="" class="title">Үнэ</option>
		<option value="100">100 сая хүртэл</option>
		<option value="75">75 сая хүртэл</option>
		<option value="50">50 сая хүртэл</option>
		<option value="25">25 сая хүртэл</option>
		<option value="20">20 сая хүртэл</option>
		<option value="15">15 сая хүртэл</option>
		<option value="10">10 сая хүртэл</option>
		<option value="5">5 сая хүртэл</option>
		</select>';
	//----------------------------------------------------------------------
	$engine = '<select name="engine"><option value="" class="title">Хөдөлгүүр</option>';
	for($i=4000;$i>=1500;$i-=500){
		$engine .= '<option value="'.$i.'">'.$i.'cc доош</option>';
	}
	$engine .= '</select>';
	//----------------------------------------------------------------------
	$benzin = '<select name="benzin"><option value="" class="title">Шатахуун</option>';
	$res = mysql_query('SELECT * FROM car_benzin') or die(mysql_error());
	while($row = mysql_fetch_array($res)){
		$benzin .= '<option value="'.$row['id'].'">'.$row['value'].'</option>';
	}
	$benzin .= '</select>';
	//----------------------------------------------------------------------
	$speedbox = '<select name="speedbox"><option value="" class="title">Хурдны хайрцаг</option>';
	$res = mysql_query('SELECT * FROM car_speedbox') or die(mysql_error());
	while($row = mysql_fetch_array($res)){
		$speedbox .= '<option value="'.$row['id'].'">'.$row['value'].'</option>';
	}
	$speedbox .= '</select>';
	//----------------------------------------------------------------------
	
	//----------------------------------------------------------------------
	$txt='
	<form method="post" action="list.html">
		<div class="selectbox">'.$firm.'</div>
		<div class="selectbox" id="mark_search">
			<select name="mark">
				<option class="title" value="">Марк</option>
			</select>
		</div>
		<div class="selectbox">'.$date_made.'</div>
		<div class="selectbox">'.$price.'</div>
		<div class="selectbox">'.$engine.'</div>
		<div class="selectbox">'.$benzin.'</div>
		<div class="selectbox">'.$speedbox.'</div>
		<div class="selectbox">
			<select name="drive_pos">
				<option class="title" value="">Жолооны хүрд</option>
				<option value="1">Зөв талдаа</option>
				<option value="0">Буруу талдаа</option>
			</select>
		</div>
		<div class="bottom_sec">
			<a href="javascript:void(0)" class="btn" onclick="ajax(\'ajax.php?action=search_mini\',\'searchbox\')" >Ерөнхий хайлт</a>
			<input type="submit" class="haih" />
		</div>
		<div class="clear"></div>
	</form>';
	return $txt;
}

function search_mini(){
	//----------------------------------------------------------------------
	$firm = '<select name="firm" onchange="getmark_search(this)"><option class="title" value="">Фирм</option>';
	$sql = 'SELECT * FROM car_type WHERE parent_id=0 AND isActive=1 ORDER BY ner';
	$res = mysql_query($sql);
	while( $row = mysql_fetch_array($res) ){
		$firm .= '<option value="'.$row['id'].'">'.$row['ner'].'</option>';
	}
	$firm .= '</select>';
	//----------------------------------------------------------------------
	$date_made = '<select name="date_made"><option value="" class="title">Үйлд/он</option>';
	$ognoo = '';
	$y = (int)date('Y');
	for( $i=$y; $i>=($y-20); $i-- ){
		$ognoo .= '<option value="'.$i.'">'.$i.' оноос хойш</option>';
	}
	$ognoo .= '</select>';
	$date_made .= $ognoo;
	//----------------------------------------------------------------------
	$price = '<select name="price">
		<option value="" class="title">Үнэ</option>
		<option value="100">100 сая хүртэл</option>
		<option value="75">75 сая хүртэл</option>
		<option value="50">50 сая хүртэл</option>
		<option value="25">25 сая хүртэл</option>
		<option value="20">20 сая хүртэл</option>
		<option value="15">15 сая хүртэл</option>
		<option value="10">10 сая хүртэл</option>
		<option value="5">5 сая хүртэл</option>
		</select>';
	//----------------------------------------------------------------------
	$txt='
	<form method="post" action="list.html">
		<div class="selectbox">'.$firm.'</div>
		<div class="selectbox" id="mark_search">
			<select name="mark">
				<option class="title" value="">Марк</option>
			</select>
		</div>
		<div class="selectbox">'.$date_made.'</div>
		<div class="selectbox">'.$price.'</div>
		<div class="bottom_sec">
			<a href="javascript:void(0)" class="btn" onclick="ajax(\'ajax.php?action=search\',\'searchbox\')">Дэлгэрэнгүй хайлт</a>
			<input type="submit" class="haih" />
		</div>
		<div class="clear"></div>
	</form>';
	return $txt;
}
?>