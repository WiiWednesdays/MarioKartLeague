Mario Kart League
===============

## Installation

First, install composer and install vendor dependencies: (see [Composer download page](http://getcomposer.org/download/))
```bash
cd /path/to/MarioKartLeague
curl -sS https://getcomposer.org/installer | php
php composer.phar update
```

Next, copy the distributed `parameters.ini.dist` file and update it with relevant information:
```bash
cp app/config/parameters.ini.dist app/config/parameters.ini
vim app/config/parameters.ini
```

Create a new mysql database (based on the `database.name` parameter you configured in the previous step) and generate the schema from the Doctrine entity files:
```bash
mysql -u root -p -e "create database your_db_name";
vendor/bin/doctrine orm:schema-tool:update --force
```

#### nginx
```nginx
server {
    server_name markiokart.local; # REMEBER TO ADD THIS TO YOUR HOSTS FILE
    server_tokens off;
    root /path/to/MarioKartLeague/web;

        #site root is redirected to the app boot script
        location = / {
                try_files @site @site;
        }

        #all other locations try other files first and go to our front controller if none of them exists
        location / {
                try_files $uri $uri/ @site;
        }

        #return 404 for all php files as we do have a front controller
        location ~ \.php$ {
                return 404;
        }

        location @site {
                fastcgi_pass   unix:/var/run/php5-fpm.sock;
                include fastcgi_params;
                fastcgi_param SCRIPT_FILENAME $document_root/index.php;
                fastcgi_param HTTPS on;
                fastcgi_param APP_ENV dev;
        }
}
```

## Usage

Basic administration is done via the console; `app/console`. Show all available console commands by executing the console with no arguments:
```bash
cd /path/to/MarioKartLeague
app/console
```

### Examples

Create a team: (will run interactively if no arguments are provided)
```bash
app/console team:add Pirates
```

Create a user and assign to a team: (will run interactively if no arguments are provided)
```bash
app/console user:add Username
```

Assign an existing user to a team: (will run interactively if no arguments are provided)
```bash
app/console user:assign Username
```