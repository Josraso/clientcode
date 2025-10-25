{*
* Vista integrada para información del cliente en pedido
* /modules/clientcode/views/templates/admin/order_customer_info.tpl
*}

<div class="card" style="margin-bottom: 15px;">
    <div class="card-header">
        <h3 class="card-header-title">
            <i class="material-icons">account_circle</i>
            Información Adicional del Cliente
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-control-label"><strong>Código de Cliente:</strong></label>
                    <div>
                        {if $client_code && $client_code != 'Not assigned'}
                            <span class="badge badge-info" style="font-size: 14px; padding: 6px 12px; background-color: #2196F3;">
                                {$client_code|escape:'htmlall':'UTF-8'}
                            </span>
                        {else}
                            <span class="text-muted">Sin código asignado</span>
                        {/if}
                    </div>
                </div>
            </div>

            {if $sales_agent}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-control-label"><strong>Comercial Asignado:</strong></label>
                    <div>
                        <span>{$sales_agent|escape:'htmlall':'UTF-8'}</span>
                    </div>
                </div>
            </div>
            {/if}
        </div>

        <div class="row mt-2">
            <div class="col-md-12">
                <a href="{$link->getAdminLink('AdminCustomers', true, [], ['id_customer' => $customer_id|intval, 'viewcustomer' => 1])|escape:'html':'UTF-8'}"
                   class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="material-icons" style="font-size: 16px; vertical-align: middle;">visibility</i>
                    Ver Ficha Completa del Cliente
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.card-header-title {
    display: flex;
    align-items: center;
    gap: 8px;
}
.card-header-title .material-icons {
    font-size: 20px;
}
</style>
