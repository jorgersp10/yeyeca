export class search {
    constructor(myurlp,mysearchp,ul_add_lip){
        this.url = myurlp;
        this.mysearch = mysearchp;
        this.ul_add_li = ul_add_lip;
        this.idli = "mylist";
        this.pcantidad = document.querySelector("#pcantidad");
    }

    InputSearch(){
        this.mysearch.addEventListener("input", (e) => {
            e.preventDefault();
            try{
                let token = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
                let minimo_letras = 0;
                let valor = this.mysearch.value;
                if (valor.length > minimo_letras){
                    let datasearch = new FormData();
                    datasearch.append("valor",valor);
                    fetch(this.url,{
                        headers: {
                            "X-CSRF-TOKEN":token,
                        },
                        method:"post",
                        body:datasearch
                    })
                    .then((data) => data.json())
                    .then((data) => {
                        //console.log(data);
                        this.Showlist(data, valor);
                    })
                    .catch(function (error){
                        console.error("Error:", error)
                    });
                }else{
                    this.ul_add_li.style.display = "";
                }
            } catch(error){

            }
        })
    }
    
    Showlist(data,valor){
        this.ul_add_li.style.display = "block";
        if (data.estado == 1){
            if (data.result != ""){
                let arrayp = data.result;
                this.ul_add_li.innerHTML = "";
                let n = 0;
                this.Show_list_each_data(arrap,valor,n);
                let adclasli = document.getElementById("1"+this.idli);
                adclasli.classList.add('selected');
            }else{
                this.ul_add_li.innerHTML = "";
                this.ul_add_li.innerHTML += `
                    <p style = "color:red;"><br>No se encontro</p>
                `;
            }
        }
    }

    Show_list_each_data(arrayp,valor){
        for (let item of arrayp){
            n++;
            let nombre = item.descripcion;
            this.ul_add_li.innerHTML +=`
            <li id="${n+this.idli}" value="${item.nombre}" class="list group item" style="">
            <div class = "d-flex flex-row" style ="">
            <div class = "p-2">
                <strong>${nombre.substr(0,valor.lenght)}</strong>
                ${nombre.substr(valor.lenght)}
                <p class="card-text">P. venta ${item.precio_venta}</p>
            </div>
            </div>
            </li>
            `
        }
    }
}