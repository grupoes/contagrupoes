const form = document.getElementById('form_cambiar');
const btnCambiar = document.getElementById('btnCambiar');

form.addEventListener('submit', (e) => {
    e.preventDefault();

    btnCambiar.disabled = true;
    btnCambiar.textContent = "Cambiando....";

    const formData = new FormData(form);

    fetch('./cambiar_contrasena', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btnCambiar.disabled = false;
        btnCambiar.textContent = "Cambiar Contraseña";
        if (data.respuesta == "ok") {
            form.reset();
            alert(data.mensaje);
        } else {
            alert(data.mensaje);
        }
    })

});