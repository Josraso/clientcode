{*
* Client Code - Customer Fields Template (Para PrestaShop 1.7.0 - 1.7.6)
* Este archivo solo es necesario si usas versiones anteriores a 1.7.7
*}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-user"></i>
        {l s='Client Information' mod='clientcode'}
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label col-lg-3" for="client_code">
                <span class="label-tooltip" 
                      data-toggle="tooltip" 
                      data-html="true" 
                      title="{l s='Unique client code (auto-generated if empty)' mod='clientcode'}">
                    {l s='Client Code' mod='clientcode'}
                </span>
            </label>
            <div class="col-lg-9">
                <input type="text" 
                       name="client_code" 
                       id="client_code" 
                       class="form-control" 
                       value="{$client_code|escape:'htmlall':'UTF-8'}"
                       placeholder="CLI-0001" />
                <p class="help-block">
                    {l s='Leave empty for automatic generation' mod='clientcode'}
                </p>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-lg-3" for="sales_agent">
                {l s='Sales Agent' mod='clientcode'}
            </label>
            <div class="col-lg-9">
                <input type="text" 
                       name="sales_agent" 
                       id="sales_agent" 
                       class="form-control" 
                       value="{$sales_agent|escape:'htmlall':'UTF-8'}"
                       placeholder="{l s='Sales agent name' mod='clientcode'}" />
            </div>
        </div>
    </div>
</div>

<style>
.panel-heading {
    background-color: #f4f4f4;
    border-bottom: 1px solid #ddd;
    padding: 10px 15px;
}
.panel-heading i {
    margin-right: 5px;
}
</style>