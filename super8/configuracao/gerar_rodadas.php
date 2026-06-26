<?php
require_once __DIR__ . '/../utils/json_helper.php';
require_once __DIR__ . '/../utils/sorteio.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: configuracao.php');
    exit;
}

$dados = ler_json(caminho_participantes());
if (!$dados || count($dados['participantes']) !== 8) {
    header('Location: ../participantes/cadastro.php');
    exit;
}
$participantes = $dados['participantes'];
$idsParticipantes = array_map(function ($p) { return (int) $p['id']; }, $participantes);

$formato = $_POST['formato'] ?? '';
if (!in_array($formato, ['rotativas', 'fixas'], true)) {
    header('Location: configuracao.php?erro=' . urlencode('Selecione um formato de jogo válido.'));
    exit;
}

$config = ['formato' => $formato];

if ($formato === 'fixas') {
    $duplas = [];
    $usados = [];

    for ($d = 1; $d <= 4; $d++) {
        $j1 = (int) ($_POST["dupla{$d}_j1"] ?? 0);
        $j2 = (int) ($_POST["dupla{$d}_j2"] ?? 0);

        if ($j1 === $j2 || !in_array($j1, $idsParticipantes, true) || !in_array($j2, $idsParticipantes, true)) {
            header('Location: configuracao.php?erro=' . urlencode("A Dupla $d possui jogadores inválidos ou repetidos."));
            exit;
        }

        $usados[] = $j1;
        $usados[] = $j2;
        $duplas[] = [$j1, $j2];
    }

    if (count(array_unique($usados)) !== 8) {
        header('Location: configuracao.php?erro=' . urlencode('Cada jogador deve aparecer em exatamente uma dupla.'));
        exit;
    }

    $config['duplas_fixas'] = $duplas;
    $rodadas = gerar_rodadas_fixas($duplas);
} else {
    $rodadas = gerar_rodadas_rotativas($idsParticipantes);
}

$okConfig = gravar_json(caminho_config(), $config);
$okRodadas = gravar_json(caminho_rodadas(), ['rodadas' => $rodadas]);

if (!$okConfig || !$okRodadas) {
    header('Location: configuracao.php?erro=' . urlencode('Não foi possível salvar as rodadas. Verifique as permissões da pasta data/.'));
    exit;
}

header('Location: ../rodadas/rodadas.php');
exit;
