<?php
require_once __DIR__ . '/utils/json_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

foreach ([caminho_participantes(), caminho_config(), caminho_rodadas()] as $arquivo) {
    if (file_exists($arquivo)) {
        unlink($arquivo);
    }
}

header('Location: index.php');
exit;
