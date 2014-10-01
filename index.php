<?php
    if (in_array(@$_SERVER['REMOTE_ADDR'], array(
        '127.0.0.1',
        '::1',
    ))) {
        echo('<script>window.location.replace("/web/app_dev.php/"); </script>');
    }
    else{
        echo('<script>window.location.replace("/web/"); </script>');
    }
?>
