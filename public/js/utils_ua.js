const deleteAll = async () => {   
    const promise = new Promise( (resolve, reject) => {
        document.querySelector('.js-btn-delete').addEventListener('click', (_) => resolve(true));
        document.querySelector('.js-btn-cancel').addEventListener('click', (_) => reject(false));
    });
    $('#id-modal-confirm').modal('show');

    promise.then(
        async (_) => {
            $('#id-modal-confirm').modal('hide');
            document.getElementById('listadoUa').classList.add('ua-is-loading');
            
            const headers = new Headers();
            headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
            const config = {
                headers,
                method: 'POST'
            };
            const source = await fetch(
                'ua/deleteall' , config
            );  
            const { errors } = await source.json();

            if(errors.length > 0){
                let templateLi = '';
                errors.forEach( ({ codigo, descripcion, error }) => { 
                    templateLi += `
                        <li>
                            La ua "${ descripcion } - ${ codigo }" no se puede eliminar porque: 
                            <span class="font-weight-bold">${ error }</span>
                        </li>`;
                });
                document.querySelector('.js-modal-title')
                    .innerText = 'Algunas Ua no pudieron eliminarse';
                document.querySelector('.js-modal-desc')
                    .innerHTML = `<ul>${ templateLi }</ul>`;
                document.querySelector('.js-btn-ua-accept').addEventListener('click', () => {
                    chargeTable();
                });
                $('#id-modal-ua').modal('show');
            }else{
                alert("Eliminado correctamente");
                mostrarMensaje('Eliminado correctamente', 'OK');
                chargeTable();
            }
        }
    )
    .catch( (_) => $('#id-modal-confirm').modal('hide') );
};

const deleteSelected = async () => {

    const lstDeleted = document.querySelectorAll('input[name="wants_delete"]:checked');
    
    if(lstDeleted.length === 0){
        document.querySelector('.js-modal-title')
            .innerText = 'No has seleccionado ningún registro';
        document.querySelector('.js-modal-desc')
            .innerText = 'Error debe seleccionar por lo menos un registro';
        $('#id-modal-ua').modal('show');
        return;
    }

    const promise = new Promise( (resolve, reject) => {
        document.querySelector('.js-btn-delete').addEventListener('click', (_) => resolve(true));
        document.querySelector('.js-btn-cancel').addEventListener('click', (_) => reject(false));
    });
    $('#id-modal-confirm').modal('show');

    promise.then(
        async (_) => {
            $('#id-modal-confirm').modal('hide');
            document.getElementById('listadoUa').classList.add('ua-is-loading');

            const formData = new FormData();
            const uaList = [];
            lstDeleted.forEach( (elm) => uaList.push(elm.value) );
            formData.append('uaList', uaList);

            const headers = new Headers();
            headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
            const config = {
                headers,
                method: 'POST',
                body: formData
            };
            const source = await fetch(
                'ua/deletelist' , config
            );  
            const { errors } = await source.json();

            if(errors.length > 0){
                let templateLi = '';
                errors.forEach( ({ codigo, descripcion, error }) => { 
                    templateLi += `
                        <li>
                            La ua "${ descripcion } - ${ codigo }" no se puede eliminar porque: 
                            <span class="font-weight-bold">${ error }</span>
                        </li>
                        `;
                });
                document.querySelector('.js-modal-title')
                    .innerText = 'Algunas Ua no pudieron eliminarse';
                document.querySelector('.js-modal-desc')
                    .innerHTML = `<ul>${ templateLi }</ul>`;
                document.querySelector('.js-btn-ua-accept').addEventListener('click', () => {
                    chargeTable();
                });
                $('#id-modal-ua').modal('show');
            }else{
                alert("Eliminado correctamente");
                mostrarMensaje('Eliminado correctamente', 'OK');
                chargeTable();
            }

        }
    ).catch( (_) => $('#id-modal-confirm').modal('hide') );
};          

const chargeTable = async () => {
    const headers = new Headers();
    headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    const formData = new FormData();
    formData.append('page', 1);
    formData.append('filas', 20);
    const config = {
        headers,
        method: 'POST',
        body: formData
    };
    const source = await fetch(
        'ua/buscar' , config
    );  

    const res = await source.text();
    document.getElementById('listadoUa').classList.remove('ua-is-loading');
    document.getElementById('listadoUa').innerHTML = res;
}
