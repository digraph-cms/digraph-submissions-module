<?php
if (!$cms->helper('users')->user()) {
    //notice for non-signed-in users
    $url = $cms->helper('users')->signinUrl($package);
    if ($package->noun()->open()) {
        $cms->helper("notifications")
            ->printNotice('<a href="'.$url.'">Sign in to start a submission or manage one you already started</a>');
    }elseif ($package->noun()->ended()) {
        $cms->helper("notifications")
            ->printNotice('<a href="'.$url.'">Sign in to see any submissions you made</a>');
    }
}