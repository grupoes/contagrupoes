function prueba_notificacion() {
    if (Notification) {
        if (Notification.permission !== "granted") {
            Notification.requestPermission()
        }

        var title = "PAGO DE IMPUESTOS"
        var extra = {
            icon: "https://esfacturador.com/sys_fact/public/img/logo56.png",
            body: "SR CONTRIBUYENTE TU IMPUESTO POR VENCER 24/04/2022, SI YA PAGO OMITIR ESTE MENSAJE"
        }

        var noti = new Notification( title, extra)
        noti.onclick = {
        // Al hacer click
        }
        noti.onclose = {
        // Al cerrar
        }

        setTimeout( function() { 
            noti.close() 
        }, 10000)
    }
}

setInterval(function () {
    prueba_notificacion();
}, 10000)