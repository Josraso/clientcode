{*
* Vista para displayAdminOrderLeft en pedidos
* /modules/clientcode/views/templates/admin/order_left.tpl
*}

<div class="card client-code-order-left-card">
    <div class="card-header">
        <h3 class="card-header-title">
            <i class="material-icons">badge</i>
            Código de Cliente
        </h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-control-label"><strong>Código:</strong></label>
            <div>
                {if $client_code && $client_code != 'Sin asignar'}
                    <span class="badge badge-primary" style="font-size: 14px; padding: 6px 12px; background-color: #2196F3; color: white;">
                        {$client_code|escape:'htmlall':'UTF-8'}
                    </span>
                {else}
                    <span class="text-muted">Sin código asignado</span>
                {/if}
            </div>
        </div>

        {if $sales_agent}
        <div class="form-group">
            <label class="form-control-label"><strong>Comercial:</strong></label>
            <div>
                <span class="text-dark">{$sales_agent|escape:'htmlall':'UTF-8'}</span>
            </div>
        </div>
        {/if}

        <div class="form-group mt-3">
            <a href="{$link->getAdminLink('AdminCustomers', true, [], ['id_customer' => $customer_id|intval, 'viewcustomer' => 1])|escape:'html':'UTF-8'}"
               class="btn btn-sm btn-outline-primary btn-block" target="_blank">
                <i class="material-icons" style="font-size: 16px; vertical-align: middle;">visibility</i>
                Ver Ficha del Cliente
            </a>
        </div>
    </div>
</div>

<style>
.client-code-order-left-card {
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.client-code-order-left-card .card-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #2196F3;
}

.client-code-order-left-card .card-header-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.client-code-order-left-card .material-icons {
    font-size: 20px;
}
</style>
