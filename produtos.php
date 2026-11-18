
<?php
/**
 * produtos.php
 
 * Catálogo de produtos (RF03), dividido por categoria. Cada categoria vira uma
 * seção com id, para o menu do header.php rolar até ela (#creatinas etc.)
 * A lista de produtos está no array $categorias, no topo do arquivo
 
 * @todo Ler os produtos da tabela produtos, em vez de usar o array fixo
 *
 * @author João Gabriel
 */
// Cada categoria vira uma seção com id, para o menu do header rolar até ela.
// Os ids precisam bater com os links do header (#suplementos, #creatinas...).
$categorias = [
    'suplementos' => [
        'titulo' => 'Suplementos',
        'produtos' => [
            ['Whey Protein', 'Proteína de alta qualidade', 99.00, 'whey.jpg'],
        ],
    ],
    'creatinas' => [
        'titulo' => 'Creatinas',
        'produtos' => [
            ['Creatina', 'Suplemento para performance', 70.00, 'creatina.jpg'],
        ],
    ],
    'pre-treinos' => [
        'titulo' => 'Pré-treinos',
        'produtos' => [
            ['Pré-Treino', 'Energia para seu treino', 119.00, 'pre-treino.jpg'],
        ],
    ],
    'vitaminas' => [
        'titulo' => 'Vitaminas',
        'produtos' => [
            ['Vitaminas', 'Suplemento para reposição', 90.00, 'vitaminas.jpg'],
        ],
    ],
    'acessorios' => [
        'titulo' => 'Acessórios',
        'produtos' => [
            ['Strap', 'Acessório para musculação', 50.00, 'strap.jpg'],
            ['Cinto para academia', 'Acessório para musculação', 150.00, 'cinto.jpg'],
        ],
    ],
];
 
include 'header.php';
?>
 
<section class="produtos">
    <h2>Produtos</h2>
 
    <?php foreach ($categorias as $id => $categoria): ?>
        <h3 id="<?= $id ?>" class="categoria-titulo"><?= htmlspecialchars($categoria['titulo']) ?></h3>
 
        <div class="cards">
            <?php foreach ($categoria['produtos'] as [$nome, $descricao, $preco, $imagem]): ?>
                <div class="card">
                    <img src="assets/IMG/<?= $imagem ?>" alt="<?= htmlspecialchars($nome) ?>">
                    <h3><?= htmlspecialchars($nome) ?></h3>
                    <p><?= htmlspecialchars($descricao) ?></p>
                    <strong>R$ <?= number_format($preco, 2, ',', '.') ?></strong>
                    <button type="button">Comprar</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</section>
 
<?php include 'footer.php'; ?>
 