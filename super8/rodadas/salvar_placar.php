<?php
require_once __DIR__ . '/../utils/json_helper.php';
require_once __DIR__ . '/../utils/pontuacao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: rodadas.php');
    exit;
}

$rodadasData = ler_json(caminho_rodadas());
if (!$rodadasData || empty($rodadasData['rodadas'])) {
    header('Location: ../configuracao/configuracao.php');
    exit;
}

$numeroRodada = (int) ($_POST['rodada'] ?? 0);

$indiceRodada = null;
foreach ($rodadasData['rodadas'] as $i => $r) {
    if ($r['numero'] === $numeroRodada) {
        $indiceRodada = $i;
        break;
    }
}

if ($indiceRodada === null) {
    header('Location: rodadas.php?erro=' . urlencode('Rodada inválida.'));
    exit;
}

foreach ($rodadasData['rodadas'][$indiceRodada]['partidas'] as $i => $partida) {
    $quadra = $partida['quadra'];
    $placarA = $_POST["placarA_$quadra"] ?? null;
    $placarB = $_POST["placarB_$quadra"] ?? null;

    if (!is_numeric($placarA) || !is_numeric($placarB)) {
        header('Location: rodadas.php?rodada=' . $numeroRodada . '&erro=' . urlencode('Informe placares válidos para todas as quadras.'));
        exit;
    }

    $placarA = (int) $placarA;
    $placarB = (int) $placarB;

    if ($placarA < 0 || $placarA > 7 || $placarB < 0 || $placarB > 7) {
        header('Location: rodadas.php?rodada=' . $numeroRodada . '&erro=' . urlencode('Os placares devem estar entre 0 e 7 games.'));
        exit;
    }

    $rodadasData['rodadas'][$indiceRodada]['partidas'][$i]['placarA'] = $placarA;
    $rodadasData['rodadas'][$indiceRodada]['partidas'][$i]['placarB'] = $placarB;
}

$ok = gravar_json(caminho_rodadas(), $rodadasData);

if (!$ok) {
    header('Location: rodadas.php?rodada=' . $numeroRodada . '&erro=' . urlencode('Não foi possível salvar o placar.'));
    exit;
}

$pendente = rodada_pendente($rodadasData);

if ($pendente && $pendente !== $numeroRodada) {
    header('Location: rodadas.php?rodada=' . $pendente . '&msg=' . urlencode("Placar da Rodada $numeroRodada salvo! Avançando para a Rodada $pendente."));
} elseif (!$pendente) {
    header('Location: ../classificacao/classificacao.php?msg=' . urlencode('Torneio finalizado! Confira a classificação final.'));
} else {
    header('Location: rodadas.php?rodada=' . $numeroRodada . '&msg=' . urlencode('Placar atualizado com sucesso.'));
}
exit;
