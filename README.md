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

### ConnectionSingleton

[Lucinda\SQL\ConnectionSingleton](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionSingleton.php) defines following public methods:


| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| static setDataSource | [Lucinda\SQL\DataSource](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/DataSource.php) | void | Sets data source detected beforehand. Done automatically by API! |
| static getInstance | void | [Lucinda\SQL\Connection](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Connection.php) | Connects to server based on above data source ONCE and returns connection for later querying. Throws [Lucinda\SQL\ConnectionException](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionException.php) if connection fails! |
| __destruct | void | void | Automatically closes connection when it becomes idle. Done automatically by API! |

Usage example:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getInstance();
$connection->statement()->execute("UPDATE users SET name='John' WHERE name='Jane'");
```

### ConnectionFactory

[Lucinda\SQL\ConnectionFactory](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionFactory.php) defines following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| static setDataSource | string $serverName, [Lucinda\SQL\DataSource](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/DataSource.php) | void | Sets data source detected beforehand per value of *name* attribute @ **server** tag. Done automatically by API! |
| static getInstance | string $serverName | [Lucinda\SQL\Connection](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Connection.php) | Connects to server based on above data source ONCE and returns connection for later querying. Throws [Lucinda\SQL\ConnectionException](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionException.php) if connection fails! |
| __destruct | void | void | Automatically closes each connection when it becomes idle. Done automatically by API! |

Usage example:

```php
$connection = Lucinda\SQL\ConnectionFactory::getInstance("myServer");
$conection->statement()->execute("UPDATE users SET name='John' WHERE name='Jane'");
```

## Connection

Now that a [Lucinda\SQL\Connection](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Connection.php) is retrieved, you are able to query database via following public methods:

Following methods are relevant to connection management:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| connect | [Lucinda\SQL\DataSource](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/DataSource.php) | void | Connects to database server based on data source. Throws [Lucinda\SQL\ConnectionException](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionException.php) if connection fails! |
| disconnect | void | void | Closes connection to database server. |
| reconnect | void | void | Closes then opens connection to database server based on stored data source. Throws [Lucinda\SQL\ConnectionException](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionException.php) if connection fails! |
| keepAlive | void | void | Restores connection to database server in case it got closed unexpectedly. Throws [Lucinda\SQL\ConnectionException](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionException.php) if connection fails! |

Following methods are relevant for configuring connection:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| setAutoCommit | bool $value | void | Sets whether or not statements executed on server are commited by default. |
| getAutoCommit | void | bool $value | Gets whether or not statements executed on server are commited by default. |
| setConnectionTimeout | int $value | void | Sets connection timeout on database server. |
| getConnectionTimeout | void | int $value | Gets connection timeout on database server. |
| setPersistent | bool $value | void | Sets whether or not current connection is persistent. |
| getPersistent | void | bool $value | Sets whether or not current connection is persistent. |

Following methods are relevant for querying:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| statement | void | [Lucinda\SQL\Statement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Statement.php) | Creates a statement to use in querying. |
| preparedStatement | void | [Lucinda\SQL\PreparedStatement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/PreparedStatement.php) | Creates a prepared statement to use in querying. |
| transaction | void | [Lucinda\SQL\Transaction](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Transaction.php) | Creates a transaction wrap above operations with. |

## Querying

Using [Lucinda\SQL\Statement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Statement.php), [Lucinda\SQL\PreparedStatement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/PreparedStatement.php) and [Lucinda\SQL\Transaction](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Transaction.php) objects returned by *statement*, *preparedStatement* and *transaction* methods of [Lucinda\SQL\Connection](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Connection.php) object, users are finally able to execute queries, wrapping them in transactions if needed, then process execution results using [Lucinda\SQL\StatementResults](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/StatementResults.php).

### Statement

[Lucinda\SQL\Statement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Statement.php) runs normal SQL unprepared statements and comes with following public methods:


| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| quote | mixed $value | void | Escapes and quotes value against SQL injection. |
| execute | string $query | [Lucinda\SQL\StatementResults](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/StatementResults.php) | Executes query and returns results. Throws [Lucinda\SQL\StatementException](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/StatementException.php) if execution fails! |

Usage example:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getInstance();
$statement = $connection->statement();
$resultSet = $statement->execute("SELECT id FROM users WHERE name='".$statement->quote($name)."'");
```

