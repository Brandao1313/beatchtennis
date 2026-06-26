// Interações visuais do Super 8 - Beach Tennis
// Toda a lógica de torneio (sorteio, rodadas, pontuação) fica no PHP.
// Aqui ficam apenas validações simples de formulário e pequenos ajustes de UX.

document.addEventListener('DOMContentLoaded', function () {

    // Tela de configuração: mostra/oculta o bloco de duplas fixas
    // e valida que os 8 jogadores aparecem em exatamente uma dupla.
    var formConfig = document.getElementById('form-config');
    if (formConfig) {
        var radioRotativas = document.getElementById('formato-rotativas');
        var radioFixas = document.getElementById('formato-fixas');
        var blocoDuplas = document.getElementById('bloco-duplas-fixas');

        function atualizarBloco() {
            blocoDuplas.style.display = (radioFixas && radioFixas.checked) ? '' : 'none';
        }

        if (radioRotativas && radioFixas && blocoDuplas) {
            radioRotativas.addEventListener('change', atualizarBloco);
            radioFixas.addEventListener('change', atualizarBloco);
            atualizarBloco();
        }

        formConfig.addEventListener('submit', function (e) {
            if (!radioFixas || !radioFixas.checked) {
                return;
            }
            var selects = blocoDuplas.querySelectorAll('select.dupla');
            var valores = [];
            selects.forEach(function (s) { valores.push(s.value); });
            var unicos = new Set(valores);
            if (unicos.size !== 8) {
                e.preventDefault();
                alert('Cada um dos 8 jogadores deve ser selecionado em exatamente uma dupla, sem repetições.');
            }
        });
    }

    // Tela de rodadas: confirma antes de reenviar um placar já lançado
    var placares = document.querySelectorAll('.placar-input');
    placares.forEach(function (input) {
        if (input.value !== '') {
            input.dataset.original = input.value;
        }
    });
});
