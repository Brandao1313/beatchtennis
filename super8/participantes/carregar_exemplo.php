<?php
require_once __DIR__ . '/../utils/json_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

$perfis = [
    'sabado' => [
        ['nome' => 'Carlos Eduardo Lima', 'apelido' => 'Cadu'],
        ['nome' => 'Mariana Oliveira Souza', 'apelido' => 'Mari'],
        ['nome' => 'Rafael Augusto Santos', 'apelido' => 'Rafa'],
        ['nome' => 'Beatriz Carvalho Almeida', 'apelido' => 'Bia'],
        ['nome' => 'Lucas Henrique Pereira', 'apelido' => 'Lucão'],
        ['nome' => 'Fernanda Costa Ribeiro', 'apelido' => 'Fê'],
        ['nome' => 'Gustavo Martins Rocha', 'apelido' => 'Guga'],
        ['nome' => 'Juliana Alves Barbosa', 'apelido' => 'Ju'],
    ],
    'domingo' => [
        ['nome' => 'Pedro Henrique Nascimento', 'apelido' => 'Pedrinho'],
        ['nome' => 'Camila Fernandes Dias', 'apelido' => 'Camis'],
        ['nome' => 'Thiago Moreira Cardoso', 'apelido' => 'Tigão'],
        ['nome' => 'Larissa Gomes Teixeira', 'apelido' => 'Lari'],
        ['nome' => 'Bruno Henrique Castro', 'apelido' => 'Bruninho'],
        ['nome' => 'Amanda Ferreira Lopes', 'apelido' => 'Mandy'],
        ['nome' => 'Diego Silva Monteiro', 'apelido' => 'Dieguinho'],
        ['nome' => 'Patricia Ramos Azevedo', 'apelido' => 'Pati'],
    ],
];

$perfilEscolhido = $_POST['perfil'] ?? '';
if (!isset($perfis[$perfilEscolhido])) {
    header('Location: cadastro.php?erro=' . urlencode('Perfil de teste inválido.'));
    exit;
}

$participantes = [];
foreach ($perfis[$perfilEscolhido] as $i => $jogador) {
    $participantes[] = [
        'id' => $i + 1,
        'nome' => $jogador['nome'],
        'apelido' => $jogador['apelido'],
    ];
}

gravar_json(caminho_participantes(), ['participantes' => $participantes]);

// Configuração/rodadas anteriores ficam inválidas com novos participantes
foreach ([caminho_config(), caminho_rodadas()] as $arquivo) {
    if (file_exists($arquivo)) {
        unlink($arquivo);
    }
}

header('Location: cadastro.php?msg=' . urlencode('Perfil de teste carregado com sucesso. Revise os dados e salve para continuar.'));
exit;
