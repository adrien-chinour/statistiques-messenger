# Facebook Messenger Statistics

## Description

Get statistics from all your conversation on Facebook Messenger, get group conversation ranking by user and more stats.

## Installation

Firstly, you have to download Messenger data from Facebook in JSON file.

**Step 1 : Go to Facebook configuration**

![configuration](https://github.com/adrien-chinour/statistiques-messenger/blob/master/.github/assets/36BOBKr.png?raw=true)

**Step 2 : Select data to download**

![selection](https://github.com/adrien-chinour/statistiques-messenger/blob/master/.github/assets/Uun4uJe.png?raw=true)

**Step 3 : export data**

Export data from zip file and put `messages` folder from your export in `data` folder.

**Now you are ready for extract stats !**

## Usage

**No configuration required with docker and docker-compose**

Use `./console` to see available commands, on first launch this command will start your docker environment.
If docker-compose is not define, this script will use your local php environment.

> Configuring DB env variables on .env file if you don't use docker.

### Import conversation

```
./console conversation:import
```

### Generate stats

```
./console conversation:stat
```

## Deployment

Simply deploy the generated page with [commons.host](https://commons.host).

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

## Upgrade

### Add a module

A module is a portion of a generated page. Each module provides independent statistics.

**Step 1 : Module Class**

On `src\Module` add a class for your module and extend ` App\Core\Module\AbstractModule`

```php
namespace App\Module;

use App\Core\Entity\Conversation;
use App\Core\Module\AbstractModule;

class DummyModule extends AbstractModule
{

    public function build(Conversation $conversation): string
    {

    }

}
```

** Step 2 : Querying DB**

Your module need to query database to get statistics. `AbstractModule` provide a method `createQueryBuilder` to create a query :

```php
class DummyModule extends AbstractModule
{

    public function build(Conversation $conversation): string
    {
        $this->createQueryBuilder()
            ->select('sum(m.nbReactions) as total_reactions')
            ->from(Message::class, 'm')
            ->where('m.conversation = :conversation_id')
            ->setParameter('conversation_id', $conversation->getId())
            ->getQuery();
    }
}
```

> More information about Doctrine QueryBuilder [here](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/query-builder.html).

** Step 3 : Rendering information**

On `templates/modules` add a twig file named like your module name : `dummy.html.twig`

On your class add rendering operation :

```php
class DummyModule extends AbstractModule
{

    public function build(Conversation $conversation): string
    {
        $query = $this->createQueryBuilder()
            ->select('sum(m.nbReactions) as total_reactions')
            ->from(Message::class, 'm')
            ->where('m.conversation = :conversation_id')
            ->setParameter('conversation_id', $conversation->getId())
            ->getQuery();
        
        $reactions = $query->execute()[0]['total_reactions'];

        return $this->render('modules/dummy.html.twig', ['reactions' => $reactions]);
    }

}
```

And on your template you can display data

```twig
<div class="container text-center">

    <h2 class="display-3 mb-3">important stat</h2>

    <h3>
        Overall reaction : <strong>{{ reactions }}</strong>
    </h3>
</div>
```
