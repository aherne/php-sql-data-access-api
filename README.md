# SQL Data Access API

This API is a ultra light weight [Data Access Layer](https://en.wikipedia.org/wiki/Data_access_layer) built on top of [PDO](https://www.php.net/manual/en/book.pdo.php) and inspired by [JDBC](https://en.wikipedia.org/wiki/Java_Database_Connectivity) in terms of architecture. As a data access layer, its purpose is to 
to shield complexity of working with different SQL vendors and provide a simple and elegant interface that also overcomes PDO design flaws (chaotic architecture and functionality).

The whole idea of working with SQL databases (vendors) is reduced to following steps:

- **[configuration](#configuration)**: setting up an XML file where SQL vendors used by your site are configured per development environment
- **[data source detection](#data-source-detection)**: encapsulating information about SQL vendors for current development environment from above XML into [Lucinda\SQL\DataSource](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/DataSource.php) object(s)
- **[connection](#connection)**: usage of object(s) detected above to connect to database(s) via [Lucinda\SQL\Connection](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Connection.php) object(s)
   - Insuring a single such object is used per session (to prevent connecting on every query) via [Lucinda\SQL\ConnectionSingleton](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionSingleton.php) or per server and session via [Lucinda\SQL\ConnectionFactory](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/ConnectionFactory.php)
- **[querying](#querying)**: usage of connection object(s) created above to query database via [Lucinda\SQL\Statement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Statement.php) or [Lucinda\SQL\PreparedStatement](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/PreparedStatement.php)
   - Ability to wrap operations with above with [Lucinda\SQL\Transaction](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/Transaction.php) to insure data consistency is maintained
- **[processing](#processing)**: processing execution results of statement objects above with   [Lucinda\SQL\StatementResults](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/src/StatementResults.php)

API is fully PSR-4 compliant, only requiring PHP7.1+ interpreter and SimpleXML extension. To quickly see how it works, check:

- **[installation](#installation)**: describes how to install API on your computer, in light of steps above
- **[unit tests](#unit-tests)**: API has 100% Unit Test coverage, using [UnitTest API](https://github.com/aherne/unit-testing) instead of PHPUnit for greater flexibility
- **[examples](https://github.com/aherne/php-sql-data-access-api/blob/v3.0.0/tests/)**: shows a deep example of API functionality based on unit tests