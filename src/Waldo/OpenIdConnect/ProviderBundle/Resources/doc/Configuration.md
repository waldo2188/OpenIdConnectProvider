
.htaccess
---------

We must add the following code in `.htaccess` file, for handle Authorization bearer
header
```
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```