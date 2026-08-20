//window.addEventListener('load', imprimirTablaDesdeLocalStorage);
const url_base = document.getElementById("url_base").value;
const ruc_contribuyente = document.getElementById("ruc_contribuyente");
const tableCompras = document.getElementById("tableCompras");

const codigo = document.getElementById("codigoqr");
const btngenerar = document.getElementById("generar_maqueta");
const form = document.getElementById("form_maqueta");
const cancelar = document.getElementById("cancelar");

const procesar = document.getElementById("procesar");

const item = document.getElementById("add_item");

const modal = document.getElementById("modal_subir_pdf");
const form_subir = document.getElementById("form_subir");

const data_maqueta = document.getElementById("data_maqueta");

const pdf_venta = document.getElementById("modal_maqueta_venta");
const form_venta = document.getElementById("form_subir_venta");

const btnrh = document.getElementById("modal_rh");
const form_rh = document.getElementById("form_subir_rh");

const ruc_activo = document.getElementById("ruc_activo").value;

const downloadMaqueta = document.getElementById("downloadMaqueta");
const maquetaRegistro = document.getElementById("maquetaRegistro");

const abancarizados = document.getElementById("abancarizados");
const generarMaquetaRegistro = document.getElementById(
  "generarMaquetaRegistro",
);

const btnSire = document.getElementById("modalSire");

function anio_actual() {
  const fechaActual = new Date();

  const anio = fechaActual.getFullYear();

  return anio;
}

function comprobar_comprobante_mismo_periodo(fecha_comprobante) {
  const fechaActual = new Date();

  const anio_actual = fechaActual.getFullYear();
  //const anio_actual = 2023;

  const fecha_c = new Date(fecha_comprobante);
  const anio_c = fecha_c.getFullYear();

  if (anio_actual === anio_c) {
    return true;
  } else {
    return false;
  }
}

modal.addEventListener("click", () => {
  $("#modal_pdf").modal("show");
  document.getElementById("comprobante").value = "";
  document.getElementById("archivo").value = "";
});

