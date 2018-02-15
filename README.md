

## Setup
** NB!! setup based on ubuntu 16.04 server **
### Apache2 mod_rewrite

 ** Enable mod_rewrite ** in terminal

    $ sudo a2enmod rewrite

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

### Database access

The class **`Q_ueryBuild`** houses the **`PDO`** database connection in the **` setdsn() `** function. 

to set it up run these in the terminal

    $ echo -n "DBuser" | base64
    REJ1c2Vy
    $ echo -n "DBP@s5w0rd" | base64 
    REJQQHM1dzByZA==

then take the results setup the **` setdsn() `** function

    ...
    $this->user = "REJ1c2Vy";
    $this->password = "REJQQHM1dzByZA==";
    ...


## Working With Requests
> examples will performed with curl

###Add User Example

    $ curl http://localhost/webapp/api -d submit=add_user -d name=andrew -d surname=Dlamini -d phone=0814017511 -d gender=m -d user_passcode=123

###Get Users Example
 
     $ curl http://localhost/webapp/api -d submit=get_users -d pntr=0

###update User Example

    $ curl http://localhost/mvc/api -d submit=update_user -d pntr=3 -d submit=add_user -d name=Mandisi -d surname=Makwakwa -d phone=0780012551 -d gender=m 
