const lista = document.getElementById('lista-empresas');
const form_facturar = document.getElementById('form-facturar');

renderEmpresas();

function renderEmpresas() {
    fetch('./listaEmpresas')
    .then(res => res.json())
    .then(data => {
        viewLista(JSON.parse(data));
    })
}

function mes_actual() {
    let date = new Date();
    let month = date.toLocaleString('es-ES', { month: 'long' });
    let year = date.getFullYear();
    return month.toUpperCase()+" "+year;
}

function mes_anterior() {
    let date = new Date();
    date.setMonth(date.getMonth() - 1);
    let previousMonth = date.toLocaleString('es-ES', { month: 'long' });
    let year = date.getFullYear();
    return previousMonth.toUpperCase()+" "+year;
}

function viewLista(data) {
    let html = "";

    data.forEach(list => {
        let mes = "";
        let servicio = "";
        if(list.tipo_de_pago === 'ADELANTADO') {
            mes = mes_actual();
        } else {
            mes = mes_anterior();
        }

        if (list.tipo_servicio === 'CONTABLE') {
            servicio = "SERVICIO CONTABLE DEL MES DE "+mes;
        } else {
            servicio = "SERVICIO DE ARRENDAMIENTO DEL SOFTWARE DEL MES DE "+mes;
        }

        html += `
        <div class="mt-4">
            <h5 class="font-size-14 mb-4"><i class="mdi mdi-arrow-right text-primary me-1"></i> ${list.ruc_empresa_razon_social}</h5>
            <input type="hidden" name="servicio[]" value="${list.tipo_servicio}">
            <input type="hidden" name="razon[]" value="${list.ruc_empresa_razon_social}">
            <div class="row gx-3 gy-2 align-items-center">
                <div class="col-sm-2">
                    <label class="" for="specificSizeInputName">RUC</label>
                    <input type="number" class="form-control" id="specificSizeInputName" name="ruc[]" placeholder="Precio" value="${list.ruc_empresa_numero}" readonly>
                </div>
                <div class="col-sm-6">
                    <label class="" for="specificSizeInputName">Descripción</label>
                    <input type="text" class="form-control" id="specificSizeInputName" name="descripcion[]" placeholder="Descripción" value="${servicio}">
                </div>
                <div class="col-sm-2">
                    <label class="" for="specificSizeInputName">Precio</label>
                    <input type="number" class="form-control" id="specificSizeInputName" name="precio[]" placeholder="Precio" value="${list.ruc_empresa_monto}">
                </div>
            </div>
        </div>
        `;
    });

    lista.innerHTML = html;

}

form_facturar.addEventListener('submit', (e) => {
    e.preventDefault();

    e.disabled = true;

    const formData = new FormData(form_facturar);

    fetch('./facturando', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        e.disabled = false;
        if (data === 'ok') {
            alert('Se genero correctamente los comprobantes')
        }
    })
})