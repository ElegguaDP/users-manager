Yii 2 User's manager
============================

REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 5.4.0.


INSTALLATION
------------

Just ```git clone ...``` project and use included MySQL DB `users-manager.sql`
Then ```composer update``` to install the dependencies

CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

### Custom params

Edit the file `config/params.php` with real data, for example:

```php
return [
    'adminEmail' => 'admin@example.com',
    'rememberMe' => true,
    'linkLenght' => 14,
    'hashLifeTime' => 1,
    'uploadPath' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR
]; 
```

# users-manager
