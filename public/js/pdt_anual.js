const form = document.getElementById("form_pdt_anual");
const consultar = document.getElementById("consultar");

const result = document.getElementById("result_data");

form.addEventListener("submit", (e) => {
  e.preventDefault();

  consultar.disabled = true;
  consultar.textContent = "Consultando...";

  const formData = new FormData(form);

  fetch("./traer_data_anual", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      consultar.disabled = false;
      consultar.textContent = "Consultar";

      if (data.respuesta == "error") {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: data.mensaje,
        });

        return false;
      }

      let html = "";

      if (data.length > 0) {
        data.forEach((pdt, index) => {
          html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${pdt.anio_descripcion}</td>
                            <td>
                                <a href="https://grupoesconsultores.com/contabilidad/public/archivos/pdt/${
                                  pdt.pdt
                                }" target="_blank" class="btn btn-soft-light btn-sm w-xs waves-effect btn-label waves-light detalle"><i class="bx bx-download label-icon"></i> Detalle</a>
                                <a href="https://grupoesconsultores.com/contabilidad/public/archivos/pdt/${
                                  pdt.constancia
                                }" target="_blank" class="btn btn-soft-light btn-sm w-xs waves-effect btn-label waves-light constancia" ><i class="bx bx-download label-icon"></i> Constancia</a>
                            </td>
                        </tr>
                    `;
        });

        result.innerHTML = html;
      } else {
        result.innerHTML = `
                    <tr>
                        <td colspan="3"><h3 class='text-center mt-4'>NO HAY INFORMACIÓN</h3></td>
                    </tr>
                `;
      }
    });
});
