<?php
/**
 * Ícones SVG embutidos (sem dependências externas), usados no lugar de emojis.
 * Uso: <?= icone('check') ?>  ou  <?= icone('praia', 'icone-grande') ?>
 */

function icone($nome, $classe = '') {
    $classes = trim('icone ' . $classe);
    $svgs = [
        'praia' => '<path d="M12 22s7-4.5 7-11a7 7 0 0 0-14 0c0 6.5 7 11 7 11Z"/><path d="M5 11h14"/><path d="M12 4v7"/>',
        'check' => '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>',
        'pendente' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>',
        'trofeu' => '<path d="M8 4h8v4a4 4 0 0 1-8 0V4Z"/><path d="M8 5H4v2a4 4 0 0 0 4 3"/><path d="M16 5h4v2a4 4 0 0 1-4 3"/><path d="M12 13v3"/><path d="M9 20h6"/><path d="M10 17h4v3h-4z"/>',
        'print' => '<path d="M6 9V3h12v6"/><rect x="6" y="13" width="12" height="8"/><path d="M6 17H4a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-2"/>',
        'dados' => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8" cy="8" r="1"/><circle cx="16" cy="8" r="1"/><circle cx="8" cy="16" r="1"/><circle cx="16" cy="16" r="1"/><circle cx="12" cy="12" r="1"/>',
    ];

    if (!isset($svgs[$nome])) {
        return '';
    }

    return '<svg class="' . htmlspecialchars($classes) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
        . 'stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . $svgs[$nome] . '</svg>';
}
