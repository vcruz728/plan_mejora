var base_url = "";

$(document).ready(() => {
    base_url = $("input[name='base_url']").val()

    $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '< Ant',
        nextText: 'Sig >',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };

    $.datepicker.setDefaults($.datepicker.regional['es']);


    let hoy = new Date();
    let mes = hoy.getMonth() + 1;
    let anio = hoy.getFullYear();
    
    mes = "0" + mes;

    $('#desde').datepicker({
        dateFormat: 'yy-mm-dd'
    }).datepicker("setDate", `${anio}-${mes.substr(-2)}-01`);

    $('#hasta').datepicker({
        dateFormat: 'yy-mm-dd'
    }).datepicker("setDate", new Date());
});

function mostrarLoader (contenedor, mensaje = "Espere un momento...") {
    var element = document.getElementById(contenedor);

    if (element) {
        element.innerHTML = `
            <div style="display: flex; justify-content: center; flex-direction: column; align-items: center;">
                <div class="loader_cfe">
                    <div class="inner logo">BUAP</div>
                    <div class="inner one"></div>
                    <div class="inner two"></div>
                    <div class="inner three"></div>
                </div>
                <div>
                    <span style="color: #757575;">${mensaje}</span>
                </div>
            </div>`;
    }
}

function ocultarLoader (contenedor) {
    var element = document.getElementById(contenedor);

    if (element) {
        element.innerHTML = null;
    }
}

const mostrarLoaderModal = () => {
    $("#modal_loading").modal({
        backdrop: 'static',
        keyboard: false
    });
}

const ocultarLoaderModal = () => {
    $("#modal_loading").modal('hide');
}


const renderSelect = (select, data, id, value, defaultId, defaultValue) => {
    var element = document.getElementById(select);

    if (!element) {
        alert(`El elemento ${select} no existe en el DOM.`);
        return;
    }

    $(`#${select}`).empty();

    var id_option = "";
    var value_option = "";

    var option = document.createElement("option");

    if (defaultId || defaultValue) {
        option.value = defaultId;
        option.text = defaultValue;

        element.appendChild(option);
    }

    data.forEach((r) => {
        id_option = r[id];
        value_option = r[value];

        option = document.createElement("option");

        option.value = id_option;
        option.text = value_option

        element.appendChild(option);
    });
}

const openSnack = (mensaje) => {
    var x = document.getElementById("snackbar");
    x.innerHTML = mensaje;
    x.className = "show";

    setTimeout(() => {
        x.className = x.className.replace("show", "");
    }, 3000);
}


const verificarFormulario = (fields) => {
    let errors = [];

    fields.forEach((field) => {
        const element = document.getElementById(field);

        if (element) {
            const value = element.value;

            if (!value) {
                errors = [
                    ...errors,
                    field
                ];
            }
        }
    });

    return errors.length == 0 ? true : errors;
}

const debounce = (func, wait, immediate) => {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};

const mostrarModalConfirmacion = (label = '# Requiere mensaje de confirmación', confirmacion) => {
    document.getElementById("leyenda_confirmacion").innerHTML = label;
    document.getElementById("btn_modal_confirmacion").disabled = false;
    $("#modal_confirmacion").modal();

    const element_btn_modal_confirmacion = document.getElementById("btn_modal_confirmacion");
    const clone_btn_modal_confirmacion = document.getElementById("btn_modal_confirmacion").cloneNode(true);
    document.getElementById("container_btns_modal_confirmacion").replaceChild(clone_btn_modal_confirmacion, element_btn_modal_confirmacion);

    document.getElementById("btn_modal_confirmacion").addEventListener("click", () => {
        document.getElementById("btn_modal_confirmacion").disabled = true;
        confirmacion();
    });
}

const cerrarModalConfirmacion = () => {
    document.getElementById("btn_modal_confirmacion").disabled = false;
    $("#modal_confirmacion").modal('hide');
}