### PreparedStatement

[Lucinda\SQL\PreparedStatement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/PreparedStatement.php) runs SQL prepared statements and comes with following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| prepare | string $query | void |  Prepares query for execution. |
| bind | string $parameter, mixed $value, int $dataType=\PDO::PARAM_STR | void | Binds parameter to prepared query. |
| execute | array $boundParameters = array()  | [Lucinda\SQL\StatementResults](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/StatementResults.php) | Executes query and returns results. Throws [Lucinda\SQL\StatementException](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/StatementException.php) if execution fails! |

Usage example:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getInstance();
$preparedStatement = $connection->preparedStatement();
$preparedStatement->prepare("SELECT id FROM users WHERE name=:name");
$preparedStatement->bind(":name", $name);
$resultSet = $preparedStatement->execute();
```

### Transaction

[Lucinda\SQL\Transaction](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Transaction.php) can wrap *execute* methods above in transactions, in order to maintain data integrity, and thus comes with following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| begin | void | void | Starts a transaction. |
| commit | void | void | Commits transaction. |
| rollback | void | void | Rolls back transaction. |

Usage example:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getInstance();
$transaction = $connection->transaction();
$transaction->begin();
$connection->statement()->execute("UPDATE users SET name='John Doe' WHERE id=1");
$transaction->commit();
```

## Processing

Once an SQL statement was executed via *execute* methods above, users are able to process results based on [Lucinda\SQL\StatementResults](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/StatementResults.php) object returned. This class comes with following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getInsertId | void | int | Gets last insert id following INSERT statement execution. |
| getAffectedRows | void | int | Gets affected rows following UPDATE/DELETE statement execution. |
| toValue | void | string | Gets value of first column & row in resultset following SELECT statement execution. |
| toRow | void | array | Gets current row from resultset as column-value associative array following SELECT statement execution. |
| toColumn | void | array | Gets first column in resulting rows following SELECT statement execution. |
| toMap | string $columnKeyName, string $columnValueName | array | Gets two columns from resulting rows, where value of one becomes key and another as value, following SELECT statement execution. |
| toList | string $columnKeyName, string $columnValueName | array | Gets all resulting rows, each as column-value associative array, following SELECT statement execution. |

### INSERT

Example of processing results of an INSERT query:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getInstance();
$resultSet = $connection->statement("INSERT INTO users (first_name, last_name) VALUES ('John', 'Doe')");
$lastInsertID = $resultSet->getInsertId();
```

### UPDATE/DELETE

Example of processing results of an UPDATE/DELETE query:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getInstance();
$resultSet = $connection->statement("UPDATE users SET first_name='Jane' WHERE id=1");
if($resultSet->getAffectedRows()>0) {
    // update occurred
}
```

### SELECT

Example of getting a single value from SELECT resultset:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getInstance();
$firstName = $connection->statement("SELECT first_name FROM users WHERE id=1")->toValue();
```

Example of parsing SELECT resultset row by row:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getInstance();
$resultSet = $connection->statement("SELECT * FROM users");
while ($row = $resultSet->toRow()) {
    // process row
}
```

Example of getting all values of first column from SELECT resultset:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getInstance();
$ids = $connection->statement("SELECT id FROM users")->toColumn();
```

Example of getting all rows from SELECT resultset as array where value of first becomes key and value of second becomes value:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getInstance();
$users = $connection->statement("SELECT id, name FROM users")->toMap("id", "name");
// above is an array where id of user becomes key and name becomes value
```

Example of getting all values from SELECT resultset:

```php
$connection = Lucinda\SQL\ConnectionSingleton::getInstance();
$users = $connection->statement("SELECT * FROM users")->toList();
// above is an array containing all rows, each as column-value associative array
```
