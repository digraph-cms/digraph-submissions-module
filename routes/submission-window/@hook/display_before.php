<?php
$package->cache_noCache();
if ($cms->helper('users')->user()) {
    //no cache for signed-in users
    $package->cache_noStore();
}
//always make browser-side TTL 0
$package['response.browserttl'] = 0;
