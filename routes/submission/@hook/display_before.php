<?php
if (!$package->noun()->isViewable()) {
    //deny access for those with no access
    $package->error(403);
}
//don't cache
$package->noCache();

$n = $cms->helper('notifications');

$submission = $package->noun();
$parts = $submission->parts();
$chunks = $parts->chunks();

if ($chunks) {
    //output status bar
    $url = $submission->url('status');
    echo "<div id='submission-status'><iframe class='embedded-iframe' src='$url'></iframe></div>";
    //output chunks
    echo "<div id='submission-chunks'>";
    foreach ($chunks as $cname => $chunk) {
        if ($cname == 'submission' || $cname == 'submitter' || !$chunk->complete()) {
            echo $chunk->body(true);
        }
    }
    foreach ($chunks as $cname => $chunk) {
        if ($cname != 'submission' && $cname != 'submitter' && $chunk->complete()) {
            echo $chunk->body(true);
        }
    }
    echo "</div>";
}
?>
<script>
    $(() => {
        $('#submission-chunks .submission-chunk a.mode-switch').click((e) => {
            var $target = $(e.target);
            var $wrapper = $target.closest('.submission-chunk');
            $wrapper.replaceWith(
                '<div class="embedded-iframe loading"><iframe class="embedded-iframe resized" src=' +
                $target.attr('href') +
                ' style="height:' + $wrapper.height() + 'px"></iframe></div>'
            );
            e.preventDefault();
            return false;
        });
    });
</script>