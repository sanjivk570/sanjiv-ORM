Mysqli Database Class
============================

A database class which uses the Mysqli extension.
 => Allows one connection with the database and deny duplicate connection, 
 => this speeds up to use the database and reduces the load on the server.
 => supports "mysql" drivers

--------------------
# to use config :

## (config) :
  => go to (config.php) file
  => Set all Database informations

------------------
# how to use :
### step 1 : 
 -Include the class in your project
 
```php
    <?php
    require_once 'config.php';
    require_once 'Libraries/Logger.php';
    require_once 'Libraries/Database/DatabaseInterface.php';
    require_once 'Libraries/Database/Database.php';
```
### step 2 :
- Create the instance 
```php
    $object = new Database\Database;
```

# how it work (methods):



#### 1.0.1
* FIX first method -> to compatible with PHP V +5.6.0

#### 1.0.0
* First Release


=============================
# License
### Sanjiv



