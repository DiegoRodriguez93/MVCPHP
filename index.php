<?php
	session_start();
	include('controllers/Views.php');

	if(isset($_SESSION['validada']))
	{
		Views::HTML(['header', 'navBar'], 'includes');
		Views::HTML('index');
		Views::HTML(['modal', 'footer'], 'includes');
	}
	else
	{
		Views::HTML('header', 'includes');
		Views::HTML('logIn');
		Views::HTML('footer', 'includes');
	}