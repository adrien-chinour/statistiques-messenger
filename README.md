# Facebook Messenger Statistics

## Description

Get statistics from all your conversation on Facebook Messenger, get group conversation ranking by user and more stats.

## Installation

Firstly, you have to download Messenger data from Facebook in JSON file.

After getting your data, download this project and edit database values in `.env` file:

> No change needed if you use docker-compose

```
git clone https://github.com/adrien-chinour/messenger-statistics.git
cp .env.dist .env
vim .env
```

Then put your data on `data` folder at the root of the project. Put only the `messages` folder from your facebook export.


Now you are ready for extract stats !

## Usage

Use `./console` to see available commands, on first launch this command will start your docker environment.
If docker-compose is not define, this script will use your local php environment.
