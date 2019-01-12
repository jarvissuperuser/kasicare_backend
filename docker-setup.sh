## docker-setup
docker build -t kasicareimg .

docker rm kcContainer

sudo -u $USER chown $USER:www-data  $(pwd)

docker run --name kcContainer  -p 8080:80  -v $(pwd):/var/www/html kasicareimg apache2ctl -D FOREGROUND