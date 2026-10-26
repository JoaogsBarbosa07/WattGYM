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
});