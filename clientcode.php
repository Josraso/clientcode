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
        $this->version = '1.0.1';
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
            && $this->registerHook('displayAdminCustomers');
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
            $clientCode = $this->generateClientCode();
        }

        Db::getInstance()->update('customer', [
            'client_code' => pSQL($clientCode),
            'sales_agent' => pSQL($salesAgent),
        ], 'id_customer = ' . (int)$customerId);
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
}