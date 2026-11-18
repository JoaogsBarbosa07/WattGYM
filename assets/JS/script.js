
document.addEventListener("DOMContentLoaded", function () {
 
    //Mostrar e ocultar senha 

    function configurarOlho(idBotao, idInput, idIcone) {
        const botao = document.getElementById(idBotao);
        const input = document.getElementById(idInput);
        const icone = document.getElementById(idIcone);
 
        if (!botao || !input || !icone) return;
 
        botao.addEventListener("click", function () {
            const escondida = input.type === "password";
            input.type = escondida ? "text" : "password";
            icone.src = escondida
                ? "assets/IMG/olho-aberto.png"
                : "assets/IMG/olho-fechado.png";
            icone.alt = escondida ? "Ocultar senha" : "Mostrar senha";
        });
    }
 
    configurarOlho("mostrarSenha", "senha", "iconeOlho");
    configurarOlho("mostrarConfirmarSenha", "confirmarSenha", "iconeOlhoConfirmar");
 
    //Validação do cadastro
    const formCadastro = document.getElementById("formCadastro");
    if (formCadastro) {
        const inputSenha = document.getElementById("senha");
        const inputConfirmar = document.getElementById("confirmarSenha");
 
        formCadastro.addEventListener("submit", function (e) {
            if (inputSenha.value !== inputConfirmar.value) {
                e.preventDefault();
                alert("As senhas não coincidem. Por favor, verifique.");
                inputConfirmar.focus();
            }
        });
    }
 
    //Máscaras do perfil
    const inputCPF = document.getElementById("cpf");
    if (inputCPF) {
        inputCPF.addEventListener("input", function (e) {
            let v = e.target.value.replace(/\D/g, "").slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, "$1.$2")
                 .replace(/(\d{3})(\d)/, "$1.$2")
                 .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
            e.target.value = v;
        });
    }
 
    const inputTel = document.getElementById("telefone");
    if (inputTel) {
        inputTel.addEventListener("input", function (e) {
            let v = e.target.value.replace(/\D/g, "").slice(0, 11);
            v = v.replace(/^(\d{2})(\d)/, "($1) $2")
                 .replace(/(\d{5})(\d)/, "$1-$2");
            e.target.value = v;
        });
    }
 
    const inputCEP = document.getElementById("cep");
    if (inputCEP) {
        inputCEP.addEventListener("input", function (e) {
            let v = e.target.value.replace(/\D/g, "").slice(0, 8);
            v = v.replace(/(\d{5})(\d)/, "$1-$2");
            e.target.value = v;
        });
    }
 
    //Confirmação ao salvar o perfil 
    const formPerfil = document.getElementById("formPerfil");
    if (formPerfil && inputTel) {
        const telefoneOriginal = inputTel.value;
 
        formPerfil.addEventListener("submit", function (e) {
            if (inputTel.value !== telefoneOriginal) {
                if (!confirm("Deseja salvar as alterações no telefone?")) {
                    e.preventDefault();
                }
            }
        });
    }
});
 