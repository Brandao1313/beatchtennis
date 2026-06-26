# Super 8 - Beach Tennis

Sistema web para organizar, acompanhar e gerar a classificação de um torneio
Super 8 de beach tennis. Toda a lógica de negócio (sorteio de duplas, geração
de rodadas, cálculo de pontuação) é feita em PHP. A persistência dos dados é
feita em arquivos JSON, sem uso de banco de dados.

## Como rodar localmente

É necessário ter o PHP instalado (versão 7.4 ou superior).

```bash
cd super8
php -S localhost:8000
```

Depois acesse [http://localhost:8000](http://localhost:8000) no navegador.

A pasta `data/` é criada automaticamente na primeira gravação. Garanta que o
PHP tenha permissão de escrita nessa pasta.

## Fluxo de uso

1. **Cadastro de Participantes** – cadastre os 8 jogadores (nome obrigatório,
   apelido opcional). Os dados são salvos em `data/participantes.json`.
2. **Configuração do Formato** – escolha entre **Duplas Rotativas** (sorteio
   automático a cada rodada, ninguém repete parceiro) ou **Duplas Fixas**
   (você define as 4 duplas, que jogam entre si). Ao confirmar, as 7 rodadas
   são geradas em `data/rodadas.json`.
3. **Rodadas e Placares** – navegue entre as 7 rodadas, lance o placar de
   cada partida (games de 0 a 7) e salve. É possível voltar e editar o
   placar de uma rodada já lançada — a classificação é recalculada
   automaticamente.
4. **Classificação** – tabela atualizada em tempo real, ordenada pela
   pontuação. No formato de duplas fixas também é exibido o ranking por
   dupla. Há um botão para impressão/exportação.

No menu principal há também a opção **Reiniciar Torneio**, que apaga todos
os arquivos de dados para começar um novo evento.

## Regras de pontuação

| Critério               | Pontos                          |
|-------------------------|----------------------------------|
| Vitória na partida       | +2 pontos por jogador da dupla   |
| Empate na partida         | +1 ponto por jogador de cada dupla |
| Derrota na partida        | +0 pontos                        |
| Cada game vencido          | +1 ponto por jogador             |

## Critério de desempate

1. Maior pontuação total
2. Maior saldo de games (games vencidos − games perdidos)
3. Maior número de games vencidos

## Algoritmo de sorteio

- **Duplas Rotativas**: utiliza o método do círculo (round robin) para 8
  jogadores. Em 7 rodadas, cada jogador forma dupla com cada um dos outros 7
  jogadores exatamente uma vez, e nunca é escalado em duas partidas da mesma
  rodada.
- **Duplas Fixas**: as 4 duplas se enfrentam em todos-contra-todos (3
  confrontos possíveis por rodada-base), repetindo o ciclo até completar as
  7 rodadas exigidas pelo formato Super 8.

## Persistência

Como toda a leitura/escrita é feita em PHP nos arquivos JSON da pasta
`super8/data/`, se o organizador fechar o navegador no meio do torneio os
dados permanecem salvos e o torneio pode ser retomado de onde parou ao
reabrir o sistema.

## Estrutura de pastas

```
super8/
├── index.php
├── participantes/
│   ├── cadastro.php
│   └── salvar_participantes.php
├── configuracao/
│   ├── configuracao.php
│   └── gerar_rodadas.php
├── rodadas/
│   ├── rodadas.php
│   └── salvar_placar.php
├── classificacao/
│   └── classificacao.php
├── utils/
│   ├── json_helper.php
│   ├── pontuacao.php
│   └── sorteio.php
├── css/style.css
├── js/ui.js
└── data/ (gerado automaticamente)
```
