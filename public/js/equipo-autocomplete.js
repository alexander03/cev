const doSearchEquipoPrim = () => {

    var autoCompletejsEquipo = new autoComplete({
        data: {
            src: async () => {
                // Loading placeholder text
                document
                    .querySelector(".js-equipo-id")
                    .setAttribute("placeholder", "Loading...");
       
                // Fetch External Data Source
                const headers = new Headers();
                headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                const config = {
                    headers,
                    method:'GET'
                };
                  //Query
                const query = document.querySelector('.js-equipo-id').value;
                const source = await fetch(
                    `equipo/search/${query}` , config
                );
                let data = await source.json();
                // Post loading placeholder text
                // Returns Fetched data
                return data;
            },
            key: [ 'search' ],
            cache: false
        },
        sort: (a, b) => {
            if (a.match < b.match) return -1;
            if (a.match > b.match) return 1;
            return 0;
        },
        selector: ".js-equipo-id",
        threshold: 1,
        debounce: 0,
        searchEngine: "strict",
        highlight: true,
        maxResults: 5,
        resultsList: {
            render: true,
            container: source => {
                source.setAttribute("id", "autoComplete_list");
                source.setAttribute("class", "u-search-ua__result");
            },
            destination: document.querySelector(".js-equipo-id"),
            position: "afterend",
            element: "ul"
        },
        resultItem: {
            content: (data, source) => {
                source.innerHTML = data.match;
            },
            element: "li"
        },
        noResults: () => {
            document.querySelector("#autoComplete_list4").innerText = 'Sin resultados';
        },
        onSelection: feedback => { //VA NUESTRA LOGICA
            document.querySelector("#autoComplete_list4").innerText = '';
            const selection = feedback.selection.value;
            document.querySelector(".js-equipo-id").value = selection.codigo;
            document.querySelector(".js-equipo-desc").innerText = selection.descripcion;
            document.querySelector('#idEquipo').value = selection.id;
            mostrarHorometro();
            //document.querySelector('#horometro_inicial').value = selection.horometroactual;
            // console.log(feedback);
        }
    });
    
    // Toggle event for search input
    // showing & hidding results list onfocus / blur
    ["focus", "blur"].forEach(function(eventType) {
      const resultsList = document.querySelector("#autoComplete_list4");
    
      document.querySelector(".js-equipo-id").addEventListener(eventType, function() {
        // Hide results list & show other elemennts
        if (eventType === "blur") {
          resultsList.style.display = "none";
        } else if (eventType === "focus") {
          // Show results list & hide other elemennts
          resultsList.style.display = "block";
        }
      });
    });
};

const doSearchVehiculoPrim = () => {

    var autoCompletejsVehiculo = new autoComplete({
        data: {
            src: async () => {
                // Loading placeholder text
                document
                    .querySelector(".js-vehiculo-id")
                    .setAttribute("placeholder", "Loading...");
       
                // Fetch External Data Source
                const headers = new Headers();
                headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                const config = {
                    headers,
                    method:'GET'
                };
                  //Query
                const query = document.querySelector('.js-vehiculo-id').value;
                const source = await fetch(
                    `vehiculo/search/${query}` , config
                );
                let data = await source.json();
                // Post loading placeholder text
                // Returns Fetched data
                return data;
            },
            key: [ 'search' ],
            cache: false
        },
        sort: (a, b) => {
            if (a.match < b.match) return -1;
            if (a.match > b.match) return 1;
            return 0;
        },
        selector: ".js-vehiculo-id",
        threshold: 1,
        debounce: 0,
        searchEngine: "strict",
        highlight: true,
        maxResults: 5,
        resultsList: {
            render: true,
            container: source => {
                source.setAttribute("id", "autoComplete_list");
                source.setAttribute("class", "u-search-ua__result");
            },
            destination: document.querySelector(".js-vehiculo-id"),
            position: "afterend",
            element: "ul"
        },
        resultItem: {
            content: (data, source) => {
                source.innerHTML = data.match;
            },
            element: "li"
        },
        noResults: () => {
            document.querySelector("#autoComplete_list4").innerText = 'Sin resultados';
        },
        onSelection: feedback => { //VA NUESTRA LOGICA
            document.querySelector("#autoComplete_list4").innerText = '';
            const selection = feedback.selection.value;
            document.querySelector(".js-vehiculo-id").value = selection.codigo;
            document.querySelector(".js-vehiculo-desc").innerText = selection.descripcion;
            document.querySelector('#idVehiculo').value = selection.id;
            // console.log(feedback);
        }
    });
    
    // Toggle event for search input
    // showing & hidding results list onfocus / blur
    ["focus", "blur"].forEach(function(eventType) {
      const resultsList = document.querySelector("#autoComplete_list4");
    
      document.querySelector(".js-vehiculo-id").addEventListener(eventType, function() {
        // Hide results list & show other elemennts
        if (eventType === "blur") {
          resultsList.style.display = "none";
        } else if (eventType === "focus") {
          // Show results list & hide other elemennts
          resultsList.style.display = "block";
        }
      });
    });
};