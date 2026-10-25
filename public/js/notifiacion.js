/*
document.getElementById('buttonNotify').addEventListener('show.bs.dropdown',function(){
	setTimeout(console.log('ok'),10000);
})
	;
*/

const ActivarNotificaciones = (button,event)=>{
	const lista = button.nextElementSibling;
	lista.innerHTML = '<a class="dropdown-item" >Cargando...</a>';	

	const headers = new Headers();
    headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
	const config = {
       	headers,
       	method:'GET'
   	};
   	
   	fetch('vehiculodocument/notifiaciones', config)
		.then( res =>{ 
			return	res.json();
		 })
		.then( data => {
			let Coleccion = '';			
			
			for(let k in data.notfy_new){
				const item = `
					<a class="dropdown-item p-0" href='vehiculodocument/exel/${data.notfy_new[k].id}' onclick='pressExelVencimiento()'>
		              <div class="alert alert-warning alert-with-icon " data-notify="container">
		              <i class="material-icons" data-notify="icon">add_alert</i>
		              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		                <i class="material-icons">close</i>
		              </button>
		              <span data-notify="message">
		              	<strong>
		              	  Vehículo: ${data.notfy_new[k].vehiculo.modelo}  - ${data.notfy_new[k].vehiculo.marca.descripcion}, placa ${data.notfy_new[k].vehiculo.placa}
		                </strong>
		                <br>
		                El documento ${data.notfy_new[k].tipo} , vence : ${data.notfy_new[k].fecha}
		              </span>
		            </div>
		            </a>`;
		        
		        Coleccion += item;
			}
			Coleccion += '<br>';

			for(let k in data.notify_vist){
				const item = `
					<a class="dropdown-item p-0" href='vehiculodocument/exel/${data.notify_vist[k].id}' >
		              <div class="alert alert-success alert-with-icon " data-notify="container">
		              <i class="material-icons" data-notify="icon">add_alert</i>
		              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		                <i class="material-icons">close</i>
		              </button>
		              <span data-notify="message">
		              	<strong>
		                	Vehículo: ${data.notify_vist[k].vehiculo.modelo}  - ${data.notify_vist[k].vehiculo.marca.descripcion}, placa ${data.notify_vist[k].vehiculo.placa}
		                </strong>
		                <br>
		                El documento ${data.notify_vist[k].tipo} , vence : ${data.notify_vist[k].fecha}
		              </span>
		            </div>
		            </a>`;
		        
		        Coleccion += item;
			}
			if(Coleccion == '<br>'){
				Coleccion = '<a class="dropdown-item">Sin notifiaciones</a>';
			}
			
			lista.innerHTML = Coleccion;
		}) 
		.catch( error => console.log(error) );
	

}

const pressExelVencimiento = ()=>{
	setTimeout(function(){
		const headers = new Headers();
	    headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
	    const config = {
	       	headers,
	       	method:'GET'
	   	};
	   	fetch('vehiculodocument/notifiacion', config)
			.then( res => res.json() )
			.then( data => {
				editButonNotify(data.numero);
			}) 
			.catch( error => console.log(error) );
	},1000);
}

/*	
	document.addEventListener("readystatechange", () =>{

		if(document.readyState == 'complete'){
			setTimeout(function(){	
				const headers = new Headers();
			    headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
			    const config = {
			       	headers,
			       	method:'GET'
			   	};
			   	fetch('vehiculodocument/notifiacion', config)
					.then( res => res.json() )
					.then( data => {
						editButonNotify(data.numero);
					}) 
					.catch( error => console.log(error) );
			},1000);
		}
		
	});
*/

const editButonNotify = (numero) =>{
	const button = document.getElementById('buttonNotify');
	const span = button.querySelector('span');
	if(numero == 0){
		span.classList.add('d-none');
	}else{
		span.classList.remove('d-none');
		span.innerText = numero;
	}
}

