<?php
if (!$package->noun()->isViewable()) {
    //deny access for those with no access
    $package->error(403);
}
//always make browser-side TTL 0
$package['response.browserttl'] = 0;

$n = $cms->helper('notifications');

$submission = $package->noun();
$parts = $submission->parts();
$chunks = $parts->chunks();

if (!$submission->complete()) {
    if ($submission->isMine()) {
        if ($submission->isEditable()) {
            $icWarning = 'Your submission has not been fully completed yet. Please finish filling out any sections marked "incomplete."';
            if ($window = $submission->window()) {
                if ($end = $window->endHR()) {
                    $icWarning .= '<br>Submission can be edited until '.$end;
                }
            }
            $icWarning .= '<br><a href="'.$submission->url().'">Re-check completion status.</a>';
            $n->warning($icWarning);
        } else {
            $n->error('Your submission was not completed by the submission deadline of '.$submission->window()->endHR());
        }
    } else {
        $n->error('This submission is currently incomplete.');
    }
} elseif ($submission->isMine()) {
    if (!$package['url.args.edit']) {
        $n->confirmation('Your submission is complete.');
    }
}

if ($chunks) {
    //determine if we're in edit mode
    $editMode = false;
    if ($submission->isMine()) {
        if (!$submission->complete() && $submission->isEditable()) {
            $editMode = true;
        }
    }
    if ($submission->isEditable()) {
        if ($package['url.args.edit']) {
            $editMode = true;
        }
    }
    //display link to enter edit mode if necessary
    if ($submission->isEditable() && !$editMode) {
        $url = $package->url();
        $url['args.edit'] = true;
        $editLink = "<a href='$url'>Edit submission</a>";
        if ($window = $submission->window()) {
            if ($end = $window->endHR()) {
                $editLink .= '<br>Submission can be edited until '.$end;
            }
        }
        $n->notice($editLink);
    } elseif ($submission->isEditable() && $package['url.args.edit']) {
        $url = $package->url();
        unset($url['args.edit']);
        $n->notice("<a href='$url'>Exit editing mode</a>");
    }
    //set cache based on edit mode
    if ($editMode) {
        $package->noCache();
    } else {
        $package['response.ttl'] = 300;
    }
    //output chunks
    echo "<div id='submission-chunks'>";
    foreach ($chunks as $cname => $chunk) {
        if (!$editMode) {
            //place chunk body directly on the page if editing is open
            echo $chunk->body(true);
        } else {
            //otherwise include an iframe to it
            $url = $submission->url('chunk', ['chunk'=>$cname], true);
            echo "<iframe src='$url' class='embedded-iframe'></iframe>";
        }
    }
    echo "</div>";
}
