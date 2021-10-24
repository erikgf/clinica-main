{{#.}}
<div class="card">
    <div class="card-header pt-2 pb-2">
        <b>Pago Fecha: </b> {{fecha_hora_registrado}}<br>
        <b>Caja:</b> {{caja}}|{{usuario_caja}}<br>
    </div>
    <div class="card-body pt-2 pb-2">
        <b>Efectivo: </b>S/ {{monto_efectivo}}<br>
        <b>Tarjeta: </b>S/ {{monto_tarjeta}}<br>
        <b>Depósito: </b>S/ {{monto_deposito}}
        <hr class="mt-2 mb-2">
        <b>Total: </b>S/ {{monto_total}}
    </div>
</div>
{{/.}}
