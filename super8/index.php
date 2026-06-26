<?php
require_once __DIR__ . '/utils/json_helper.php';
require_once __DIR__ . '/utils/pontuacao.php';
require_once __DIR__ . '/utils/icones.php';

$participantes = ler_json(caminho_participantes());
$config = ler_json(caminho_config());
$rodadasData = ler_json(caminho_rodadas());

$temParticipantes = $participantes && count($participantes['participantes']) === 8;
$temConfig = $config !== null;
$temRodadas = $rodadasData && !empty($rodadasData['rodadas']);

$rodadaPendente = $temRodadas ? rodada_pendente($rodadasData) : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super 8 - Beach Tennis</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="topo">
        <h1><?= icone('praia', 'icone-grande') ?>Super 8 - Beach Tennis</h1>
        <p>Sistema de classificação para torneios Super 8</p>
    </header>
    <main>
        <div class="card">
            <h2>Menu Principal</h2>
            <ul class="menu-lista">
                <li>
                    <a href="participantes/cadastro.php">1. Cadastrar Participantes</a>
                    <div class="status">
                        <?= $temParticipantes ? icone('check') . '8 participantes cadastrados' : icone('pendente') . 'Aguardando cadastro' ?>
                    </div>
                </li>
                <li>
                    <a href="<?= $temParticipantes ? 'configuracao/configuracao.php' : '#' ?>"
                       class="<?= $temParticipantes ? '' : 'desabilitado' ?>">2. Configurar Formato</a>
                    <div class="status">
                        <?php if ($temConfig): ?>
                            <?= icone('check') ?>Formato escolhido: <?= $config['formato'] === 'fixas' ? 'Duplas Fixas' : 'Duplas Rotativas' ?>
                        <?php else: ?>
                            <?= icone('pendente') ?>Formato ainda não definido
                        <?php endif; ?>
                    </div>
                </li>
                <li>
                    <a href="<?= $temRodadas ? 'rodadas/rodadas.php' : '#' ?>"
                       class="<?= $temRodadas ? '' : 'desabilitado' ?>">3. Rodadas e Placares</a>
                    <div class="status">
                        <?php if ($temRodadas): ?>
                            <?= $rodadaPendente ? icone('pendente') . "Rodada $rodadaPendente de 7 pendente" : icone('check') . 'Todas as rodadas concluídas' ?>
                        <?php else: ?>
                            <?= icone('pendente') ?>Rodadas ainda não geradas
                        <?php endif; ?>
                    </div>
                </li>
                <li>
                    <a href="<?= $temRodadas ? 'classificacao/classificacao.php' : '#' ?>"
                       class="<?= $temRodadas ? '' : 'desabilitado' ?>">4. Classificação</a>
                    <div class="status">Ranking atualizado em tempo real</div>
                </li>
            </ul>
        </div>

        <div class="card">
            <h2>Reiniciar Torneio</h2>
            <p>Apaga todos os dados (participantes, configuração e rodadas) para começar um novo evento.</p>
            <form action="reset.php" method="post" onsubmit="return confirm('Tem certeza que deseja reiniciar o torneio? Todos os dados serão apagados.');">
                <button type="submit" class="btn perigo">Reiniciar Torneio</button>
            </form>
        </div>
    </main>
</body>
</html>
