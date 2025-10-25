{*
* Vista para displayAdminCustomersForm en ficha de cliente
* /modules/clientcode/views/templates/admin/customer_form.tpl
*}

<div class="alert alert-info client-code-customer-compact" style="margin: 15px 0;">
    <div class="row align-items-center">
        <div class="col-auto">
            <i class="material-icons" style="vertical-align: middle; margin-right: 5px;">badge</i>
            <strong>Código de Cliente:</strong>
            {if $client_code}
                <span class="badge badge-primary" style="font-size: 14px; padding: 6px 12px; background-color: #2196F3; color: white; margin-left: 5px;">
                    {$client_code|escape:'htmlall':'UTF-8'}
                </span>
            {else}
                <em class="text-muted" style="margin-left: 5px;">Se generará automáticamente</em>
            {/if}
        </div>

        {if $sales_agent}
        <div class="col-auto">
            <strong>Comercial:</strong>
            <span style="margin-left: 5px;">{$sales_agent|escape:'htmlall':'UTF-8'}</span>
        </div>
        {/if}
    </div>
</div>

<style>
.client-code-customer-compact {
    padding: 12px 15px;
    border-left: 4px solid #2196F3;
    background-color: #e3f2fd !important;
}

.client-code-customer-compact .row {
    margin: 0;
}

.client-code-customer-compact .col-auto {
    padding: 0 10px;
}
</style>
