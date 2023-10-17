 
{{-- //-{{$inmueble->id}} --}}
 <div id="pagarRegistro" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">    
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titulo_pago"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form_pago" action="{{route('caja.pagar')}}" method="POST"> 
                    
                    {{csrf_field()}}

                    <h5>¿Desea Pagar el registro ?</h5>                    

                    <div class="row mb-4">
                        <div class="col-sm-9">
                            <input type="hidden" id="id_cuota" name="id_cuota" class="form-control">
                        </div>
                        <div class="col-sm-9">
                            <input type="hidden" id="id_mueble" name="id_mueble" class="form-control">
                        </div>
                        <div class="col-sm-9">
                            <input type="hidden" id="id_electro" name="id_electro" class="form-control">
                        </div>
                        <div class="col-sm-9">
                            <input type="hidden" id="id_acuerdo" name="id_acuerdo" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Descripcion</label>
                        <div class="col-sm-6">
                            <input type="text" id="producto" name="producto" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Cuotas Pendientes</label>
                        <div class="col-sm-4">
                            <input readonly="readonly" type="text" id="cuotas_max" name="cuotas_max" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Cuotas Vencidas</label>
                        <div class="col-sm-4">
                            <input readonly="readonly"type="text" id="cuotas_ven" name="cuotas_ven" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Cantidad Cuotas a Pagar</label>
                        <div class="col-sm-4">
                            <input type="text" id="cuotas_apag" name="cuotas_apag" class="form-control">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table  class="table table-editable table-nowrap align-middle table-edits">
                            <thead>
                                <tr>
                                    <th data-priority="1">Cuota Nro</th>
                                    <th data-priority="1">Vencimiento</th>
                                    <th data-priority="1">Total Cuota</th>
                                    <th data-priority="1">Mora</th>
                                    <th data-priority="1">Punitorio</th>
                                    <th data-priority="1">Iva</th>
                                    <th data-priority="1">Total a Pagar</th>
                                </tr>
                            </thead>
                            <tbody id="cuotas_inmuebles">

            
                            </tbody>
                            
                        </table>
                   
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total a Pagar</label>
                            <div class="col-sm-6">
                                <input readonly="readonly" type="text" id="total_apag" name="total_apag" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Fecha Pago</label>
                            <div class="col-sm-6">
                                <input type="date" id="fec_pag" name="fec_pag" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total Efectivo </label>
                        <div class="col-sm-3">
                            <input type="text" id="total_pagadof" name="total_pagadof" class="form-control">
                        </div>
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nro Cheque </label>
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Banco </label>
                    </div>

                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total Cheque </label>
                        <div class="col-sm-3">
                            <input type="text" id="total_pagadoch" name="total_pagadoch" class="form-control">
                        </div>
                        
                        <div class="col-sm-3">
                            <input type="text" id="nro_cheque" name="nro_cheque" class="form-control">
                        </div>
                        
                        <div class="col-sm-3">
                        <select class="form-control" name="ban_che_id" id="ban_che_id">                                     
                            <option value="0" disabled>Seleccione</option>
                            
                            @foreach($bancos as $ban)
                                <option value="{{$ban->id}}">{{$ban->descripcion}}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total T. Credito </label>
                        <div class="col-sm-3">
                            <input type="text" id="total_pagadotc" name="total_pagadotc" class="form-control">
                        </div>
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nro Tarjeta </label>
                        <div class="col-sm-3">
                            <input type="text" id="nro_tcredito" name="nro_tcredito" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total T. Debito </label>
                        <div class="col-sm-3">
                            <input type="text" id="total_pagadotd" name="total_pagadotd" class="form-control">
                        </div>
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nro Tarjeta </label>
                        <div class="col-sm-3">
                            <input type="text" id="nro_tdebito" name="nro_tdebito" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total Transferencia </label>
                        <div class="col-sm-3">
                            <input type="text" id="total_pagadotr" name="total_pagadotr" value=0 class="form-control">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total Vuelto</label>
                        <div class="col-sm-3">
                            <input readonly="readonly" type="text" id="total_vuelto" name="total_vuelto" class="form-control">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button id="boton_guardar" type="submit" class="btn btn-primary" >Aceptar </button>
                    </div>

                </form>
            </div>                                    
        </div>
    </div>
</div>