<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<meta name="screen-orientation" content="portrait">
<meta name="x5-orientation" content="portrait">
<title>My Site</title>

<link href="<?php echo $assets_url;?>bootstrap/css/bootstrap.min.css" rel="stylesheet">

<?php if(!empty($styles)): foreach($styles as $style): ?>
<link href="<?php echo $style;?>" rel="stylesheet">
<?php endforeach; endif; ?>

<script src="<?php echo $assets_url;?>js/jquery.min.js"></script>
<script src="<?php echo $assets_url;?>bootstrap/js/bootstrap.min.js"></script>

<?php if(!empty($scripts)): foreach($scripts as $script): ?>
<script src="<?php echo $script;?>"></script>
<?php endforeach; endif; ?>

</head>
<body<?php if(!empty($page_id)):?> id="<?php echo $page_id;?>"<?php endif;?>>
