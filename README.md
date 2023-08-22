# Petshop API

Project contains two endpoints for admin, login and admin dashboard

## Installation

*Project requires docker and docker-compose needs to be installed.*

Clone git repository

```sh
git clone git@github.com:chiragparekh/petshop-demo.git
cd petshop-demo
cp .env.example .env
```

Setup docker images
```sh
docker-compose up -d 
```

Install composer dependencies
```sh
docker-compose run --rm composer install
```

Generate application key
```sh
docker-compose run --rm artisan key:generate
```

Run the database migrations and seeder

```sh
docker-compose run --rm artisan migrate --seed
```

After that done [Click here](http://localhost) to check the swagger documentation for the endpoints