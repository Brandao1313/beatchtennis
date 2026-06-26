<?php
/**
 * Regras de pontuação do Super 8 (definidas pelo sistema):
 *
 *   - Vitória na partida ........ +2 pontos por jogador da dupla vencedora
 *   - Empate na partida .......... +1 ponto por jogador de cada dupla
 *   - Derrota na partida ......... +0 pontos
 *   - Cada game vencido ........... +1 ponto por jogador (cumulativo)
 *
 * Critério de desempate (nesta ordem):
 *   1) Maior número de pontos
 *   2) Maior saldo de games (games vencidos - games perdidos)
 *   3) Maior número de games vencidos
 */

function calcular_classificacao($participantes, $rodadasData) {
    $stats = [];

    foreach ($participantes as $p) {
        $stats[$p['id']] = [
            'id' => $p['id'],
            'nome' => $p['nome'],
            'apelido' => $p['apelido'],
            'jogos' => 0,
            'vitorias' => 0,
            'empates' => 0,
            'derrotas' => 0,
            'games_pro' => 0,
            'games_contra' => 0,
            'pontos' => 0,
        ];
    }

    if ($rodadasData && !empty($rodadasData['rodadas'])) {
        foreach ($rodadasData['rodadas'] as $rodada) {
            foreach ($rodada['partidas'] as $partida) {
                if ($partida['placarA'] === null || $partida['placarB'] === null) {
                    continue;
                }

                $golsA = (int) $partida['placarA'];
                $golsB = (int) $partida['placarB'];

                aplicar_resultado_dupla($stats, $partida['duplaA'], $golsA, $golsB);
                aplicar_resultado_dupla($stats, $partida['duplaB'], $golsB, $golsA);
            }
        }
    }

    $lista = array_values($stats);

    usort($lista, function ($x, $y) {
        if ($x['pontos'] !== $y['pontos']) {
            return $y['pontos'] - $x['pontos'];
        }
        $saldoX = $x['games_pro'] - $x['games_contra'];
        $saldoY = $y['games_pro'] - $y['games_contra'];
        if ($saldoX !== $saldoY) {
            return $saldoY - $saldoX;
        }
        return $y['games_pro'] - $x['games_pro'];
    });

    return $lista;
}

function aplicar_resultado_dupla(&$stats, $jogadores, $gamesPro, $gamesContra) {
    foreach ($jogadores as $jid) {
        if (!isset($stats[$jid])) {
            continue;
        }
        $stats[$jid]['jogos']++;
        $stats[$jid]['games_pro'] += $gamesPro;
        $stats[$jid]['games_contra'] += $gamesContra;
        $stats[$jid]['pontos'] += $gamesPro;

        if ($gamesPro > $gamesContra) {
            $stats[$jid]['vitorias']++;
            $stats[$jid]['pontos'] += 2;
        } elseif ($gamesPro === $gamesContra) {
            $stats[$jid]['empates']++;
            $stats[$jid]['pontos'] += 1;
        } else {
            $stats[$jid]['derrotas']++;
        }
    }
}

// Retorna o número da próxima rodada com placares pendentes, ou null se o torneio acabou
function rodada_pendente($rodadasData) {
    if (!$rodadasData || empty($rodadasData['rodadas'])) {
        return null;
    }
    foreach ($rodadasData['rodadas'] as $rodada) {
        foreach ($rodada['partidas'] as $partida) {
            if ($partida['placarA'] === null || $partida['placarB'] === null) {
                return $rodada['numero'];
            }
        }
    }
    return null;
}

// Verifica se todas as partidas de uma rodada possuem placar lançado
function rodada_completa($rodada) {
    foreach ($rodada['partidas'] as $partida) {
        if ($partida['placarA'] === null || $partida['placarB'] === null) {
            return false;
        }
    }
    return true;
}
