<?php
require_once __DIR__ . '/../utils/json_helper.php';
require_once __DIR__ . '/../utils/pontuacao.php';
require_once __DIR__ . '/../utils/icones.php';

$dadosParticipantes = ler_json(caminho_participantes());
$rodadasData = ler_json(caminho_rodadas());
$config = ler_json(caminho_config());

if (!$dadosParticipantes || !$rodadasData) {
    header('Location: ../configuracao/configuracao.php');
    exit;
}

$participantes = $dadosParticipantes['participantes'];
$classificacao = calcular_classificacao($participantes, $rodadasData);

// Dados para o gráfico: pontuação acumulada por jogador após cada rodada
$dadosGrafico = ['rodadas' => [], 'jogadores' => []];
for ($nr = 1; $nr <= 7; $nr++) {
    $dadosGrafico['rodadas'][] = $nr;
}
foreach ($participantes as $p) {
    $total = 0;
    $serie = [];
    for ($nr = 1; $nr <= 7; $nr++) {
        foreach ($rodadasData['rodadas'] as $r) {
            if ($r['numero'] !== $nr) {
                continue;
            }
            foreach ($r['partidas'] as $partida) {
                if ($partida['placarA'] === null || $partida['placarB'] === null) {
                    continue;
                }
                $pid = (int) $p['id'];
                $emA = in_array($pid, array_map('intval', $partida['duplaA']));
                $emB = in_array($pid, array_map('intval', $partida['duplaB']));
                if (!$emA && !$emB) {
                    continue;
                }
                $pro    = $emA ? (int) $partida['placarA'] : (int) $partida['placarB'];
                $contra = $emA ? (int) $partida['placarB'] : (int) $partida['placarA'];
                $total += $pro;
                if ($pro > $contra) {
                    $total += 2;
                } elseif ($pro === $contra) {
                    $total += 1;
                }
            }
        }
        $serie[] = $total;
    }
    $dadosGrafico['jogadores'][] = ['nome' => nome_exibicao($p), 'pontos' => $serie];
}

$mensagem = isset($_GET['msg']) ? $_GET['msg'] : null;

$formato = $config['formato'] ?? 'rotativas';
$duplasFixas = $config['duplas_fixas'] ?? [];

// Monta ranking de duplas (apenas no formato de duplas fixas)
$rankingDuplas = [];
if ($formato === 'fixas' && !empty($duplasFixas)) {
    $statsPorId = [];
    foreach ($classificacao as $stat) {
        $statsPorId[$stat['id']] = $stat;
    }

    foreach ($duplasFixas as $dupla) {
        $j1 = $statsPorId[$dupla[0]];
        $j2 = $statsPorId[$dupla[1]];
        $rankingDuplas[] = [
            'nomes' => nome_exibicao(obter_jogador($participantes, $dupla[0])) . ' / ' . nome_exibicao(obter_jogador($participantes, $dupla[1])),
            'jogos' => $j1['jogos'],
            'vitorias' => $j1['vitorias'],
            'empates' => $j1['empates'],
            'derrotas' => $j1['derrotas'],
            'games_pro' => $j1['games_pro'],
            'games_contra' => $j1['games_contra'],
            'pontos' => $j1['pontos'] + $j2['pontos'],
        ];
    }

    usort($rankingDuplas, function ($x, $y) {
        if ($x['pontos'] !== $y['pontos']) {
            return $y['pontos'] - $x['pontos'];
        }
        $saldoX = $x['games_pro'] - $x['games_contra'];
        $saldoY = $y['games_pro'] - $y['games_contra'];
        return $saldoY - $saldoX;
    });
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classificação - Super 8</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="topo no-print">
        <h1><?= icone('praia', 'icone-grande') ?>Super 8 - Beach Tennis</h1>
        <p>Etapa 4: Classificação</p>
    </header>
    <main>
        <div class="card">
            <?php if ($mensagem): ?>
                <div class="alerta sucesso"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <h2>Classificação Individual</h2>
            <div class="tabela-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Pos.</th>
                            <th>Jogador</th>
                            <th>PJ</th>
                            <th>V</th>
                            <th>E</th>
                            <th>D</th>
                            <th>Games Pró</th>
                            <th>Games Contra</th>
                            <th>Saldo</th>
                            <th>Pontos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classificacao as $pos => $stat): ?>
                            <tr>
                                <td><?= $pos + 1 ?>º</td>
                                <td style="text-align:left"><?= htmlspecialchars($stat['apelido'] ?: $stat['nome']) ?></td>
                                <td><?= $stat['jogos'] ?></td>
                                <td><?= $stat['vitorias'] ?></td>
                                <td><?= $stat['empates'] ?></td>
                                <td><?= $stat['derrotas'] ?></td>
                                <td><?= $stat['games_pro'] ?></td>
                                <td><?= $stat['games_contra'] ?></td>
                                <td><?= $stat['games_pro'] - $stat['games_contra'] ?></td>
                                <td><?= $stat['pontos'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($rankingDuplas)): ?>
                <h2>Classificação por Dupla (Duplas Fixas)</h2>
                <div class="tabela-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Pos.</th>
                                <th>Dupla</th>
                                <th>PJ</th>
                                <th>V</th>
                                <th>E</th>
                                <th>D</th>
                                <th>Games Pró</th>
                                <th>Games Contra</th>
                                <th>Saldo</th>
                                <th>Pontos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rankingDuplas as $pos => $d): ?>
                                <tr>
                                    <td><?= $pos + 1 ?>º</td>
                                    <td style="text-align:left"><?= htmlspecialchars($d['nomes']) ?></td>
                                    <td><?= $d['jogos'] ?></td>
                                    <td><?= $d['vitorias'] ?></td>
                                    <td><?= $d['empates'] ?></td>
                                    <td><?= $d['derrotas'] ?></td>
                                    <td><?= $d['games_pro'] ?></td>
                                    <td><?= $d['games_contra'] ?></td>
                                    <td><?= $d['games_pro'] - $d['games_contra'] ?></td>
                                    <td><?= $d['pontos'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="regras">
                <strong>Critérios de pontuação:</strong>
                Vitória na partida = +2 pontos por jogador da dupla vencedora;
                Empate = +1 ponto por jogador de cada dupla;
                Derrota = +0 pontos;
                cada game vencido soma +1 ponto ao jogador.
                <br>
                <strong>Critério de desempate:</strong> 1) maior pontuação; 2) maior saldo de games (pró - contra); 3) maior número de games vencidos.
            </div>

            <button onclick="window.print()" class="btn secundario no-print"><?= icone('print') ?>Imprimir / Exportar</button>
            <a href="../rodadas/rodadas.php" class="btn no-print">Voltar às Rodadas</a>
            <a href="../index.php" class="btn cinza no-print">Voltar ao Menu</a>
        </div>

        <div class="card no-print" id="card-grafico">
            <h2>Evolução de Pontuação por Rodada</h2>
            <canvas id="grafico-evolucao" width="800" height="280" style="width:100%;display:block"></canvas>
            <div id="legenda-grafico"></div>
        </div>
    </main>
    <script>window.dadosGrafico = <?= json_encode($dadosGrafico, JSON_UNESCAPED_UNICODE) ?>;</script>
    <script src="../js/ui.js"></script>
</body>
</html>
