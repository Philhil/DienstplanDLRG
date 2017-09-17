[![Build Status](https://travis-ci.org/Philhil/DienstplanDLRG.svg?branch=master)](https://travis-ci.org/Philhil/DienstplanDLRG)

# DienstplanDLRG
This Project is a Laravel based web application to manage volunteer services at the German Life Saving Society (DLRG) of Stuttgart.

## Issues & Feature requests

Before opening an issue, make sure to check whether any existing issues
(open or closed) match. If you're suggesting a new feature, text me first or talk direktly to me.

## Use a release!

Please refrain from using the `master` branch for anything else but development purposes!
Use the most recent release instead. You can list all releases by running `git tag`
and switch to one by running `git checkout *name*`.

## License
The Laravel framework is open-sourced software licensed under the MIT license.

## Setup on Debian based Linux (Server).
_People with other Distros like me with Gentoo should know what to do_

* <code>apt-get install mariadb-server nginx php phpunit php-mysql php-mbstring php-zip php-mcrypt supervisor</code>

* Clone this Project in your web dir like <code>/var/www/</code>

* File Permissons:
```bash
  sudo chown -R www-data:www-data /path/to/your/root/directory
  sudo find /path/to/your/root/directory -type f -exec chmod 644 {} \;  
  sudo find /path/to/your/root/directory -type d -exec chmod 755 {} \;
```

**If you run this in production please make sure to use TLS ( [LetsEncrypt for Ubuntu Fanboys](https://www.digitalocean.com/community/tutorials/how-to-secure-nginx-with-let-s-encrypt-on-ubuntu-16-04) )**

### Mysql (mariadb) and Laravel

 <code>mysql_secure_installation</code>

 <code>mysql -u root -p</code>

```sql
CREATE DATABASE IF NOT EXISTS dlrgdienstplan;
CREATE USER 'dlrgdienstplan'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON dlrgdienstplan.* To 'dlrgdienstplan'@'localhost';
```

#### .env File
<code>cp .env.example .env</code>

Set the parameters in .env
```bash
DB_HOST=localhost
DB_DATABASE=dlrgdienstplan
DB_USERNAME=dlrgdienstplan
DB_PASSWORD=password

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=mailuser
MAIL_PASSWORD=pass
MAIL_ENCRYPTION=tls

FACEBOOK_CLIENTID = 000
FACEBOOK_CLIENTSECRET = 000
```


#### Setup Laravel Environment
<code>php composer.phar install</code>

<code>php artisan key:generate</code>

<code>php artisan migrate</code>

#### Create Superadmin
<code>php artisan tinker</code>

```bash
\App\User::create(['name' => 'LastName','first_name' => 'FirstName','email' => 'email@domain.de', 'password' => Hash::make('test'), 'role' => 'admin', 'approved' => '1']);
```

#### Cron and Autostart
<code>crontab -e</code> and paste:
<pre>* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1</pre>

 Supervisor: create /etc/supervisor/conf.d/laravel-worker.conf		
 <pre>		
 [program:laravel-worker]		
 process_name=%(program_name)s_%(process_num)02d		
 command=php /var/www/DienstplanDLRG/artisan queue:work --tries=3		
 autostart=true		
 autorestart=true		
 numprocs=1		
 redirect_stderr=true		
 stdout_logfile=/var/log/supervisor/laravel-worker.log		
 </pre>