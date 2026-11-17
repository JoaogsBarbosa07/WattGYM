document.addEventListener("DOMContentLoaded", function () {
    const btnMostrarSenha = document.getElementById("mostrarSenha");
    const inputSenha = document.getElementById("senha");
    const iconeOlho = document.getElementById("iconeOlho");

    if (btnMostrarSenha && inputSenha && iconeOlho) {
        btnMostrarSenha.addEventListener("click", function () {
            if (inputSenha.type === "password") {
                inputSenha.type = "text";
                iconeOlho.src = "assets/IMG/olho-aberto.png";
                iconeOlho.alt = "Ocultar senha";
            } else {
                inputSenha.type = "password";
                iconeOlho.src = "assets/IMG/olho-fechado.png";
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
                iconeOlhoConfirmar.src = "assets/IMG/olho-aberto.png";
                iconeOlhoConfirmar.alt = "Ocultar senha";
            } else {
                inputConfirmar.type = "password";
                iconeOlhoConfirmar.src = "assets/IMG/olho-fechado.png";
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
const inputCPF = document.getElementById('cpf');
inputCPF?.addEventListener('input', (e) => {
let v = e.target.value.replace(/\D/g, '');
v = v.replace(/(\d{3})(\d)/, '$1.$2')
.replace(/(\d{3})(\d)/, '$1.$2')
.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
e.target.value = v;
});

const inputTel = document.getElementById('telefone');
inputTel?.addEventListener('input', (e) => {
let v = e.target.value.replace(/\D/g, '');
v = v.replace(/^(\d{2})(\d)/g, '($1) $2')
.replace(/(\d{5})(\d)/, '$1-$2');
e.target.value = v;
});

const form = document.querySelector('form');

const senha = document.getElementById('senha');
form?.addEventListener('submit', (e) => {
if (senha && confirmaSenha && senha.value !== confirmaSenha.value) {
e.preventDefault();
alert('As senhas não coincidem!');
return;
}});

const confirmaSenha = document.getElementById('confirma_senha');
if (window.location.pathname.includes('perfil.php')) {
if (!confirm("Deseja salvar as alterações no telefone?"))

e.preventDefault();

}
