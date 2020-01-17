<?php

namespace App\Service;

class DataFolderReader
{

    public function getInboxFolder()
    {
        return __DIR__ . '/../../data/messages/inbox';
    }

    public function checkFolderExist()
    {
        // TODO
        return true;
    }

    public function getMessageFolders(): array
    {
        $json = [];
        foreach (scandir($this->getInboxFolder()) as $item) {
            if (file_exists("{$this->getInboxFolder()}/$item/message_1.json")) {
                $json[] = $item;
            }
        }

        return $json;
    }


}
