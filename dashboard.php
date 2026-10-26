<?php include 'header.php'; ?>

    <main>

<div class="indicador">

    <div class="kpi">
        <h3>Pedidos</h3>
        <strong>32</strong>
    </div>

    <div class="kpi">
        <h3>Produtos</h3>
        <strong>23</strong>
    </div>

    <div class="kpi">
        <h3>Clientes</h3>
        <strong>129</strong>
    </div>

    <div class="kpi">
        <h3>Faturamento</h3>
        <strong>R$ 2.322</strong>
    </div>

    <div>
    <h5>Pedidos Restantes</h5>
    <table>
            <thead>
                <tr>
                    <th>pedido</th>
                    <th>cliente</th>
                    <th>produto</th>
                    <th>valor</th>
                    <th>status</th>
                </tr>
            </thead>

        <tbody>

            <tr>
                <td>#01</td>
                <td>joao</td>
                <td>anabolizante</td>
                <td>R$ 58,00</td>
                <td><span class="status-pronto">pronto</span></td>
            </tr>

            <tr>
                <td>#02</td>
                <td>vitor</td>
                <td>whey</td>
                <td>R$ 90,00</td>
                <td><span class="status-preparando">preparando</span></td>
            </tr>

            <tr>
                <td>#03</td>
                <td>arthur</td>
                <td>carinho</td>
                <td>R$ 199,00</td>
                <td><span class="status-pronto">pronto</span></td>
            </tr>

        </tbody>

    </table>

    </main>

<?php include 'footer.php'; ?>