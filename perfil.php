<?php include 'header.php'; ?>

<div class="container_perfil">
    <h2>Editar Perfil</h2>
    
    <form method="POST" action="">

        <h3>Dados Pessoais</h3>
        <div class="form-grupo">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" placeholder="Maromba da Silva" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="yeshua@email.com" required>

            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>

            <label for="telefone">Telefone / WhatsApp</label>
            <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" required>
        </div>

        <h3>Endereço</h3>
        <div class="form-grupo">
            <label for="cep">CEP</label>
            <input type="text" id="cep" name="cep" placeholder="00000-000" required>

            <label for="rua">Rua</label>
            <input type="text" id="rua" name="rua" placeholder="Rua do Jasmim" required>

            <label for="numero">Número</label>
            <input type="text" id="numero" name="numero" placeholder="123" required>

            <label for="bairro">Bairro</label>
            <input type="text" id="bairro" name="bairro" placeholder="Centro" required>

            <label for="cidade">Cidade</label>
            <input type="text" id="cidade" name="cidade" placeholder="Sua cidade" required>
        </div>

        <button type="submit" class="btn-salvar">Salvar Alterações</button>
    </form>
</div>

<?php include 'footer.php'; ?>