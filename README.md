# SQL Data Access API

Table of contents:

- [About](#about)
- [Configuration](#configuration)
- [Execution](#execution)
- [Installation](#installation)
- [Unit Tests](#unit-tests)
- [Examples](#examples)
- [Reference Guide](#reference-guide)

## About

This API is an ultra lightweight [Data Access Layer](https://en.wikipedia.org/wiki/Data_access_layer) built on top of [PDO](https://www.php.net/manual/en/book.pdo.php) and inspired by [JDBC](https://en.wikipedia.org/wiki/Java_Database_Connectivity). Its goal is to shield applications from PDO's uneven low-level API and offer a small, predictable interface for:

- configuring SQL data sources
- opening and reusing connections
- executing regular and prepared statements
- handling transactions
- reading statement results through common result shapes

![diagram](https://www.lucinda-framework.com/sql-data-access-api.svg)

The API is fully PSR-4 compliant and requires PHP 8.1+, SimpleXML, and PDO.

## Configuration

Configuration is XML based. A **sql** tag contains one or more **server** child tags, as this example shows:

```xml
<sql>
    <server name="main" driver="mysql" host="localhost" port="3306" username="hello" password="world" schema="example" charset="utf8">
</sql>
```

Attributes supported by **server**:

| Attribute | Required | Description |
| --- | --- | --- |
| name | Only when multiple servers exist in the same environment | Unique identifier used by `ConnectionFactory::getInstance`. If omitted for a single server, use `""` as server name. |
| driver | Yes | PDO driver name, such as `mysql`, `pgsql`, or `sqlite`. |
| host | Yes | Database server host name. |
| port | No | Database server port. If omitted, the PDO driver default is used. |
| username | Yes | User name used by PDO when opening the connection. |
| password | No | Password used by PDO. Empty passwords are accepted. |
| schema | No | Database name sent as `dbname` in the generated PDO DSN. |
| charset | No | Charset sent in the generated PDO DSN. |
| file | Required for `sqlite` | SQLite database file path, or `:memory:` for an in-memory database. |
| autocommit | No | Maps to `PDO::ATTR_AUTOCOMMIT`. Value should be `0` or `1`. Not supported by all drivers. |
| persistent | No | Maps to `PDO::ATTR_PERSISTENT`. Value should be `0` or `1`. Not supported by all drivers. |
| timeout | No | Maps to `PDO::ATTR_TIMEOUT`. Not supported by all drivers. |

`PDO::ATTR_ERRMODE` is always set to `PDO::ERRMODE_EXCEPTION` by default, so SQL and connection errors can be translated into this API's exceptions.

### DSN Notes

For MySQL and PostgreSQL, the API builds a PDO DSN from `driver`, `host`, `port`, `schema`, and `charset`.

Examples:

```text
mysql:host=localhost;port=3306;dbname=example;charset=utf8
pgsql:host=localhost;port=5432;dbname=example
```

For SQLite, provide `driver="sqlite"` and `file="..."`:

```xml
<server driver="sqlite" host="localhost" username="sqlite" password="" file=":memory:"/>
```

## Execution

Once configuration is ready, bootstrap the wrapper:

```php
require __DIR__."/vendor/autoload.php";

new Lucinda\SQL\Wrapper(simplexml_load_file("configuration.xml"));
```

This reads the **server** tags, converts them into `Lucinda\SQL\DataSource` objects, and registers them into `Lucinda\SQL\ConnectionFactory`.

Then retrieve a connection:

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("main");
```

If your environment has one unnamed server:

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
```

Available operations on a connection:

- `statement()` creates a `Lucinda\SQL\Statement`.
- `preparedStatement()` creates a `Lucinda\SQL\PreparedStatement`.
- `transaction()` creates a `Lucinda\SQL\Transaction`.
- `keepAlive()` checks the current connection with `SELECT 1`.
- `reconnect()` closes and opens the connection again using the stored datasource.
- `disconnect()` closes the PDO connection.

Calling `statement()`, `preparedStatement()`, or `transaction()` after `disconnect()` throws `Lucinda\SQL\ConnectionException`.

## Installation

Install through Composer:

```console
composer require lucinda/sql-data-access
```

Create a configuration file and bootstrap it:

```php
require __DIR__."/vendor/autoload.php";

new Lucinda\SQL\Wrapper(simplexml_load_file("configuration.xml"));

$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
$users = $connection->statement()
    ->execute("SELECT id, first_name FROM users")
    ->toMap("id", "first_name");
```

## Unit Tests

This package uses [lucinda/unit-testing](https://github.com/aherne/unit-testing).

Relevant files:

- [unit-tests.sql](https://github.com/aherne/php-sql-data-access-api/blob/master/unit-tests.sql): SQL setup required before running the tests.
- [unit-tests.xml](https://github.com/aherne/php-sql-data-access-api/blob/master/unit-tests.xml): test runner and SQL test datasource configuration.
- [test.php](https://github.com/aherne/php-sql-data-access-api/blob/master/test.php): console test runner.
- [tests](https://github.com/aherne/php-sql-data-access-api/tree/master/tests): unit tests for classes in `src`.
- `TESTING_GUIDELINES.md`: local conventions for writing tests with `Lucinda\UnitTest\Validator`.

Run tests:

```console
php test.php
```

Before running the suite locally, import `unit-tests.sql` into your SQL server and make sure credentials in `unit-tests.xml` match your local setup.

## Examples

### INSERT

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
$resultSet = $connection->statement()
    ->execute("INSERT INTO users (first_name, last_name) VALUES ('John', 'Doe')");

$lastInsertID = $resultSet->getInsertId();
```

### UPDATE/DELETE

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
$resultSet = $connection->statement()
    ->execute("UPDATE users SET first_name='Jane' WHERE id=1");

if ($resultSet->getAffectedRows() > 0) {
    // update occurred
}
```

### SELECT

Single value:

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
$firstName = $connection->statement()
    ->execute("SELECT first_name FROM users WHERE id=1")
    ->toValue();
```

Rows one by one:

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
$resultSet = $connection->statement()->execute("SELECT * FROM users");

while ($row = $resultSet->toRow()) {
    // process row
}
```

First column:

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
$ids = $connection->statement()
    ->execute("SELECT id FROM users")
    ->toColumn();
```

Map:

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
$users = $connection->statement()
    ->execute("SELECT id, first_name FROM users")
    ->toMap("id", "first_name");
```

List:

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
$users = $connection->statement()
    ->execute("SELECT * FROM users")
    ->toList();
```

### Prepared Statement

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
$preparedStatement = $connection->preparedStatement();
$preparedStatement->prepare("SELECT id FROM users WHERE first_name=:first_name");
$preparedStatement->bind(":first_name", "John");

$id = $preparedStatement->execute()->toValue();
```

Or pass bound parameters directly to `execute`:

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
$preparedStatement = $connection->preparedStatement();
$preparedStatement->prepare("SELECT id FROM users WHERE first_name=:first_name");

$id = $preparedStatement->execute([":first_name"=>"John"])->toValue();
```

### Transaction

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("");
$transaction = $connection->transaction();

$transaction->begin();

try {
    $connection->statement()->execute("UPDATE users SET first_name='John' WHERE id=1");
    $transaction->commit();
} catch (Throwable $e) {
    if ($transaction->isActive()) {
        $transaction->rollback();
    }
    throw $e;
}
```

## Reference Guide

### Class Connection

`Lucinda\SQL\Connection` represents a PDO-backed database connection.

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| connect | `Lucinda\SQL\DataSource $dataSource` | void | Opens a PDO connection. Throws `Lucinda\SQL\ConnectionException` if connection fails. |
| disconnect | void | void | Closes the PDO connection. |
| reconnect | void | void | Closes then opens the connection again using the stored datasource. |
| keepAlive | void | void | Runs `SELECT 1`; if the statement fails, reconnects using the stored datasource. |
| statement | void | `Lucinda\SQL\Statement` | Creates a regular SQL statement helper. |
| preparedStatement | void | `Lucinda\SQL\PreparedStatement` | Creates a prepared statement helper. |
| transaction | void | `Lucinda\SQL\Transaction` | Creates a transaction helper. |

### Class ConnectionFactory

`Lucinda\SQL\ConnectionFactory` stores datasources and reuses one connection per server name.

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| static setDataSource | `string $serverName, Lucinda\SQL\DataSource $dataSource` | void | Registers a datasource. Normally called by `Wrapper`. |
| static getInstance | `string $serverName` | `Lucinda\SQL\Connection` | Returns the existing connection or opens it once. |

### Class DataSource

`Lucinda\SQL\DataSource` reads one XML **server** tag and exposes connection details.

| Method | Returns |
| --- | --- |
| getDriverName | string |
| getDriverOptions | array |
| getHost | string |
| getPort | int |
| getUserName | string |
| getPassword | string |
| getSchema | string |
| getCharset | string |
| getFile | string |

### Class Statement

`Lucinda\SQL\Statement` executes regular SQL statements.

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| quote | `string $value` | string | Quotes a value using PDO. |
| execute | `string $query` | `Lucinda\SQL\StatementResults` | Executes a query. Throws `Lucinda\SQL\StatementException` if execution fails. |

### Class PreparedStatement

`Lucinda\SQL\PreparedStatement` wraps PDO prepared statements.

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| prepare | `string $query` | void | Prepares a query. Throws `Lucinda\SQL\StatementException` if preparation fails. |
| bind | `string $parameter, string|int $value, int $dataType=PDO::PARAM_STR` | void | Binds one value to a prepared query. |
| execute | `array $boundParameters=[]` | `Lucinda\SQL\StatementResults` | Executes the prepared query. Throws `Lucinda\SQL\StatementException` if execution fails. |

### Class Transaction

`Lucinda\SQL\Transaction` wraps PDO transaction operations.

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| begin | void | void | Starts a transaction. |
| commit | void | void | Commits the active transaction. |
| rollback | void | void | Rolls back the active transaction. |
| isActive | void | bool | Checks whether PDO is currently inside a transaction. |

### Class StatementResults

`Lucinda\SQL\StatementResults` exposes common ways to read statement output.

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getInsertId | void | int | Returns the last inserted id. |
| getAffectedRows | void | int | Returns affected rows from INSERT/UPDATE/DELETE statements. |
| toValue | void | string | Returns first column of first row, or an empty string when none exists. |
| toRow | void | array | Returns next row as an associative array, or an empty array when none exists. |
| toColumn | void | array | Returns the first column of all remaining rows. |
| toMap | `string $columnKeyName, string $columnValueName` | array | Returns rows as key/value pairs using two named columns. |
| toList | void | array | Returns all remaining rows as associative arrays. |

### Exceptions

| Class | Description |
| --- | --- |
| `Lucinda\SQL\ConfigurationException` | Thrown when XML configuration is invalid. |
| `Lucinda\SQL\ConnectionException` | Thrown when opening or using a connection fails. Stores the related host name when available. |
| `Lucinda\SQL\StatementException` | Thrown when preparing or executing SQL fails. Stores the related SQL query. |
| `Lucinda\SQL\Exception` | Thrown for API usage errors. |
