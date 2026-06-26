<?php
require_once __DIR__ . '/../utils/json_helper.php';
require_once __DIR__ . '/../utils/icones.php';

$dadosExistentes = ler_json(caminho_participantes());
$lista = [];
if ($dadosExistentes && !empty($dadosExistentes['participantes'])) {
    $lista = $dadosExistentes['participantes'];
}

$erro = isset($_GET['erro']) ? $_GET['erro'] : null;
$mensagem = isset($_GET['msg']) ? $_GET['msg'] : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Participantes - Super 8</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="topo">
        <h1><?= icone('praia', 'icone-grande') ?>Super 8 - Beach Tennis</h1>
        <p>Etapa 1: Cadastro dos 8 participantes</p>
    </header>
    <main>
        <div class="card">
            <?php if ($erro): ?>
                <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <?php if ($mensagem): ?>
                <div class="alerta sucesso"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <h2>Cadastro de Participantes</h2>
            <p>Informe o nome completo (obrigatório) e o apelido/nickname (opcional) dos 8 jogadores.</p>

            <fieldset class="no-print">
                <legend><?= icone('dados') ?>Perfis de Teste</legend>
                <p class="status">Carregue um conjunto de 8 jogadores de exemplo para testar o sistema rapidamente.</p>
                <form action="carregar_exemplo.php" method="post" class="confronto">
                    <select name="perfil" class="dupla">
                        <option value="sabado">Turma de Sábado</option>
                        <option value="domingo">Torneio de Domingo</option>
                    </select>
                    <button type="submit" class="btn secundario">Carregar Perfil</button>
                </form>
            </fieldset>

            <form action="salvar_participantes.php" method="post" id="form-cadastro">
                <?php for ($i = 1; $i <= 8; $i++): ?>
                    <fieldset>
                        <legend>Jogador <?= $i ?></legend>
                        <label for="nome_<?= $i ?>">Nome completo</label>
                        <input type="text" id="nome_<?= $i ?>" name="nome_<?= $i ?>" required
                               value="<?= htmlspecialchars($lista[$i - 1]['nome'] ?? '') ?>">

                        <label for="apelido_<?= $i ?>">Apelido / Nickname (opcional)</label>
                        <input type="text" id="apelido_<?= $i ?>" name="apelido_<?= $i ?>"
                               value="<?= htmlspecialchars($lista[$i - 1]['apelido'] ?? '') ?>">
                    </fieldset>
                <?php endfor; ?>

                <button type="submit" class="btn">Salvar Participantes</button>
                <a href="../index.php" class="btn cinza">Voltar ao Menu</a>
            </form>
        </div>
    </main>
    <script src="../js/ui.js"></script>
</body>
</html>
