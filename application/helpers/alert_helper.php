<?php
function alert($msg='', $url='') {
$CI =& get_instance();

if (!$msg) $msg = '비정상 경로로 접근하였습니다.';

echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
echo "<script type='text/javascript'>alert('".$msg."');";
    if ($url)
        echo "location.replace('".$url."');";
    else
    echo "history.go(-1);";
    echo "</script>";
exit;
}