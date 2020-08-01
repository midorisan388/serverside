<?php
   session_start();

   ini_set('display_errors',"On");
   error_reporting(E_ALL);

 $list=array(1,2,3,4,5);

var_dump($list);

    file_put_contents("test.txt",$list,FILE_APPEND|LOCK_EX);
?>