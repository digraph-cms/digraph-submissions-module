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
    echo "<div id='submission-status-wrapper'><div id='submission-status'></div></div>";
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
        // status checker
        var $status = $("#submission-status");
        var updateStatus = _.debounce(
            function() {
                $status.addClass('loading');
                digraph.getJSON(
                    "<?php echo $submission['dso.id']; ?>/status",
                    function (status) {
                        // update status bar
                        $status
                            .removeClass('loading')
                            .addClass('notification')
                            .removeClass('notification-warning')
                            .removeClass('notification-error')
                            .removeClass('notification-info')
                            .removeClass('notification-notice')
                            .removeClass('notification-confirmation')
                            .addClass('notification-'+status.type)
                            .html(status.message);
                    }
                );
            },
            1000
        );
        updateStatus();
        // signup chunk swapper
        $('.submission-chunk a.mode-switch').click((e) => {
            var $target = $(e.target);
            var $wrapper = $target.closest('.submission-chunk');
            var url = $target.attr('href')+'&iframe=1';
            var $chunk = $(
                '<iframe class="embedded-iframe resized" src=' +
                url +
                ' style="height:' + $wrapper.height() + 'px"></iframe>'
            );
            $chunk.on('load',updateStatus);
            $wrapper.replaceWith($chunk);
            e.preventDefault();
            return false;
        });
        // make status sticky
        var $wrapper = $('#submission-status-wrapper');
        // $('#digraph-meta > .digraph-area-wrapper').append($wrapper);
        var doSticky = function() {
            var sticky = ($(window).scrollTop()-$wrapper.offset().top) >= 0;
            if (sticky) {
                if (!$status.is('.sticky')) {
                    $wrapper.height($wrapper.height());
                }
                $status.addClass('sticky');
            }else {
                $status.removeClass('sticky');
                $wrapper.height('auto');
            }
        };
        doSticky();
        $(window).scroll(doSticky);
        $(window).resize(doSticky);
    });
</script>
<style>
    #submission-status {
        position: relative;
        text-align: center;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0;
    }
    #submission-status.sticky {
        position:fixed;
        margin:0;
        left:0;
        right:0;
        top:0;
        border-radius: 0;
        z-index: 100;
    }
    #submission-status.notification {
        transition: opacity 0.5s ease-in-out;
    }
    #submission-status.notification:after {
        content: "\f021";
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        display: block;
        position: absolute;
        top: 0;
        right: 0;
        font-size: 1rem;
        line-height: 1rem;
        padding: 0.5rem;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }
    #submission-status.notification.loading {
        opacity: 0.85;
    }
    #submission-status.notification.loading:after {
        opacity: 1;
    }
</style>
