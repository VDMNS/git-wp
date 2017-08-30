<?php
require "db.php";

$data = $_POST;
if ( isset ($data['enter']) )
{ //здесь регистрируем
   
         $errors=array ();
		if( trim($data['login'] ) == '' )
		{
		$errors[] = 'Введите логин';
		}
		
		if (trim ( $data ['password'] ) == '' )
		{
		$errors[] = 'Введите пароль';
		}
		
		if (R::count('users', "login = ? ", array($data['login'], )) > 0  )
		{
		$errors[] = 'Пользователь с таким именем существует';
		}
		if (R::count('users', "email = ? ", array($data['email'], )) > 0  )
		{
		$errors[] = 'Пользователь с таким email существует';
		}
		
		
		if (trim ( $data ['email'] ) == '' )
		{
		$errors[] = 'Введите email';
		}
		
		if (trim ( $data ['name'] ) == '' )
		{
		$errors[] = 'Введите имя';
		}
		
		if (trim ( $data ['surname'] ) == '' )
		{
		$errors[] = 'Введите фамилию';
		}
		
		if (trim( $data ['phone'] ) == '' )
		
		{
		$errors[] = 'Введите телефон' ;
		}
		
		if(empty($errors)){
			//все хорошо регистрируем
			 $user = R::dispense('users');
	$user->login = $data['login'];
	$user->password = password_hash($data['password'],PASSWORD_DEFAULT);
	$user->email = $data['email'];
	$user->name = $data['name'];
	$user->surname = $data['surname'];
	$user->phone = $data['phone'];
	R::store($user);
	echo '<div id = "erors">Вы успешно зарегестрированны</div> <hr>';
		} else
		{
			echo '<div id = "erors">'.array_shift($errors).'</div> <hr>';
		}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 

"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>'.$p1.'</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="resource/templatemo_style.css" rel="stylesheet" type="text/css" 

/>
<link href="resource/jquery.ennui.contentslider.css" rel="stylesheet" 

type="text/css" media="screen,projection" />
<!-- templatemo 321 glossy box -->
<!-- 
Glossy Box Template
http://www.templatemo.com/preview/templatemo_321_glossy_box
-->
</head>




<body>

<div id="templatemo_wrapper_outer">
	<div id="templatemo_wrapper">
    
    	<div id="templatemo_header">
			<div id="site_title">
                <h1><a href="#"><strong>Блог</strong> для<span>WORDPRESS</span></a></h1>
            </div> <!-- end of site_title -->
           
            <ul id="social_box">
                <li><a href="#"><img src="resource/images/facebook.png" alt="facebook" /></a></li>
                <li><a href="#"><img src="resource/images/twitter.png" alt="twitter" /></a></li>
                <li><a href="#"><img src="resource/images/linkedin.png" alt="linkin" /></a></li>
                <li><a href="#"><img src="resource/images/technorati.png" alt="technorati" /></a></li>
                <li><a href="#"><img src="resource/images/myspace.png" alt="myspace" /></a></li>                
            </ul>
			
			<div class="cleaner"></div>
		</div>
        
        <div id="templatemo_menu">
            <ul>
                <li><a href="/" class="current">Главная</a></li>
                <li><a href="/signup.php">Регистрация</a></li>
                <li><a href="/login.php">Вход</a></li>
                <li><a href="/reg">reg</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>    	
        </div>




Заполните форму:
<form method="POST" action="/signup.php">
<br><input type="text" name="login" > - Логин
<br><input type="email" name="email" > - E-Mail
<br><input type="password" name="password" > -Пароль
<br><input type="text" name="name" > - Имя
<br><input type="text" name="surname" > - Фамилия
<br><input type="text" name="phone" > - Телефон
<br><br><input type="submit" name="enter" value="Регистрация"> <input type="reset" value="Очистить">
</form>

<div id="templatemo_footer">
		
             Copyright © 2017 <a href="#">Vadim Manzhos</a>
			 
       </div>


</body>
</html>