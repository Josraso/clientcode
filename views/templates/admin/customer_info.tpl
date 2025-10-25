{*
* Vista para mostrar en la ficha del cliente
* /modules/clientcode/views/templates/admin/customer_info.tpl
*}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-info-circle"></i>
        Información Adicional del Cliente
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="col-lg-3 control-label"><strong>Código de Cliente:</strong></label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {if $client_code}
                        <span class="badge badge-info" style="font-size: 14px; padding: 6px 12px;">{$client_code|escape:'htmlall':'UTF-8'}</span>
                    {else}
                        <em class="text-muted">Se generará automáticamente</em>
                    {/if}
                </p>
            </div>
        </div>
        {if $sales_agent}
        <div class="form-group">
            <label class="col-lg-3 control-label"><strong>Comercial:</strong></label>
            <div class="col-lg-9">
                <p class="form-control-static">{$sales_agent|escape:'htmlall':'UTF-8'}</p>
            </div>
        </div>
        {/if}
    </div>
</div>

<style>
.panel-heading {
    background-color: #f8f9fa;
    font-weight: 600;
}
</style>