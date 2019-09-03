<?php
if ($cms->helper('users')->user()) {
    //no cache for signed-in users
    $package->noCache();
}
//always make browser-side TTL 0
$package['response.browserttl'] = 0;
