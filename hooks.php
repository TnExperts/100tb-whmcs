<?php

function servers100tb_remove_mod100tb_record($vars) {
	$serviceId = $vars['id'];
	$query = "DELETE FROM `mod_100tb` WHERE `serviceid` = '{$serviceId}' LIMIT 1";
	mysql_query($query);
}

add_hook("ServiceDelete",1,"servers100tb_remove_mod100tb_record");