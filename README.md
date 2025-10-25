# Client Code - Módulo PrestaShop

**Versión:** 1.0.1
**Autor:** Tu Nombre
**Compatibilidad:** PrestaShop 1.7.0 - 9.99.99

## Descripción

Módulo para PrestaShop que añade dos campos adicionales a los clientes:
- **Código de Cliente**: Campo de texto único autogenerado (formato: CLI-0001, CLI-0002, etc.)
- **Comercial**: Campo de texto libre para asignar un comercial/vendedor al cliente

## Características Principales

### ✅ Gestión de Códigos de Cliente

- **Generación automática**: Si no se especifica un código, se genera automáticamente con formato CLI-XXXX
- **Edición manual**: Los códigos pueden editarse manualmente desde el formulario del cliente
- **Unicidad**: Cada código es único en la base de datos
- **Formato libre**: Aunque la autogeneración usa el formato CLI-XXXX, puedes usar cualquier formato manualmente

### ✅ Visualización Integrada

#### En Ficha de Cliente:
- Panel destacado con el código del cliente en la zona de información principal
- Se muestra junto a email, sexo, fecha de registro, etc.
- Visible al editar o ver un cliente

#### En Listado de Clientes:
- Columna "Código de Cliente" visible en el grid
- Filtro de búsqueda por código
- Ordenable y filtrable

#### En Pedidos:
- **Listado de pedidos**: Columna con el código del cliente asociado
- **Detalle de pedido**: Card con información del cliente incluyendo:
  - Código de cliente
  - Comercial asignado (si existe)
  - Enlace directo a la ficha del cliente

### ✅ Búsqueda Mejorada

- **Búsqueda en grid de clientes**: Filtro específico por código
- **Búsqueda en grid de pedidos**: Filtro específico por código de cliente
- **Búsqueda global**: El buscador del backoffice incluye sugerencias para buscar por código

## Instalación

1. Sube el módulo a la carpeta `/modules/clientcode/`
2. Accede al backoffice de PrestaShop
3. Ve a **Módulos > Module Manager**
4. Busca "Client Code"
5. Haz clic en **Instalar**

## Base de Datos

El módulo añade automáticamente los siguientes campos a la tabla `ps_customer`:

```sql
ALTER TABLE `ps_customer` ADD `client_code` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `ps_customer` ADD `sales_agent` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `ps_customer` ADD UNIQUE KEY `unique_client_code` (`client_code`);
```

## Uso

### Crear un nuevo cliente

1. Ve a **Clientes > Clientes > Añadir nuevo**
2. Rellena los campos habituales
3. En el formulario verás dos nuevos campos:
   - **Código de Cliente**: Déjalo vacío para autogenerar o introduce uno manualmente
   - **Comercial**: Introduce el nombre del comercial asignado (opcional)
4. Guarda el cliente

### Editar un cliente existente

1. Ve a **Clientes > Clientes**
2. Edita un cliente
3. Modifica el código o comercial según necesites
4. Guarda los cambios

### Buscar por código

#### En listado de clientes:
1. Ve a **Clientes > Clientes**
2. Usa el filtro "Código de Cliente" en la cabecera de la columna

#### En listado de pedidos:
1. Ve a **Pedidos > Pedidos**
2. Usa el filtro "Código de Cliente" para encontrar pedidos de un cliente específico

### Ver código en pedidos

1. Ve a **Pedidos > Pedidos**
2. Abre cualquier pedido
3. Verás un card con "Información Adicional del Cliente" mostrando:
   - El código del cliente
   - El comercial asignado (si existe)
   - Un botón para ir a la ficha del cliente

## Estructura del Módulo

```
clientcode/
├── clientcode.php                          # Archivo principal del módulo
├── README.md                               # Este archivo
└── views/
    ├── css/
    │   └── clientcode.css                 # Estilos personalizados
    └── templates/
        └── admin/
            ├── customer_info.tpl          # Vista para ficha de cliente
            ├── customer_fields.tpl        # Vista para formulario (PS 1.7.0-1.7.6)
            ├── order_detail.tpl           # Vista lateral en detalle de pedido
            └── order_customer_info.tpl    # Vista integrada en detalle de pedido
```

## Hooks Utilizados

| Hook | Descripción |
|------|-------------|
| `actionCustomerFormBuilderModifier` | Añade campos al formulario de cliente |
| `actionAfterCreateCustomerFormHandler` | Guarda datos al crear cliente |
| `actionAfterUpdateCustomerFormHandler` | Guarda datos al actualizar cliente |
| `actionObjectCustomerAddAfter` | Genera código automático si está vacío |
| `actionCustomerGridDefinitionModifier` | Añade columna en grid de clientes |
| `actionCustomerGridQueryBuilderModifier` | Modifica query para incluir código en grid de clientes |
| `actionOrderGridDefinitionModifier` | Añade columna en grid de pedidos |
| `actionOrderGridQueryBuilderModifier` | Modifica query para incluir código en grid de pedidos |
| `displayAdminOrderLeft` | Muestra info en panel lateral de pedido |
| `displayAdminOrder` | Muestra info integrada en vista de pedido |
| `displayAdminCustomers` | Muestra info en ficha de cliente |
| `displayBackOfficeHeader` | Carga CSS y JS personalizado |

## Desinstalación

Al desinstalar el módulo:
- Se eliminan los campos `client_code` y `sales_agent` de la tabla `ps_customer`
- Se elimina el índice único `unique_client_code`
- **ADVERTENCIA**: Se perderán todos los códigos y comerciales asignados

## Notas Técnicas

### Generación de Códigos

- Formato: `CLI-XXXX` (donde XXXX es un número secuencial de 4 dígitos)
- Ejemplo: CLI-0001, CLI-0002, ..., CLI-9999
- Si se alcanza CLI-9999, continúa con CLI-10000, CLI-10001, etc.

### Validación

- No hay validación de formato para códigos editados manualmente
- Solo se valida la unicidad del código
- Si introduces un código duplicado, se generará uno automático

### Compatibilidad

- Compatible con PrestaShop 1.7.x, 1.8.x y versiones superiores
- Usa Symfony Form Builder (recomendado para PS 1.7.7+)
- Incluye templates alternativos para versiones 1.7.0-1.7.6

## Soporte y Mejoras Futuras

### Posibles mejoras:
- Exportación de códigos de clientes
- Configuración del formato de código desde backoffice
- Integración con módulos de facturación
- API para sincronización con sistemas externos

## Changelog

### v1.0.1 (2025-10-25)
- Corrección de codificación UTF-8 en templates
- Añadida columna de código en listado de pedidos
- Mejorada visualización en detalle de pedido
- Añadido archivo CSS para estilos personalizados
- Mejora de búsqueda global por código
- Eliminado archivo generate_codes.php no utilizado

### v1.0.0 (2025)
- Versión inicial
- Campos de código de cliente y comercial
- Generación automática de códigos
- Visualización en listados y fichas

## Licencia

MIT License
