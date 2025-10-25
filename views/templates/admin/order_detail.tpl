{*
* Vista para detalle de pedido
* /modules/clientcode/views/templates/admin/order_detail.tpl
*}

<div class="card mt-2">
    <div class="card-header">
        <h3 class="card-header-title">
            <i class="material-icons">badge</i>
            Información del Cliente
        </h3>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-4">
                <strong>Código de Cliente:</strong>
            </div>
            <div class="col-md-8">
                {if $client_code}
                    <span class="badge badge-info">{$client_code|escape:'htmlall':'UTF-8'}</span>
                {else}
                    <span class="text-muted">Sin asignar</span>
                {/if}
            </div>
        </div>
        
        {if $sales_agent}
        <div class="row mb-2">
            <div class="col-md-4">
                <strong>Comercial:</strong>
            </div>
            <div class="col-md-8">
                {$sales_agent|escape:'htmlall':'UTF-8'}
            </div>
        </div>
        {/if}
        
        <div class="row">
            <div class="col-md-12">
                <a href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&id_customer={$customer_id|intval}&viewcustomer" 
                   class="btn btn-sm btn-outline-secondary" target="_blank">
                    <i class="material-icons">visibility</i>
                    Ver Cliente
                </a>
            </div>
        </div>
    </div>
</div>