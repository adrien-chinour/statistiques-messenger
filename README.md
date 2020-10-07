# Facebook Messenger Statistics

## Description

Get statistics from all your conversation on Facebook Messenger, get group conversation ranking by user and more stats.

## Installation

Firstly, you have to download Messenger data from Facebook in JSON file.

**Step 1 : Go to Facebook configuration**

![configuration](https://github.com/adrien-chinour/statistiques-messenger/blob/master/.github/assets/36BOBKr.png?raw=true)

**Step 2 : Select data to download**

![selection](https://github.com/adrien-chinour/statistiques-messenger/blob/master/.github/assets/Uun4uJe.png?raw=true)

Put `messages` folder from your export in `data` folder.

Now you are ready for extract stats !

## Usage

**No configuration required with docker and docker-compose**

Use `./console` to see available commands, on first launch this command will start your docker environment.
If docker-compose is not define, this script will use your local php environment.

> Configuring DB env variables on .env file if you don't use docker.

### Import conversation

Command `./console conversation:import`

### Generate stats

Command `./console conversation:stat`

### Deployment

Simply deploy stats with [commons.host](commons.host).

**Step 1 : Install CLI**
```
npm install -g @commonshost/cli
```

**Step 2 : Log In**
```
commonshost login
```

**Step 3 : Deploy**
```
commonshost deploy --root "output/conversations/MA_CONVERSATION"
```

> Remove host with `commonshost delete`


