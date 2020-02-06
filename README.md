# Facebook Messenger Statistics

## Description

Get statistics from all your conversation on Facebook Messenger, get group conversation ranking by user and more stats.

## Installation

Firstly, you have to download Messenger data from Facebook in JSON file.

After getting your data, download this project and edit database values in `.env` file:

```
git clone https://github.com/adrien-chinour/messenger-statistics.git
cp .env.dist .env
vim .env
```

Then put your data on `data` folder at the root of the project.

Now you are ready for extract stats !

## Setup

- `docker-compose build`
- `docker-run app composer install`
- `docker-run app vendor/bin/doctrine orm:schema-tool:update --force`
- `docker-run app php application.php` and see availables commands.