const mostrarModalResultado = (isOk = true, title, message = '', functionContinue) => {
    document.getElementById("title_modal_resultado").innerText = title;

    document.getElementById("container_modal_resultado").innerHTML = isOk
        ? `<svg style="width: 80px;" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
			<circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
			<polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
        </svg>
        <p class="resultado success">${message}</p>`
        : `<svg style="width: 80px;" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
            <circle class="path circle" fill="none" stroke="#D06079" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
            <line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3"/>
            <line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2"/>
        </svg>
        <p class="error">${message}</p>`;

    $("#modal_resultado").modal({
        backdrop: 'static',
        keyboard: false
    });

    const element_btn_modal_resultado = document.getElementById("btn_modal_resultado");
    const clone_btn_modal_resultado = document.getElementById("btn_modal_resultado").cloneNode(true);

    document.getElementById("container_btns_modal_resultado").replaceChild(clone_btn_modal_resultado, element_btn_modal_resultado);

    document.getElementById("btn_modal_resultado").addEventListener("click", () => {
        $("#modal_resultado").modal('hide');
        setTimeout(() => {
            functionContinue();
        }, 500);
    });
}


const beforeHandleOnChange = (value, container, defaultValue, defaultText) => {
    $(`#${container}`).empty();

    if (defaultText) {
        $(`#${container}`).append($('<option>').val(defaultValue).text(defaultText));
    }

    document.getElementById(container).disabled = true;

    if (document.getElementById(container).attributes.getNamedItem("onchange")) {
        document.getElementById(container).onchange();
    }
}


const obtenerMarcas = async () => {
    const response = await fetch(base_url + '/vehiculos/marcas');
    const marcas = await response.json();

    return marcas.data;
}

/** handle functions */

const handleOnChangeDivision = async (division, container, defaultValue, defaultText) => {
    beforeHandleOnChange(division, container, defaultValue, defaultText);

    if (!division) {
        return;
    }

    const zonas = await obtenerArea(division,'division');

    zonas.forEach(({ cve_zona, zona }) => {
        $(`#${container}`).append($('<option>').val(cve_zona).text(`${cve_zona} ${zona}`));
    });

    document.getElementById(container).disabled = false;
}

const obtenerArea = async (division,opcion,zona = null) => {
    const body = new FormData();
    body.append('division', division)
    body.append('opcion', opcion)
    body.append('zona', zona)
    const response = await fetch(base_url + '/consultas/get-areas', {
        method: 'post',
        body,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const zonas = await response.json();

    return zonas;
}


const handleOnChangeZona = async (zona, divisionContainer, container, defaultValue, defaultText) => {
    beforeHandleOnChange(zona, container, defaultValue, defaultText);

    if (!zona) {
        return;
    }

    let division = '';
    const element_division = document.getElementById(divisionContainer);
    if (element_division) {
        division = element_division.value;
    }

    const agencias = await obtenerArea(division,'zona',zona);

    agencias.forEach(({ cve_centro, centro }) => {
        $(`#${container}`).append($('<option>').val(cve_centro).text(`${cve_centro} ${centro}`));
    });

    document.getElementById(container).disabled = false;

    return agencias;
}

const cambiarAgencia = () => {
    $('#modal_agencia').modal({ backdrop: 'static', keyboard: false });
}


const getColorByBgColor = (bgColor) => {
    return (parseInt(bgColor.replace('#', ''), 16) > 0xffffff / 2) ? '#000000' : '#FFFFFF';
}

const idealTextColor = (bgColor) => {
    const nThreshold = 105;
    const components = getRGBComponents(bgColor);
    const bgDelta = (components.R * 0.299) + (components.G * 0.587) + (components.B * 0.114);

    return ((255 - bgDelta) < nThreshold) ? "#000000" : "#ffffff";
}

const getRGBComponents = (color) => {
    const r = color.substring(0, 2);
    const g = color.substring(2, 4);
    const b = color.substring(4, 6);

    return {
        R: parseInt(r, 16),
        G: parseInt(g, 16),
        B: parseInt(b, 16)
    };
}

const downloadFile = (file, name) => {
    const a = document.createElement("a");
    document.body.appendChild(a);
    a.style = "display: none";

    const url = window.URL.createObjectURL(file);
    a.href = url;
    a.download = `${name}.xlsx`;
    a.click();
    window.URL.revokeObjectURL(url);
}
