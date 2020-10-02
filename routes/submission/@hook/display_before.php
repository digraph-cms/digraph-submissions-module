<?php
$package->cache_noStore();

if (!$package->noun()->isViewable()) {
    //deny access for those with no access
    $package->error(403);
}

$n = $cms->helper('notifications');

$submission = $package->noun();
$parts = $submission->parts();
$chunks = $parts->chunks();

if ($chunks) {
    //output status bar
    echo "<div id='submission-status'></div>";
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
        // submission chunk swapper
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
        // status checker
        var updateStatus = function() {
            digraph.getJSON(
                "<?php echo $submission['dso.id']; ?>/status",
                function (status) {
                    $('#submission-status')
                        .addClass('notification')
                        .addClass('notification-'+status.type)
                        .html(status.message);
                    setTimeout(updateStatus,<?php echo $cms->config['submissions.status_update']*1000; ?>);
                }
            );
        }
        updateStatus();
    });
</script>