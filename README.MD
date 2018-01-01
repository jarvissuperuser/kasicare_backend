Require mod_rewrite

 ** Enable mod_rewrite **
`sudo a2enmod rewrite`

** add to httpd.conf **


    <ifModule mod_rewrite.c>
        Load
    </ifModule>


** edit /etc/apache2/apache2.conf **


    <Directory /path/to/webapp>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

** edit /path/to/webapp/.htaccess **

    Options -MultiViews
    RewriteEngine On

    RewriteBase /mvc/
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f

    RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]