form_subir.addEventListener("submit", (e) => {
  e.preventDefault();

  procesar.disabled = true;
  procesar.textContent = "Procesando...";

  const formData = new FormData(form_subir);

  fetch(url_base + "/procesar_pdf", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      $("#modal_pdf").modal("hide");
      procesar.disabled = false;
      procesar.textContent = "Procesar";

      console.log(data);

      const no_existe = data.no_existe;
      const existe = data.existe;

      if (existe.length != 0) {
        $("#modalMensaje").modal("show");

        let tbody = "";

        let html = `<h4>Estos comprobantes ya fueron registrados</h4>`;

        existe.forEach((tb, index) => {
          tbody += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${tb.documento}</td>
                            <td>${tb.proveedor}</td>
                        </tr>
                    `;
        });

        html += `
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Documento</th>
                            <th>Proveedor</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tbody}
                    </tbody>
                </table>
                `;

        const body = document.getElementById("modalBody");
        body.innerHTML = html;
      }

      const fila = document.querySelectorAll(".fila-maqueta");
      const glosa = document.querySelectorAll(".edit-glosa");
      const cuenta = document.querySelectorAll(".edit-cuenta");

      let text_glosa;
      let text_cuenta;

      if (fila.length == 0) {
        text_glosa = "";
        text_cuenta = "";
      } else {
        $cant_fila = glosa.length;
        text_glosa = glosa[0].value;
        text_cuenta = cuenta[0].value;
      }

      no_existe.forEach((once) => {
        let items = filas();

        let total_string = once.monto.split(",");

        let total = total_string.join("");

        let select_moneda;
        let select_comp;

        if (once.moneda == "D") {
          select_moneda = `
                    <option value="S">S</option>
                    <option value="D" selected>D</option>
                `;
        } else {
          select_moneda = `
                    <option value="S" selected>S</option>
                    <option value="D">D</option>
                `;
        }

        if (once.comprobante == "BOLETA") {
          select_comp = `
                    <option value="FACTURA">F</option>
                    <option value="BOLETA" selected>B</option>
                    <option value="NOTA DE CREDITO">NC</option>
                `;
        } else {
          if (once.comprobante == "FACTURA") {
            select_comp = `
                        <option value="FACTURA" selected>F</option>
                        <option value="BOLETA">B</option>
                        <option value="NOTA DE CREDITO">NC</option>
                    `;
          } else {
            select_comp = `
                        <option value="FACTURA">F</option>
                        <option value="BOLETA">B</option>
                        <option value="NOTA DE CREDITO" selected>NC</option>
                    `;
          }
        }

        let style_tr = "";

        if (once.moneda == "D" && parseFloat(total) >= 500) {
          style_tr = "background-color: #ebeb41;";
        }

        if (once.moneda == "S" && parseFloat(total) >= 2000) {
          style_tr = "background-color: #ebeb41;";
        }

        let html = `
                <tr class="fila-maqueta" style="${style_tr}">
                    <input type="hidden" name="items[]" value="${items}">
                    <input type="hidden" name="condicion[]" value="A">
                    <input type="hidden" name="bolsa[]" value="0">
                    <input type="hidden" name="total[]" value="${total}" id="t-${items}">
                    <input type="hidden" name="numeracion[]" value="${items}">

                    <td>${items}</td>
                    <td><input type="date" name="fecha[]" class="form-control" id="fecha-date-${items}" value="${once.fecha}" required="" /></td>
                    <td class="tipo_moneda">
                        <select class="form-control" name="tipo_moneda[]" onchange="ver_tipo_cambio(event,${items},2)">
                            ${select_moneda}
                        </select>
                    </td>
                    <td class="documento">
                        <select class="form-control" name="documento[]" style="width: 40px">
                            ${select_comp}
                        </select>
                    </td>
                    <td><input type="text" id="numero-${items}" name="serie_correlativo[]" class="form-control" value="${once.serie}-${once.numero}" readonly="" /></td>
                    <td><input type="text" name="ruc_cliente[]" id="num-ruc-${items}" class="form-control" style="width: 120px;" onkeypress="return solonumeros(event)" value="${once.ruc}" readonly /></td>
                    <td><input type="text" name="vventa[]" class="form-control" value="${total}" onkeypress="return solonumeros(event)" id="vventa-${items}" style="width: 90px;" readonly="" onkeyup="new_total(event, ${items})" /></td>
                    <td><input type="text" name="valor_venta[]" class="form-control" value="${total}" onkeypress="return solonumeros(event)" id="valor_venta-${items}" style="width: 90px;" readonly="" /></td>
                    <td><input type="text" name="igv[]" class="form-control" value="0" style="width: 70px;" onkeypress="return solonumeros(event)" id="data-igv-${items}" readonly="" onkeyup="new_total_igv(event,${items})"/> <input type="checkbox" id="check-igv-${items}" onclick="calcular_igv_pdf(event,${items})"></td>
                    <td>0</td>
                    <td><input type="text" name="icb[]" class="form-control" value="0" style="width: 70px;" id="data-icb-${items}" onkeyup="calcular_total_icb_pdf(event,${items})" /></td>
                    <td id="tt-${items}">${total}</td>
                    <td><input type="text" class="form-control" name="tipo_cambio[]" id="tipo-cambio-${items}" value="${once.tipo_cambio}" style="width: 70px;"></td>
                    <td><input type="text" class="form-control edit-glosa" name="glosa[]" id="glosa_${items}" value="${text_glosa}" onkeyup="completarGlosa(event, ${items})" style="width: 90px;"></td>
                    <td><input type="text" class="form-control edit-cuenta" name="cuenta[]" id="cuenta_${items}" value="${text_cuenta}" onkeyup="sugerirGlosa(event, ${items})" autocomplete="off" style="width: 90px;"></td>
                    <td>
                        <select class="form-control" name="afectacion[]" id="afectacion-compra-${items}">
                            <option value="N">N</option>
                            <option value="S">S</option>
                        </select>
                    </td>
                    <td>
                        <i class="bx bx-trash text-danger fs-2 eliminar_fila"></i>
                        <i class="bx bxs-edit text-info fs-2 editar_fila" data-item="${items}"></i>
                    </td>
                </tr>
                `;

        $("#data_maqueta").prepend(html);
      });
    });
});

item.addEventListener("click", () => {
  const fila = document.querySelectorAll(".fila-maqueta");
  const glosa = document.querySelectorAll(".edit-glosa");
  const cuenta = document.querySelectorAll(".edit-cuenta");

  let text_glosa;
  let text_cuenta;

  if (fila.length == 0) {
    text_glosa = "";
    text_cuenta = "";
  } else {
    $cant_fila = glosa.length;
    text_glosa = glosa[0].value;
    text_cuenta = cuenta[0].value;
  }

  //onkeypress="return solonumeros(event)"

  const items = filas();
  let html = `
        <tr class="fila-maqueta" id="tr_${items}">
            <input type="hidden" name="items[]" value="${items}">
            <input type="hidden" name="condicion[]" value="A">
            <input type="hidden" name="bolsa[]" value="0">
            <input type="hidden" name="total[]" value="0" id="total-${items}">
            <input type="hidden" name="numeracion[]" value="${items}">

            <td>${items}</td>
            <td><input type="date" name="fecha[]" id="fecha-date-${items}" class="form-control" required="" /></td>
            <td class="tipo_moneda">
                <select class="form-control" id="tipo_moneda_${items}" name="tipo_moneda[]" onchange="ver_tipo_cambio(event,${items},2)">
                    <option value="S">S</option>
                    <option value="D">D</option>
                </select>
            </td>
            <td class="documento">
                <select class="form-control" id="doc_${items}" name="documento[]" style="width: 40px">
                    <option value="FACTURA">F</option>
                    <option value="BOLETA">B</option>
                    <option value="NOTA DE CREDITO">NC</option>
                    <option value="NOTA DE DEBITO">ND</option>
                </select>
            </td>
            <td><input type="text" name="serie_correlativo[]" id="numero-${items}" class="form-control" onblur="comprobar_duplicidad(${items}, 0)" autocomplete="off" /></td>
            <td>
                <input type="text" name="ruc_cliente[]" id="num-ruc-${items}" onkeyup="completarRuc(event, ${items})" class="form-control" style="width: 120px;" maxlength="11" onblur="comprobar_duplicidad(${items}, 1)" />
            </td>
            <td><input type="text" name="vventa[]" class="form-control" value="0" onkeypress="return solonumeros(event)" id="vventa-${items}" onkeyup="calcular_total(event,${items})" style="width: 90px;" /></td>
            <td><input type="text" name="valor_venta[]" class="form-control" value="0" onkeypress="return solonumeros(event)" id="valor-venta-${items}" onkeyup="calcular_total(event,${items})" style="width: 90px;" /></td>
            <td><input type="text" name="igv[]" class="form-control" value="0" style="width: 70px;" onkeypress="return solonumeros(event)" id="igv-${items}" onkeyup="calcular_total_igv(event,${items})" /></td>
            <td>0</td>
            <td><input type="text" name="icb[]" class="form-control" value="0" style="width: 70px;" id="icb-${items}" onkeyup="calcular_total_icb(event,${items})" /></td>
            <td id="text-total-${items}">0</td>
            <td><input type="text" class="form-control" name="tipo_cambio[]" id="tipo-cambio-${items}" value="1" style="width: 70px;"></td>
            <td><input type="text" class="form-control edit-glosa" name="glosa[]" value="${text_glosa}" onkeyup="completarGlosa(event, ${items})" id="glosa_${items}" style="width: 90px;"></td>
            <td><input type="text" class="form-control edit-cuenta" name="cuenta[]" value="${text_cuenta}" onkeyup="sugerirGlosa(event, ${items})" id="cuenta_${items}" autocomplete="off" style="width: 90px;" ></td>
            <td>
                <select class="form-control" name="afectacion[]" id="afectacion_${items}">
                    <option value="N">N</option>
                    <option value="S">S</option>
                </select>
            </td>
            <td>
                <i class="bx bx-trash text-danger fs-2 eliminar_fila" title="eliminar fila"></i>
                <i class="bx bx-plus text-info fs-2 duplicar_fila" title="duplicar fila" data-item="${items}"></i>
            </td>
        </tr>
    `;

  $("#data_maqueta").prepend(html);

  //guardarTablaEnLocalStorage();
});

let timeout;

codigo.addEventListener("keydown", (e) => {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    clearTimeout(timeout);
    const cod = e.target.value;
    addMaqueta(cod);
    codigo.value = "";
  }, 1000);
});

let data_numbers = [];

function addMaqueta(qr) {
  const items = filas();
  //console.log(qr); return false;

  //20231266993]01]F102]00003932]0.00]800.00]2022'08'18]6]10011211831]TKZyJFf-44HmG9Ev¡wxIzXBH8v0¿
  //20601305772]1]FE02]00010643]]1870]1870]20-08-2022]6]10402851291]  ] '
  //20600025377]07]ffd1]000582]0.00]208.22]08-08-2022]6]10443671507]]

  let separados = qr.split("]");
  let quanty = separados.length;

  for (let i = quanty - 1; i > quanty - 4; i--) {
    let encontrar = separados[i].trim();

    if (encontrar.length == 11) {
      if (encontrar != ruc_contribuyente.value) {
        alert("El comprobante no pertenece al contribuyente: " + qr);
        return;
      }
    }
  }

  let igv_fecha = separados[5].trim();
  let date_fecha;
  let igv;
  let total;

  if (igv_fecha.length == 10) {
    date_fecha = igv_fecha;
    igv = "0";
    total = separados[4].trim();
  } else {
    if (quanty == 12) {
      date_fecha = separados[7].trim();
    } else {
      date_fecha = separados[6].trim();
    }

    igv = separados[4].trim();
    total = separados[5].trim();
  }

  let fecha;
  let documento;
  let docu;

  let fechaqr = date_fecha;

  let datet = fechaqr.split("-");

  if (datet.length > 1) {
    fecha = `${datet[2]}-${datet[1]}-${datet[0]}`;
  } else {
    fechaqr = fechaqr.split(" ");

    fechaqr = fechaqr[0];

    let fecha_separada = fechaqr.split("'");

    if (fecha_separada.length == 1) {
      let fecha_normal = fechaqr.split("-");
      //fecha = `${fecha_normal[2]}-${fecha_normal[1]}-${fecha_normal[0]}`;
      fecha = `${fecha_normal[0]}-${fecha_normal[1]}-${fecha_normal[2]}`;
    } else {
      if (fecha_separada[2].length > 2) {
        //fecha = `${fecha_separada[0]}-${fecha_separada[1]}-${fecha_separada[2]}`;
        fecha = `${fecha_separada[2]}-${fecha_separada[1]}-${fecha_separada[0]}`;
      } else {
        //fecha = `${fecha_separada[2]}-${fecha_separada[1]}-${fecha_separada[0]}`;
        fecha = `${fecha_separada[0]}-${fecha_separada[1]}-${fecha_separada[2]}`;
      }
    }

    const compr = fecha.split("-");

    if (compr[1] == "undefined") {
      let fe = separados[6].trim();

      let fec = fe.split("-");

      if (fec.length > 1) {
        fecha = `${fec[2]}-${fec[1]}-${fec[0]}`;
      } else {
        fe = fe.split(" ");
        fe = fe[0];

        let fese = fe.split("'");

        if (fese.length == 1) {
          let fecha_normal = fese.split("-");
          fecha = `${fecha_normal[0]}-${fecha_normal[1]}-${fecha_normal[2]}`;
        } else {
          if (fese[2].length > 2) {
            fecha = `${fese[2]}-${fese[1]}-${fese[0]}`;
          } else {
            fecha = `${fese[0]}-${fese[1]}-${fese[2]}`;
          }
        }
      }
    }
  }

  let tipo_documento = separados[1].trim();
  let ruc = separados[0].trim();
  let tipo_cliente;

  if (ruc == ruc_contribuyente.value) {
    alert("RUC PERTENECE AL CLIENTE");
    return false;
  }

  if (separados.length == 7) {
    tipo_cliente = "";
  }

  if (separados.length > 7) {
    tipo_cliente = separados[7].trim();
  }

  let total_string = total.split(",");

  let total_ = total_string.join("");

  let data_igv;

  if (igv == "") {
    data_igv = "0";
  } else {
    data_igv = igv;
  }

  if (
    tipo_documento == "01" ||
    tipo_documento == "1" ||
    tipo_documento == "03" ||
    tipo_documento == "3" ||
    tipo_documento == "07" ||
    tipo_documento == "7" ||
    tipo_documento == "FAC" ||
    tipo_documento == "BOL"
  ) {
    tipo_documento = tipo_documento;
  } else {
    tipo_documento = tipo_cliente;
  }

  let correlativo;
  let serie;
  let number_serie = separados[1].trim();

  if (number_serie.length > 2) {
    serie = number_serie;
    correlativo = parseInt(separados[2].trim());
  } else {
    let serie_ = separados[2].trim();
    correlativo = parseInt(separados[3].trim());

    if (serie_.length == 3) {
      if (
        tipo_documento == "01" ||
        tipo_documento == "FAC" ||
        tipo_documento == "1"
      ) {
        serie = "F" + serie_;
      } else {
        if (
          tipo_documento == "03" ||
          tipo_documento == "BOL" ||
          tipo_documento == "3"
        ) {
          serie = "B" + serie_;
        } else {
          serie = "F" + serie_;
        }
      }
    } else {
      let desc_serie = serie_.split("'");

      if (desc_serie.length == 1) {
        serie = separados[2].trim();
      } else {
        serie = desc_serie[0];
        correlativo = parseInt(desc_serie[1]);

        data_igv = separados[3].trim();
      }
    }
  }

  let valor_venta = parseFloat(total_) - parseFloat(data_igv);

  if (
    tipo_documento == "01" ||
    tipo_documento == "FAC" ||
    tipo_documento == "1"
  ) {
    documento = "FACTURA";
    tipo_documento = "01";
    docu = "F";
  } else {
    if (
      tipo_documento == "03" ||
      tipo_documento == "BOL" ||
      tipo_documento == "3"
    ) {
      documento = "BOLETA";
      tipo_documento = "03";
      docu = "B";
    } else {
      if (
        tipo_documento == "07" ||
        tipo_documento == "NC" ||
        tipo_documento == "7"
      ) {
        documento = "NOTA DE CREDITO";
        tipo_documento = "07";
        docu = "NC";

        valor_venta = -valor_venta;
        total_ = -total_;
      } else {
        documento = "FACTURA";
        tipo_documento = "01";
        docu = "F";
      }
    }
  }

  //ACA VERIFICAMOS SI EL DOCUMENTO YA EXISTE EN LA LISTA SEGUN EL PROVEEDOR
  const serieNum = serie.toUpperCase() + "-" + correlativo;

  const repetido = verificar_existe_lista(serieNum, ruc);

  if (repetido[0] != 0) {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text:
        "Ya se ingreso dicho comprobante y se encuentra en el item " +
        repetido[1],
    });
    return false;
  }

  //aca verificamos si el ruc esta activo para consultar
  if (ruc_activo == "1") {
    const ruc_proveedor = ruc;
    const serie_consultar = serie.toUpperCase();
    const numero_consultar = correlativo;

    consultar_compra_sunat(ruc_proveedor, serie_consultar, numero_consultar);

    return false;
  }

  //fin consulta

  const fila = document.querySelectorAll(".fila-maqueta");
  const glosa = document.querySelectorAll(".edit-glosa");
  const cuenta = document.querySelectorAll(".edit-cuenta");

  let text_glosa;
  let text_cuenta;

  if (fila.length == 0) {
    text_glosa = "";
    text_cuenta = "";
  } else {
    $cant_fila = glosa.length;
    text_glosa = glosa[0].value;
    text_cuenta = cuenta[0].value;
  }

  let comprobante = [
    {
      documento: "FACTURA",
      abreviatura: "F",
      codigo: "01",
    },
    {
      documento: "BOLETA",
      abreviatura: "B",
      codigo: "03",
    },
    {
      documento: "NOTA DE CREDITO",
      abreviatura: "NC",
      codigo: "07",
    },
    {
      documento: "NOTA DE DEBITO",
      abreviatura: "NB",
      codigo: "08",
    },
  ];

  let select_comp = "";
  let select_tipo_doc = "";

  comprobante.forEach((comp) => {
    if (comp.codigo == tipo_documento) {
      select_tipo_doc = "selected";
    } else {
      select_tipo_doc = "";
    }
    select_comp += `
            <option value="${comp.documento}" ${select_tipo_doc}>${comp.abreviatura}</option>
        `;
  });

  let afect;

  if (data_igv > 0) {
    afect = "S";
  } else {
    afect = "N";
  }

  let data_afect = ["N", "S"];
  let option_afect = "";
  let select_afect;

  data_afect.forEach((a) => {
    if (a == afect) {
      select_afect = "selected";
    } else {
      select_afect = "";
    }

    option_afect += `<option value="${a}" ${select_afect}>${a}</option>`;
  });

  let background = "";

  if (parseFloat(total_) >= 2000) {
    background = `style="background-color: #ebeb41;"`;
  }

  const fechaComprobante = fecha.slice(0, 10);

  if (esFechaValida(fechaComprobante)) {
    const comprobar_fecha =
      comprobar_comprobante_mismo_periodo(fechaComprobante);

    if (comprobar_fecha == false) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text:
          "La fecha del comprobante (" +
          fechaComprobante +
          ") pertenece a otro año",
      });

      return false;
    }
  }

  let html = `
        <tr class="fila-maqueta" data-id="${items}" id="tr_${items}" ${background}>
            <input type="hidden" name="items[]" value="${items}">
            <input type="hidden" name="condicion[]" value="A">
            <input type="hidden" name="bolsa[]" value="0">
            <input type="hidden" name="total[]" id="t-${items}" value="${total_}">
            <input type="hidden" name="totales_[]" value="${total_}" id="total-${items}">
            <input type="hidden" name="tipo_cliente[]" value="${tipo_cliente}">
            <input type="hidden" name="serie[]" value="${serie}">
            <input type="hidden" name="correlativo[]" value="${correlativo}">
            <input type="hidden" name="tipo_documento[]" value="${tipo_documento}">

            <input type="hidden" name="numeracion[]" value="${items}">

            <td>${items}</td>
            <td><input type="date" class="form-control" name="fecha[]" id="fecha-date-${items}" value="${fecha.slice(
              0,
              10,
            )}" required="" readonly></td>
            <td class="tipo_moneda">
                <select class="form-control" name="tipo_moneda[]" onchange="ver_tipo_cambio(event,${items},1)">
                    <option value="S">S</option>
                    <option value="D">D</option>
                </select>
            </td>
            <td>
                <select name="documento[]" class="form-control">
                    ${select_comp}
                </select>
            </td>
            <td><input type="text" id="numero-${items}" name="serie_correlativo[]" class="form-control" value="${serie.toUpperCase()}-${correlativo}" readonly></td>
            <td><input type="text" name="ruc_cliente[]" id="num-ruc-${items}" class="form-control" value="${ruc}" onkeyup="completarRuc(event, ${items})" autocomplete="off" style="width: 120px;" readonly></td>
            <td id="text_vventa_${items}"><input type="text" class="form-control" name="vventa[]" id="vventa-${items}" value="${valor_venta.toFixed(
              2,
            )}" onkeyup="new_total(event,${items})" style="width: 80px;" readonly></td>
            <td id="text_valor_venta_${items}"><input type="text" class="form-control" name="valor_venta[]" id="valor_venta-${items}" value="${valor_venta.toFixed(
              2,
            )}" style="width: 80px;" readonly></td>
            <td><input type="text" name="igv[]" class="form-control" id="data-igv-${items}" value="${data_igv}" onkeyup="new_total_igv(event,${items})" style="width: 80px;" readonly></td>
            <td>0</td>
            <td><input type="text" id="data-icb-${items}" class="form-control" name="icb[]" value="0" onkeyup="new_total_icb(event,${items})" style="width: 60px;" readonly></td>
            <td id="tt-${items}">${total_}</td>
            <td><input type="text" class="form-control" name="tipo_cambio[]" id="tipo-cambio-${items}" value="1" style="width: 70px;"></td>
            <td><input type="text" class="form-control edit-glosa" name="glosa[]" id="glosa_${items}" value="${text_glosa}" onkeyup="completarGlosa(event, ${items})" autocomplete="off" style="width: 90px;"></td>
            <td><input type="text" class="form-control edit-cuenta" name="cuenta[]" id="cuenta_${items}" value="${text_cuenta}" onkeyup="sugerirGlosa(event, ${items})" autocomplete="off" style="width: 90px;"></td>
            <td>
                <select class="form-control" name="afectacion[]">
                    ${option_afect}
                </select>
            </td>
            <td>
                <i class="bx bx-trash text-danger fs-2 eliminar_fila"></i>
                <i class="bx bxs-edit text-info fs-2 editar_fila" data-item="${items}"></i>
            </td>
        </tr>
    `;

  const formData = new FormData();
  formData.append("serie_numero", serie.toUpperCase() + "-" + correlativo);
  formData.append("cliente", ruc_contribuyente.value);
  formData.append("proveedor", ruc);
  formData.append("tipo_doc", documento);

  fetch(url_base + "/comprobar-duplicidad", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.respuesta == "ok") {
        $("#data_maqueta").prepend(html);

        if (parseFloat(total_) >= 2000) {
          Swal.fire({
            icon: "success",
            title: "Recomendación",
            text: "Por favor identifica su comprobante bancarizado!",
          });
        }
      } else {
        Swal.fire({
          title: "Desea agregar a la lista?",
          text: data.mensaje,
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Si, agregar!",
        }).then((result) => {
          if (result.isConfirmed) {
            $("#data_maqueta").prepend(html);
          }
        });
      }
    });
}

form.addEventListener("submit", (e) => {
  e.preventDefault();

  btngenerar.disabled = true;
  btngenerar.textContent = "GENERANDO MAQUETA...";

  const formData = new FormData(form);

  fetch(url_base + "/generar_maqueta", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      btngenerar.disabled = false;
      btngenerar.textContent = "GENERAR MAQUETA";

      if (data.respuesta == "ok") {
        if (data.url_compra != "") {
          var element = document.createElement("a");
          element.setAttribute(
            "href",
            url_base + "/descargar-maqueta-xlsx/" + data.url_compra,
          );
          element.setAttribute("download", data.url_compra);
          document.body.appendChild(element);
          element.click();
        }

        if (data.url_venta != "") {
          var el = document.createElement("a");
          el.setAttribute(
            "href",
            url_base + "/descargar-maqueta-xlsx/" + data.url_venta,
          );
          el.setAttribute("download", data.url_venta);
          document.body.appendChild(el);
          el.click();
        }
      } else {
        if (data.respuesta == "error") {
          alert(data.mensaje);
        } else {
          let mensaje = "";

          mensaje += data[0].mensaje + " | ";

          data.forEach((dat) => {
            mensaje += "item " + dat.item + " - " + dat.ruc + " | ";
          });

          alert(mensaje);
        }
      }

      let bancari = data.bancarizados;

      if (bancari.length > 0) {
        $("#modal_bancarizar").modal("show");

        let lista = "";

        const listaB = document.getElementById("listaBancarizar");

        bancari.forEach((ban) => {
          lista += `
                        <tr>
                            <td>${ban.numero_documento}</td>
                            <td>${ban.razon_social}</td>
                            <td>${ban.total}</td>
                            <td>
                                <input type="file" class="form-control" name="" id="voucher-${ban.id_maqueta}">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="" id="description-${ban.id_maqueta}" value="">
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="uploadVaucher(${ban.id_maqueta})"><i class="bx bx-upload"></i></button>
                            </td>
                        </tr>
                    `;
        });

        listaB.innerHTML = lista;
      }

      let honorarios = data.honorarios;

      if (honorarios.length > 0) {
        window.open(
          url_base + "/descargar-honorarios/" + data.registro,
          "_blank",
        );
      }
    });
});

function calcular_icb(e, numero, valor_venta, tipo_documento) {
  const valor = parseFloat(e.target.value);
  let nuevo_valor_venta;
  if (tipo_documento == "07") {
    nuevo_valor_venta = parseFloat(valor_venta) + valor;
  } else {
    nuevo_valor_venta = parseFloat(valor_venta) - valor;
  }

  const vventa = document.getElementById("vventa-" + numero);
  const valor_de_venta = document.getElementById("valor_venta-" + numero);

  const text_vventa = document.getElementById("text_vventa_" + numero);
  const text_valor_venta = document.getElementById(
    "text_valor_venta_" + numero,
  );

  vventa.value = nuevo_valor_venta.toFixed(2);
  valor_de_venta.value = nuevo_valor_venta.toFixed(2);
  text_vventa.textContent = nuevo_valor_venta.toFixed(2);
  text_valor_venta.textContent = nuevo_valor_venta.toFixed(2);
}

data_maqueta.addEventListener("click", (e) => {
  if (e.target.classList.contains("eliminar_fila")) {
    e.target.parentElement.parentElement.remove();
  }

  if (e.target.classList.contains("eliminar_fila_sire")) {
    const item = e.target.getAttribute("data_item");
    const serieb = e.target.getAttribute("data-serie");
    const rucb = e.target.getAttribute("data-ruc");

    e.target.parentElement.parentElement.remove();

    //comprobar duplicidad en la lista de los documentos
    const serieInputs = document.querySelectorAll(
      'input[name="serie_correlativo[]"]',
    );
    const rucInputs = document.querySelectorAll('input[name="ruc_cliente[]"]');
    const numeracion = document.querySelectorAll('input[name="numeracion[]"]');

    let existeSerie = 0;
    let existeRuc = 0;
    let itemRep = 0;

    for (let i = 0; i < serieInputs.length; i++) {
      const serie = serieInputs[i];
      const rucs = rucInputs[i];

      if (serie.value === serieb) {
        existeSerie = 1;
      }

      if (rucs.value === rucb) {
        existeRuc = 1;
      }

      if (existeSerie === 1 && existeRuc === 1) {
        itemRep = numeracion[i].value;
        break;
      }
    }

    if (existeSerie === 1 && existeRuc === 1) {
      const moneda = document.getElementById("tipo_moneda_" + itemRep).value;
      const monto = document.getElementById("totalSire-" + itemRep).value;

      const tr = document.getElementById("tr-" + itemRep);

      if (
        (moneda === "D" && monto >= 500) ||
        (moneda === "S" && monto >= 2000)
      ) {
        tr.style.background = "yellow";

        //actualizar_tabla();
        return;
      }

      if (moneda === "D") {
        tr.style.background = "green";
      } else {
        tr.style.background = "white";
      }
    } else {
      console.log("no existe");
    }

    //actualizar_tabla();
  }

  if (e.target.classList.contains("verificar_sunat")) {
    $("#modal_consulta").modal("show");

    document.getElementById("mensajeSunat").innerHTML = "";

    const idItem = e.target.getAttribute("data-id");
    const monto = e.target.getAttribute("data-monto");
    const cambio = e.target.getAttribute("data-cambio");
    const serie_numero = document.getElementById("numero-" + idItem).value;
    const fecha_c = document.getElementById("fecha-date-" + idItem);
    const emisor = document.getElementById("num-ruc-" + idItem);

    const serieNumber = serie_numero.split("-");

    const serie = serieNumber[0];
    const correlativo = serieNumber[1];

    document.getElementById("serie").value = serie;
    document.getElementById("numero").value = correlativo;
    document.getElementById("fecha_emision").value = fecha_c.value;
    document.getElementById("monto").value = parseFloat(monto).toFixed(2);
    document.getElementById("emisor").value = emisor.value;
  }

  if (e.target.classList.contains("editar_fila")) {
    const i = e.target.getAttribute("data-item");

    const fec = document.getElementById("fecha-date-" + i);
    fec.removeAttribute("readonly");

    const num = document.getElementById("numero-" + i);
    num.removeAttribute("readonly");

    const num_ruc = document.getElementById("num-ruc-" + i);
    num_ruc.removeAttribute("readonly");

    const vventa = document.getElementById("vventa-" + i);
    vventa.removeAttribute("readonly");

    /*const valor_venta = document.getElementById('valor_venta-'+i);
        valor_venta.removeAttribute('readonly');*/

    const igv = document.getElementById("data-igv-" + i);
    igv.removeAttribute("readonly");

    const icb = document.getElementById("data-icb-" + i);
    icb.removeAttribute("readonly");
  }

  if (e.target.classList.contains("duplicar_fila")) {
    const item = e.target.getAttribute("data-item");

    const fecha = document.getElementById("fecha-date-" + item);
    const moneda = document.getElementById("tipo_moneda_" + item);
    const docu = document.getElementById("doc_" + item);
    const numero = document.getElementById("numero-" + item);
    const ruc = document.getElementById("num-ruc-" + item);
    const vventa = document.getElementById("vventa-" + item);
    const valor_venta = document.getElementById("valor-venta-" + item);
    const igv = document.getElementById("igv-" + item);
    const icb = document.getElementById("icb-" + item);
    const total = document.getElementById("total-" + item);
    const tipo_cambio = document.getElementById("tipo-cambio-" + item);
    const glosa = document.getElementById("glosa_" + item);
    const cuenta = document.getElementById("cuenta_" + item);
    const afectacion = document.getElementById("afectacion_" + item);

    let select_moneda;
    let select_comp;
    let select_afectacion;

    if (moneda.value == "D") {
      select_moneda = `
                <option value="S">S</option>
                <option value="D" selected>D</option>
            `;
    } else {
      select_moneda = `
                <option value="S" selected>S</option>
                <option value="D">D</option>
            `;
    }

    if (afectacion.value == "N") {
      select_afectacion = `
                <option value="N" selected>N</option>
                <option value="S">S</option>
            `;
    } else {
      select_afectacion = `
                <option value="N">N</option>
                <option value="S" selected>S</option>
            `;
    }

    if (docu.value == "BOLETA") {
      select_comp = `
                <option value="FACTURA">F</option>
                <option value="BOLETA" selected>B</option>
                <option value="NOTA DE CREDITO">NC</option>
                <option value="NOTA DE DEBITO">ND</option>
            `;
    } else {
      if (docu.value == "FACTURA") {
        select_comp = `
                    <option value="FACTURA" selected>F</option>
                    <option value="BOLETA">B</option>
                    <option value="NOTA DE CREDITO">NC</option>
                    <option value="NOTA DE DEBITO">ND</option>
                `;
      } else {
        if (docu.value == "NOTA DE CREDITO") {
          select_comp = `
                        <option value="FACTURA">F</option>
                        <option value="BOLETA">B</option>
                        <option value="NOTA DE CREDITO" selected>NC</option>
                        <option value="NOTA DE DEBITO">ND</option>
                    `;
        } else {
          select_comp = `
                        <option value="FACTURA">F</option>
                        <option value="BOLETA">B</option>
                        <option value="NOTA DE CREDITO">NC</option>
                        <option value="NOTA DE DEBITO" selected>ND</option>
                    `;
        }
      }
    }

    const items = filas();
    let html = `
            <tr class="fila-maqueta" id="tr_${items}">
                <input type="hidden" name="condicion[]" value="A">
                <input type="hidden" name="bolsa[]" value="0">
                <input type="hidden" name="total[]" value="${total.value}" id="total-${items}">

                <input type="hidden" name="numeracion[]" value="${items}">

                <td>${items}</td>
                <td><input type="date" name="fecha[]" id="fecha-date-${items}" class="form-control" value="${fecha.value}" required="" /></td>
                <td class="tipo_moneda">
                    <select class="form-control" id="tipo_moneda_${items}" name="tipo_moneda[]" onchange="ver_tipo_cambio(event,${items},2)">
                        ${select_moneda}
                    </select>
                </td>
                <td class="documento">
                    <select class="form-control" id="doc_${items}" name="documento[]" style="width: 40px">
                        ${select_comp}
                    </select>
                </td>
                <td><input type="text" name="serie_correlativo[]" id="numero-${items}" class="form-control" value="" /></td>
                <td><input type="text" name="ruc_cliente[]" id="num-ruc-${items}" class="form-control" style="width: 120px;" onkeypress="return solonumeros(event)" value="${ruc.value}" /></td>
                <td><input type="text" name="vventa[]" class="form-control" value="${vventa.value}" onkeypress="return solonumeros(event)" id="vventa-${items}" onkeyup="calcular_total(event,${items})" style="width: 90px;" /></td>
                <td><input type="text" name="valor_venta[]" class="form-control" value="${valor_venta.value}" onkeypress="return solonumeros(event)" id="valor-venta-${items}" onkeyup="calcular_total(event,${items})" style="width: 90px;" /></td>
                <td><input type="text" name="igv[]" class="form-control" value="${igv.value}" style="width: 70px;" onkeypress="return solonumeros(event)" id="igv-${items}" onkeyup="calcular_total_igv(event,${items})" /></td>
                <td>0</td>
                <td><input type="text" name="icb[]" class="form-control" value="${icb.value}" style="width: 70px;" id="icb-${items}" onkeyup="calcular_total_icb(event,${items})" /></td>
                <td id="text-total-${items}">${total.value}</td>
                <td><input type="text" class="form-control" name="tipo_cambio[]" id="tipo-cambio-${items}" value="${tipo_cambio.value}" style="width: 70px;"></td>
                <td><input type="text" class="form-control edit-glosa" name="glosa[]" autocomplete="off" value="${glosa.value}" onkeyup="completarGlosa(event, ${items})" id="glosa_${items}" style="width: 90px;"></td>
                <td><input type="text" class="form-control edit-cuenta" name="cuenta[]" autocomplete="off" onkeyup="sugerirGlosa(event, ${items})" value="${cuenta.value}" id="cuenta_${items}" style="width: 90px;" ></td>
                <td>
                    <select class="form-control" name="afectacion[]" id="afectacion_${items}">
                        ${select_afectacion}
                    </select>
                </td>
                <td>
                    <i class="bx bx-trash text-danger fs-2 eliminar_fila" title="eliminar fila"></i>
                    <i class="bx bx-plus text-info fs-2 duplicar_fila" title="duplicar fila" data-item="${items}"></i>
                </td>
            </tr>
        `;

    $("#data_maqueta").prepend(html);
  }
});

function filas() {
  const fila = document.querySelectorAll("#data_maqueta tr").length;
  return fila + 1;
}

function filas_venta() {
  const fila = document.querySelectorAll("#data_maqueta_ventas tr").length;
  return fila + 1;
}

function filas_() {
  const fila = document.querySelectorAll("#data_maqueta tr").length;
  return fila;
}

function solonumeros(e) {
  key = e.keyCode || e.which;
  teclado = String.fromCharCode(key);
  numeros = " 0123456789.";
  especiales = "8-37-38-46";
  teclado_especial = false;

  for (var i in especiales) {
    if (key == especiales[i]) {
      teclado_especial = true;
    }
  }
  if (numeros.indexOf(teclado) == -1 && !teclado_especial) {
    return false;
  }
}

function calcular_total(e, item) {
  const vventa = document.getElementById("vventa-" + item);
  const valor_venta = document.getElementById("valor-venta-" + item);
  const total = document.getElementById("total-" + item);
  const text_total = document.getElementById("text-total-" + item);
  const igv = document.getElementById("igv-" + item);
  const icb = document.getElementById("icb-" + item);

  const valor = e.target.value;

  vventa.value = valor;
  valor_venta.value = valor;

  let amount =
    parseFloat(valor) + parseFloat(igv.value) + parseFloat(icb.value);

  total.value = amount.toFixed(2);

  text_total.textContent = amount.toFixed(2);
}

function calcular_total_igv(e, item) {
  const valor_venta = document.getElementById("valor-venta-" + item);
  const total = document.getElementById("total-" + item);
  const text_total = document.getElementById("text-total-" + item);
  const icb = document.getElementById("icb-" + item);

  const valor = e.target.value;

  let amount =
    parseFloat(valor) + parseFloat(valor_venta.value) + parseFloat(icb.value);

  total.value = amount.toFixed(2);
  text_total.textContent = amount.toFixed(2);

  const afect = document.getElementById("afectacion_" + item);

  afect.forEach((af) => {
    if (valor > 0) {
      if (af.value == "N") {
        af.selected = false;
      } else {
        af.selected = true;
      }
    } else {
      if (af.value == "N") {
        af.selected = true;
      } else {
        af.selected = false;
      }
    }
  });
}

function calcular_total_icb(e, item) {
  const valor_venta = document.getElementById("valor-venta-" + item);
  const total = document.getElementById("total-" + item);
  const text_total = document.getElementById("text-total-" + item);
  const igv = document.getElementById("igv-" + item);

  const valor = e.target.value;

  let amount =
    parseFloat(valor) + parseFloat(valor_venta.value) + parseFloat(igv.value);

  total.value = amount.toFixed(2);
  text_total.textContent = amount.toFixed(2);
}

function calcular_igv_pdf(e, number) {
  const vventa = document.getElementById("vventa-" + number);
  const valor_venta = document.getElementById("valor_venta-" + number);
  const igv = document.getElementById("data-igv-" + number);
  const icb = document.getElementById("data-icb-" + number);
  const total = document.getElementById("tt-" + number);
  const afect = document.getElementById("afectacion-compra-" + number);

  let total_sin_icb = parseFloat(total.textContent) - parseFloat(icb.value);

  let total_igv;
  let subtotal;

  if (e.target.checked) {
    total_igv = total_sin_icb - total_sin_icb / 1.18;
    subtotal = total_sin_icb / 1.18;
    afect.value = "S";
  } else {
    total_igv = 0.0;
    subtotal = total_sin_icb;
    afect.value = "N";
  }

  vventa.value = subtotal.toFixed(2);
  valor_venta.value = subtotal.toFixed(2);

  igv.value = total_igv.toFixed(2);
}

function calcular_total_icb_pdf(e, number) {
  const vventa = document.getElementById("vventa-" + number);
  const valor_venta = document.getElementById("valor_venta-" + number);
  const igv = document.getElementById("data-igv-" + number);
  const icb = document.getElementById("data-icb-" + number);
  const total = document.getElementById("tt-" + number);

  const check = document.getElementById("check-igv-" + number);

  let total_sin_icb = parseFloat(total.textContent) - e.target.value;

  let total_igv;
  let subtotal;

  if (check.checked) {
    total_igv = total_sin_icb - total_sin_icb / 1.18;
    subtotal = total_sin_icb / 1.18;
  } else {
    total_igv = 0.0;
    subtotal = total_sin_icb;
  }

  vventa.value = subtotal.toFixed(2);
  valor_venta.value = subtotal.toFixed(2);

  igv.value = total_igv.toFixed(2);
}

function ver_tipo_cambio(e, number, tipo) {
  const valor = e.target.value;
  const fecha = document.getElementById("fecha-date-" + number);
  const cambio = document.getElementById("tipo-cambio-" + number);
  const totalCompra = document.getElementById("total-" + number);

  const tr = document.getElementById("tr_" + number);

  let background = "background-color: #ebeb41;";

  if (tipo == 2) {
    if (fecha.value == "") {
      alert("Ingrese una fecha válida");
      e.target.value = "S";
      return false;
    }
  }

  if (valor == "D") {
    fetch(url_base + "/tipo_cambio/" + fecha.value)
      .then((res) => res.json())
      .then((data) => {
        cambio.value = data;
      });

    if (parseFloat(totalCompra.value) >= 500) {
      tr.setAttribute("style", background);

      Swal.fire({
        icon: "success",
        title: "Recomendación",
        text: "Por favor identifica su comprobante bancarizado!",
      });
    } else {
      tr.setAttribute("style", "");
    }
  } else {
    cambio.value = 1;

    if (parseFloat(totalCompra.value) >= 2000) {
      tr.setAttribute("style", background);

      Swal.fire({
        icon: "success",
        title: "Recomendación",
        text: "Por favor identifica su comprobante bancarizado!",
      });
    } else {
      tr.setAttribute("style", "");
    }
  }
}

pdf_venta.addEventListener("click", () => {
  $("#modal_pdf_venta").modal("show");
  const file_factura = document.getElementById("archivo_factura");
  const file_boleta = document.getElementById("archivo_boleta");
  const file_nota_credito = document.getElementById("archivo_nota_credito");
  const file_nota_debito = document.getElementById("archivo_nota_debito");
  const glosaModal = document.getElementById("glosa_modal");
  const cuentaModal = document.getElementById("cuenta_modal");
  const checkIgv = document.getElementById("switch3");

  file_factura.value = "";
  file_boleta.value = "";
  file_nota_credito.value = "";
  file_nota_debito.value = "";
  glosaModal.value = "";
  cuentaModal.value = "";
  checkIgv.checked = false;
});

cancelar.addEventListener("click", () => {
  btngenerar.disabled = false;
  btngenerar.textContent = "GENERAR MAQUETA DE COMPRAS";
});

form_venta.addEventListener("submit", (e) => {
  e.preventDefault();

  const btn_pdf_venta = document.getElementById("procesar_venta");
  btn_pdf_venta.disabled = true;
  btn_pdf_venta.textContent = "Procesando...";

  const formData = new FormData(form_venta);

  fetch(url_base + "/maqueta_ventas", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      btn_pdf_venta.disabled = false;
      btn_pdf_venta.textContent = "Procesar";
      $("#modal_pdf_venta").modal("hide");
      view_venta(data);
    });
});

function view_venta(data) {
  let html = "";

  let facturas = data.facturas;
  let boletas = data.boletas;
  let notas_credito = data.notas_credito;
  let notas_debito = data.notas_debito;

  facturas.forEach((fact) => {
    let fila = filas_venta();
    let select_igv;

    if (data.igv == 1) {
      select_igv = `
                <option value="N">N</option>
                <option value="S" selected>S</option>
            `;
    } else {
      select_igv = `
                <option value="N" selected>N</option>
                <option value="S">S</option>
            `;
    }

    html = `
            <tr>
                <input type="hidden" name="fecha_venta[]" value="${fact.fecha}"/>
                <input type="hidden" name="moneda_venta[]" value="${fact.moneda}" />
                <input type="hidden" name="comprobante_venta[]" value="${fact.comprobante}" />
                <input type="hidden" name="numero_venta[]" value="${fact.serie}-${fact.numero}" />
                <input type="hidden" name="condicion_venta[]" value="A" />
                <input type="hidden" name="ruc_venta[]" value="${fact.ruc}" />
                <input type="hidden" name="vventa_venta[]" id="valor_venta${fila}" value="${fact.valor_venta}" />
                <input type="hidden" name="valor_v[]" id="valor_v${fila}" value="${fact.valor_venta}" />
                <input type="hidden" name="igv_venta[]" id="totalIgv${fila}" value="${fact.total_igv}" />
                <input type="hidden" name="bolsa_venta[]" value="0" />
                <input type="hidden" name="icb_venta[]" value="0" />
                <input type="hidden" name="total_venta[]" id="totalMonto${fila}" value="${fact.monto}" />
                <input type="hidden" name="tipo_cambio_venta[]" value="${fact.tipo_cambio}" />
                <input type="hidden" name="name_razon[]" value="" />
                <input type="hidden" name="tipo[]" value="" />
                <input type="hidden" name="referencia[]" value="" />
                <input type="hidden" name="fecha_referencia[]" value="" />

                <td>${fila}</td>
                <td>${fact.fecha}</td>
                <td>${fact.moneda}</td>
                <td>${fact.comprobante}</td>
                <td>${fact.serie}-${fact.numero}</td>
                <td>A</td>
                <td>${fact.ruc}</td>
                <td id="textValorVenta${fila}">${fact.valor_venta}</td>
                <td id="textValor${fila}">${fact.valor_venta}</td>
                <td> <span id="textTotalIgv${fila}">${fact.total_igv}</span> <input type="checkbox" onchange="calcularIgv(event, ${fila})" /> </td>
                <td>0</td>
                <td>0</td>
                <td id="textTotal${fila}">${fact.monto}</td>
                <td>${fact.tipo_cambio}</td>
                <td><input type="text" class="form-control edit-glosa" name="glosa_venta[]" value="${data.glosa}" style="width: 90px;"></td>
                <td><input type="text" class="form-control edit-cuenta" name="cuenta_venta[]" value="${data.cuenta}" style="width: 90px;"></td>
                <td>
                    <select class="form-control" name="afectacion_venta[]">
                        ${select_igv}
                    </select>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        `;

    $("#data_maqueta_ventas").append(html);
  });

  boletas.forEach((bol) => {
    let fila = filas_venta();

    let select_igv;

    if (data.igv == 1) {
      select_igv = `
                <option value="N">N</option>
                <option value="S" selected>S</option>
            `;
    } else {
      select_igv = `
                <option value="N" selected>N</option>
                <option value="S">S</option>
            `;
    }

    html = `
            <tr>
                <input type="hidden" name="fecha_venta[]" value="${bol.fecha}"/>
                <input type="hidden" name="moneda_venta[]" value="${bol.moneda}" />
                <input type="hidden" name="comprobante_venta[]" value="${bol.comprobante}" />
                <input type="hidden" name="numero_venta[]" value="${bol.serie}-${bol.numero}" />
                <input type="hidden" name="condicion_venta[]" value="A" />
                <input type="hidden" name="ruc_venta[]" value="${bol.ruc}" />
                <input type="hidden" name="vventa_venta[]" id="valor_venta${fila}" value="${bol.valor_venta}" />
                <input type="hidden" name="valor_v[]" id="valor_v${fila}" value="${bol.valor_venta}" />
                <input type="hidden" name="igv_venta[]" id="totalIgv${fila}" value="${bol.total_igv}" />
                <input type="hidden" name="bolsa_venta[]" value="0" />
                <input type="hidden" name="icb_venta[]" value="0" />
                <input type="hidden" name="total_venta[]" id="totalMonto${fila}" value="${bol.monto}" />
                <input type="hidden" name="tipo_cambio_venta[]" value="${bol.tipo_cambio}" />
                <input type="hidden" name="name_razon[]" value="${bol.razon}" />
                <input type="hidden" name="tipo[]" value="" />
                <input type="hidden" name="referencia[]" value="" />
                <input type="hidden" name="fecha_referencia[]" value="" />

                <td>${fila}</td>
                <td>${bol.fecha}</td>
                <td>${bol.moneda}</td>
                <td>${bol.comprobante}</td>
                <td>${bol.serie}-${bol.numero}</td>
                <td>A</td>
                <td>${bol.ruc}</td>
                <td id="textValorVenta${fila}">${bol.valor_venta}</td>
                <td id="textValor${fila}">${bol.valor_venta}</td>
                <td> <span id="textTotalIgv${fila}">${bol.total_igv}</span> <input type="checkbox" onchange="calcularIgv(event, ${fila})" /> </td>
                <td>0</td>
                <td>0</td>
                <td id="textTotal${fila}">${bol.monto}</td>
                <td>${bol.tipo_cambio}</td>
                <td><input type="text" class="form-control edit-glosa" name="glosa_venta[]" value="${data.glosa}" style="width: 90px;"></td>
                <td><input type="text" class="form-control edit-cuenta" name="cuenta_venta[]" value="${data.cuenta}" style="width: 90px;"></td>
                <td>
                    <select class="form-control" name="afectacion_venta[]">
                        ${select_igv}
                    </select>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        `;

    $("#data_maqueta_ventas").append(html);
  });

  notas_credito.forEach((note) => {
    let fila = filas_venta();

    let select_igv;

    if (data.igv == 1) {
      select_igv = `
                <option value="N">N</option>
                <option value="S" selected>S</option>
            `;
    } else {
      select_igv = `
                <option value="N" selected>N</option>
                <option value="S">S</option>
            `;
    }

    html = `
            <tr>
                <input type="hidden" name="fecha_venta[]" value="${note.fecha}"/>
                <input type="hidden" name="moneda_venta[]" value="${note.moneda}" />
                <input type="hidden" name="comprobante_venta[]" value="${note.comprobante}" />
                <input type="hidden" name="numero_venta[]" value="${note.serie}-${note.numero}" />
                <input type="hidden" name="condicion_venta[]" value="A" />
                <input type="hidden" name="ruc_venta[]" value="${note.ruc}" />
                <input type="hidden" name="vventa_venta[]" value="${note.valor_venta}" />
                <input type="hidden" name="valor_v[]" value="${note.valor_venta}" />
                <input type="hidden" name="igv_venta[]" value="${note.total_igv}" />
                <input type="hidden" name="bolsa_venta[]" value="0" />
                <input type="hidden" name="icb_venta[]" value="0" />
                <input type="hidden" name="total_venta[]" value="${note.monto}" />
                <input type="hidden" name="tipo_cambio_venta[]" value="${note.tipo_cambio}" />
                <input type="hidden" name="name_razon[]" value="${note.razon}" />
                <input type="hidden" name="tipo[]" value="${note.tipo}" />
                <input type="hidden" name="referencia[]" value="${note.referencia}" />
                <input type="hidden" name="fecha_referencia[]" value="${note.fecha_referencia}" />

                <td>${fila}</td>
                <td>${note.fecha}</td>
                <td>${note.moneda}</td>
                <td>${note.comprobante}</td>
                <td>${note.serie}-${note.numero}</td>
                <td>A</td>
                <td>${note.ruc}</td>
                <td>${note.valor_venta}</td>
                <td>${note.valor_venta}</td>
                <td>${note.total_igv}</td>
                <td>0</td>
                <td>0</td>
                <td>${note.monto}</td>
                <td>${note.tipo_cambio}</td>
                <td><input type="text" class="form-control edit-glosa" name="glosa_venta[]" value="${data.glosa}" style="width: 90px;"></td>
                <td><input type="text" class="form-control edit-cuenta" name="cuenta_venta[]" value="${data.cuenta}" style="width: 90px;"></td>
                <td>
                    <select class="form-control" name="afectacion_venta[]">
                        ${select_igv}
                    </select>
                </td>
                <td>${note.tipo}</td>
                <td>${note.referencia}</td>
                <td>${note.fecha_referencia}</td>
            </tr>
        `;

    $("#data_maqueta_ventas").append(html);
  });

  notas_debito.forEach((debito) => {
    let fila = filas_venta();

    let select_igv;

    if (data.igv == 1) {
      select_igv = `
                <option value="N">N</option>
                <option value="S" selected>S</option>
            `;
    } else {
      select_igv = `
                <option value="N" selected>N</option>
                <option value="S">S</option>
            `;
    }

    html = `
            <tr>
                <input type="hidden" name="fecha_venta[]" value="${debito.fecha}"/>
                <input type="hidden" name="moneda_venta[]" value="${debito.moneda}" />
                <input type="hidden" name="comprobante_venta[]" value="${debito.comprobante}" />
                <input type="hidden" name="numero_venta[]" value="${debito.serie}-${debito.numero}" />
                <input type="hidden" name="condicion_venta[]" value="A" />
                <input type="hidden" name="ruc_venta[]" value="${debito.ruc}" />
                <input type="hidden" name="vventa_venta[]" value="${debito.valor_venta}" />
                <input type="hidden" name="valor_v[]" value="${debito.valor_venta}" />
                <input type="hidden" name="igv_venta[]" value="${debito.total_igv}" />
                <input type="hidden" name="bolsa_venta[]" value="0" />
                <input type="hidden" name="icb_venta[]" value="0" />
                <input type="hidden" name="total_venta[]" value="${debito.monto}" />
                <input type="hidden" name="tipo_cambio_venta[]" value="${debito.tipo_cambio}" />
                <input type="hidden" name="name_razon[]" value="${debito.razon}" />
                <input type="hidden" name="tipo[]" value="${debito.tipo}" />
                <input type="hidden" name="referencia[]" value="${debito.referencia}" />
                <input type="hidden" name="fecha_referencia[]" value="${debito.fecha_referencia}" />

                <td>${fila}</td>
                <td>${debito.fecha}</td>
                <td>${debito.moneda}</td>
                <td>${debito.comprobante}</td>
                <td>${debito.serie}-${debito.numero}</td>
                <td>A</td>
                <td>${debito.ruc}</td>
                <td>${debito.valor_venta}</td>
                <td>${debito.valor_venta}</td>
                <td>${debito.total_igv}</td>
                <td>0</td>
                <td>0</td>
                <td>${debito.monto}</td>
                <td>${debito.tipo_cambio}</td>
                <td><input type="text" class="form-control edit-glosa" name="glosa_venta[]" value="${data.glosa}" style="width: 90px;"></td>
                <td><input type="text" class="form-control edit-cuenta" name="cuenta_venta[]" value="${data.cuenta}" style="width: 90px;"></td>
                <td>
                    <select class="form-control" name="afectacion_venta[]">
                        ${select_igv}
                    </select>
                </td>
                <td>${debito.tipo}</td>
                <td>${debito.referencia}</td>
                <td>${debito.fecha_referencia}</td>
            </tr>
        `;

    $("#data_maqueta_ventas").append(html);
  });
}

function calcularIgv(e, fila) {
  const total = document.getElementById("totalMonto" + fila);
  const tdTotal = document.getElementById("textTotal" + fila);

  const valor_venta = document.getElementById("valor_venta" + fila);
  const valor_v = document.getElementById("valor_v" + fila);

  const textValorVenta = document.getElementById("textValorVenta" + fila);
  const textValor = document.getElementById("textValor" + fila);

  const totalIgv = document.getElementById("totalIgv" + fila);
  const textTotalIgv = document.getElementById("textTotalIgv" + fila);

  if (e.target.checked) {
    const monto = parseFloat(total.value);
    const newIgv = monto * 0.18;
    const newValor = monto - newIgv;

    valor_venta.value = newValor;
    valor_v.value = newValor;

    textValorVenta.textContent = newValor;
    textValor.textContent = newValor;

    totalIgv.value = newIgv;
    textTotalIgv.textContent = newIgv;
  } else {
    valor_venta.value = total.value;
    valor_v.value = total.value;

    textValorVenta.textContent = total.value;
    textValor.textContent = total.value;

    totalIgv.value = 0.0;
    textTotalIgv.textContent = "0.00";
  }
}

btnrh.addEventListener("click", () => {
  $("#modalrh").modal("show");
  const modal_txt = document.getElementById("form_subir_rh");
  modal_txt.reset();
});

form_rh.addEventListener("submit", (e) => {
  e.preventDefault();

  const proce = document.getElementById("procesarrh");

  proce.disabled = false;
  proce.textContent = "Procesando...";

  const formData = new FormData(form_rh);

  fetch(url_base + "/procesar_rh", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      $("#modalrh").modal("hide");

      proce.disabled = true;
      proce.textContent = "Procesar";

      data.forEach((el) => {
        let fila = filas();

        let html = `

                <tr class="fila-maqueta">
                    <input type="hidden" name="items[]" value="${fila}">
                    <input type="hidden" name="fecha[]" value="${el.fecha}">
                    <input type="hidden" name="documento[]" value="${
                      el.comprobante
                    }">
                    <input type="hidden" name="serie_correlativo[]" value="${
                      el.num_doc
                    }">
                    <input type="hidden" name="condicion[]" value="A">
                    <input type="hidden" name="ruc_cliente[]" value="${el.ruc}">
                    <input type="hidden" name="vventa[]" value="${el.monto}">
                    <input type="hidden" name="valor_venta[]" value="${
                      el.monto
                    }">
                    <input type="hidden" name="igv[]" value="0">
                    <input type="hidden" name="bolsa[]" value="0">
                    <input type="hidden" name="icb[]" value="${
                      el.otros_impuestos
                    }">
                    <input type="hidden" name="total[]" value="${el.monto}">
                    <input type="hidden" name="tipo_cliente[]" >
                    <input type="hidden" name="serie[]" >
                    <input type="hidden" name="correlativo[]" >
                    <input type="hidden" name="tipo_documento[]" >

                    <input type="hidden" name="numeracion[]" value="${fila}">

                    <td>${fila}</td>
                    <td>${el.fecha_date}</td>
                    <td class="tipo_moneda">
                        <select class="form-control" name="tipo_moneda[]" onchange="ver_tipo_cambio(event,${1},1)">
                            <option value="S" selected>S</option>
                            <option value="D">D</option>
                        </select>
                    </td>
                    <td>${el.doc}</td>
                    <td>${el.num_doc}</td>
                    <td>${el.ruc}</td>
                    <td>${el.monto}</td>
                    <td>${el.monto}</td>
                    <td>0</td>
                    <td>0</td>
                    <td>${el.otros_impuestos}</td>
                    <td>${el.total_cancelar}</td>
                    <td><input type="text" class="form-control" name="tipo_cambio[]" id="tipo-cambio-${
                      el.tipo_cambio
                    }" value="1" style="width: 70px;"></td>
                    <td><input type="text" class="form-control edit-glosa" name="glosa[]" value="${
                      el.glosa
                    }" onkeyup="completarGlosa(event, ${fila})" style="width: 90px;"></td>
                    <td><input type="text" class="form-control edit-cuenta" name="cuenta[]" onkeyup="sugerirGlosa(event, ${fila})" value="${
                      el.cuenta
                    }" style="width: 90px;"></td>
                    <td>
                        <select class="form-control" name="afectacion[]">
                            <option value="N" selected>N</option>
                            <option value="S">S</option>
                        </select>
                    </td>
                    <td>
                        <i class="bx bx-trash text-danger fs-2 eliminar_fila"></i>
                    </td>
                </tr>
            `;

        $("#data_maqueta").prepend(html);
      });
    })
    .catch(function (error) {
      proce.disabled = true;
      proce.textContent = "Procesar";
      console.log("Hubo un problema con la petición Fetch:" + error.message);
      alert(error.message);
    });
});

function new_total(e, number) {
  const valor = e.target.value;
  const valor_venta = document.getElementById("valor_venta-" + number);

  const igv = document.getElementById("data-igv-" + number);
  const icb = document.getElementById("data-icb-" + number);

  const t = document.getElementById("t-" + number);
  const tt = document.getElementById("tt-" + number);

  const total =
    parseFloat(valor) + parseFloat(igv.value) + parseFloat(icb.value);

  valor_venta.value = valor;
  t.value = total;
  tt.innerText = total;
}

function new_total_igv(e, number) {
  const valor = e.target.value;
  const valor_venta = document.getElementById("vventa-" + number);

  const icb = document.getElementById("data-icb-" + number);

  const t = document.getElementById("t-" + number);
  const tt = document.getElementById("tt-" + number);

  const total =
    parseFloat(valor) + parseFloat(valor_venta.value) + parseFloat(icb.value);

  t.value = total;
  tt.innerText = total;
}

function new_total_icb(e, number) {
  const valor = e.target.value;
  const valor_venta = document.getElementById("vventa-" + number);

  const igv = document.getElementById("data-igv-" + number);

  const t = document.getElementById("t-" + number);
  const tt = document.getElementById("tt-" + number);

  const total =
    parseFloat(valor) + parseFloat(valor_venta.value) + parseFloat(igv.value);

  t.value = total;
  tt.innerText = total;
}

function comprobar_duplicidad(item, tipoEntrada) {
  const numero = document.getElementById("numero-" + item);
  const ruc = document.getElementById("num-ruc-" + item);
  const doc = document.getElementById("doc_" + item);

  if (ruc.value == ruc_contribuyente.value) {
    alert("El ruc del proveedor es igual al ruc del contribuyente");
    return false;
  }

  //comprobar duplicidad en la lista de los documentos
  const serieInputs = document.querySelectorAll(
    'input[name="serie_correlativo[]"]',
  );
  const rucInputs = document.querySelectorAll('input[name="ruc_cliente[]"]');
  const numeracion = document.querySelectorAll('input[name="numeracion[]"]');

  if (serieInputs.length > 1) {
    let existeSerie = 0;
    let existeRuc = 0;
    let itemRep = 0;

    for (let i = 1; i < serieInputs.length; i++) {
      const serie = serieInputs[i];
      const rucs = rucInputs[i];

      if (serie.value === numero.value) {
        existeSerie = 1;
      }

      if (rucs.value === ruc.value) {
        existeRuc = 1;
      }

      if (existeSerie === 1 && existeRuc === 1) {
        itemRep = numeracion[i].value;
        break;
      }
    }

    if (existeSerie === 1 && existeRuc === 1) {
      console.log(itemRep);
      alert(
        `El comprobante ${numero.value} ya existe de dicho proveedor ${ruc.value} en el item ${itemRep}`,
      );
      return;
    }
  }

  //comprobar duplicidad desde la base de datos
  const formData = new FormData();
  formData.append("serie_numero", numero.value);
  formData.append("cliente", ruc_contribuyente.value);
  formData.append("proveedor", ruc.value);
  formData.append("tipo_doc", doc.value);

  fetch(url_base + "/comprobar-duplicidad", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      console.log(data);
      if (data.respuesta == "existe") {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: data.mensaje,
        });
      } else {
        const ruc_active = document.getElementById("ruc_activo");

        if (ruc_active.value == 1) {
          const serie_num = numero.value;
          const serie_numero = serie_num.split("-");

          const serie_ = serie_numero[0];
          const correl_ = serie_numero[1];

          const ruc_ = ruc.value;

          let primerDigitoSerie = serie_num.slice(0, 1);

          primerDigitoSerie = primerDigitoSerie.toUpperCase();

          console.log(primerDigitoSerie);

          if (primerDigitoSerie == "E") {
            Swal.fire({
              icon: "error",
              title: "Oops...",
              text: "Recuerde que los comprobantes que tienen la serie con letra E se descarga de la clave sol y se sube por pdf",
            });

            return false;
          }

          if (primerDigitoSerie == "F") {
            //console.log('entra');
            if (tipoEntrada == 0) {
              if (ruc_.length == 11) {
                consultar_compra_sunat_manual(ruc_, serie_, correl_, item);
              }
            } else {
              if (serie_numero.length == 2) {
                if (ruc_.length == 11) {
                  consultar_compra_sunat_manual(ruc_, serie_, correl_, item);
                }
              }
            }
          }
        }
      }
    });
}

function consultar_compra_sunat_manual(
  ruc_proveedor,
  serie_consultar,
  numero_consultar,
  item,
) {
  const rucActivo = ruc_contribuyente.value;
  const formData = new FormData();
  formData.append("ruc", ruc_proveedor);
  formData.append("serie", serie_consultar);
  formData.append("correlativo", numero_consultar);
  formData.append("ruc_activo", rucActivo);

  const spinner = document.getElementById("spinnerVisible");

  spinner.classList.remove("spinner-visible");

  const timeout = new Promise((resolve, reject) => {
    setTimeout(() => {
      reject(
        new Error(
          "Tiempo de espera excedido, escribe los datos de forma manual o intente de nuevo consultar",
        ),
      );
    }, 5000);
  });

  Promise.race([
    fetch(url_base + "/consultar_compra", {
      method: "POST",
      body: formData,
    }),
    timeout,
  ])
    .then((res) => res.json())
    .then((data) => {
      const datos = JSON.parse(data);

      spinner.classList.add("spinner-visible");
      //console.log(datos);
      if (datos.success === true) {
        itemConsultaManual(datos, numero_consultar, item);
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: datos.message,
        });
      }
    })
    .catch((error) => {
      spinner.classList.add("spinner-visible");

      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: error,
      });
    });
}

function consultar_compra_sunat(
  ruc_proveedor,
  serie_consultar,
  numero_consultar,
) {
  const rucActivo = ruc_contribuyente.value;
  const formData = new FormData();
  formData.append("ruc", ruc_proveedor);
  formData.append("serie", serie_consultar);
  formData.append("correlativo", numero_consultar);
  formData.append("ruc_activo", rucActivo);

  const spinner = document.getElementById("spinnerVisible");

  spinner.classList.remove("spinner-visible");

  const timeout = new Promise((resolve, reject) => {
    setTimeout(() => {
      reject(
        new Error(
          "Tiempo de espera excedido, escribe los datos de forma manual o intente de nuevo consultar",
        ),
      );
    }, 5000);
  });

  Promise.race([
    fetch(url_base + "/consultar_compra", {
      method: "POST",
      body: formData,
    }),
    timeout,
  ])
    .then((res) => res.json())
    .then((data) => {
      const datos = JSON.parse(data);
      console.log(datos);
      spinner.classList.add("spinner-visible");
      if (datos.success === true) {
        itemConsulta(datos, numero_consultar);
      } else {
        Swal.fire({
          //title: 'Are you sure?',
          text: datos.message,
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Agregar",
          cancelButtonText: "Cancelar",
        }).then((result) => {
          if (result.isConfirmed) {
            mostrarItemErrorConsulta(
              serie_consultar + "-" + numero_consultar,
              ruc_proveedor,
            );
          }
        });
      }
    })
    .catch((error) => {
      spinner.classList.add("spinner-visible");

      Swal.fire({
        //title: 'Are you sure?',
        text: error,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Agregar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          mostrarItemErrorConsulta(
            serie_consultar + "-" + numero_consultar,
            ruc_proveedor,
          );
        }
      });
    });
}

function itemConsulta(data, correlativo) {
  const items = filas();

  const fila = document.querySelectorAll(".fila-maqueta");
  const glosa = document.querySelectorAll(".edit-glosa");
  const cuenta = document.querySelectorAll(".edit-cuenta");

  let text_glosa;
  let text_cuenta;

  if (fila.length == 0) {
    text_glosa = "";
    text_cuenta = "";
  } else {
    $cant_fila = glosa.length;
    text_glosa = glosa[0].value;
    text_cuenta = cuenta[0].value;
  }

  let comprobante = [
    {
      documento: "FACTURA",
      abreviatura: "F",
      codigo: "01",
    },
    {
      documento: "BOLETA",
      abreviatura: "B",
      codigo: "03",
    },
    {
      documento: "NOTA DE CREDITO",
      abreviatura: "NC",
      codigo: "07",
    },
    {
      documento: "NOTA DE DEBITO",
      abreviatura: "NB",
      codigo: "08",
    },
  ];

  let select_comp = "";
  let select_tipo_doc = "";

  comprobante.forEach((comp) => {
    if (comp.codigo == data.result.comprobante.tipo) {
      select_tipo_doc = "selected";
    } else {
      select_tipo_doc = "";
    }
    select_comp += `
            <option value="${comp.documento}" ${select_tipo_doc}>${comp.abreviatura}</option>
        `;
  });

  let afect;

  if (parseFloat(data.result.impuesto.IGV.total) > 0) {
    afect = "S";
  } else {
    afect = "N";
  }

  let data_afect = ["N", "S"];
  let option_afect = "";
  let select_afect;

  data_afect.forEach((a) => {
    if (a == afect) {
      select_afect = "selected";
    } else {
      select_afect = "";
    }

    option_afect += `<option value="${a}" ${select_afect}>${a}</option>`;
  });

  const moneda = data.result.comprobante.moneda;
  let background = "";

  let data_moneda = ["S", "D"];
  let option_moneda = "";
  let select_moneda;
  let mon_;

  if (moneda === "USD") {
    mon_ = "D";
  } else {
    mon_ = "S";
  }

  data_moneda.forEach((mon) => {
    if (mon == mon_) {
      select_moneda = "selected";
    } else {
      select_moneda = "";
    }

    option_moneda += `<option value="${mon}" ${select_moneda}>${mon}</option>`;
  });

  let color_otros;

  if (data.result.totales.otros_cargos == "0.00") {
    color_otros = "";
  } else {
    color_otros = "background: #49d049; color: white";
  }

  if (moneda == "PEN" && parseFloat(data.result.totales.importe) >= 2000) {
    background = `style="background-color: #ebeb41;"`;
  }

  if (moneda == "USD" && parseFloat(data.result.totales.importe) >= 500) {
    background = `style="background-color: #ebeb41;"`;
  }

  const icb =
    parseFloat(data.result.totales.otros_cargos) +
    parseFloat(data.result.impuesto.ISC.total) +
    parseFloat(data.result.impuesto.EXO.total);

  if (esFechaValida(data.result.comprobante.fecha_emision)) {
    const comprobar_fecha = comprobar_comprobante_mismo_periodo(
      data.result.comprobante.fecha_emision,
    );

    if (comprobar_fecha == false) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text:
          "La fecha del comprobante (" +
          data.result.comprobante.fecha_emision +
          ") pertenece a otro año",
      });

      return false;
    }
  }

  let html = `
        <tr class="fila-maqueta" data-id="${items}" id="tr_${items}" ${background}>
            <input type="hidden" name="items[]" value="${items}">
            <input type="hidden" name="condicion[]" value="A">
            <input type="hidden" name="bolsa[]" value="0">
            <input type="hidden" name="total[]" id="t-${items}" value="${
              data.result.totales.importe
            }">
            <input type="hidden" name="totales_[]" value="${
              data.result.totales.importe
            }" id="total-${items}">
            <input type="hidden" name="tipo_cliente[]" value="${
              data.result.receptor.tipo_doc
            }">
            <input type="hidden" name="serie[]" value="${
              data.result.comprobante.serie
            }">
            <input type="hidden" name="correlativo[]" value="${correlativo}">
            <input type="hidden" name="tipo_documento[]" value="${
              data.result.comprobante.tipo
            }">

            <input type="hidden" name="numeracion[]" value="${items}">

            <td>${items}</td>
            <td><input type="date" class="form-control" name="fecha[]" id="fecha-date-${items}" value="${
              data.result.comprobante.fecha_emision
            }" required="" readonly></td>
            <td class="tipo_moneda">
                <select class="form-control" name="tipo_moneda[]" onchange="ver_tipo_cambio(event,${items},1)">
                    ${option_moneda}
                </select>
            </td>
            <td>
                <select name="documento[]" class="form-control">
                    ${select_comp}
                </select>
            </td>
            <td><input type="text" id="numero-${items}" name="serie_correlativo[]" class="form-control" value="${
              data.result.comprobante.serie
            }-${correlativo}" readonly></td>
            <td><input type="text" name="ruc_cliente[]" id="num-ruc-${items}" class="form-control" value="${
              data.result.emisor.ruc
            }" style="width: 120px;" readonly></td>
            <td id="text_vventa_${items}"><input type="text" class="form-control" name="vventa[]" id="vventa-${items}" value="${
              data.result.totales.valor
            }" onkeyup="new_total(event,${items})" style="width: 80px;" readonly></td>
            <td id="text_valor_venta_${items}"><input type="text" class="form-control" name="valor_venta[]" id="valor_venta-${items}" value="${
              data.result.totales.valor
            }" style="width: 80px;" readonly></td>
            <td><input type="text" name="igv[]" class="form-control" id="data-igv-${items}" value="${
              data.result.impuesto.IGV.total
            }" onkeyup="new_total_igv(event,${items})" style="width: 80px;" readonly></td>
            <td>0</td>
            <td><input type="text" id="data-icb-${items}" class="form-control" name="icb[]" value="${icb.toFixed(
              2,
            )}" onkeyup="new_total_icb(event,${items})" style="width: 60px;${color_otros}" readonly></td>
            <td id="tt-${items}">${data.result.totales.importe}</td>
            <td><input type="text" class="form-control" name="tipo_cambio[]" id="tipo-cambio-${items}" value="1" style="width: 70px;"></td>
            <td><input type="text" class="form-control edit-glosa" name="glosa[]" autocomplete="off" id="glosa_${items}" value="${text_glosa}" onkeyup="completarGlosa(event, ${items})" style="width: 90px;"></td>
            <td><input type="text" class="form-control edit-cuenta" name="cuenta[]" autocomplete="off" id="cuenta_${items}" value="${text_cuenta}" onkeyup="sugerirGlosa(event, ${items})" style="width: 90px;"></td>
            <td>
                <select class="form-control" name="afectacion[]">
                    ${option_afect}
                </select>
            </td>
            <td>
                <i class="bx bx-trash text-danger fs-2 eliminar_fila"></i>
                <i class="bx bxs-edit text-info fs-2 editar_fila" data-item="${items}"></i>
            </td>
        </tr>
    `;

  let tipodoc = "";

  if (data.result.comprobante.tipo == "01") {
    tipodoc = "FACTURA";
  }

  if (data.result.comprobante.tipo == "03") {
    tipodoc = "BOLETA";
  }

  if (data.result.comprobante.tipo == "07") {
    tipodoc = "NOTA DE CREDITO";
  }

  if (data.result.comprobante.tipo == "08") {
    tipodoc = "NOTA DE DEBITO";
  }

  const formData = new FormData();
  formData.append(
    "serie_numero",
    data.result.comprobante.serie + "-" + correlativo,
  );
  formData.append("cliente", ruc_contribuyente.value);
  formData.append("proveedor", data.result.emisor.ruc);
  formData.append("tipo_doc", tipodoc);

  fetch(url_base + "/comprobar-duplicidad", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((datas) => {
      console.log(datas);
      if (datas.respuesta == "ok") {
        $("#data_maqueta").prepend(html);

        if (
          moneda == "PEN" &&
          parseFloat(data.result.totales.importe) >= 2000
        ) {
          Swal.fire({
            icon: "success",
            title: "Recomendación",
            text: "Por favor identifica su comprobante bancarizado!",
          });
        }

        if (moneda == "USD" && parseFloat(data.result.totales.importe) >= 500) {
          Swal.fire({
            icon: "success",
            title: "Oops...",
            text: "Por favor identifica su comprobante bancarizado!",
          });
        }
      } else {
        Swal.fire({
          title: "Desea agregar a la lista?",
          text: datas.mensaje,
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Si, agregar!",
        }).then((result) => {
          if (result.isConfirmed) {
            $("#data_maqueta").prepend(html);
          }
        });
      }
    });

  //$("#data_maqueta").prepend(html);
}

function itemConsultaManual(data, correlativo, item) {
  if (esFechaValida(data.result.comprobante.fecha_emision)) {
    const comprobar_fecha = comprobar_comprobante_mismo_periodo(
      data.result.comprobante.fecha_emision,
    );

    if (comprobar_fecha == false) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text:
          "La fecha del comprobante (" +
          data.result.comprobante.fecha_emision +
          ") pertenece a otro año",
      });

      return false;
    }
  }

  const fecha = document.getElementById("fecha-date-" + item);
  fecha.value = data.result.comprobante.fecha_emision;

  const tipoMoneda = document.getElementById("tipo_moneda_" + item);
  const tipo_moneda = data.result.comprobante.moneda;

  const idtr = document.getElementById("tr_" + item);

  let background = "background-color: #ebeb41;";

  if (tipo_moneda == "USD") {
    tipoMoneda.value = "D";
  } else {
    tipoMoneda.value = "S";
  }

  if (tipo_moneda == "PEN" && parseFloat(data.result.totales.importe) >= 2000) {
    idtr.setAttribute("style", background);
  }

  if (tipo_moneda == "USD" && parseFloat(data.result.totales.importe) >= 500) {
    idtr.setAttribute("style", background);
  }

  idtr.removeAttribute("style");

  const documento = document.getElementById("doc_" + item);
  const doc = data.result.comprobante.tipo;

  if (doc == "01") {
    documento.value = "FACTURA";
  }

  if (doc == "03") {
    documento.value = "BOLETA";
  }

  if (doc == "07") {
    documento.value = "NOTA DE CREDITO";
  }

  if (doc == "08") {
    documento.value = "NOTA DE DEBITO";
  }

  const valor_venta = document.getElementById("vventa-" + item);
  valor_venta.value = data.result.totales.valor;

  const valorv = document.getElementById("valor-venta-" + item);
  valorv.value = data.result.totales.valor;

  const igv = document.getElementById("igv-" + item);
  igv.value = data.result.impuesto.IGV.total;

  const txtTotal = document.getElementById("text-total-" + item);
  const totalHide = document.getElementById("total-" + item);
  txtTotal.textContent = data.result.totales.importe;
  totalHide.value = data.result.totales.importe;

  const otros = document.getElementById("icb-" + item);

  const icb =
    parseFloat(data.result.totales.otros_cargos) +
    parseFloat(data.result.impuesto.ISC.total) +
    parseFloat(data.result.impuesto.EXO.total);

  otros.value = icb;

  if (data.result.totales.otros_cargos == "0.00") {
    otros.removeAttribute("style");
    otros.setAttribute("style", "width: 60px;");
  } else {
    otros.removeAttribute("style");
    otros.setAttribute(
      "style",
      "width: 60px; background: #49d049; color: white",
    );
  }

  const afectacion = document.getElementById("afectacion_" + item);

  if (parseFloat(data.result.impuesto.IGV.total) > 0) {
    afectacion.value = "S";
  } else {
    afectacion.value = "N";
  }
}

function completarGlosa(e, item) {
  const glosa = e.target.value;
  //console.log(glosa);
  const valorPlan = getPlan();

  $("#glosa_" + item).autocomplete({
    source: function (request, response) {
      $.ajax({
        url: url_base + "/completar_glosa",
        dataType: "jsonp",
        data: {
          term: request.term,
          plan: valorPlan,
        },
        success: function (data) {
          response(data);
        },
      });
    },
    minLength: 2,
    select: function (event, ui) {
      const cuenta = document.getElementById("cuenta_" + item);
      cuenta.value = ui.item.id;
    },
  });
}

function sugerirGlosa(e, item) {
  const valorPlan = getPlan();

  $("#cuenta_" + item)
    .autocomplete({
      source: function (request, response) {
        $.ajax({
          url: url_base + "/sugerir_glosa",
          dataType: "jsonp",
          data: {
            term: request.term,
            plan: valorPlan,
          },
          success: function (data) {
            response(data);
          },
        });
      },
      minLength: 2,
      select: function (event, ui) {
        console.log(event);

        const glosa = document.getElementById("glosa_" + item);
        glosa.value = ui.item.id;
      },
    })
    .autocomplete("instance")._renderItem = function (ul, item) {
    return $("<li>")
      .append("<div>" + item.label + " - " + item.desc + "</div>")
      .appendTo(ul);
  };
}

function getPlan() {
  const radioButtons = document.querySelectorAll('input[name="radioGroup"]');
  var valor = 1;

  // Agregar un evento de cambio a cada elemento de radio
  radioButtons.forEach(function (radioButton) {
    if (radioButton.checked) {
      console.log(
        "El radio button con valor " + radioButton.value + " está seleccionado",
      );
      // Realizar las acciones correspondientes al seleccionar este radio button
      valor = radioButton.value;
    }
  });

  return valor;
}

function verificar_existe_lista(serieNum, ruc) {
  const seriesCorrelativos = document.querySelectorAll(
    'input[name="serie_correlativo[]"]',
  );
  const rucClientes = document.querySelectorAll('input[name="ruc_cliente[]"]');

  //const serieNum = serie.toUpperCase()+"-"+correlativo;
  let repetido = 0;
  let itemCap;

  seriesCorrelativos.forEach((sc, index) => {
    let serieNumero = sc.value;
    let rucProveedor = rucClientes[index].value;
    let itemcon = sc.parentElement.parentElement.getAttribute("data-id");

    if (serieNumero == serieNum && rucProveedor == ruc) {
      repetido = 1;
      itemCap = itemcon;
    }
  });

  const response = [repetido, itemCap];

  return response;
}

function completarRuc(e, item) {
  $("#num-ruc-" + item)
    .autocomplete({
      source: function (request, response) {
        $.ajax({
          url: url_base + "/completar_ruc",
          dataType: "jsonp",
          data: {
            term: request.term,
          },
          success: function (data) {
            response(data);
          },
        });
      },
      minLength: 2,
    })
    .autocomplete("instance")._renderItem = function (ul, item) {
    return $("<li>")
      .append("<div>" + item.label + " - " + item.desc + "</div>")
      .appendTo(ul);
  };
}

function guardarTablaEnLocalStorage() {
  const tabla = document.getElementById("data_maqueta");
  const filas = Array.from(tabla.querySelectorAll("tr"));
  const datos = [];

  filas.forEach((fila) => {
    const celdas = Array.from(fila.querySelectorAll("td"));
    const filaDatos = [];

    celdas.forEach((celda) => {
      filaDatos.push(celda.textContent);
    });

    datos.push(filaDatos);
  });

  const datosJSON = JSON.stringify(datos);
  localStorage.setItem("tablaDatosCompra", datosJSON);
}

function imprimirTablaDesdeLocalStorage() {
  const datosAlmacenados = localStorage.getItem("tablaDatosCompra");
  if (datosAlmacenados) {
    const datosRecuperados = JSON.parse(datosAlmacenados);

    const tabla = document.getElementById("data_maqueta");
    datosRecuperados.forEach((filaDatos) => {
      const nuevaFila = document.createElement("tr");
      filaDatos.forEach((celdaDato) => {
        const nuevaCelda = document.createElement("td");
        nuevaCelda.textContent = celdaDato;
        nuevaFila.appendChild(nuevaCelda);
      });
      tabla.appendChild(nuevaFila);
    });
  }
}

function mostrarItemErrorConsulta(serie_numero, proveedor) {
  const fila = document.querySelectorAll(".fila-maqueta");
  const glosa = document.querySelectorAll(".edit-glosa");
  const cuenta = document.querySelectorAll(".edit-cuenta");

  let text_glosa;
  let text_cuenta;

  if (fila.length == 0) {
    text_glosa = "";
    text_cuenta = "";
  } else {
    $cant_fila = glosa.length;
    text_glosa = glosa[0].value;
    text_cuenta = cuenta[0].value;
  }

  //onkeypress="return solonumeros(event)"

  const items = filas();
  let html = `
        <tr class="fila-maqueta">
            <input type="hidden" name="items[]" value="${items}">
            <input type="hidden" name="condicion[]" value="A">
            <input type="hidden" name="bolsa[]" value="0">
            <input type="hidden" name="total[]" value="0" id="total-${items}">
            <input type="hidden" name="numeracion[]" value="${items}">

            <td>${items}</td>
            <td><input type="date" name="fecha[]" id="fecha-date-${items}" class="form-control" required="" /></td>
            <td class="tipo_moneda">
                <select class="form-control" id="tipo_moneda_${items}" name="tipo_moneda[]" onchange="ver_tipo_cambio(event,${items},2)">
                    <option value="S">S</option>
                    <option value="D">D</option>
                </select>
            </td>
            <td class="documento">
                <select class="form-control" id="doc_${items}" name="documento[]" style="width: 40px">
                    <option value="FACTURA">F</option>
                    <option value="BOLETA">B</option>
                    <option value="NOTA DE CREDITO">NC</option>
                    <option value="NOTA DE DEBITO">ND</option>
                </select>
            </td>
            <td><input type="text" name="serie_correlativo[]" id="numero-${items}" class="form-control" onblur="comprobar_duplicidad(${items}, 0)" value="${serie_numero}"/></td>
            <td>
                <input type="text" name="ruc_cliente[]" id="num-ruc-${items}" onkeyup="completarRuc(event, ${items})" class="form-control" style="width: 120px;" maxlength="11" onblur="comprobar_duplicidad(${items}, 1)" value="${proveedor}" />
            </td>
            <td><input type="text" name="vventa[]" class="form-control" value="0" onkeypress="return solonumeros(event)" id="vventa-${items}" onkeyup="calcular_total(event,${items})" style="width: 90px;" /></td>
            <td><input type="text" name="valor_venta[]" class="form-control" value="0" onkeypress="return solonumeros(event)" id="valor-venta-${items}" onkeyup="calcular_total(event,${items})" style="width: 90px;" /></td>
            <td><input type="text" name="igv[]" class="form-control" value="0" style="width: 70px;" onkeypress="return solonumeros(event)" id="igv-${items}" onkeyup="calcular_total_igv(event,${items})" /></td>
            <td>0</td>
            <td><input type="text" name="icb[]" class="form-control" value="0" style="width: 70px;" id="icb-${items}" onkeyup="calcular_total_icb(event,${items})" /></td>
            <td id="text-total-${items}">0</td>
            <td><input type="text" class="form-control" name="tipo_cambio[]" id="tipo-cambio-${items}" value="1" style="width: 70px;"></td>
            <td><input type="text" class="form-control edit-glosa" name="glosa[]" value="${text_glosa}" onkeyup="completarGlosa(event, ${items})" id="glosa_${items}" autocomplete="off" style="width: 90px;"></td>
            <td><input type="text" class="form-control edit-cuenta" name="cuenta[]" autocomplete="off" value="${text_cuenta}" onkeyup="sugerirGlosa(event, ${items})" id="cuenta_${items}" style="width: 90px;" ></td>
            <td>
                <select class="form-control" name="afectacion[]" id="afectacion_${items}">
                    <option value="N">N</option>
                    <option value="S">S</option>
                </select>
            </td>
            <td>
                <i class="bx bx-trash text-danger fs-2 eliminar_fila" title="eliminar fila"></i>
                <i class="bx bx-plus text-info fs-2 duplicar_fila" title="duplicar fila" data-item="${items}"></i>
            </td>
        </tr>
    `;

  $("#data_maqueta").prepend(html);
}

downloadMaqueta.addEventListener("click", (e) => {
  e.preventDefault();
  const periodo_ = document.getElementById("periodo");

  if (periodo_.value == "") {
    alert("Seleccione un periodo por favor");
    return false;
  }

  const formData = new FormData();
  formData.append("periodo", periodo_.value);
  formData.append("ruc", ruc_contribuyente.value);

  fetch(url_base + "/downloadMaqueta", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      var element = document.createElement("a");
      element.setAttribute(
        "href",
        url_base + "/descargar-maqueta-periodo/" + data.url_compra,
      );
      element.setAttribute("download", data.url_compra);
      document.body.appendChild(element);
      element.click();
    });
});

abancarizados.addEventListener("click", (e) => {
  const periodo_ = document.getElementById("periodo");

  if (periodo_.value == "") {
    alert("Seleccione un periodo por favor");
    return false;
  }

  e.target.disabled = true;
  e.target.textContent = "Descargando...";

  const name_contribuyente = document.getElementById("name_contribuyente");

  const formData = new FormData();
  formData.append("periodo", periodo_.value);
  formData.append("ruc", ruc_contribuyente.value);
  formData.append("name", name_contribuyente.value);

  fetch(url_base + "/downloadAbancarizados", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      console.log(data);

      e.target.disabled = false;
      e.target.textContent = "Comprobantes a bancarizar";

      var element = document.createElement("a");
      element.setAttribute("href", "../public/maquetas/" + data.url_compra);
      element.setAttribute("download", data.url_compra);
      document.body.appendChild(element);
      element.click();
    });
});

maquetaRegistro.addEventListener("click", (e) => {
  e.preventDefault();

  const periodo_ = document.getElementById("periodo");

  if (periodo_.value == "") {
    alert("Seleccione un periodo por favor");
    return false;
  }

  const formData = new FormData();
  formData.append("periodo", periodo_.value);
  formData.append("ruc", ruc_contribuyente.value);

  fetch(url_base + "/getRegistros", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      $("#modalListaRegistro").modal("show");
      console.log(data);
      const title = document.getElementById("titleMensaje");
      title.textContent = `Registros de Maquetas en el periodo ${data.periodo_formato}`;

      const modalR = document.getElementById("bodyModal");
      modalR.innerHTML = data.check;
    });
});

generarMaquetaRegistro.addEventListener("submit", (e) => {
  e.preventDefault();

  const btnget = document.getElementById("traerMaqueta");

  btnget.disabled = true;
  btnget.textContent = "Generando...";

  const formData = new FormData(generarMaquetaRegistro);

  fetch(url_base + "/traer-maqueta-por-registros", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      btnget.disabled = false;
      btnget.textContent = "Generar";

      var element = document.createElement("a");
      element.setAttribute(
        "href",
        url_base + "/descargar-maqueta-registros/" + data.url_compra,
      );
      element.setAttribute("download", data.url_compra);
      document.body.appendChild(element);
      element.click();
    });
});

function esFechaValida(fecha) {
  var dateObj = new Date(fecha);
  return !isNaN(dateObj);
}

btnSire.addEventListener("click", () => {
  $("#modal_excel").modal("show");
});

const form_subir_excel = document.getElementById("form_subir_excel");
const procesarExcel = document.getElementById("procesarExcel");

form_subir_excel.addEventListener("submit", (e) => {
  e.preventDefault();

  let fileInput = document.getElementById("archivoExcel");
  let filePath = fileInput.value;

  // Obtener la extensión del archivo
  let fileExtension = filePath
    .substring(filePath.lastIndexOf(".") + 1)
    .toLowerCase();

  // Verificar si la extensión es xls o xlsx
  if (fileExtension === "xls" || fileExtension === "xlsx") {
    // Aceptar el archivo y hacer lo que quieras aquí, por ejemplo, enviarlo al servidor
    const formData = new FormData(form_subir_excel);

    procesarExcel.textContent = "Procesando...";
    procesarExcel.disabled = true;

    fetch(url_base + "/uploadExcel", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        fileExcelSire(data);

        $("#modal_excel").modal("hide");

        form_subir_excel.reset();

        procesarExcel.textContent = "Procesar";
        procesarExcel.disabled = false;
      });
  } else {
    alert("Por favor, carga un archivo Excel válido (xls o xlsx)");
  }
});

function fileExcelSire(data) {
  data.forEach((el) => {
    let fila = filas();
    let cambio = "";
    let afectacion = "";
    let docu = "";
    let subtotal = 0;

    if (el.tipo_cambio === 1) {
      cambio = `
            <option value="S" selected>S</option>
            <option value="D">D</option>`;
    } else {
      cambio = `
            <option value="S">S</option>
            <option value="D" selected>D</option>`;
    }

    if (el.igv == 0) {
      afectacion = `
            <option value="N" selected>N</option>
            <option value="S">S</option>
            `;

      subtotal = el.subtotal;
    } else {
      afectacion = `
            <option value="N">N</option>
            <option value="S" selected>S</option>
            `;
      subtotal = el.valor_venta;
    }

    if (el.documento === "F") {
      docu = `
                <option value="FACTURA" selected>F</option>
                <option value="NOTA DE CREDITO">NC</option>
            `;
    } else {
      docu = `
                <option value="FACTURA">F</option>
                <option value="NOTA DE CREDITO" selected>NC</option>
            `;
    }

    let styleRow = "";

    if (el.tipo_cambio != 1) {
      if (el.repetido === true) {
        styleRow = `style="background-color: orange"`;
      } else {
        if (el.total >= 500) {
          styleRow = `style="background-color: yellow"`;
        } else {
          styleRow = `style="background-color: #6fbd64"`;
        }
      }
    } else {
      if (el.repetido === true) {
        styleRow = `style="background-color: orange"`;
      } else {
        if (el.total >= 2000) {
          styleRow = `style="background-color: yellow"`;
        }
      }
    }

    let html = `
                <tr class="fila-maqueta" id="tr-${fila}" ${styleRow}>
                    <input type="hidden" name="items[]" value="${fila}">
                    <input type="hidden" name="condicion[]" value="A">
                    <input type="hidden" name="bolsa[]" value="0">
                    <input type="hidden" name="total[]" id="totalSire-${fila}" value="${
                      el.total
                    }">
                    <input type="hidden" name="tipo_cliente[]" >
                    <input type="hidden" name="serie[]" >
                    <input type="hidden" name="correlativo[]" >
                    <input type="hidden" name="tipo_documento[]" >

                    <input type="hidden" name="numeracion[]" id="numeracion-${fila}" value="${fila}">

                    <td id="tdnumeracion-${fila}">${fila}</td>
                    <td>
                        <input type="date" name="fecha[]" id="fecha-date-${fila}" class="form-control" required="" value="${
                          el.fecha_emision
                        }">
                    </td>
                    <td class="tipo_moneda">
                        <select class="form-control" name="tipo_moneda[]" id="tipo_moneda_${fila}" onchange="ver_tipo_cambio(event,${1},1)">
                            ${cambio}
                        </select>
                    </td>
                    <td>
                        <select class="form-control" id="doc_${fila}" name="documento[]">
                            ${docu}
                        </select>
                    </td>
                    <td>
                        <input type="text" name="serie_correlativo[]" id="numero-${fila}" class="form-control" autocomplete="off" value="${
                          el.serie_numero
                        }">
                    </td>
                    <td>
                        <input type="text" name="ruc_cliente[]" id="num-ruc-${fila}" onkeyup="completarRuc(event, ${fila})" class="form-control" style="width: 120px;" maxlength="11" value="${
                          el.ruc
                        }">
                    </td>
                    <td>
                        <input type="text" name="vventa[]" class="form-control" value="${subtotal}" onkeypress="return solonumeros(event)" id="vventa-${fila}" onkeyup="calcular_total(event,${fila})" style="width: 90px;">
                    </td>
                    <td>
                        <input type="text" name="valor_venta[]" class="form-control" value="${subtotal}" onkeypress="return solonumeros(event)" id="valor-venta-${fila}" onkeyup="calcular_total(event,${fila})" style="width: 90px;">
                    </td>
                    <td>
                        <input type="text" name="igv[]" class="form-control" value="${
                          el.igv
                        }" style="width: 70px;" onkeypress="return solonumeros(event)" id="igv-${fila}" onkeyup="calcular_total_igv(event,${fila})">
                    </td>
                    <td>0</td>
                    <td>
                        <input type="text" name="icb[]" class="form-control" value="${
                          el.icbper
                        }" style="width: 70px;" id="icb-${fila}" onkeyup="calcular_total_icb(event,${fila})">
                    </td>
                    <td>${el.total}</td>
                    <td><input type="text" class="form-control" name="tipo_cambio[]" id="tipo-cambio-${fila}" value="${
                      el.tipo_cambio
                    }" style="width: 70px;"></td>
                    <td><input type="text" class="form-control edit-glosa" name="glosa[]" value="${
                      el.glosa
                    }" onkeyup="completarGlosa(event, ${fila})" style="width: 90px;" id="glosa_${fila}"></td>
                    <td><input type="text" class="form-control edit-cuenta" name="cuenta[]" onkeyup="sugerirGlosa(event, ${fila})" value="${
                      el.cuenta
                    }" style="width: 90px;" id="cuenta_${fila}"></td>
                    <td>
                        <select class="form-control" name="afectacion[]">
                            ${afectacion}
                        </select>
                    </td>
                    <td>
                        <i class="bx bx-trash text-danger fs-2 eliminar_fila_sire" data_item="${fila}" data-serie="${
                          el.serie_numero
                        }" data-ruc="${el.ruc}"></i>
                        <i class="bx bx-cog text-primary fs-2 verificar_sunat" data-id="${fila}" data-monto="${
                          el.total
                        }" data-cambio="${el.tipo_cambio}"></i>
                    </td>
                </tr>`;

    $("#data_maqueta").prepend(html);
  });
}

const form_verificar = document.getElementById("form_verificar");

form_verificar.addEventListener("submit", (e) => {
  e.preventDefault();

  document.getElementById("mensajeSunat").innerHTML = "";
  const verificar = document.getElementById("verificar");

  verificar.disabled = true;
  verificar.textContent = "Verificando...";

  const formData = new FormData(form_verificar);

  fetch(url_base + "/verificar_sunat", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      console.log(data);
      verificar.disabled = false;
      verificar.textContent = "Verificar";

      if (data.success == true) {
        const lengthData = data.data;

        let mensaje = "";

        if (lengthData.length == 0) {
          mensaje =
            "Vuelve a consultar, Sunat no esta respondiendo la solicitud!";

          let alert = `
                <div class="alert alert-danger" role="alert">
                    ${mensaje}
                </div>`;

          document.getElementById("mensajeSunat").innerHTML = alert;

          return false;
        }

        let estadoSunat = "";
        let color = "";

        if (data.data.estadoCp == 1) {
          estadoSunat = "ACEPTADO";
          color = "success";
        }

        if (data.data.estadoCp == 0) {
          estadoSunat = "NO EXISTE";
          color = "danger";
        }

        if (data.data.estadoCp == 2) {
          estadoSunat = "ANULADO";
          color = "warning";
        }

        mensaje = `Estado del comprobante a la fecha de la consulta: ${estadoSunat}`;

        let html = `
            <div class="alert alert-${color}" role="alert">
                ${mensaje}
            </div>`;

        document.getElementById("mensajeSunat").innerHTML = html;
      }
    });
});

async function uploadVaucher(idcompra) {
  let fileInput = document.getElementById("voucher-" + idcompra);
  let descripcion = document.getElementById("description-" + idcompra);
  let formData = new FormData();

  if (fileInput.files.length > 0) {
    let file = fileInput.files[0]; // Aquí es donde accedemos al archivo en sí
    formData.append("archivo", file);
    formData.append("descripcion", descripcion.value);
    formData.append("idcompra", idcompra);

    try {
      let response = await fetch(url_base + "/loadVaucher", {
        method: "POST",
        body: formData,
      });

      let result = await response.json();

      fileInput.value = null;
      descripcion.value = "";

      // Manejo de la respuesta, por ejemplo:
      if (result.status === "success") {
        alert(result.message);
      } else {
        alert("Error: " + result.message);
      }
    } catch (error) {
      console.error("Error al subir el archivo:", error);
    }
  } else {
    alert("Por favor, selecciona un archivo antes de subir.");
  }
}

const consultaBancarizados = document.getElementById("consultaBancarizados");

consultaBancarizados.addEventListener("click", (e) => {
  e.preventDefault();
  const periodo = document.getElementById("periodo");

  if (periodo.value === "") {
    alert("Seleccione un periodo por favor");
    return;
  }

  $("#modal_consulta_bancarizar").modal("show");

  loadTablaBancarizados(ruc_contribuyente.value, periodo.value);
});

function loadTablaBancarizados(ruc, periodo) {
  const formData = new FormData();

  formData.append("ruc", ruc);
  formData.append("periodo", periodo);

  fetch(url_base + "/consulta_bancarizados", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      let lista = "";

      const listaB = document.getElementById("listaBancarizarConsulta");

      data.forEach((ban) => {
        let path_vaucher = "";
        let alink = "";
        let estado = "";

        let files = ban.files;

        if (files.length > 0) {
          path_vaucher = url_base + ban.link;
          alink = `<a href="#" onclick="verVauchers(event,${ban.id_maqueta})">Ver vauchers</a>`;
          estado = `<i class="bx bx-check text-success"></i>`;
        }

        lista += `
                <tr>
                    <td>
                    ${ban.serie_numero} <br>
                    ${ban.razon_social}
                    </td>
                    <td>${ban.total}</td>
                    <td>
                        <input type="file" class="form-control" name="" id="voucher-${ban.id_maqueta}">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="" id="description-${ban.id_maqueta}" value="">
                    </td>
                    <td>
                        ${alink}
                    </td>
                    <td>
                        ${estado}
                        <button type="button" class="btn btn-primary" onclick="uploadVaucher(${ban.id_maqueta})"><i class="bx bx-upload"></i></button>
                    </td>
                </tr>
            `;
      });

      listaB.innerHTML = lista;
    });
}

function actualizar_tabla() {
  const numeracion_item = document.querySelectorAll(
    'input[name="numeracion[]"]',
  );

  for (let i = 0; i < numeracion_item.length; i++) {
    let numero = numeracion_item[numeracion_item.length - i - 1].value;
    let new_numero = i + 1;

    let numhidden = document.getElementById("numeracion-" + numero);
    numhidden.id = "numeracion-" + new_numero;
    numhidden.value = new_numero;

    let numtd = document.getElementById("tdnumeracion-" + numero);
    numtd.textContent = new_numero;
    numtd.id = "tdnumeracion-" + new_numero;
  }
}

function verVauchers(e) {
  e.preventDefault();

  alert();
}

let ordenOriginal = [];
let estadoOrden = {};

document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.querySelector("#tableCompras tbody");
  ordenOriginal = Array.from(tbody.children);
});

function obtenerValorCelda(celda) {
  const input = celda.querySelector("input, select, textarea");
  return input ? input.value.trim() : celda.textContent.trim();
}

function ordenarPorVacios(colIndex, th) {
  const tbody = document.querySelector("#tableCompras tbody");
  const filas = Array.from(tbody.querySelectorAll("tr"));

  // 0 = original | 1 = vacíos arriba | 2 = vacíos abajo
  estadoOrden[colIndex] = (estadoOrden[colIndex] ?? 0) + 1;
  if (estadoOrden[colIndex] > 2) estadoOrden[colIndex] = 0;

  // reset iconos
  document
    .querySelectorAll(".icono-orden")
    .forEach((i) => (i.textContent = "⭥"));

  const icono = th.querySelector(".icono-orden");

  // 🔁 volver a orden original
  if (estadoOrden[colIndex] === 0) {
    ordenOriginal.forEach((fila) => tbody.appendChild(fila));
    icono.textContent = "⭥";
    return;
  }

  filas.sort((a, b) => {
    const aVacio = obtenerValorCelda(a.cells[colIndex]) === "";
    const bVacio = obtenerValorCelda(b.cells[colIndex]) === "";

    // fuerza diferencia
    if (aVacio === bVacio) return 0;
    return estadoOrden[colIndex] === 1 ? (aVacio ? -1 : 1) : aVacio ? 1 : -1;
  });

  filas.forEach((fila) => tbody.appendChild(fila));

  icono.textContent = estadoOrden[colIndex] === 1 ? "▲" : "▼";
}
