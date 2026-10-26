<?php include 'header.php'; ?>

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

        <button type="submit">Entrar</button>
    </form>
</div>


<?php include 'footer.php'; ?>