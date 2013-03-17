<?php
//确定性别以及性取向
$ruler[] = array( 're!/[1-4]/' => 'handler!sex_and_orientation' );
//匹配用户输入的年龄
$ruler[] = array( 're!/[0-9]{1,2}$/' => 'handler!search' );
//更换查询的结果 显示下一个
$ruler[] = array( 're!/[hH]/' => 'handler!next' );
//更换查询的对象性别
$ruler[] = array( 're!/[sS]/' => 'handler!change_orientation_sex' );
//帮助信息
$ruler[] = array( 'text!/?' => 'text!输入 “h” 显示下一个，输入 “s” 更换查询性别.' );

