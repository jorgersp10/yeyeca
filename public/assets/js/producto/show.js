$(function() {

    const addImagenURL=document.getElementById("addImagenURL")

    const addImagen=document.getElementById("addImagen")
    const tokens_form= document.getElementsByName("_token");
    var token_form=tokens_form[0].value;

    renderImages();

    addImagen.addEventListener("submit", e =>{
        e.preventDefault();
        putImages('fija');
    });

    addImagenURL.addEventListener("submit", e =>{
        e.preventDefault();
        putImages('url');
    });


    async function getImages() 
    {
        var pro_id = document.getElementById("id_producto").value;
        // para local usar asi
          let url = '../imagenpro/'+pro_id;

        // para la web usar asi
        //let url = '../imagenpro/'+pro_id; 
        try 
        {
            let res = await fetch(url);
            return await res.json();
        } catch (error) {
            console.log(error);
        }
    };

    async function renderImages() 
    {
        let images = await getImages();
        let html = '';
        images.forEach(image => {
            let urlImagen='';
            if (image.tipo=="U")
            {
                urlImagen=image.imagen;
            }
            else
            {
                // para local
                //urlImagen='http:\\storage/img/electro/'+image.imagen;
                // para la web usar asi
                urlImagen='../storage/img/electro/'+image.imagen;
            }
            
            // para la web usar asi
            //let urlImagen='../storage/img/electro/'+image.imagen;
            let imagenBorrar='borrarImagen-'+image.id;
            let divBorrar='div-'+image.id;
            let htmlSegment = ` `;            
            
                htmlSegment = `
                <div id="${divBorrar}" class="button-borrar">
                    <img src="${urlImagen}" id="imagen-${image.id}" alt="" class="galeria-img">
                    <button type="button"  id="${imagenBorrar}" class="button-borrar">Borrar</button>
                </div>
                `;

            

            html += htmlSegment;
            });
           

        let container = document.querySelector('.contenedor-galeria');
        container.innerHTML = html;
        
        const buttonsb = document.querySelectorAll('button.button-borrar')
        buttonsb.forEach(b =>{
            b.addEventListener("click", () => {
                var idim = b.id.replace("borrarImagen-", "");
                document.getElementById('div-'+idim).className = 'eliminado'
                var idim = b.id.replace("borrarImagen-", "");
                //let url = 'http://127.0.0.1:8000/imagen/'+idim;
                let url = '../producto/imagen/'+idim;
                console.log(url)
  
                    $.ajax(
                    {
                        url: url,
                        type: 'POST',
                        dataType: "JSON",
                        data: {
                            "id": idim,
                            "_method": 'POST',
                            "_token": token_form,
                        },
                        success: function ()
                        {
                            console.log("it Work");
                        },
                        error: function (e)
                        {
                            console.log(e);
                        }
                        
                    });
            })
        })
    };


    async function putImages(fijaoUrl) 
    {
        ///////// Sacando las variables de la pagina ////////
        var pro_id = document.getElementById("id_producto").value;
        const formData = new FormData();
        const imagen_agregar =document.getElementById("imagen_agregar");
        const imagenURL =document.getElementById("imagen_url");

        formData.append('_token',token_form);
        formData.append('id_producto',pro_id);
        if (fijaoUrl=='url')
        {
            formData.append('imagenUrl',imagenURL.value);
            formData.append('tipo','U');
        }
        else
        {
            formData.append('imagen',imagen_agregar.files[0]);
            formData.append('tipo','F');
        }
        
        //para local asi
        let url = '/imagenpro';
        //para la web usar asi
        //let url = '../imagenpro';

        $.ajax({
            method: "POST",
            processData: false,  // tell jQuery not to process the data
            contentType: false,  
            url: url,
            data: formData,
            success: function (response) {
                renderImages();
            },
            error : function (error){
            }
        });
    }

})
