Opengest 4
===========

A Symfony 4.4 LTS webapp project to manage [Opengest](https://opengest4.gruasromani.com/admin) enterprise management.

---

#### Install requirements

* PHP 7.4
* MySQL 5.7
* Git
* Composer
* Yarn

#### Install instructions

```bash
$ git clone git@github.com:Flexible-User-Experience/opengest-4.git
$ cd opengest-4
$ cp env.dit .env
$ nano .env
$ composer install
$ yarn install
```

Remeber to edit `.env` file according to your system environment needs.

#### Load database fixtures

```bash
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate
$ php bin/console hautelook:fixtures:load
```

#### Test suite

```bash
$ ./scripts/developer-tools/test-database-reset.sh
$ ./scripts/developer-tools/run-test.sh
```
