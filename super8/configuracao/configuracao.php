<?php
require_once __DIR__ . '/../utils/json_helper.php';
require_once __DIR__ . '/../utils/icones.php';

$dados = ler_json(caminho_participantes());
if (!$dados || count($dados['participantes']) !== 8) {
    header('Location: ../participantes/cadastro.php');
    exit;
}
$participantes = $dados['participantes'];

$erro = isset($_GET['erro']) ? $_GET['erro'] : null;

$configAtual = ler_json(caminho_config());
$formatoAtual = $configAtual['formato'] ?? 'rotativas';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração do Torneio - Super 8</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="topo">
        <h1><?= icone('praia', 'icone-grande') ?>Super 8 - Beach Tennis</h1>
        <p>Etapa 2: Formato do torneio</p>
    </header>
    <main>
        <div class="card">
            <?php if ($erro): ?>
                <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <h2>Escolha o Formato de Jogo</h2>
            <form action="gerar_rodadas.php" method="post" id="form-config">
                <fieldset>
                    <legend>Formato</legend>

                    <label>
                        <input type="radio" name="formato" value="rotativas" id="formato-rotativas"
                            <?= $formatoAtual === 'rotativas' ? 'checked' : '' ?>>
                        Duplas Rotativas (Rei/Rainha da Quadra) — as duplas mudam a cada rodada
                    </label>

                    <label>
                        <input type="radio" name="formato" value="fixas" id="formato-fixas"
                            <?= $formatoAtual === 'fixas' ? 'checked' : '' ?>>
                        Duplas Fixas — 4 duplas definidas agora, jogam todos contra todos
                    </label>
                </fieldset>

                <fieldset id="bloco-duplas-fixas">
                    <legend>Defina as 4 duplas fixas</legend>
                    <?php for ($d = 1; $d <= 4; $d++):
                        $padrao1 = $participantes[($d - 1) * 2]['id'];
                        $padrao2 = $participantes[($d - 1) * 2 + 1]['id'];
                    ?>
                        <p><strong>Dupla <?= $d ?></strong></p>
                        <div class="confronto">
                            <select name="dupla<?= $d ?>_j1" class="dupla">
                                <?php foreach ($participantes as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $p['id'] == $padrao1 ? 'selected' : '' ?>><?= htmlspecialchars(nome_exibicao($p)) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="versus">+</span>
                            <select name="dupla<?= $d ?>_j2" class="dupla">
                                <?php foreach ($participantes as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $p['id'] == $padrao2 ? 'selected' : '' ?>><?= htmlspecialchars(nome_exibicao($p)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endfor; ?>
                    <p class="status">Cada um dos 8 jogadores deve ser selecionado em exatamente uma dupla.</p>
                </fieldset>

                <button type="submit" class="btn">Gerar Rodadas</button>
                <a href="../participantes/cadastro.php" class="btn cinza">Voltar</a>
            </form>
        </div>
    </main>
    <script src="../js/ui.js"></script>
</body>
</html>
