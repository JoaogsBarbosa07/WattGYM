<?php include 'header.php'; ?>

<div class="cadastro">
    <form action="inicio.php" id="formCadastro">
        <h2>Cadastro</h2>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="yahweh@gmail.com" required>

        <label for="senha">Senha</label>
        <div class="cadastro-senha-container">
            <input type="password" id="senha" name="senha" placeholder="********" required>
            <button type="button" id="mostrarSenha">
                <img id="iconeOlho" src="IMG/olho-fechado.png" alt="Mostrar senha">
            </button>
        </div>

        <label for="confirmarSenha">Confirmar senha:</label>
        <div class="cadastro-senha-container">
            <input type="password" id="confirmarSenha" name="confirmarSenha" placeholder="********" required>
            <button type="button" id="mostrarConfirmarSenha">
                <img id="iconeOlhoConfirmar" src="IMG/olho-fechado.png" alt="Mostrar senha">
            </button>
        </div>

        <button type="submit">Cadastrar</button>
    </form>
</div>

<?php include 'footer.php'; ?>