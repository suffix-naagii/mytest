<?php
	session_start();
	error_reporting(0);
	
	include("../connect.php");
	include("../constant.php");
	include("functions.php");
	
	connecting();
	//delete_old_cars();
	inactive_widgets();
	
	if(isset($_GET['action']))		$action= $_GET['action'];
	elseif(isset($_POST['action'])) $action= $_POST['action'];
	else $action= "";
	
	$file = fopen("theme.html","r");
	$html = fread($file,filesize("theme.html"));
	switch($action){
		case 'detail':{
			if(isset($_GET['id']))
			$html=ereg_replace("---main---",detail($_GET['id']),$html);
		} break;
		case 'result':{
			$html=ereg_replace("---main---",result(20,$_GET['page'],$_GET['search']),$html);
		} break;
		case 'add':{
			$html=ereg_replace("---main---",add(),$html);
		} break;
		case 'addcar_post':{
			$firm = $_POST['firm'];
			$mark = $_POST['mark'];
			$date_made = $_POST['date_made'];
			$category = $_POST['category'];
			$engine = $_POST['engine'];
			$price = $_POST['price'];
			$phone = $_POST['phone'];
			$password = md5($_POST['password']);

			$captcha = $_POST['captcha'];
			if( md5($captcha) != $_SESSION['security_number'] ){
				$txt = '
				<div class="box">
					<div class="error">
						<ul>
							<li>Баталгаажуулах код буруу байна.</li>
						</ul>
					</div>
				</div>';
				$html=ereg_replace("---main---",$txt.add(),$html);
				break;
			}
			
			$s_sql = 'SELECT id FROM car_list WHERE car_type_id="'.$mark.'" AND made_date="'.$date_made.'" AND category_id="'.$category.'" AND phone LIKE "%'.$phone.'%"';
			$s_res = mysql_query($s_sql) or die(mysql_error());
			$cnt = mysql_num_rows($s_res);
			//---------------------------------
			
			if($cnt>0){
				$txt = '
				<div class="box">
					<div class="error">
						<p>Та өмнө нь энэ автомашиныг нэмж байсан учир давхар оруулах боломжгүй. Харин та өмнө оруулсан зараа шинэчлэх боломжтой. Шинэчлэхдээ өөрийн зар руугаа орж шинэчлэх товч дээр дарна уу.</p>
					</div>
				</div>';
				$html=ereg_replace("---main---",$txt.latest(20),$html);
			} else {
				$txt = '';
				$carid=uniqid();
				define('UPLOAD_CAR', 'data/');
				$image_cnt = 0;
				for($i=1; $i<10; $i++){
					$unique = uniqid();
					if($_POST['big_img_data_'.$i]!=""){
						$img = $_POST['big_img_data_'.$i];
						$data = base64_decode($img);
						$file = UPLOAD_CAR . $unique . '_big.png';
						$success = file_put_contents($file, $data);
						$sql = 'INSERT INTO car_images VALUES ("", "'.$carid.'", "'.$file.'", "big")';
						if($success && mysql_query($sql)) $image_cnt++;
						
					}
					if($_POST['small_img_data_'.$i]!="" && $i==1){
						$img = $_POST['small_img_data_'.$i];
						$data = base64_decode($img);
						$file = UPLOAD_CAR . $unique . '_small.png';
						$success = file_put_contents($file, $data);
						$sql = 'INSERT INTO car_images VALUES ("", "'.$carid.'", "'.$file.'", "small")';
						if($success && mysql_query($sql)) $image_cnt++;
					}
				}
				if($image_cnt==0){
					$sql_big = 'INSERT INTO car_images VALUES ("", "'.$carid.'", "resources/images/noimage_big.png", "big")';
					$sql_small = 'INSERT INTO car_images VALUES ("", "'.$carid.'", "resources/images/noimage.png", "small")';
					mysql_query($sql_big) or die(mysql_error());
					mysql_query($sql_small) or die(mysql_error());
				}
				
				//---------------------------------
				$version = isset($_POST['version'])?$_POST['version']:'NULL';
				$date_import = isset($_POST['date_import'])?$_POST['date_import']:'NULL';
				$km = isset($_POST['km'])?$_POST['km']:'NULL';
				$facade = isset($_POST['facade'])?$_POST['facade']:'NULL';
				$color_out = isset($_POST['color_out'])?$_POST['color_out']:'NULL';
				$color_in = isset($_POST['color_in'])?$_POST['color_in']:'NULL';
				$drive_pos = isset($_POST['drive_pos'])?$_POST['drive_pos']:'NULL';
				$most = isset($_POST['most'])?$_POST['most']:'NULL';
				$speedbox = isset($_POST['speedbox'])?$_POST['speedbox']:'NULL';
				$benzin = isset($_POST['benzin'])?$_POST['benzin']:'NULL';
				$cylinder = isset($_POST['cylinder'])?$_POST['cylinder']:'NULL';
				$email = isset($_POST['email'])?$_POST['email']:'NULL';
				$website = isset($_POST['website'])?$_POST['website']:'NULL';
				$about = isset($_POST['about'])?$_POST['about']:'NULL';
				
				$sql = 'INSERT INTO car_list VALUES (
					"'.$carid.'", "'.$date_made.'", "'.$mark.'", 
					"'.$version.'", "'.$date_import.'", "'.$price.'", 
					"'.$km.'", "'.$facade.'", "'.$about.'", 
					"'.$category.'", "'.$speedbox.'", "'.$drive_pos.'", 
					"'.$benzin.'", "'.$engine.'", "'.$cylinder.'", 
					"'.$most.'", "'.$color_out.'", "'.$color_in.'", 
					"'.$password.'", "'.$phone.'", "'.$email.'", 
					"'.$website.'", UNIX_TIMESTAMP(), CURRENT_TIMESTAMP(), 
					1, 0, "'.$_SERVER['REMOTE_ADDR'].'", (UNIX_TIMESTAMP()+7776000))';
				
				if(mysql_query($sql)){
					$txt = '
					<div class="box">
						<div class="info_">
							<p>Амжилттай илгээгдлээ. Таны зар 90 хоногийн турш сайтад байршина. </p>
							<p> <span>Та зараа олон мянган хүнд зэрэг хүргэхийг хүсвэл</span>
								<input type="button" class="ontsloh" onclick="location.href=\'page2.html\'" />
								<span>болгоорой</span>
							</p>
						</div>
					</div>';
				} else {
					$txt = '
					<div class="box">
						<div class="error">
							<p>Таны автомашины зар оруулах үед ямар нэг саатал гарлаа.</p>
						</div>
					</div>';
				}
				$html=ereg_replace("---main---",$txt.latest(20),$html);
			}
		} break;
		case 'regen':{
			if(isset($_POST['id'])){
				$id = $_POST['id'];
				$sql = 'SELECT pass, added_date, added_date_str FROM car_list WHERE id="'.$id.'"';
				$res = mysql_query($sql);
				$obj = mysql_fetch_object($res);
				
				if($obj->pass == md5($_POST['upass'])){
					if((time() - $obj->added_date)>259200){
						$sql = 'UPDATE car_list SET added_date=UNIX_TIMESTAMP(), added_date_str=CURRENT_TIMESTAMP() WHERE id = "'.$id.'"';
						if(mysql_query($sql)){
							$info = '<div class="box"><div class="warning"><ul><li>Таны автомашины зар амжилттай шинэчлэгдлээ.</li></ul></div></div>';
						} else {
							$info = '<div class="box"><div class="error"><ul><li>Сүлжээний саатал гарлаа.</li></ul></div></div>';
						}
					} else {
						$info = '<div class="box"><div class="error"><ul><li>Таны зарын шинэчлэх эрх нэмэгдсэнээс хойш 72 цагийн дараа буюу '.date('Y-m-d өдрийн H цаг i минут',($obj->added_date+259200)).'-д нээгдэнэ.</li></ul></div></div>';
					}
					$html=ereg_replace("---main---",$info.latest(20),$html);
				} else {
					$info = '<div class="box"><div class="error"><ul><li>Нууц үг буруу.</li></ul></div></div>';
					$html=ereg_replace("---main---",$info.detail($id),$html);
				}
			}
		} break;
		case 'deletecar_pre':{
			if(isset($_POST['id'])){
				$id = $_POST['id'];
				$sql = 'SELECT pass FROM car_list WHERE id="'.$id.'"';
				$res = mysql_query($sql);
				$pass = mysql_fetch_object($res)->pass;
				
				if($pass == md5($_POST['upass'])){
					if(delete_car($id))
						$info = '<div class="box"><div class="warning"><ul><li>Таны зар амжилттай устлаа.</li></ul></div></div>';
					else 
						$info = '<div class="box"><div class="error"><ul><li>Сүлжээний саатал гарлаа.</li></ul></div></div>';
					$html=ereg_replace("---main---",$info.latest(20),$html);
				} else {
					$info = '<div class="box"><div class="error"><ul><li>Нууц үг буруу.</li></ul></div></div>';
					$html=ereg_replace("---main---",$info.detail($id),$html);
				}
			}
		} break;
		case 'editcar_pre':{
			if(isset($_POST['id'])){
				$user_pass = md5($_POST['upass']);
				$sql = 'SELECT pass FROM car_list WHERE id="'.$_POST['id'].'"';
				$res = mysql_query($sql);
				$pass = mysql_fetch_object($res)->pass;
				
				$admin_sql = 'SELECT priv,upass FROM users WHERE priv=1 LIMIT 0,1';
				$admin_res = mysql_query($admin_sql) or die(mysql_error());
				$admin_pass = mysql_fetch_object($admin_res)->upass;
				
				if($pass == $user_pass || $admin_pass == $user_pass ){
					$info = '<div class="box"><div class="warning"><ul><li>Та зарыг засах эрхтэй.</li></ul></div></div>';
					if($pass == $user_pass) $pass_ = $_POST['upass'];
					else $pass_ = '##########';
					$html=ereg_replace("---main---",$info.add($_POST[id],$pass_),$html);
				} else {
					$info = '<div class="box"><div class="error"><ul><li>Нууц үг буруу.</li></ul></div></div>';
					$html=ereg_replace("---main---",$info.detail($_POST[id]),$html);
				}
			}
		} break;
		case 'editcar_post':{
			$id = $_POST['id'];
			$firm = $_POST['firm'];
			$mark = $_POST['mark'];
			$date_made = $_POST['date_made'];
			$category = $_POST['category'];
			$engine = $_POST['engine'];
			$price = $_POST['price'];
			$phone = $_POST['phone'];
			$password = md5($_POST['password']);
			//---------------------------------
			$version = isset($_POST['version'])?$_POST['version']:'NULL';
			$date_import = isset($_POST['date_import'])?$_POST['date_import']:'NULL';
			$km = isset($_POST['km'])?$_POST['km']:'NULL';
			$facade = isset($_POST['facade'])?$_POST['facade']:'NULL';
			$color_out = isset($_POST['color_out'])?$_POST['color_out']:'NULL';
			$color_in = isset($_POST['color_in'])?$_POST['color_in']:'NULL';
			$drive_pos = isset($_POST['drive_pos'])?$_POST['drive_pos']:'NULL';
			$most = isset($_POST['most'])?$_POST['most']:'NULL';
			$speedbox = isset($_POST['speedbox'])?$_POST['speedbox']:'NULL';
			$benzin = isset($_POST['benzin'])?$_POST['benzin']:'NULL';
			$cylinder = isset($_POST['cylinder'])?$_POST['cylinder']:'NULL';
			$email = isset($_POST['email'])?$_POST['email']:'NULL';
			$website = isset($_POST['website'])?$_POST['website']:'NULL';
			$about = isset($_POST['about'])?$_POST['about']:'NULL';
			//---------------------------------
			
			if($_POST['password']!='##########')
			$sql = '
			UPDATE car_list SET made_date="'.$date_made.'", car_type_id="'.$mark.'", version="'.$version.'", import_date="'.$date_import.'", price="'.$price.'", km="'.$km.'", facade_id="'.$facade.'", about="'.$about.'", category_id="'.$category.'", speedbox_id="'.$speedbox.'", drive_pos="'.$drive_pos.'", benzin_id="'.$benzin.'", engine="'.$engine.'", cylinder="'.$cylinder.'", most_id="'.$most.'", out_color_id="'.$color_out.'", in_color_id="'.$color_in.'", phone="'.$phone.'", email="'.$email.'", website="'.$website.'", pass="'.$password.'", isActive=1 WHERE id="'.$id.'"';
			else 
			$sql = '
			UPDATE car_list SET made_date="'.$date_made.'", car_type_id="'.$mark.'", version="'.$version.'", import_date="'.$date_import.'", price="'.$price.'", km="'.$km.'", facade_id="'.$facade.'", about="'.$about.'", category_id="'.$category.'", speedbox_id="'.$speedbox.'", drive_pos="'.$drive_pos.'", benzin_id="'.$benzin.'", engine="'.$engine.'", cylinder="'.$cylinder.'", most_id="'.$most.'", out_color_id="'.$color_out.'", in_color_id="'.$color_in.'", phone="'.$phone.'", email="'.$email.'", website="'.$website.'", isActive=1 WHERE id="'.$id.'"';
			
			if(mysql_query($sql)){
				$txt = '
				<div class="box">
					<div class="info_">
						<p>Таны автомашины зар амжилттай засагдаж шинэчлэгдлээ.</p>
					</div>
				</div>';
			} else {
				$txt = '
				<div class="box">
					<div class="error">
						<ul><li>Таны автомашиныг засах явцад сүлжээний доголдол үүссэн тул та дахин засна уу.</li></ul>
					</div>
				</div>';
			}
			$html=ereg_replace("---main---",$txt.detail($id),$html);
		} break;
		case 'search':{
				$where  = $_POST['firm'].'i';
				$where .= $_POST['mark'].'i';
				$where .= $_POST['date_made'].'i';
				$where .= $_POST['price'].'i';
				
				$where .= $_POST['engine'].'i';
				$where .= $_POST['benzin'].'i';
				$where .= $_POST['speedbox'].'i';
				$where .= $_POST['drive_pos'].'i';
				$html=ereg_replace("---main---",result(20,1,($where)),$html);
		} break;
		case 'page':{
			$id = $_GET['type'];
			$sql = 'SELECT * FROM car_submenu WHERE id='.$id;
			$value = mysql_fetch_object(mysql_query($sql))->value;
			$html=ereg_replace("---main---","<div class='box'>".html_entity_decode($value)."</div>",$html);
		} break;
		default: {
			$html=ereg_replace("---main---",latest(20),$html);
		}
	}
	
	$html=ereg_replace("---title---",$GLOBALS['title_auto'],$html);
	$html=ereg_replace("---description---",$GLOBALS['description_auto'],$html);
	$html=ereg_replace("---bannerA---",bannerA(),$html);
	$html=ereg_replace("---topmenu---",topmenu(),$html);
	$html=ereg_replace("---czslider---",czslider(),$html);
	$html=ereg_replace("---bannerB1---",bannerB1(),$html);
	$html=ereg_replace("---bannerB2---",bannerB2(),$html);
	$html=ereg_replace("---search---",searchbox(),$html);
	$html=ereg_replace("---statistic---",'',$html);
	$html=ereg_replace("---widget---",widget($action),$html);
	$html=ereg_replace("---footermenu---",footermenu(),$html);
	echo $html;
	
?>

