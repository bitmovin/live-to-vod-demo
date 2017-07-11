<?php
    $config = json_decode(file_get_contents('config.json'), true);

    $manifests = array(
        'DASH' => $config['HTTP_ROOT_PATH'] . 'stream.mpd'
    );

    echo json_encode($manifests);