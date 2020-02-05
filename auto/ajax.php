<?php
	error_reporting(0);
	include("../connect.php");
	include("../constant.php");
	include("search.php");
	
	if(isset($_GET['action'])) $action= $_GET['action'];
	else $action = "";
	
	switch($action){
		case 'getmark': {
			$parent_id = $_GET['parent_id'];
			$mark = '<select name="mark"><option class="title" value="">Марк*</option>';
			$sql = "SELECT * FROM car_type WHERE parent_id=".$parent_id.' ORDER BY ner';
			$res = mysql_query($sql);
			while($row = mysql_fetch_array($res)){
				$mark .= '<option value="'.$row['id'].'">'.$row['ner'].'</option>';
			}
			echo $mark .= '</select>';
		} break;
		case 'getmark_search':{
			$parent_id = $_GET['parent_id'];
			$mark = '<select name="mark"><option class="title" value="">Марк</option>';
			$sql = "SELECT * FROM car_type WHERE parent_id=".$parent_id.' ORDER BY ner';
			$res = mysql_query($sql);
			while($row = mysql_fetch_array($res)){
				$mark .= '<option value="'.$row['id'].'">'.$row['ner'].'</option>';
			}
			echo $mark .= '</select>';
		} break;
		case 'search_mini':{
			echo search_mini();
		} break;
		case 'search':{
			echo search();
		} break;
		default: {
			
		}
	}
?>