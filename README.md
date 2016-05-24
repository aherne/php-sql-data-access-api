# Data Access API for PHP

This API implements operations that can be performed on an SQL server from a PHP script. It runs a layer of abstraction above PDO, which is (like most OO PHP libraries), disorganized and poorly designed.

On a closer inspection of PDO class, we notice that methods inside are in fact clearly divided into four types:

- connection related
- transaction related
- statement execution related
- error related

While having a look at PDOStatement class, the same pattern is observed. Methods are now clearly divided into four groups:

- attribute related
- error related
- statement execution related
- statement results related

This current library fixes these problems with a simple light weight architecture built on top of PDO chaos providing: 

- connection via data source objects
- flexible error management system
- singleton factories for database connections
- a far more logical organization of operations implemented by PDO
- resultset parsers

This API is <i>conceptual</i> part of SQL Suites, an integrated solution designed to cover all aspects of communication with SQL servers. To see the full documentation, please visit to SQL suites docs :

https://docs.google.com/document/d/1U5PtPyub4t273gB9gXoZTX7TQasjP6lj93kMcClovS4/edit# 