# SRI Accesskey Generator 🇪🇨

Librería PHP para generar claves de acceso del SRI (Servicio de Rentas Internas) para documentos electrónicos en Ecuador. Implementa el algoritmo oficial con cálculo de dígito verificador módulo 11.

## Instalación

```bash
composer require dazza-dev/sri-accesskey-generator
```

## Ejemplo de uso

```php
use DazzaDev\SriAccesskeyGenerator\AccesskeyGenerator;

$accessKey = AccessKeyGenerator::generate([
    'date' => 'Y-m-d',
    'document_type' => '01',
    'ruc' => 'numero_del_ruc',
    'environment_code' => 1,
    'sequential' => '000000001',
    'establishment_code' => '001',
    'emission_point_code' => '001'
]);
```

## Parámetros

| Parámetro             | Tipo   | Descripción                                                          | Ejemplo           |
| --------------------- | ------ | -------------------------------------------------------------------- | ----------------- |
| `date`                | string | Fecha de emisión del documento en formato Y-m-d                      | `'2024-01-15'`    |
| `document_type`       | string | Código del tipo de documento según SRI (ver tabla de códigos)        | `'01'`            |
| `ruc`                 | string | Número de RUC del emisor (13 dígitos)                                | `'1234567890001'` |
| `environment_code`    | int    | Código del ambiente: 1 = Pruebas, 2 = Producción                     | `1`               |
| `sequential`          | string | Número secuencial del documento (9 dígitos con ceros a la izquierda) | `'000000001'`     |
| `establishment_code`  | string | Código del establecimiento (3 dígitos, opcional, por defecto '001')  | `'001'`           |
| `emission_point_code` | string | Código del punto de emisión (3 dígitos, opcional, por defecto '001') | `'001'`           |

## Codigos de documentos

| Código SRI | Documento (Español)      | Document Type (English) |
| ---------- | ------------------------ | ----------------------- |
| 01         | Factura                  | `invoice`               |
| 04         | Nota de crédito          | `credit-note`           |
| 05         | Nota de débito           | `debit-note`            |
| 06         | Guía de remisión         | `delivery-guide`        |
| 07         | Comprobante de retención | `withholding-receipt`   |

## Contribuciones

Contribuciones son bienvenidas. Si encuentras algún error o tienes ideas para mejoras, por favor abre un issue o envía un pull request. Asegúrate de seguir las guías de contribución.

## Autor

SRI AccessKey Generator fue creado por [DAZZA](https://github.com/dazza-dev).

## Licencia

Este proyecto está licenciado bajo la [Licencia MIT](https://opensource.org/licenses/MIT).
