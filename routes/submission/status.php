<?php
$package->noCache();
$package['fields.page_title'] = '';

$submission = $package->noun();
if (!$submission->isViewable()) {
    //deny access for those with no access
    $package->error(403);
}
$n = $cms->helper('notifications');

echo "<div class='submission-status-wrapper'>";
if (!$submission->complete()) {
    if ($submission->isMine()) {
        if ($submission->isEditable()) {
            $icWarning = 'Your submission has not been fully completed yet. Please finish any incomplete sections.';
            if ($window = $submission->window()) {
                if ($window->end() && $window->end() > time()) {
                    $icWarning .= '<br>Submission can be edited until '.$window->endHR();
                }
            }
            $n->printNotice($icWarning);
        } else {
            $n->printError('Your submission was not completed by the submission deadline of '.$submission->window()->endHR());
        }
    } else {
        if ($submission->isEditable()) {
            $n->printNotice('This submission is currently incomplete.');
        } else {
            $n->printError('This submission was not completed.');
        }
    }
} elseif ($submission->isMine()) {
    if ($submission->isEditable() && !$package['url.args.edit']) {
        $n->printConfirmation('This submission is complete.');
    }
}
echo "</div>";
?>
<style>
    .digraph-area-wrapper {
        max-width: none !important;
    }
</style>
<?php if ($submission->isEditable() && !$submission->window()->ended()) { ?>
<script>
    setTimeout(function() {
        window.location.reload(1);
    }, 15 * 1000);
</script>
<?php }
