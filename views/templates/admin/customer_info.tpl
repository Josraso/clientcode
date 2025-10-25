{*
* Vista para mostrar en la ficha del cliente
* /modules/clientcode/views/templates/admin/customer_info.tpl
*}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-info-circle"></i>
        Información Adicional
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="col-lg-3 control-label">Código de Cliente:</label>
            <div class="col-lg-9">
                <p class="form-control-static">
                    {if $client_code}
                        <span class="badge badge-info">{$client_code|escape:'htmlall':'UTF-8'}</span>
                    {else}
                        <em class="text-muted">Se generará automáticamente</em>
                    {/if}
                </p>
            </div>
        </div>
        {if $sales_agent}
        <div class="form-group">
            <label class="col-lg-3 control-label">Comercial:</label>
            <div class="col-lg-9">
                <p class="form-control-static">{$sales_agent|escape:'htmlall':'UTF-8'}</p>
            </div>
        </div>
        {/if}
    </div>
</div>