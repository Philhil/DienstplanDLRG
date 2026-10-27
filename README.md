[![Actions Status](https://github.com/philhil/DienstplanDLRG/workflows/Unit-Tests/badge.svg)](https://github.com/philhil/DienstplanDLRG/actions)

# DienstplanDLRG
This Project is a Laravel based web application to manage volunteer services at the German Life Saving Society (DLRG) of Stuttgart.
A production version could be found here https://dlrgdienstplan.de. If you want to register a new Client (Gliederung), just contact me.

## Demo 
A Demo Application is provided here: https://demo.dlrgdienstplan.de. 

Login as a Admin with 
>user: user.demodienstplan@philhil.de 
>password: user 

or as a User with 

>user: admin.demodienstplan@philhil.de 
>password: admin

## Issues & Feature requests

Before opening an issue, make sure to check whether any existing issues
(open or closed) match. If you're suggesting a new feature, text me first or talk direktly to me.

## Use a release!

Please refrain from using the `master` branch for anything else but development purposes!
Use the most recent release instead. You can list all releases by running `git tag`
and switch to one by running `git checkout *name*`.

## Upgrade

If you run a old version follow the upgrade Guide: [https://github.com/Philhil/DienstplanDLRG/wiki#upgrade-guide](https://github.com/Philhil/DienstplanDLRG/wiki#upgrade-guide)

## Development Setup with Docker

Use Docker Compose for a local test/development instance. The stack consists of:

| Service   | Purpose                                                        | URL / Port              |
|-----------|----------------------------------------------------------------|-------------------------|
| `app`     | nginx + php-fpm + queue worker + scheduler (via supervisord)   | http://localhost:8080   |
| `mariadb` | Database                                                       | `127.0.0.1:3306`        |
| `redis`   | Cache, sessions, queue                                         | internal only           |
| `mailpit` | Catches all outgoing mails                                     | http://localhost:8025   |

The image contains no `.env` and no `APP_KEY`. All configuration is passed as environment variables in
`docker-compose.yaml`. If `APP_KEY` is not set, a key is generated on first start and stored in the `app-storage` volume.
On start the container runs the migrations and (with `IS_DEMO=true` and `DEMO_DATA_ON_START=true`) creates the demo client if the database is empty.

### Requirements

* Docker Engine + Compose plugin (or Docker Desktop)
* Free local ports `8080`, `8025` and `3306` (override with `APP_PORT`, `MAILPIT_PORT`, `DB_FORWARD_PORT`)

### Start

```bash
docker compose up -d --build
```

Login with the demo accounts:

* Admin: `admin.demodienstplan@philhil.de` / `admin`
* User: `user.demodienstplan@philhil.de` / `user`

### Useful commands

```bash
docker compose logs -f app                          # logs of nginx, php-fpm, queue, scheduler
docker compose exec app php artisan migrate:status
docker compose exec app php artisan demo:createDemoClient   # reset demo data
docker compose up -d --build                        # apply code changes
docker compose down                                 # stop
docker compose down -v                              # stop and delete database + storage
```

### Corporate proxy / TLS interception (Composer curl error 60)

If build fails with an SSL certificate error while Composer downloads packages from GitHub, your network likely uses a company proxy with TLS interception.

1. Export your company root certificate (for example to `C:\cert\mycert.crt`).
2. In the same Windows PowerShell shell where you run Docker, set proxy and certificate variables:

```powershell
$env:HTTPS_PROXY = "http://proxy.company.local:8080"
$env:CUSTOM_CA_CERT_B64 = [Convert]::ToBase64String([IO.File]::ReadAllBytes("C:\cert\mycert.crt"))
```

3. Rebuild the image from that same shell:

```bash
docker compose build --no-cache --progress=plain app
```

Important: variables must be set in the same shell session as `docker compose build`. If not, Compose resolves `CUSTOM_CA_CERT_B64` as empty and the certificate is not imported.

The Dockerfile imports this certificate into the container trust store before running Composer.

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
$client = \App\Client::create(['name' => "InstanceName",'seasonStart' => "2000-01-01", 'isMailinglistCommunication' => false, 'weeklyServiceviewEmail' => false, 'mailinglistAddress' => null, 'mailSenderName' => "Dienstplan", 'mailReplyAddress' => "dienstplan@yourdomain.de", 'module_training' => true, 'module_training_credit' => false, 'module_statistic' => true, 'module_survey' => true]);
$user = \App\User::create(['name' => 'LastName','first_name' => 'FirstName','email' => 'email@domain.de', 'password' => Hash::make('test'), 'role' => 'admin', 'approved' => '1', 'currentclient_id' => $client->id]);
\App\Client_user::create(['client_id' => $client->id, 'user_id' => $user->id, 'isAdmin' => 1,'isTrainingEditor' => 1]);
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
