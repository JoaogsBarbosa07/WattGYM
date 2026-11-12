<?php include 'header.php'; ?>

<div class="card-login">

    <img src="assets/IMG/braco_esq_login.png"
         class="braco_esquerdo">

    <img src="assets/IMG/braco_dir_login.png"
         class="braco_direito">

    <div class="formulario_login">

<div class="login">
        <form action="dashboard.php">
        <h2>Login</h2>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Digite seu email">

        <label for="senha">Senha</label>
        <div class="senha-container">
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha">
            <button type="button" id="mostrarSenha">
                <img id="iconeOlho" src="IMG/olho-fechado.png" alt="Mostrar senha">
            </button>
        </div>

        <p class="sobre-link">
            Não tem conta?
            <a href="cadastro.php">Cadastre-se</a>
        </p>

        <button type="submit">Entrar</button>
    </form>
</div>

        </div>

    </div>

</div>

<?php include 'footer.php'; ?>