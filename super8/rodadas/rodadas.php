<?php
require_once __DIR__ . '/../utils/json_helper.php';
require_once __DIR__ . '/../utils/pontuacao.php';
require_once __DIR__ . '/../utils/icones.php';

$dadosParticipantes = ler_json(caminho_participantes());
$rodadasData = ler_json(caminho_rodadas());

if (!$dadosParticipantes || !$rodadasData || empty($rodadasData['rodadas'])) {
    header('Location: ../configuracao/configuracao.php');
    exit;
}

$participantes = $dadosParticipantes['participantes'];
$rodadas = $rodadasData['rodadas'];

$pendente = rodada_pendente($rodadasData);
$numeroSelecionado = isset($_GET['rodada']) ? (int) $_GET['rodada'] : ($pendente ?? 7);
if ($numeroSelecionado < 1 || $numeroSelecionado > 7) {
    $numeroSelecionado = $pendente ?? 7;
}

$rodadaAtual = null;
foreach ($rodadas as $r) {
    if ($r['numero'] === $numeroSelecionado) {
        $rodadaAtual = $r;
        break;
    }
}

$mensagem = isset($_GET['msg']) ? $_GET['msg'] : null;
$erro = isset($_GET['erro']) ? $_GET['erro'] : null;

function nomes_dupla($participantes, $idsArray) {
    $nomes = [];
    foreach ($idsArray as $id) {
        $j = obter_jogador($participantes, $id);
        $nomes[] = $j ? nome_exibicao($j) : "Jogador $id";
    }
    return implode(' / ', $nomes);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rodadas - Super 8</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="topo">
        <h1><?= icone('praia', 'icone-grande') ?>Super 8 - Beach Tennis</h1>
        <p>Etapa 3: Rodadas e Placares</p>
    </header>
    <main>
        <div class="card">
            <?php if ($mensagem): ?>
                <div class="alerta sucesso"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="rodadas-nav no-print">
                <?php foreach ($rodadas as $r):
                    $classe = '';
                    if ($r['numero'] === $numeroSelecionado) {
                        $classe = 'ativa';
                    } elseif (rodada_completa($r)) {
                        $classe = 'completa';
                    }
                ?>
                    <a href="?rodada=<?= $r['numero'] ?>" class="<?= $classe ?>">Rodada <?= $r['numero'] ?></a>
                <?php endforeach; ?>
            </div>

            <div class="indicador-rodada">
                Rodada <?= $numeroSelecionado ?> de 7
                <?php if ($pendente): ?>
                    — faltam <?= 7 - $pendente + 1 ?> rodada(s) para finalizar
                <?php else: ?>
                    — <?= icone('trofeu') ?>Torneio concluído!
                <?php endif; ?>
            </div>

            <form action="salvar_placar.php" method="post">
                <input type="hidden" name="rodada" value="<?= $rodadaAtual['numero'] ?>">

                <?php foreach ($rodadaAtual['partidas'] as $partida): ?>
                    <div class="partida">
                        <h3>Quadra <?= $partida['quadra'] ?></h3>
                        <div class="confronto">
                            <div class="dupla"><?= htmlspecialchars(nomes_dupla($participantes, $partida['duplaA'])) ?></div>
                            <input type="number" min="0" max="7" class="placar-input"
                                   name="placarA_<?= $partida['quadra'] ?>"
                                   value="<?= $partida['placarA'] !== null ? (int) $partida['placarA'] : '' ?>" required>
                            <span class="versus">x</span>
                            <input type="number" min="0" max="7" class="placar-input"
                                   name="placarB_<?= $partida['quadra'] ?>"
                                   value="<?= $partida['placarB'] !== null ? (int) $partida['placarB'] : '' ?>" required>
                            <div class="dupla"><?= htmlspecialchars(nomes_dupla($participantes, $partida['duplaB'])) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn">Salvar Placar</button>
                <a href="../classificacao/classificacao.php" class="btn secundario">Ver Classificação</a>
                <a href="../index.php" class="btn cinza">Voltar ao Menu</a>
            </form>
        </div>
    </main>
    <script src="../js/ui.js"></script>
</body>
</html>
