{*
* Vista para mostrar en la ficha del cliente
* /modules/clientcode/views/templates/admin/customer_info.tpl
*}

<div class="card client-code-customer-card" id="client-code-customer-card" style="position: relative !important; z-index: 9999 !important;">
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
                            <span class="badge badge-primary" style="font-size: 16px; padding: 8px 16px; background-color: #2196F3; color: white;">
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
.client-code-customer-card {
    margin-bottom: 20px !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid #dee2e6 !important;
}

.client-code-customer-card .card-header {
    background-color: #f8f9fa !important;
    border-bottom: 2px solid #2196F3 !important;
}

.client-code-customer-card .card-header-title {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    margin: 0 !important;
}

.client-code-customer-card .material-icons {
    font-size: 20px !important;
}
</style>

<script>
(function() {
    function moveClientCardToTop() {
        var clientCard = document.getElementById('client-code-customer-card');
        if (!clientCard) {
            return false;
        }

        // Buscar el contenedor principal - intentar múltiples selectores
        var mainContent = document.querySelector('#content .content-div') ||
                         document.querySelector('#main-div') ||
                         document.querySelector('.content-div') ||
                         document.querySelector('[role="main"]') ||
                         document.querySelector('#content');

        if (!mainContent) {
            return false;
        }

        // Buscar el primer elemento hijo que sea una card, panel o row
        var firstElement = mainContent.querySelector('.card, .panel, .row, .alert');

        if (firstElement && firstElement !== clientCard) {
            // Insertar ANTES del primer elemento
            firstElement.parentNode.insertBefore(clientCard, firstElement);
            return true;
        } else if (mainContent.firstChild && mainContent.firstChild !== clientCard) {
            // Si no hay elementos específicos, insertar al principio
            mainContent.insertBefore(clientCard, mainContent.firstChild);
            return true;
        }

        return false;
    }

    // Intentar mover varias veces con diferentes delays
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() { moveClientCardToTop(); }, 100);
        setTimeout(function() { moveClientCardToTop(); }, 300);
        setTimeout(function() { moveClientCardToTop(); }, 500);
        setTimeout(function() { moveClientCardToTop(); }, 800);
        setTimeout(function() { moveClientCardToTop(); }, 1200);
        setTimeout(function() { moveClientCardToTop(); }, 2000);
    });

    // También intentar inmediatamente
    moveClientCardToTop();
})();
</script>