//document.addEventListener('DOMContentLoaded', cargaTipos);
//document.addEventListener('DOMContentLoaded', muestraTareas);

const listaTareas = document.getElementById('list-tareas');
const listCategorias = document.getElementById('categoria');

let categorias = [];

eventListener();

function eventListener(){
    listaCategorias.addEventListener("change", getTareas);
    document.addEventListener('DOMContentLoaded', documentReady);
}

function cargaTipos(e){
    e.preventDefault();

    //const padre = document.getElementById('categoria');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "./Controllers/categoriasController.php", true);

    xhr.onload = function(){//Funcion que carga las imagenes
    
        if(this.status === 200)
        {
            categorias = JSON.parse(this.responseText);

            console.log(categorias);

            categorias.forEach(function(cat){
                let html = "";
                html = `<option value="${cat.id}">${cat.nombre}</option>`;
                listCategorias.innerHTML += html;
            });
        }
    }
    xhr.send();
}

function getTareas(){
    console.log("rfgrgtgh");
    const xhr = new XMLHttpRequest();

    var cat_id = listCategorias.Value;
    if(cat_id != 0){
        xhr.open('GET', "./Controllers/tareasController.php?categoria_id=" + cat_id, true);
    }
    else{
        xhr.open('GET', "./Controllers/tareasController.php", true);
    }

    xhr.onload = function(){//Funcion que carga las imagenes
    
        if(this.status === 200)
        {
            var tareas = JSON.parse(this.responseText);
            console.log("asfdaefesfdsf");
            console.log(tareas);

            tareas.forEach(function(tar){
                let html = "";
                html = `<div class="col-m-12 col-s-12 bt p-top-bot">
                            <div class="info">
                                Datos de la consulta
                            </div>
                            <h2>${tar.titulo}</h2>
                            <div class="descripcion">
                                ${tar.descripcion}
                            </div>
                        </div>`;
                padre.innerHTML += html;
            });
        }
    }
    xhr.send();

}

function muestraTareas(e){
    //e.preventDefault();
    console.log("sdffds");

    //const padre = document.getElementById('tareas');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "./Controllers/tareasController.php", true);

    xhr.onload = function(){//Funcion que carga las imagenes
    
        if(this.status === 200)
        {
            tareas = JSON.parse(this.responseText);
            console.log("asfdaefesfdsf");
            console.log(tareas);

            tareas.forEach(function(tar){
                let html = "";
                html = `<div class="col-m-12 col-s-12 bt p-top-bot">
                            <div class="info">
                                Datos de la consulta
                            </div>
                            <h2>${tar.titulo}</h2>
                            <div class="descripcion">
                                ${tar.descripcion}
                            </div>
                        </div>`;
                listaTareas.innerHTML += html;
            });
        }
    }
    xhr.send();
}

function documentReady(){
    cargaTipos();
    muestraTareas();

}