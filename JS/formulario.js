document.addEventListener("DOMContentLoaded", function () {
    const btnMostrarSenha = document.getElementById("mostrarSenha");
    const inputSenha = document.getElementById("senha");
    const iconeOlho = document.getElementById("iconeOlho");

    if (btnMostrarSenha && inputSenha && iconeOlho) {
        btnMostrarSenha.addEventListener("click", function () {
            if (inputSenha.type === "password") {
                inputSenha.type = "text";
                iconeOlho.src = "IMG/olho-aberto.png";
                iconeOlho.alt = "Ocultar senha";
            } else {
                inputSenha.type = "password";
                iconeOlho.src = "IMG/olho-fechado.png";
                iconeOlho.alt = "Mostrar senha";
            }
        });
    }

    const btnMostrarConfirmar = document.getElementById("mostrarConfirmarSenha");
    const inputConfirmar = document.getElementById("confirmarSenha");
    const iconeOlhoConfirmar = document.getElementById("iconeOlhoConfirmar");

    if (btnMostrarConfirmar && inputConfirmar && iconeOlhoConfirmar) {
        btnMostrarConfirmar.addEventListener("click", function () {
            if (inputConfirmar.type === "password") {
                inputConfirmar.type = "text";
                iconeOlhoConfirmar.src = "IMG/olho-aberto.png";
                iconeOlhoConfirmar.alt = "Ocultar senha";
            } else {
                inputConfirmar.type = "password";
                iconeOlhoConfirmar.src = "IMG/olho-fechado.png";
                iconeOlhoConfirmar.alt = "Mostrar senha";
            }
        });
    }

    const formCadastro = document.getElementById("formCadastro");
    if (formCadastro) {
        formCadastro.addEventListener("submit", function (e) {
            if (inputSenha.value !== inputConfirmar.value) {
                e.preventDefault();
                alert("As senhas não coincidem. Por favor, verifique.");
                inputConfirmar.focus();
            }
        });
    }
});