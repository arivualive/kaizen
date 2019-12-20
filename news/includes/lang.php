<?php

$lang = [];
if(!isset($_SESSION['lang']))
{
  $_SESSION['lang'] = 'jp';
  $lang = json_decode(file_get_contents('language/jp.json'), true);
}
else if($_SESSION['lang'] == 'en') 
{
  $lang = json_decode(file_get_contents('language/en.json'), true);
}
else if($_SESSION['lang'] == 'jp')
{
  $lang = json_decode(file_get_contents('language/jp.json'), true);
}

return $lang;