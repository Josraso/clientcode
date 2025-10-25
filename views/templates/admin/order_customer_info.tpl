{*
* Vista integrada para información del cliente en pedido
* /modules/clientcode/views/templates/admin/order_customer_info.tpl
*}

<div class="card client-code-order-card" id="client-code-order-card">
    <div class="card-header">
        <h3 class="card-header-title">
            <i class="material-icons">account_circle</i>
            Información del Cliente
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-control-label"><strong>Código de Cliente:</strong></label>
                    <div>
                        {if $client_code && $client_code != 'Not assigned'}
                            <span class="badge badge-primary" style="font-size: 14px; padding: 6px 12px; background-color: #2196F3;">
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
                    <label class="form-control-label"><strong>Comercial:</strong></label>
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
                    Ver Ficha del Cliente
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.client-code-order-card {
    order: 2 !important;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.client-code-order-card .card-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #2196F3;
}

.client-code-order-card .card-header-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.client-code-order-card .card-header-title .material-icons {
    font-size: 20px;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function moveClientCardInOrder() {
        var clientCard = document.getElementById('client-code-order-card');
        if (!clientCard) return;

        // Buscar el contenedor principal de cards en la vista de pedido
        var orderContainer = clientCard.closest('.order-view-page, #content');
        if (!orderContainer) return;

        // Buscar la columna izquierda
        var leftColumn = orderContainer.querySelector('.col-lg-7, .col-md-7, .column-left');
        if (!leftColumn) {
            leftColumn = orderContainer.querySelector('.row .col');
        }

        if (leftColumn) {
            // Buscar todas las cards en la columna
            var allCards = leftColumn.querySelectorAll('.card');

            if (allCards.length >= 2 && allCards[0] !== clientCard) {
                // Insertar después de la primera card (normalmente "Pedido")
                allCards[0].parentNode.insertBefore(clientCard, allCards[1]);
            } else if (allCards.length === 1 && allCards[0] !== clientCard) {
                // Si solo hay una card, insertar después
                allCards[0].parentNode.insertBefore(clientCard, allCards[0].nextSibling);
            }
        }
    }

    // Intentar mover varias veces
    setTimeout(moveClientCardInOrder, 100);
    setTimeout(moveClientCardInOrder, 500);
    setTimeout(moveClientCardInOrder, 1000);
});
</script>
