# Data Access API for PHP

This API, loosely inspired from Java JDBC, encapsulates communication between an SQL server and a PHP script. It runs a layer of abstraction above PDO, which is (like most native OO PHP libraries), disorganized and poorly designed. In adition of enveloping PDO operations into a far more logical layer and solving adjacent flaws, it adds: 

- connection via data source objects (encapsulating connection credentials)
- flexible error management system (different exceptions thrown on connection and query execution errors, filled with all information necessary to tackle the issue later on)
- singleton factories for database connections (in order to give developers an option to use a single connection for same server during a single session)
- a far more logical organization of PDO operations (split into connection, statement, prepared statement and transaction related)
- query results parsers (in order to transform results of SELECT statements into PHP scalars/arrays and also to get number of rows affected / last insert id of INSERT/UPDATE statements)

Everything is built on "less is more" principle: nothing but server related logic is present here! This makes the cost of using it negligible, unlike its much heavier weight "competitors" (generally embedded in some framework).

More information here:<br/>
http://www.lucinda-framework.com/sql-data-access-api
