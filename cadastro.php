<?php include 'header.php'; ?>

    <div class="card-cadastro">

    <img src="assets/IMG/braco_esq.png" class="braco esquerda">
    <img src="assets/IMG/braco_dir.png" class="braco direita">

    <div class="formulario">

<div class="cadastro">
    <form action="inicio.php" id="formCadastro">
        <h2>Cadastro</h2>

    <div class="input-grupo">
        <div class="grupo">
            <label for="nome">nome</label>
            <input type="text" id="nome" name="nome" placeholder="Maromba da silva" required>
        </div>
        <div class="grupo">
            <label for="nascimento">data de nascimento</label>
            <input type="date" id="nascimento" name="nascimento" placeholder="dd/mm/aaaa" required>
        </div>
    </div>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="yahweh@gmail.com" required>

        <label for="senha">Senha</label>
        <div class="cadastro-senha-container">
            <input type="password" id="senha" name="senha" placeholder="********" required>
            <button type="button" id="mostrarSenha">
                <img id="iconeOlho" src="assets/IMG/olho-fechado.png" alt="Mostrar senha">
            </button>
        </div>

        <label for="confirmarSenha">Confirmar senha:</label>
        <div class="cadastro-senha-container">
            <input type="password" id="confirmarSenha" name="confirmarSenha" placeholder="********" required>
            <button type="button" id="mostrarConfirmarSenha">
                <img id="iconeOlhoConfirmar" src="assets/IMG/olho-fechado.png" alt="Mostrar senha">
            </button>
        </div>

        <button type="submit">Cadastrar</button>
        <p class="sobre-link">
            Já tem conta?
            <a href="login.php">Login</a>
        </p>
        </div>
    </form>
</div>


    </div>

</div>

<?php include 'footer.php'; ?>