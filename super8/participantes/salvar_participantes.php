<?php
require_once __DIR__ . '/../utils/json_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

$participantes = [];
for ($i = 1; $i <= 8; $i++) {
    $nome = trim($_POST["nome_$i"] ?? '');
    $apelido = trim($_POST["apelido_$i"] ?? '');

    if ($nome === '') {
        header('Location: cadastro.php?erro=' . urlencode("O nome do Jogador $i é obrigatório."));
        exit;
    }

    $participantes[] = [
        'id' => $i,
        'nome' => $nome,
        'apelido' => $apelido,
    ];
}

$ok = gravar_json(caminho_participantes(), ['participantes' => $participantes]);

if (!$ok) {
    header('Location: cadastro.php?erro=' . urlencode('Não foi possível salvar os dados. Verifique as permissões da pasta data/.'));
    exit;
}

// Como os participantes mudaram, qualquer configuração/rodada anterior fica inválida
$caminhoConfig = caminho_config();
$caminhoRodadas = caminho_rodadas();
if (file_exists($caminhoConfig)) {
    unlink($caminhoConfig);
}
if (file_exists($caminhoRodadas)) {
    unlink($caminhoRodadas);
}

header('Location: ../configuracao/configuracao.php');
exit;
