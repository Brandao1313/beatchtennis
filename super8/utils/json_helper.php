<?php
/**
 * Funções utilitárias para leitura e gravação de arquivos JSON.
 * Toda persistência do sistema passa por aqui.
 */

function ler_json($caminho) {
    if (!file_exists($caminho)) {
        return null;
    }
    $conteudo = file_get_contents($caminho);
    if ($conteudo === false || trim($conteudo) === '') {
        return null;
    }
    return json_decode($conteudo, true);
}

function gravar_json($caminho, $dados) {
    $pasta = dirname($caminho);
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }
    $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($caminho, $json) !== false;
}

// Retorna o apelido do jogador (ou o nome, se não houver apelido)
function nome_exibicao($jogador) {
    if (!empty($jogador['apelido'])) {
        return $jogador['apelido'];
    }
    return $jogador['nome'];
}

// Busca um jogador pelo id dentro da lista de participantes
function obter_jogador($participantes, $id) {
    foreach ($participantes as $p) {
        if ((int) $p['id'] === (int) $id) {
            return $p;
        }
    }
    return null;
}

// Caminhos padrão dos arquivos de dados
function caminho_participantes() {
    return __DIR__ . '/../data/participantes.json';
}

function caminho_config() {
    return __DIR__ . '/../data/config.json';
}

function caminho_rodadas() {
    return __DIR__ . '/../data/rodadas.json';
}
