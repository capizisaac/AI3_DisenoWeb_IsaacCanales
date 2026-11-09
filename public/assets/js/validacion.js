'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('formulario-movimiento');

    if (!formulario) {
        return;
    }

    const selectCategoria = formulario.querySelector('#categoria');

    const tipoSeleccionado = () => {
        const marcado = formulario.querySelector('input[name="tipo"]:checked');

        return marcado ? marcado.value : '';
    };

    const filtrarCategorias = () => {
        const tipo = tipoSeleccionado();

        for (const opcion of selectCategoria.options) {
            if (opcion.value === '') {
                continue;
            }

            const permitidos = (opcion.dataset.tipos || '').split(' ');
            const habilitada = tipo === '' || permitidos.includes(tipo);

            opcion.disabled = !habilitada;
            opcion.hidden = !habilitada;
        }

        const actual = selectCategoria.selectedOptions[0];

        if (actual && actual.disabled) {
            selectCategoria.value = '';
        }
    };

    formulario.querySelectorAll('input[name="tipo"]').forEach((radio) => {
        radio.addEventListener('change', filtrarCategorias);
    });

    filtrarCategorias();
});
