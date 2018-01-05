

## Setup
** NB!! setup based on ubuntu 16.04 server **
### Apache2 mod_rewrite

 ** Enable mod_rewrite **
`sudo a2enmod rewrite`

** add to httpd.conf **


    <ifModule mod_rewrite.c>
        LoadModule rewrite_module modules/mod_rewrite.so
    </ifModule>


** edit /etc/apache2/apache2.conf **


    <Directory /path/to/webapp>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

** create or edit /path/to/webapp/.htaccess **

    Options -MultiViews
    RewriteEngine On

    RewriteBase /webapp/
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f

    RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]

### SQL

Add MySQL script to any folder you desire of the with in your webapp folder. 

edit the file **`classes\Q_ueryBuild.php`** 

in the function **`init()`** 

    ...
    $sql = file_get_contents('relative/path/to/sql.sql');
    ...



## Working With Requests
>> obselete 
### Example > add_article : 
(** Requires read/write permissions For user:group **|`www-data:www-data`|** write file data to folder **)
>Designed for Multipart File Upload 


>> POST vars for files
**(executes when exp < chksum)**
>>> `image_1` => file name;

>>> `ext` => file extension;

>>> `exp` => part-number of file expected;

>>> `chcksm` => total number parts expected(+ 1);

>>> `data` => file data expected as base64;

>> POST vars for article
**(saves to database)**
>>> `image_2` => description;

>>> `title` => ;

>>> `main` => ;

>>> `creator` => ;
