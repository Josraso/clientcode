{*
* Vista para mostrar en la ficha del cliente
* /modules/clientcode/views/templates/admin/customer_info.tpl
*}

<div class="card client-code-card-top" id="client-code-card">
    <div class="card-header">
        <h3 class="card-header-title">
            <i class="material-icons">badge</i>
            Código de Cliente
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-control-label"><strong>Código:</strong></label>
                    <div>
                        {if $client_code}
                            <span class="badge badge-primary" style="font-size: 16px; padding: 8px 16px; background-color: #2196F3;">
                                {$client_code|escape:'htmlall':'UTF-8'}
                            </span>
                        {else}
                            <em class="text-muted">Se generará automáticamente</em>
                        {/if}
                    </div>
                </div>
            </div>

            {if $sales_agent}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-control-label"><strong>Comercial:</strong></label>
                    <div>
                        <span class="text-dark">{$sales_agent|escape:'htmlall':'UTF-8'}</span>
                    </div>
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>

<style>
.client-code-card-top {
    order: -1 !important;
    margin-bottom: 20px !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.client-code-card-top .card-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #2196F3;
}

.client-code-card-top .card-header-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
}

.client-code-card-top .material-icons {
    font-size: 20px;
}

/* Forzar que aparezca primero */
#content .client-code-card-top {
    position: relative;
    z-index: 10;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Mover la card al principio del contenido
    var clientCard = document.getElementById('client-code-card');
    if (clientCard) {
        var container = clientCard.parentElement;
        if (container) {
            container.insertBefore(clientCard, container.firstChild);
        }
    }
});
</script>