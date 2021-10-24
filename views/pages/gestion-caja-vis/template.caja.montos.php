<input type="hidden" class="txt-estacerrada" value="{{esta_cerrada}}"/>
<div class="form-group row">
    <label for="lbl-montoapertura" class="col-md-2 col-form-label text-info">Apertura</label>
    <div class="col-md-3">
        <input type="text" class="form-control" id="lbl-montoapertura" value="{{monto_apertura}}" readonly>
    </div>
</div>  
<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="form-group row">
            <label for="lbl-montoingresos" class="col-md-4 col-form-label text-green">Ingresos</label>
            <div class="col-md-6">
                <input type="text" class="form-control" id="lbl-montoingresos"  value="{{ingresos.monto_total}}" readonly>
            </div>
        </div>
        <div class="form-group row">
            <label for="lbl-ingresoefectivo" class="col-md-4 col-form-label">Efectivo</label>
            <div class="col-md-6">
                <input type="text" class="form-control" id="lbl-ingresoefectivo"  value="{{ingresos.monto_efectivo}}" readonly>
            </div>
        </div>
        <div class="form-group row">
            <label for="lbl-ingresodeposito" class="col-md-4 col-form-label">Depósito</label>
            <div class="col-md-6">
                <input type="text" class="form-control" id="lbl-ingresodeposito"  value="{{ingresos.monto_deposito}}" readonly>
            </div>
        </div>
        <div class="form-group row">
            <label for="lbl-ingresotarjeta" class="col-md-4 col-form-label">Tarjeta</label>
            <div class="col-md-6">
                <input type="text" class="form-control" id="lbl-ingresotarjeta"  value="{{ingresos.monto_tarjeta}}" readonly>
            </div>
        </div>
        <div class="form-group row">
            <label for="lbl-ingresocredito" class="col-md-4 col-form-label">Saldo</label>
            <div class="col-md-6">
                <input type="text" class="form-control" id="lbl-ingresocredito"  value="{{ingresos.monto_credito}}" readonly>
            </div>
        </div>

    </div>
    <div class="col-md-6 col-sm-12">
        <div class="form-group row">
            <label for="lbl-montoegresos" class="col-md-4 col-form-label text-red">Egresos</label>
            <div class="col-md-6">
                <input type="text" class="form-control" id="lbl-montoegresos" value="{{egresos.monto_total}}"  readonly>
            </div>
        </div>
        <div class="form-group row">
            <label for="lbl-egresoefectivo" class="col-md-4 col-form-label">Efectivo</label>
            <div class="col-md-6">
                <input type="text" class="form-control" id="lbl-egresoefectivo"   value="{{egresos.monto_efectivo}}"   readonly>
            </div>
        </div>   
    </div>
</div>

<label  class="form-label">Balance ( I - E)</label>
<div class="form-group row">
    <label for="lbl-totalcaja" class="col-md-2 col-form-label">Total</label>
    <div class="col-md-3">
        <input type="text" class="form-control" id="lbl-totalcaja" value="{{balance_total}}" readonly>
    </div>
    <label for="lbl-soloefectivo" class="offset-1 col-md-2 col-form-label">Sólo Efectivo</label>
    <div class="col-md-3">
        <input type="text" class="form-control" id="lbl-soloefectivo" value="{{balance_total_solo_efectivo}}" readonly>
    </div>
</div>