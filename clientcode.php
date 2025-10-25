<?php
/**
 * Client Code Module
 *
 * @author    Tu Nombre
 * @copyright 2025
 * @license   MIT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ClientCode extends Module
{
    public function __construct()
    {
        $this->name = 'clientcode';
        $this->tab = 'administration';
        $this->version = '1.0.2';
        $this->author = 'Tu Nombre';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '9.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Client Code');
        $this->description = $this->l('Adds client code and sales agent fields to customers');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHook('actionCustomerFormBuilderModifier')
            && $this->registerHook('actionAfterCreateCustomerFormHandler')
            && $this->registerHook('actionAfterUpdateCustomerFormHandler')
            && $this->registerHook('actionObjectCustomerAddAfter')
            && $this->registerHook('actionCustomerGridDefinitionModifier')
            && $this->registerHook('actionCustomerGridQueryBuilderModifier')
            && $this->registerHook('displayAdminOrderLeft')
            && $this->registerHook('displayAdminCustomers')
            && $this->registerHook('actionOrderGridDefinitionModifier')
            && $this->registerHook('actionOrderGridQueryBuilderModifier')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayAdminOrder');
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallDb();
    }

    protected function installDb()
    {
        $sql = [];
        
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer` 
                  ADD `client_code` VARCHAR(50) NULL DEFAULT NULL';
        
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer` 
                  ADD `sales_agent` VARCHAR(255) NULL DEFAULT NULL';

        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer` 
                  ADD UNIQUE KEY `unique_client_code` (`client_code`)';

        foreach ($sql as $query) {
            try {
                Db::getInstance()->execute($query);
            } catch (Exception $e) {
                continue;
            }
        }

        return true;
    }

    protected function uninstallDb()
    {
        $sql = [];
        
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer` DROP INDEX `unique_client_code`';
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer` DROP `client_code`';
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer` DROP `sales_agent`';

        foreach ($sql as $query) {
            try {
                Db::getInstance()->execute($query);
            } catch (Exception $e) {
                continue;
            }
        }

        return true;
    }

    public function hookActionCustomerFormBuilderModifier(array $params)
    {
        $formBuilder = $params['form_builder'];
        $customerId = isset($params['id']) ? (int)$params['id'] : 0;
        
        $clientCode = '';
        $salesAgent = '';
        
        if ($customerId > 0) {
            $clientCode = $this->getClientCode($customerId);
            $salesAgent = $this->getSalesAgent($customerId);
        }
        
        $formBuilder->add('client_code', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->l('Client Code'),
            'required' => false,
            'help' => $this->l('Unique client code (auto-generated if empty)'),
            'data' => $clientCode,
        ]);

        $formBuilder->add('sales_agent', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->l('Sales Agent'),
            'required' => false,
            'data' => $salesAgent,
        ]);
        
        $params['data']['client_code'] = $clientCode;
        $params['data']['sales_agent'] = $salesAgent;
    }

    public function hookActionAfterCreateCustomerFormHandler(array $params)
    {
        $this->saveCustomerData($params);
    }

    public function hookActionAfterUpdateCustomerFormHandler(array $params)
    {
        $this->saveCustomerData($params);
    }

    public function hookActionObjectCustomerAddAfter(array $params)
    {
        $customer = $params['object'];
        
        $currentCode = Db::getInstance()->getValue(
            'SELECT client_code FROM `' . _DB_PREFIX_ . 'customer` 
             WHERE id_customer = ' . (int)$customer->id
        );
        
        if (empty($currentCode)) {
            $clientCode = $this->generateClientCode();
            Db::getInstance()->update('customer', [
                'client_code' => pSQL($clientCode),
            ], 'id_customer = ' . (int)$customer->id);
        }
    }

    protected function saveCustomerData(array $params)
    {
        $customerId = $params['id'];
        $formData = $params['form_data'];

        $clientCode = isset($formData['client_code']) && !empty(trim($formData['client_code']))
            ? trim($formData['client_code'])
            : $this->generateClientCode();

        $salesAgent = isset($formData['sales_agent']) ? trim($formData['sales_agent']) : '';

        // Verificar si el código ya existe para otro cliente
        $existingId = Db::getInstance()->getValue(
            'SELECT id_customer FROM `' . _DB_PREFIX_ . 'customer`
             WHERE client_code = "' . pSQL($clientCode) . '"
             AND id_customer != ' . (int)$customerId
        );

        if ($existingId) {
            // Mostrar advertencia al usuario y generar código automático
            if ($this->context && isset($this->context->controller)) {
                $this->context->controller->warnings[] = sprintf(
                    $this->l('El código "%s" ya está asignado a otro cliente. Se ha generado un código automático.'),
                    $clientCode
                );
            }
            $clientCode = $this->generateClientCode();
        }

        try {
            $result = Db::getInstance()->update('customer', [
                'client_code' => pSQL($clientCode),
                'sales_agent' => pSQL($salesAgent),
            ], 'id_customer = ' . (int)$customerId);

            if (!$result) {
                throw new Exception($this->l('No se pudo actualizar el código de cliente.'));
            }
        } catch (Exception $e) {
            if ($this->context && isset($this->context->controller)) {
                $this->context->controller->errors[] = $this->l('Error al guardar el código de cliente: ') .
                    $this->l('Por favor, verifica que el código no esté duplicado.');
            }
            // Log del error real para debugging
            PrestaShopLogger::addLog(
                'ClientCode Module Error: ' . $e->getMessage(),
                3,
                null,
                'Customer',
                $customerId
            );
        }
    }

    protected function generateClientCode()
    {
        $prefix = 'CLI-';
        
        $sql = 'SELECT client_code FROM `' . _DB_PREFIX_ . 'customer` 
                WHERE client_code LIKE "' . pSQL($prefix) . '%" 
                AND client_code REGEXP "^' . pSQL($prefix) . '[0-9]+$"
                ORDER BY CAST(SUBSTRING(client_code, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC 
                LIMIT 1';
        
        $lastCode = Db::getInstance()->getValue($sql);
        
        if ($lastCode) {
            $number = (int)str_replace($prefix, '', $lastCode);
            $newNumber = $number + 1;
        } else {
            $newNumber = 1;
        }

        $newCode = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        
        $exists = Db::getInstance()->getValue(
            'SELECT id_customer FROM `' . _DB_PREFIX_ . 'customer` 
             WHERE client_code = "' . pSQL($newCode) . '"'
        );
        
        if ($exists) {
            return $this->generateClientCode();
        }
        
        return $newCode;
    }

    protected function getClientCode($customerId)
    {
        $result = Db::getInstance()->getValue(
            'SELECT client_code FROM `' . _DB_PREFIX_ . 'customer` 
             WHERE id_customer = ' . (int)$customerId
        );
        return $result ? $result : '';
    }

    protected function getSalesAgent($customerId)
    {
        $result = Db::getInstance()->getValue(
            'SELECT sales_agent FROM `' . _DB_PREFIX_ . 'customer` 
             WHERE id_customer = ' . (int)$customerId
        );
        return $result ? $result : '';
    }

    public function hookActionCustomerGridDefinitionModifier(array $params)
    {
        $definition = $params['definition'];
        
        $column = new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn('client_code');
        $column->setName($this->l('Client Code'))
            ->setOptions([
                'field' => 'client_code',
            ]);

        $definition->getColumns()->addAfter('id_customer', $column);
        
        $filters = $definition->getFilters();
        $filters->add(
            (new PrestaShop\PrestaShop\Core\Grid\Filter\Filter('client_code', Symfony\Component\Form\Extension\Core\Type\TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->l('Search code'),
                    ],
                ])
                ->setAssociatedColumn('client_code')
        );
    }

    public function hookActionCustomerGridQueryBuilderModifier(array $params)
    {
        $searchQueryBuilder = $params['search_query_builder'];
        $searchQueryBuilder->addSelect('c.client_code');

        // Aplicar filtro de búsqueda si existe
        if (isset($params['filters']['client_code']) && !empty($params['filters']['client_code'])) {
            $searchQueryBuilder->andWhere('c.client_code LIKE :client_code');
            $searchQueryBuilder->setParameter('client_code', '%' . $params['filters']['client_code'] . '%');
        }
    }

    public function hookDisplayAdminOrderLeft($params)
    {
        $orderId = $params['id_order'];
        $order = new Order($orderId);
        $customer = new Customer($order->id_customer);
        
        $clientCode = $this->getClientCode($customer->id);
        $salesAgent = $this->getSalesAgent($customer->id);
        
        $this->context->smarty->assign([
            'client_code' => $clientCode ? $clientCode : $this->l('Not assigned'),
            'sales_agent' => $salesAgent,
            'customer_id' => $customer->id,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/order_detail.tpl');
    }

    public function hookDisplayAdminCustomers($params)
    {
        $customerId = Tools::getValue('id_customer');

        if (!$customerId) {
            return '';
        }

        $clientCode = $this->getClientCode($customerId);
        $salesAgent = $this->getSalesAgent($customerId);

        $this->context->smarty->assign([
            'client_code' => $clientCode,
            'sales_agent' => $salesAgent,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/customer_info.tpl');
    }

    public function hookActionOrderGridDefinitionModifier(array $params)
    {
        $definition = $params['definition'];

        $column = new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn('client_code');
        $column->setName($this->l('Client Code'))
            ->setOptions([
                'field' => 'client_code',
            ]);

        // Añadir después de 'reference' que sí existe en el grid de pedidos
        $definition->getColumns()->addAfter('reference', $column);

        $filters = $definition->getFilters();
        $filters->add(
            (new PrestaShop\PrestaShop\Core\Grid\Filter\Filter('client_code', Symfony\Component\Form\Extension\Core\Type\TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->l('Search code'),
                    ],
                ])
                ->setAssociatedColumn('client_code')
        );
    }

    public function hookActionOrderGridQueryBuilderModifier(array $params)
    {
        $searchQueryBuilder = $params['search_query_builder'];

        $searchQueryBuilder->leftJoin(
            'o',
            _DB_PREFIX_ . 'customer',
            'cust',
            'o.id_customer = cust.id_customer'
        );

        $searchQueryBuilder->addSelect('cust.client_code');

        if (isset($params['filters']['client_code']) && !empty($params['filters']['client_code'])) {
            $searchQueryBuilder->andWhere('cust.client_code LIKE :client_code');
            $searchQueryBuilder->setParameter('client_code', '%' . $params['filters']['client_code'] . '%');
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controller = $this->context->controller;
        $controllerName = get_class($controller);

        // Cargar CSS del módulo
        $this->context->controller->addCSS($this->_path . 'views/css/clientcode.css');

        $output = '';

        // JS para mejorar la visualización en ficha de cliente
        if (strpos($controllerName, 'AdminCustomers') !== false) {
            $output .= '
            <script>
                // Mover el panel de código de cliente a la zona principal de información
                document.addEventListener("DOMContentLoaded", function() {
                    function moveClientCodePanel() {
                        var panels = document.querySelectorAll(".panel");
                        var clientCodePanel = null;

                        // Buscar el panel que contiene "Código de Cliente"
                        panels.forEach(function(panel) {
                            if (panel.textContent.indexOf("Código de Cliente") > -1 ||
                                panel.textContent.indexOf("Información Adicional") > -1) {
                                clientCodePanel = panel;
                            }
                        });

                        if (clientCodePanel) {
                            // Buscar el contenedor principal de información del cliente
                            var mainContainer = document.querySelector("#main-div, .content-div, [role=\\"main\\"]");
                            var firstPanel = mainContainer ? mainContainer.querySelector(".panel") : null;

                            if (firstPanel && firstPanel !== clientCodePanel) {
                                // Insertar antes del primer panel
                                firstPanel.parentNode.insertBefore(clientCodePanel, firstPanel);
                            }
                        }
                    }

                    // Intentar varias veces por si el DOM no está completamente cargado
                    setTimeout(moveClientCodePanel, 300);
                    setTimeout(moveClientCodePanel, 800);
                    setTimeout(moveClientCodePanel, 1500);
                });
            </script>';
        }

        // JS para mover el card en detalle de pedido
        if (strpos($controllerName, 'AdminOrders') !== false) {
            $output .= '
            <script>
                // Mover card de información del cliente en pedido
                document.addEventListener("DOMContentLoaded", function() {
                    function moveOrderClientCard() {
                        var cards = document.querySelectorAll(".card");
                        var clientCard = null;

                        // Buscar el card que contiene "Información del Cliente" o "Código de Cliente"
                        cards.forEach(function(card) {
                            var headerText = card.querySelector(".card-header-title");
                            if (headerText && (
                                headerText.textContent.indexOf("Información del Cliente") > -1 ||
                                headerText.textContent.indexOf("Información Adicional") > -1
                            )) {
                                clientCard = card;
                            }
                        });

                        if (clientCard) {
                            // Buscar el contenedor de la columna izquierda
                            var leftColumn = document.querySelector(".order-view-page .col-lg-6, .order-view-page .column-left");

                            if (leftColumn) {
                                // Insertar como segundo elemento (después del primer card de "Pedido")
                                var firstCard = leftColumn.querySelector(".card");
                                if (firstCard && firstCard !== clientCard) {
                                    firstCard.parentNode.insertBefore(clientCard, firstCard.nextSibling);
                                }
                            }
                        }
                    }

                    setTimeout(moveOrderClientCard, 300);
                    setTimeout(moveOrderClientCard, 800);
                });
            </script>';
        }

        return $output;
    }

    public function hookDisplayAdminOrder($params)
    {
        $orderId = $params['id_order'];
        $order = new Order($orderId);
        $customer = new Customer($order->id_customer);

        $clientCode = $this->getClientCode($customer->id);
        $salesAgent = $this->getSalesAgent($customer->id);

        $this->context->smarty->assign([
            'client_code' => $clientCode ? $clientCode : $this->l('Not assigned'),
            'sales_agent' => $salesAgent,
            'customer_id' => $customer->id,
            'customer_email' => $customer->email,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/order_customer_info.tpl');
    }
}