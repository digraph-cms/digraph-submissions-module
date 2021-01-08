<?php
$package->cache_private();
$package['response.ttl'] = $cms->config['submissions.status_ttl'];
$package->makeMediaFile('status.json');
$status = [
    'type' => 'none',
    'message' => 'Unknown status',
];

$submission = $package->noun();
if (!$submission->isViewable()) {
    //deny access for those with no access
    $package->error(403);
}

if (!$submission->complete()) {
    if ($submission->isMine()) {
        if ($submission->isEditable()) {
            $icWarning = 'Your submission has not been fully completed yet. Please finish any incomplete sections.';
            if ($window = $submission->window()) {
                if ($window->end() && !$window->ended()) {
                    $icWarning .= '<br>Submission can be edited until ' . $window->endHR();
                }
            }
            $status['type'] = 'notice';
            $status['message'] = $icWarning;
        } else {
            $status['type'] = 'error';
            $status['message'] = 'Your submission was not completed by the submission deadline of ' . $submission->window()->endHR();
        }
    } else {
        if ($submission->isEditable()) {
            $status['type'] = 'notice';
            $status['message'] = 'This submission is currently incomplete.';
        } else {
            $status['type'] = 'error';
            $status['message'] = 'This submission was not completed by the deadline.';
        }
        $reloadTime = 60;
    }
} elseif ($submission->isMine()) {
    $cMessage = 'Your submission is complete and submitted.';
    if ($submission->isEditable() && $window = $submission->window()) {
        if ($window->end() && !$window->ended()) {
            $cMessage .= '<br>submission can be edited until ' . $window->endHR();
        }
        $reloadTime = 30;
    }
    $status['type'] = 'confirmation';
    $status['message'] = $cMessage;
} else {
    $status['type'] = 'confirmation';
    $status['message'] = 'This submission is complete';
}

echo json_encode($status);
