Por defecto, XAMPP expone la ruta /opt/lampp/htdocs/ para linus
En mi caso, utilizaré esta ruta: /home/jorge/Teoría_Sistemas/

Para cambiarlo: 
1. Abrir el archivo de configuracion de XAMPP
```
sudo nano /opt/lampp/etc/httpd.conf
```

2. Reemplazar: 
```
DocumentRoot "/opt/lampp/htdocs"
<Directory "/opt/lampp/htdocs">
```

Por: 
```
DocumentRoot "/home/jorge/Teoría_Sistemas"
<Directory "/home/jorge/Teoría_Sistemas">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

3. Dar permisos a la carpeta:
```
chmod -R 755 "/home/jorge/Teoría_Sistemas"
chown -R jorge:jorge "/home/jorge/Teoría_Sistemas"
```