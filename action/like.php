<?php
include 'load.php';
$a = checkpost('key');
$b = checkpost('pid');
$c = get_post_permalink($b);
if ($a !== 'like' && !$c && !isInStr($c, 'post_type=post')) {
    print_r(json_encode(array('error' => 1)));
    exit;
}
$d = false;
$e = false;
if (is_user_logged_in()) {
    $f = get_current_user_id();
    $d = get_user_meta($f, 'like-posts', true);
    if ($d) {
        $d = unserialize($d);
    	$e = in_array($b, $d);
    }
}
if (!$d || !$e) {
    if (!$d) {
        $d = array($b);
    } else {
        array_unshift($d, $b);
    }
    upmeta($d);
    $g = (int) get_post_meta($b, $a, true);
    if (!$g) {
        $g = 0;
    }
    update_post_meta($b, $a, $g + 1);
    print_r(json_encode(array('error' => 0, 'like' => 1, 'response' => $g + 1)));
    exit;
}
if ($e) {
    $h = array_search($b, $d);
    unset($d[$h]);
    upmeta($d);
    $g = (int) get_post_meta($b, $a, true);
    if (!$g) {
        $g = 1;
    }
    update_post_meta($b, $a, $g - 1);
    print_r(json_encode(array('error' => 0, 'like' => 0, 'response' => $g - 1)));
    exit;
}
exit;
function upmeta($i)
{
    if (is_user_logged_in()) {
        global $f;
        update_user_meta($f, 'like-posts', serialize($i));
    }
}
function checkpost($j)
{
    return isset($_POST[$j]) ? trim(htmlspecialchars($_POST[$j], ENT_QUOTES)) : '';
}
function isInStr($k, $l)
{
    $k = '-_-!' . $k;
    return (bool) strpos($k, $l);
}
