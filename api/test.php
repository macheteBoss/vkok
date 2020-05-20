<?php
	$url = 'https://timeweb.com/ru/';
	$output = curl_init();	//подключаем курл
	curl_setopt($output, CURLOPT_URL, $url);	//отправляем адрес страницы
	curl_setopt($output, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($output, CURLOPT_HEADER, 0);
	$out = curl_exec($output);		//помещаем html-контент в строку
	curl_close($output);	//закрываем подключение
	
		
	preg_match_all('/(?<=<div>).*(?=<\/div>)/',$out, $result);
	print_r($result);
		
?>