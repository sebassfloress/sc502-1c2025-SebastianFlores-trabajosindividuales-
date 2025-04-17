document.addEventListener('DOMContentLoaded', function () {
    const listaTareas = [
        { id: 1, titulo: "Completar práctica 4", detalle: "Avanzar el estudio de caso", fechaLimite: "2025-04-10" },
        { id: 2, titulo: "Revisar entregables", detalle: "Verificar archivos para entrega", fechaLimite: "2025-04-11" },
        { id: 3, titulo: "Estudiar para examen", detalle: "Repasar temas de estructuras", fechaLimite: "2025-04-12" }
    ];

    function mostrarComentarios(tareaId) {
        fetch(`backend/api.php?accion=traerComentarios&tarea_id=${tareaId}`)
            .then(res => res.json())
            .then(data => {
                const lista = document.getElementById(`comentarios-lista-${tareaId}`);
                lista.innerHTML = '';
                data.forEach(c => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    li.innerHTML = `
                        ${c.texto}
                        <button class="btn btn-sm btn-danger btn-borrar-comentario" data-id="${c.id}" data-tarea="${tareaId}">Borrar</button>
                    `;
                    lista.appendChild(li);
                });

                document.querySelectorAll(`#comentarios-lista-${tareaId} .btn-borrar-comentario`).forEach(btn => {
                    btn.addEventListener('click', borrarComentario);
                });
            });
    }

    function añadirComentario(e) {
        const tareaId = e.target.dataset.id;
        const input = document.getElementById(`comentario-input-${tareaId}`);
        const contenido = input.value.trim();

        if (contenido !== "") {
            fetch("backend/api.php?accion=crearComentario", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `tarea_id=${tareaId}&texto=${encodeURIComponent(contenido)}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        input.value = '';
                        mostrarComentarios(tareaId);
                    }
                });
        }
    }

    function borrarComentario(e) {
        const comentarioId = e.target.dataset.id;
        const tareaId = e.target.dataset.tarea;

        fetch("backend/api.php?accion=borrarComentario", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${comentarioId}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    mostrarComentarios(tareaId);
                }
            });
    }

    function renderizarTareas() {
        const contenedorTareas = document.getElementById('task-list');
        contenedorTareas.innerHTML = '';
        listaTareas.forEach(t => {
            const tarjeta = document.createElement('div');
            tarjeta.className = 'col-md-4 mb-3';
            tarjeta.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">${t.titulo}</h5>
                        <p class="card-text">${t.detalle}</p>
                        <p class="card-text"><small class="text-muted">Límite: ${t.fechaLimite}</small></p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button class="btn btn-secondary btn-sm editar-tarea" data-id="${t.id}">Editar</button>
                        <button class="btn btn-danger btn-sm eliminar-tarea" data-id="${t.id}">Eliminar</button>
                        <button class="btn btn-info btn-sm ver-comentarios" data-id="${t.id}">Comentarios</button>
                    </div>
                    <div class="seccion-comentarios p-2 bg-light d-none" id="comentarios-${t.id}">
                        <ul class="list-group mb-2" id="comentarios-lista-${t.id}"></ul>
                        <input type="text" class="form-control mb-2" id="comentario-input-${t.id}" placeholder="Escribe tu comentario...">
                        <button class="btn btn-primary btn-sm btn-agregar-comentario" data-id="${t.id}">Enviar</button>
                    </div>
                </div>
            `;
            contenedorTareas.appendChild(tarjeta);

            mostrarComentarios(t.id);
        });

        document.querySelectorAll('.ver-comentarios').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const seccion = document.getElementById(`comentarios-${id}`);
                seccion.classList.toggle('d-none');
            });
        });

        document.querySelectorAll('.btn-agregar-comentario').forEach(btn => {
            btn.addEventListener('click', añadirComentario);
        });
    }

    renderizarTareas();
});
