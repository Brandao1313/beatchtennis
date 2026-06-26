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

    // Tela de rodadas: marca os placares já lançados
    var placares = document.querySelectorAll('.placar-input');
    placares.forEach(function (input) {
        if (input.value !== '') {
            input.dataset.original = input.value;
        }
    });

    // Gráfico de evolução de pontuação (página de classificação)
    var canvasGraf = document.getElementById('grafico-evolucao');
    if (canvasGraf && window.dadosGrafico) {
        var dados = window.dadosGrafico;
        var temDados = dados.jogadores.some(function (j) {
            return j.pontos.some(function (p) { return p > 0; });
        });
        if (!temDados) {
            var cardGraf = document.getElementById('card-grafico');
            if (cardGraf) { cardGraf.style.display = 'none'; }
        } else {
            var cores = ['#0077b6', '#e63946', '#2a9d8f', '#e9c46a', '#f4a261', '#264653', '#457b9d', '#a8dadc'];
            var ctx = canvasGraf.getContext('2d');
            var W = canvasGraf.width;
            var H = canvasGraf.height;
            var pad = { top: 20, right: 20, bottom: 35, left: 42 };
            var cW = W - pad.left - pad.right;
            var cH = H - pad.top - pad.bottom;
            var nR = dados.rodadas.length;
            var maxPts = 1;
            dados.jogadores.forEach(function (j) {
                j.pontos.forEach(function (p) { if (p > maxPts) { maxPts = p; } });
            });

            function xP(i) { return pad.left + (i / (nR - 1)) * cW; }
            function yP(v) { return pad.top + cH - (v / maxPts) * cH; }

            ctx.fillStyle = '#f7f9fb';
            ctx.fillRect(0, 0, W, H);

            ctx.font = '11px Segoe UI, Arial, sans-serif';
            for (var g = 0; g <= 4; g++) {
                var gy = pad.top + (g / 4) * cH;
                ctx.strokeStyle = '#e0e6ec';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(pad.left, gy);
                ctx.lineTo(pad.left + cW, gy);
                ctx.stroke();
                ctx.fillStyle = '#6c757d';
                ctx.textAlign = 'right';
                ctx.fillText(Math.round(maxPts * (1 - g / 4)), pad.left - 5, gy + 4);
            }

            ctx.fillStyle = '#1d3557';
            ctx.textAlign = 'center';
            ctx.font = 'bold 11px Segoe UI, Arial, sans-serif';
            for (var ri = 0; ri < nR; ri++) {
                ctx.fillText('R' + (ri + 1), xP(ri), H - pad.bottom + 15);
            }

            dados.jogadores.forEach(function (j, idx) {
                var cor = cores[idx % cores.length];
                ctx.strokeStyle = cor;
                ctx.lineWidth = 2.5;
                ctx.lineJoin = 'round';
                ctx.beginPath();
                j.pontos.forEach(function (pts, i) {
                    if (i === 0) { ctx.moveTo(xP(i), yP(pts)); }
                    else { ctx.lineTo(xP(i), yP(pts)); }
                });
                ctx.stroke();
                j.pontos.forEach(function (pts, i) {
                    ctx.fillStyle = cor;
                    ctx.beginPath();
                    ctx.arc(xP(i), yP(pts), 4, 0, 2 * Math.PI);
                    ctx.fill();
                });
            });

            var leg = document.getElementById('legenda-grafico');
            if (leg) {
                dados.jogadores.forEach(function (j, idx) {
                    var cor = cores[idx % cores.length];
                    var span = document.createElement('span');
                    span.style.cssText = 'display:inline-flex;align-items:center;gap:5px;margin:5px 10px 0 0;font-size:0.82rem';
                    span.innerHTML = '<span style="display:inline-block;width:16px;height:4px;background:' + cor + ';border-radius:2px;flex-shrink:0"></span>' + j.nome;
                    leg.appendChild(span);
                });
            }
        }
    }
});
