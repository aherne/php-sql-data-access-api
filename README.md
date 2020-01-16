# SQL Data Access API

This API is a ultra light weight [Data Access Layer](https://en.wikipedia.org/wiki/Data_access_layer) built on top of [PDO](https://www.php.net/manual/en/book.pdo.php) and inspired by [JDBC](https://en.wikipedia.org/wiki/Java_Database_Connectivity) in terms of architecture. As a data access layer, its purpose is to 
to shield complexity of working with different SQL vendors and provide a simple and elegant interface for connecting, querying and parsing query results that overcomes PDO design flaws (such as chaotic architecture and functionality).

The whole idea of working with SQL databases (vendors) is reduced to following steps:

- **[configuration](#configuration)**: setting up an XML file where SQL vendors used by your site are configured per development environment
- **[initialization](#initialization)**: using [Lucinda\SQL\Wrapper](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Wrapper.php) to read above XML based on development environment, compile [Lucinda\SQL\DataSource](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/DataSource.php) object(s) storing connection information and inject them statically into
[Lucinda\SQL\ConnectionSingleton](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionSingleton.php) or [Lucinda\SQL\ConnectionFactory](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionFactory.php) classes
- **[connection](#connection)**: using the two classes above to connect to database(s) via [Lucinda\SQL\Connection](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Connection.php) object(s)
- **[querying](#querying)**: using object created above to query database via [Lucinda\SQL\Statement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Statement.php) or [Lucinda\SQL\PreparedStatement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/PreparedStatement.php) objects, able to be wrapped with [Lucinda\SQL\Transaction](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Transaction.php) operations to insure data consistency
- **[processing](#processing)**: using [Lucinda\SQL\StatementResults](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/StatementResults.php) object that comes from query execution to process resultsets, affected rows or last insert id

API is fully PSR-4 compliant, only requiring PHP7.1+ interpreter and SimpleXML extension. To quickly see how it works, check:

- **[installation](#installation)**: describes how to install API on your computer, in light of steps above
- **[unit tests](#unit-tests)**: API has 100% Unit Test coverage, using [UnitTest API](https://github.com/aherne/unit-testing) instead of PHPUnit for greater flexibility
- **[examples](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/tests/)**: shows a deep example of API functionality based on unit tests

## Configuration

To configure this API you must have a XML with a **sql** tag inside:

```xml
<sql>
	<{ENVIRONMENT}>
		<server name="..." driver="..." host="..." port="..." username="..." password="..." schema="..." charset="..."/>
		...
	</{ENVIRONMENT}>
	...
</sql>
```

Where:

- **sql**: holds global connection information for SQL servers used
    - {ENVIRONMENT}: name of development environment (to be replaced with "local", "dev", "live", etc)
        - **server**: stores connection information about a single server via attributes:
            - *name*: (optional) unique sql server identifier. Required if multiple sql servers are used for same environment!
            - *driver*: (mandatory) PDO driver name (pdo drivers)
            - *host*: (mandatory) server host name.
            - *port*: (optional) server port. If not set, default server port is used.
            - *username*: (mandatory) user name to use in connection.
            - *password*: (mandatory) password to use in connection.
            - *schema*: (optional) default schema to use after connecting.
            - *charset*: (optional) default charset to use in queries after connecting.

Example:

```xml
<sql>
    <local>
        <server driver="mysql" host="localhost" port="3306" username="root" password="" schema="example" charset="utf8"/>
    </local>
    <live>
        <server driver="mysql" host="localhost" port="3306" username="hello" password="world" schema="example" charset="utf8"/>
    </live>
</sql>
```

## Initialization

Once you have completed step above, you need to run this in order to be able to connect and query database(s) later on:

```php
new Lucinda\SQL\Wrapper(simplexml_load_file(XML_FILE_NAME), DEVELOPMENT_ENVIRONMENT);
```

This will wrap each **server** tag found for current development environment into [Lucinda\SQL\DataSource](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/DataSource.php) objects and inject them statically into:

- [Lucinda\SQL\ConnectionSingleton](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionSingleton.php): if your application uses a single SQL server per environment (the usual case)
- [Lucinda\SQL\ConnectionFactory](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionFactory.php): if your application uses multiple SQL servers per environment (in which case **server** tags must have *name* attribute)

Both classes above insure a single [Lucinda\SQL\Connection](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Connection.php) is reused per server throughout session (input-output request flow) duration.

[Lucinda\SQL\ConnectionSingleton](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionSingleton.php) defines following public methods:


| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| static setDataSource | [Lucinda\SQL\DataSource](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/DataSource.php) | void | Sets data source detected beforehand. Done automatically by API! |
| static getInstance | void | [Lucinda\SQL\Connection](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Connection.php) | Gets single connection to query later on. |
| __destruct | void | void | Automatically closes connection when it becomes idle. Done automatically by API! |

To get a connection using above simply run:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getConnection();
// now query
```

[Lucinda\SQL\ConnectionFactory](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionFactory.php) defines following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| static setDataSource | string $serverName, [Lucinda\SQL\DataSource](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/DataSource.php) | void | Sets data source detected beforehand per value of *name* attribute @ **server** tag. Done automatically by API! |
| static getInstance | string $serverName | [Lucinda\SQL\Connection](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Connection.php) | Gets single connection per value of *name* attribute @ **server** tag  to query later on. |
| __destruct | void | void | Automatically closes each connection when it becomes idle. Done automatically by API! |

To get a connection using above simply run:

```php
$connection = Lucinda\SQL\ConnectionFactory::getConnection(SERVER_NAME);
// now query
```

## Connection

Now that a [Lucinda\SQL\Connection](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Connection.php) is retrieved, you are able to query database via following public methods:

Following methods are relevant to connection management:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| connect | void | --- | --- |
| reconnect | void | --- | --- |
| keepAlive | void | --- | --- |
| disconnect | void | --- | --- |

Following methods are relevant for configuring connection:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| setAutoCommit | bool $value | void | Sets whether or not statements executed on server are commited by default. |
| getAutoCommit | void | bool $value | Gets whether or not statements executed on server are commited by default. |
| setConnectionTimeout | int $value | void | Sets connection timeout on database server. |
| getConnectionTimeout | void | int $value | Gets connection timeout on database server. |
| setPersistent | bool $value | void | Sets whether or not current connection is persistent. |
| getPersistent | void | bool $value | Sets whether or not current connection is persistent. |

Following methods are relevant to querying:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| statement | void | [Lucinda\SQL\Statement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Statement.php) | Creates a statement to use in querying. |
| preparedStatement | void | [Lucinda\SQL\PreparedStatement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/PreparedStatement.php) | Creates a prepared statement to use in querying. |
| transaction | void | [Lucinda\SQL\Transaction](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Transaction.php) | Creates a transaction wrap above operations with. |
