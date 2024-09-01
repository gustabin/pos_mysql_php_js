$(document).ready(function () {
    // alert("El DOM está completamente cargado");
    loadCategorias();

    // Limpiar el formulario al abrir el modal
    function limpiarFormulario() {
        $('#categoriaForm')[0].reset(); // Esto limpia todos los campos del formulario
        $('#categoriaID').val(''); // Limpiar el ID del producto
    }

    // Ejecutar la limpieza del formulario y cargar categorías solo cuando se pulse "Crear Categoria"
    $('#crearCategoriaBtn').click(function () {
        limpiarFormulario(); // Limpiar el formulario al abrir el modal para crear un nuevo categoria
        $('#categoriaModal').modal('show'); // Mostrar el modal
    });

    function loadCategorias() {
        $.ajax({
            url: '../php/categorias.php?action=getAll', // URL del endpoint para obtener todas las categorías
            type: 'GET', // Método HTTP para la solicitud (GET para obtener datos)
            dataType: 'json', // Tipo de datos que se espera recibir (JSON)
            success: function (response) {
                // Destruir DataTable si ya está inicializada para evitar duplicados
                const table = $('#categoriaTable').DataTable(); // Obtener instancia de DataTable
                if ($.fn.DataTable.isDataTable('#categoriaTable')) {
                    table.destroy(); // Destruir la instancia existente de DataTable
                }

                $('#categoriaTable').DataTable({
                    data: response.data, // Datos a mostrar en la tabla, provenientes del JSON
                    columns: [
                        { data: 'CategoriaID' }, // Columna para el ID de la categoría
                        { data: 'Nombre' }, // Columna para el nombre de la categoría
                        { data: 'Acciones' } // Columna para los botones de acción
                    ],
                    language: {
                        "sProcessing": "Procesando...", // Texto mostrado mientras se procesan los datos
                        "sLengthMenu": "Mostrar _MENU_ registros", // Texto para seleccionar el número de registros a mostrar
                        "sZeroRecords": "No se encontraron resultados", // Texto cuando no hay resultados
                        "sEmptyTable": "Ningún dato disponible en esta tabla", // Texto cuando la tabla está vacía
                        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros", // Texto de información sobre el rango de registros mostrados
                        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros", // Texto cuando no hay registros para mostrar
                        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)", // Texto sobre la cantidad de registros filtrados
                        "sSearch": "Buscar:", // Texto para el campo de búsqueda
                        "sLoadingRecords": "Cargando...", // Texto mostrado mientras se cargan los registros
                        "oPaginate": {
                            "sFirst": "Primero", // Texto para el primer botón de paginación
                            "sLast": "Último", // Texto para el último botón de paginación
                            "sNext": "Siguiente", // Texto para el botón de paginación siguiente
                            "sPrevious": "Anterior" // Texto para el botón de paginación anterior
                        },
                        "oAria": {
                            "sSortAscending": ": Activar para ordenar la columna de manera ascendente", // Texto para ordenar ascendentemente
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente" // Texto para ordenar descendentemente
                        }
                    },
                });
                if (response.error) {
                    Swal.fire('Error', response.error, 'error'); // Notificación de error
                }
            },
            error: function (xhr, status, error) {
                handleAjaxError(status, error, xhr.responseText); // Llamar a la función para manejar errores
            }
        });
    }

    // Función para manejar errores AJAX
    function handleAjaxError(status, error, responseText) {
        console.error("Error en la solicitud AJAX: ", status, error);

        try {
            var jsonResponse = JSON.parse(responseText);
            console.log("Respuesta JSON:", jsonResponse);
        } catch (e) {
            console.log("Respuesta no es JSON, contenido:", responseText);
        }
    }


    // Manejar el clic en el botón para guardar una categoría (crear o actualizar)
    $('#guardarCategoria').click(function () {
        // alert("entro")
        // alert($('#categoriaId').val());
        const id = $('#categoriaId').val(); // Obtener el ID de la categoría (si existe)
        const nombre = $('#categoriaNombre').val().trim();// Obtener y limpiar el nombre de la categoría

        // Validar que el nombre no esté vacío
        if (nombre === '') {
            Swal.fire('Error', 'El nombre no puede estar vacío.', 'error'); // Notificación de error si el nombre está vacío
            return;
        }

        const action = id ? 'update' : 'create'; // Determinar la acción a realizar (crear o actualizar)

        // Deshabilitar el botón mientras se procesa la solicitud para evitar múltiples envíos
        $(this).prop('disabled', true);
        $("#barra").show(); // Mostrar barra de procesamiento

        $.ajax({
            url: '../php/categorias.php?action=' + action, // URL del endpoint para crear o actualizar una categoría
            method: 'POST', // Método HTTP para la solicitud (POST para enviar datos)
            data: { id: id, nombre: nombre }, // Enviar los datos de la categoría como parámetros            
            success: function (data) {
                console.log(data)
                $("#barra").hide(); // Ocultar barra de procesamiento
                if (data.status === 'success') {
                    Swal.fire('¡Guardado!', data.message, 'success'); // Notificación de éxito
                    $('#categoriaModal').modal('hide'); // Ocultar el modal
                    loadCategorias(); // Volver a cargar las categorías
                } else {
                    Swal.fire('Error', data.error, 'error'); // Notificación de error
                }
            },
            error: function (xhr, status, error) {
                handleAjaxError(status, error, xhr.responseText); // Llamar a la función para manejar errores
            },
            complete: function () {
                $('#guardarCategoria').prop('disabled', false); // Habilitar el botón nuevamente después de que la solicitud haya terminado
            }
        });
    });

    // Manejar el clic en el botón de edición dentro de la tabla
    $('#categoriaTable tbody').on('click', '.edit-btn', function () {
        const id = $(this).data('id'); // Obtener el ID de la categoría desde el botón clickeado
        $.ajax({
            url: '../php/categorias.php?action=getById', // URL del endpoint para obtener una categoría específica
            data: { id: id }, // Enviar el ID de la categoría como parámetro
            success: function (data) {
                $('#categoriaId').val(data.CategoriaID); // Rellenar el campo del ID con el valor recibido
                $('#categoriaNombre').val(data.Nombre); // Rellenar el campo del nombre con el valor recibido
                $('#categoriaModal').modal('show'); // Mostrar el modal para editar la categoría
            },

            error: function (xhr, status, error) {
                handleAjaxError(status, error, xhr.responseText); // Llamar a la función para manejar errores
            }
        });
    });

    // Manejar el clic en el botón de eliminación dentro de la tabla
    $('#categoriaTable tbody').on('click', '.delete-btn', function () {
        const id = $(this).data('id'); // Obtener el ID de la categoría desde el botón clickeado
        Swal.fire({
            title: '¿Estás seguro de querer eliminar este registro?', // Título de la alerta de confirmación
            text: "No podrás revertir esto.", // Texto de advertencia
            icon: 'warning', // Icono de advertencia
            showCancelButton: true, // Mostrar botón de cancelar
            confirmButtonColor: '#3085d6', // Color del botón de confirmación
            cancelButtonColor: '#d33', // Color del botón de cancelación
            confirmButtonText: '¡Sí, eliminar!' // Texto del botón de confirmación
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../php/categorias.php?action=delete', // URL del endpoint para eliminar una categoría
                    method: 'POST', // Método HTTP para la solicitud (POST para enviar datos)
                    data: { id: id }, // Enviar el ID de la categoría como parámetro
                    success: function (data) {
                        if (data.status === 'success') {
                            Swal.fire('¡Eliminado!', data.message, 'success'); // Notificación de éxito
                            loadCategorias(); // Volver a cargar las categorías
                        } else {
                            Swal.fire('Error', data.message, 'error'); // Notificación de error
                        }
                    },
                    error: function (xhr, status, error) {
                        handleAjaxError(status, error, xhr.responseText); // Llamar a la función para manejar errores
                    }
                });
            }
        });
    });
});