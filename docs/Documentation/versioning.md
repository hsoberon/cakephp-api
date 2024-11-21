# Versioning


The cakephp-api supports versioning which allows for better management of features or plugins compatibility.

Cakephp-api use URL versioning. With a default semantic style like v1, v2, v3

**With versioning:** _www.example.com/api/**v2**/users_

**Without versioning:** *www.example.com/api/users*



--

### Enable versioning:

To enable it, you just need to turn true the configuration in the `config/api.php` 

```'useVersioning' => true,```

### Testing

If you want to use versioingn on testing. Enable it in the  `test/Config/api.php`

```'useVersioning' => true,```




