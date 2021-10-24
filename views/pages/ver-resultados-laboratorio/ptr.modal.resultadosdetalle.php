<div class="modal fade" id="mdl-resultadosdetalle" role="dialog" aria-labelledby="mdl-resultadosdetallelabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" id="frm-resultadosdetalle">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-resultadosdetallelabel">Nro Recibo:   <b id="lbl-recibo"></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input id="txt-idatencionmedica" type="hidden">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Examenes <b id="lbl-cantidadservicios"></b></h3>
                    </div>
                    <div class="card-body">
                        <table style="font-size:.95em" class="table table-sm">
                            <thead>
                                <tr>
                                    <th style="width:80px;">¿Imprimir?</th>
                                    <th>Descripción</th>
                                    <th style="width:80px;">Validado</th>
                                    <th style="width:130px;">Fecha Muestra</th>
                                    <th style="width:130px;">Fecha Entrega</th>
                                </tr>
                            </thead>
                            <tbody id="tbd-resultadosdetalle">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <div class="btn-group">
                    <button id="btn-imprimirmuestratodojunto"  type="button" class="btn btn-primary  dropdown-toggle dropdown-icon"  data-toggle="dropdown"><span class="fa fa-print"></span> IMPRIMIR TODO JUNTO</button>
                        <span class="sr-only">Toggle Dropdown</span>
                        <div class="dropdown-menu" role="menu">
                            <button class="dropdown-item" id="btn-imprimirtodojuntologo"> CON LOGO</button>
                            <button class="dropdown-item" id="btn-imprimirtodojuntosinlogo"> SIN LOGO</button>
                        </div>
                    </button>
                </div>
                <div class="btn-group">
                    <button id="btn-imprimirmuestra"  type="button" class="btn btn-info  dropdown-toggle dropdown-icon"  data-toggle="dropdown"><span class="fa fa-print"></span> IMPRIMIR</button>
                        <span class="sr-only">Toggle Dropdown</span>
                        <div class="dropdown-menu" role="menu">
                            <button class="dropdown-item" id="btn-imprimirlogo"> CON LOGO</button>
                            <button class="dropdown-item" id="btn-imprimirsinlogo"> SIN LOGO</button>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


