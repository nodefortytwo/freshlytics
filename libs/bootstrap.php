<?php
require('facebook/facebook.php');
require('settings.php');
require('misc.php');
require('db.php');
require('theme.php');

$menu = array();
$js = array();
$js['footer']['core'][] = '/' . SITE_ROOT . "/js/plugins.js";
$js['footer']['core'][] = '/' . SITE_ROOT . "/js/script.js";
$js['footer']['core'][] = '/' . SITE_ROOT . "/js/mylibs/chosen/chosen.jquery.min.js";

$css = array();
$css['all']['core'][] = "/".SITE_ROOT."/css/style.css";
$css['screen']['core'][] = "/".SITE_ROOT."/css/960/reset.css";
$css['screen']['core'][] = "/".SITE_ROOT."/css/960/text.css";
$css['screen']['core'][] = "/".SITE_ROOT."/css/960/grid.css";
$css['all']['core'][] = "/".SITE_ROOT."/css/blitzer/jquery-ui-1.8.16.custom.css";
$css['all']['core'][] = "/".SITE_ROOT."/js/mylibs/chosen/chosen.css";
$css['all']['core'][] = "/".SITE_ROOT."/css/core.css";

foreach (glob("libs/modules/*") as $filename)
{
    $module = str_replace('libs/modules/', '', $filename);
    require('libs/modules/' . $module . '/' . $module . '.php');

    //Add the menu hooks
    if(function_exists($module.'_menu')){
        $data = call_user_func($module.'_menu');
        $menu = array_merge($data, $menu);
    }
    //get any js files
    if(function_exists($module.'_js')){
        $data = call_user_func($module.'_js');
        $js = array_merge_recursive($data,  $js);
    }
    
    //get any css files
    if(function_exists($module.'_css')){
        $data = call_user_func($module.'_css');
        $css = array_merge_recursive($data, $css);
    }
}

// This request is either a clean URL, or 'index.php', or nonsense.
// Extract the path from REQUEST_URI.
$request_path = strtok($_SERVER['REQUEST_URI'], '?');
$base_path_len = strlen(rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/'));
// Unescape and strip $base_path prefix, leaving q without a leading slash.
$path = substr(urldecode($request_path), $base_path_len + 1);
// If the path equals the script filename, either because 'index.php' was
// explicitly provided in the URL, or because the server added it to
// $_SERVER['REQUEST_URI'] even when it wasn't provided in the URL (some
// versions of Microsoft IIS do this), the front page should be served.
if ($path == basename($_SERVER['PHP_SELF'])) {
  $path = '';
}

if ($path == ''){$path = 'home';}
$ajax = false;
if (beginsWith($path, 'ajax')){
    $ajax = true;
}

$split = explode('~', $path);
$path = rtrim($split[0], "/");
$args = array();
if (!empty($split[1])){
    $split[1] = trim($split[1], "/");
    $args = explode('/', $split[1]);
}

//init the db connection
$db = new Database();

if (!$ajax){include "theme/header.tpl.php";}

if (array_key_exists($path, $menu)){
    if(function_exists($menu[$path]['callback'])){
        call_user_func_array($menu[$path]['callback'], $args);
    }
}else{
    if (!$ajax){
        print '<div class="grid_16" id="fourohfour"><h1>404! - Page Not Found</h1></div>';
    }else{
        print json_encode(array('error'=>'404'));
    }
}

if (!$ajax){include "theme/footer.tpl.php";}

