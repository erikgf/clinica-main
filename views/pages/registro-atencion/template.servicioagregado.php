{{#.}}
<div class="blk-servicioagregado" data-concampana="{{con_campaña}}" style="width: calc(100% - 1em);" tabindex="0" data-serviciojson= '{"id_servicio":"{{id_servicio}}","nombre_servicio":"{{nombre_servicio}}","idtipo_afectacion":"{{idtipo_afectacion}}","idunidad_medida":"{{idunidad_medida}}","descripcion":"{{descripcion}}"}'>
        <div class="row callout callout-warning">
            <div class="col-sm-12 col-md-7 {{#if con_campaña}}text-primary{{/if}}">
                <h5>{{nombre_servicio}}</h5>
                <small>{{descripcion}}</small>
            </div>
            <div class="col-sm-6 col-md-2">
                <label>P. Unitario: </label>
                <input class="form-control precio-unitario {{#if con_campaña}}bg-primary{{/if}}"  readonly  value="{{precio_unitario}}" style="width: 130px;display:inline-block">
            </div>
            <!--
            <div class="col-sm-6 col-md-2">
                <button type="button" class="btn btn-block btn-outline-warning btn-descuento" data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-money"></i>
                    <i style="pointer-events:none">Sin Descuento</i>
                </button>
            </div>
            <span class="blk-descuentodetalles hide col-md-2 col-sm-12">
                <strong>Autorizado por: </strong>
                <span class="lbl-autorizadopor"></span>
                <br>
                <strong>Validado por: </strong>
                <span class="lbl-validadopor"></span>
                <br>
                <span style="max-width:350px;display:inline-block">
                    <strong>Motivo: </strong>
                    <span class="lbl-motivo"></span>
                </span>
                <br>
            </span>
            -->
            <span class="col-sm-12 col-md-2">
                Subtotal: <h5 class=lbl-subtotalitem style="color:green;display:inline-block">{{precio_unitario}}</h5>
            </span>
        
            <div class="col-sm-12 col-md-1">
                <button type="button" class="btn btn-block btn-danger btn-quitarservicio">&times;</button>
            </div>
        </div>
</div>
{{/.}}