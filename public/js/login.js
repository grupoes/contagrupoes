const form = document.getElementById('form_login');

const mensaje = document.getElementById('mensaje_alerta');
const btn_sesion = document.getElementById('btn_sesion');

form.addEventListener('submit', (e) => {
    e.preventDefault();

    btn_sesion.disabled = true;
    btn_sesion.textContent = "Ingresando...";

    const formData = new FormData(form);

    fetch("./acceder",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.respuesta == "ok") {
            window.location.href = "./home"; 
        } else {
            mensaje.innerHTML = `
            <div class="alert alert-danger" role="alert">
                ${data.mensaje}
            </div>
            `;
        }

        btn_sesion.disabled = false;
        btn_sesion.textContent = "Iniciar sesión";
    })

})