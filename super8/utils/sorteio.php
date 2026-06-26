<?php
/**
 * Algoritmos de geração de confrontos para o Super 8.
 *
 * Duplas Rotativas: usa o método do "círculo" (round robin) para 8 jogadores,
 * garantindo que cada jogador forme dupla com cada outro jogador exatamente
 * uma vez ao longo das 7 rodadas (7 = C(8,2) / 4 pares por rodada).
 *
 * Duplas Fixas: 4 duplas se enfrentam em todos-contra-todos (3 confrontos
 * possíveis), repetindo o ciclo até completar as 7 rodadas exigidas.
 */

function gerar_rodadas_rotativas($idsParticipantes) {
    $arr = array_values($idsParticipantes); // 8 ids
    $rodadas = [];

    for ($r = 0; $r < 7; $r++) {
        $pares = [
            [$arr[0], $arr[7]],
            [$arr[1], $arr[6]],
            [$arr[2], $arr[5]],
            [$arr[3], $arr[4]],
        ];

        $partidas = [
            [
                'quadra' => 1,
                'duplaA' => $pares[0],
                'duplaB' => $pares[1],
                'placarA' => null,
                'placarB' => null,
            ],
            [
                'quadra' => 2,
                'duplaA' => $pares[2],
                'duplaB' => $pares[3],
                'placarA' => null,
                'placarB' => null,
            ],
        ];

        $rodadas[] = [
            'numero' => $r + 1,
            'partidas' => $partidas,
        ];

        // Rotaciona mantendo arr[0] fixo
        $ultimo = $arr[7];
        for ($i = 7; $i > 1; $i--) {
            $arr[$i] = $arr[$i - 1];
        }
        $arr[1] = $ultimo;
    }

    return $rodadas;
}

function gerar_rodadas_fixas($duplas) {
    // Confrontos possíveis entre 4 duplas (índices 0..3), todos contra todos
    $confrontos = [
        [[0, 1], [2, 3]],
        [[0, 2], [1, 3]],
        [[0, 3], [1, 2]],
    ];

    $rodadas = [];

    for ($r = 0; $r < 7; $r++) {
        $c = $confrontos[$r % 3];

        $partidas = [
            [
                'quadra' => 1,
                'duplaA' => $duplas[$c[0][0]],
                'duplaB' => $duplas[$c[0][1]],
                'placarA' => null,
                'placarB' => null,
            ],
            [
                'quadra' => 2,
                'duplaA' => $duplas[$c[1][0]],
                'duplaB' => $duplas[$c[1][1]],
                'placarA' => null,
                'placarB' => null,
            ],
        ];

        $rodadas[] = [
            'numero' => $r + 1,
            'partidas' => $partidas,
        ];
    }

    return $rodadas;
}
