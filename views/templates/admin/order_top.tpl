{*
* Vista para displayAdminOrderTop en pedidos
* /modules/clientcode/views/templates/admin/order_top.tpl
*}

<div class="alert alert-info client-code-order-compact">
    <div class="row align-items-center">
        <div class="col-auto">
            <i class="material-icons" style="vertical-align: middle; margin-right: 5px;">badge</i>
            <strong>Código:</strong>
            {if $client_code && $client_code != 'Sin asignar'}
                <span class="badge badge-primary" style="font-size: 13px; padding: 5px 10px; background-color: #2196F3; color: white; margin-left: 5px;">
                    {$client_code|escape:'htmlall':'UTF-8'}
                </span>
            {else}
                <span class="text-muted" style="margin-left: 5px;">Sin código</span>
            {/if}
        </div>

        {if $sales_agent}
        <div class="col-auto">
            <strong>Comercial:</strong>
            <span style="margin-left: 5px;">{$sales_agent|escape:'htmlall':'UTF-8'}</span>
        </div>
        {/if}

        <div class="col-auto ml-auto">
            <a href="{$link->getAdminLink('AdminCustomers', true, [], ['id_customer' => $customer_id|intval, 'viewcustomer' => 1])|escape:'html':'UTF-8'}"
               class="btn btn-sm btn-outline-primary" target="_blank" style="white-space: nowrap;">
                <i class="material-icons" style="font-size: 14px; vertical-align: middle;">visibility</i>
                Ver Cliente
            </a>
        </div>
    </div>
</div>

<style>
.client-code-order-compact {
    margin-bottom: 15px;
    padding: 10px 15px;
    border-left: 4px solid #2196F3;
}

.client-code-order-compact .row {
    margin: 0;
}

.client-code-order-compact .col-auto {
    padding: 0 10px;
}
</style>
