<?php
/**
 * dashboard.php
 *
 * Painel do administrador (RF06). Exige administrador ativo (RN03):
 * sem login vai para login.php, e os demais perfis vão para inicio.php.
 * Mostra totais de pedidos, produtos e clientes, o faturamento (RN04) e a
 * lista dos pedidos em aberto (Pendente, Pago ou Em Transporte).
 *
 * @author João Gabriel
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/funcoes.php';

// Exige administrador ativo: sem login vai para login.php, e os demais para inicio.php
exigir_admin($conn);

$totalPedidos  = 0;
$totalProdutos = 0;
$totalClientes = 0;
$faturamento   = 0.0;
$pedidos       = [];

try {
    // Total de pedidos
    $totalPedidos = (int) $conn->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();

    // Total de produtos cadastrados
    $totalProdutos = (int) $conn->query("SELECT COUNT(*) FROM produtos")->fetchColumn();

    // Total de clientes ativos (perfil 'cliente')
    $totalClientes = (int) $conn->query(
        "SELECT COUNT(*) FROM usuarios u
         JOIN perfis p ON p.id_perfil = u.id_perfil
         WHERE p.nome = 'cliente' AND u.status = 'Ativo'"
    )->fetchColumn();

    // Faturamento: só conta pedidos já pagos, em transporte ou entregues
    // (Pendente ainda não foi pago e Cancelado não entra)
    $faturamento = (float) $conn->query(
        "SELECT COALESCE(SUM(valor_total), 0) FROM pedidos
         WHERE status_pedido IN ('Pago', 'Em Transporte', 'Entregue')"
    )->fetchColumn();

    // Pedidos restantes: ainda não finalizados (nem entregues, nem cancelados)
    $stmt = $conn->query(
        "SELECT p.id_pedido, u.nome AS cliente, p.valor_total, p.status_pedido, p.data_do_pedido
         FROM pedidos p
         JOIN usuarios u ON u.id_usuario = p.id_usuario
         WHERE p.status_pedido IN ('Pendente', 'Pago', 'Em Transporte')
         ORDER BY p.data_do_pedido DESC
         LIMIT 15"
    );
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro no dashboard: " . $e->getMessage());
}

/**
 * Transforma o status do pedido no nome da classe CSS da etiqueta colorida.
 *
 * Exemplo: "Em Transporte" vira "status-em-transporte". As cores de cada
 * classe ficam no style.css.
 *
 * @param string $status Valor de pedidos.status_pedido.
 * @return string Nome da classe, como "status-pago".
 */
function classeStatus(string $status): string {
    return 'status-' . str_replace(' ', '-', strtolower($status));
}

include 'header.php';
?>

<main>

    <div class="indicador">
        <div class="kpi">
            <h3>Pedidos</h3>
            <strong><?= $totalPedidos ?></strong>
        </div>

        <div class="kpi">
            <h3>Produtos</h3>
            <strong><?= $totalProdutos ?></strong>
        </div>

        <div class="kpi">
            <h3>Clientes</h3>
            <strong><?= $totalClientes ?></strong>
        </div>

        <div class="kpi">
            <h3>Faturamento</h3>
            <strong>R$ <?= number_format($faturamento, 2, ',', '.') ?></strong>
        </div>

        <h5>Pedidos Restantes</h5>
    </div>

    <table>
        <thead>
            <tr>
                <th>pedido</th>
                <th>cliente</th>
                <th>data</th>
                <th>valor</th>
                <th>status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pedidos)): ?>
                <tr>
                    <td colspan="5">Nenhum pedido em aberto.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td>#<?= (int) $p['id_pedido'] ?></td>
                        <td><?= htmlspecialchars($p['cliente']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['data_do_pedido'])) ?></td>
                        <td>R$ <?= number_format($p['valor_total'], 2, ',', '.') ?></td>
                        <td>
                            <span class="status <?= classeStatus($p['status_pedido']) ?>">
                                <?= htmlspecialchars($p['status_pedido']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</main>

<?php include 'footer.php'; ?>
