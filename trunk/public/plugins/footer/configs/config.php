<?php
//config Url and Params for get database information
define('BASE_URL', 'https://persepback00.net.persei.com.es/pandora_console/include/api.php');
define('SERVER_UPTIME_QUERY', '?op=get&op2=module_value_all_agents&id=MySQL:%20Uptime%20%28s%29&apipass=1234&user=miguel&pass=test');
//config Url and Params for get server information
define('DATABASE_UPTIME_QUERY', '?op=get&op2=module_value_all_agents&id=Apache:%20Uptime&apipass=1234&user=miguel&pass=test');
//config Url and Params for get databse sate
define('SERVER_STATE_QUERY', '?op=get&op2=module_value_all_agents&id=MySQL:%20Daemon%20alive&apipass=1234&user=miguel&pass=test');
//config Url and Params for get server state
define('DATABASE_STATE_QUERY', '?op=get&op2=module_value_all_agents&id=Apache:%20Daemon%20alive&apipass=1234&user=miguel&pass=test');
//config Url and Params for response time
define('RESPONSE_TIME_QUERY', '?op=get&op2=module_value_all_agents&id=WebMon_time_total&apipass=1234&user=miguel&pass=test');
?>
