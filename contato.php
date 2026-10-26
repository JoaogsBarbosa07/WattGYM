<?php include 'header.php'; ?>

<div class="alinha">
    <div class="info">
        <h3>Informações</h3>
        <h4>Email</h4>
        <p>RicardoSexy@gmail.com</p>

        <h4>Telefone</h4>
        <p>(81)1945-1500</p>

        <h4>Endereço</h4>
        <p>Rua do Embaço, 200</p>
    </div>



  <div class="contato">

    <form action="sobre.php">

        <h2>Contate-nos</h2>

        <h5>Email</h5>
        <label for="email"></label>
        <input type="email" id="email" placeholder="Digite seu email">

        <h5>Assunto</h5>
        <label for="assunto"></label>
        <input type="text" id="assunto" placeholder="Digite o assunto">

        <h5>Mensagem</h5>
        <label for="mensagem"></label>
        <input type="text" id="mensagem" placeholder="Digite sua mensagem">

        <button>Enviar</button>

        <p class="sobre-link">
            Quer Saber Mais?
            <a href="sobre.php">Sobre</a>
        </p>

    </form>
    <form class="form-area" id="formContato">
    
    </form>
    
    <div id="toast" class="toast" role="status"></div>

</div>
</div>

<?php include 'footer.php'; ?>