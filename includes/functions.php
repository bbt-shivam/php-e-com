<?php

function base_url($path = '') {
    $baseUrl = 'http://localhost:8080/mtz/';
    return $baseUrl . ltrim($path, '/');
}

?>
