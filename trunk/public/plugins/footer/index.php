<?php 
//initialize class Footer
require 'library/Footer.php';
//Get server information
$sever_alive = Footer::server_alive();
//Get database information
$database_alive = Footer::database_alive();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Design Footer</title>
<!-- Begin copy the script code below  to your website-->
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script src="js/jquery-1.9.1.js"></script>
<script src="js/jquery-ui.js"></script>
<script>
$(function() {    
    $('.database_time').text('<?php echo $database_alive;?>');
    $('.server_time').text('<?php echo $sever_alive;?>');
});
</script> 
<!-- End copy the script code -->
</head>
<body>  
    <div id="footer">               
        <div class="footer_show">
            <div class="left">
				<table border='1' bordercolor='#fff' cellspacing='0' cellpadding='5' style='border-collapse:collapse;'>
					<thead style="background:#3BADF2;color:#fff;">
						<tr>
							<th>&nbsp;&nbsp;&nbsp;</th>
							<th>Sevice</th>
							<th>Availability status</th>
						</tr>
					</thead>
					<tbody>
						<tr style="background:#CEE1E9;">
							<td><?php if($sever_alive != null):?><img src="images/tick_circle_frame.png"><?php else:?><img src="images/error_do_not.png"><?php endif;?></td>
							<td>Monitoring system</td>
							<td><?php echo ($sever_alive != null?'Service is operating normally. Alive from '.$sever_alive:null)?></td>
						</tr>
						<tr style="background:#F0F6F8;">
							<td><?php if($sever_alive != null):?><img src="images/tick_circle_frame.png"><?php else:?><img src="images/error_do_not.png"><?php endif;?></td>
							<td>Web server</td>
							<td><?php echo ($sever_alive != null?'Service is operating normally. Alive from '.$sever_alive:null)?></td>							
						</tr>
						<tr style="background:#CEE1E9;">
							<td><?php if($database_alive != null):?><img src="images/tick_circle_frame.png"><?php else:?><img src="images/error_do_not.png"><?php endif;?></td>
							<td>Database server</td>
							<td><?php echo ($database_alive != null?'Service is operating normally. Alive from '.$database_alive:null)?></td>							
						</tr>
					</tbody>
				</table>
            </div>
            <div class="right">
                 <!--<div id="thawteseal" style="text-align:center;" title="Click to Verify - This site chose Thawte SSL for secure e-commerce and confidential communications.">

                <div><script type="text/javascript" src="https://seal.thawte.com/getthawteseal?host_name=www.perseiconsulting.com.es&size=M&lang=en"></script></div>

                <div><a href="http://www.thawte.com/ssl-certificates/" target="_blank" style="color:#000000; text-decoration:none; font:bold 10px arial,sans-serif; margin:0px; padding:0px;">ABOUT SSL CERTIFICATES</a></div>

                </div>    -->    
            </div>
        </div>        
    </div>   
    <!-- End footer-->    
</body>
</html>