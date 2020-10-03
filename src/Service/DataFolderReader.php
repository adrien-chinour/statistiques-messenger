<?php

namespace App\Service;

class DataFolderReader
{

    public function getInboxFolder(): string
    {
        return __DIR__ . '/../../data/messages/inbox';
    }

    public function checkMessengerFolderExist(): bool
    {
        return is_dir($this->getInboxFolder());
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

    public function getConversationFolder(string $conversationFolderName): string
    {
        $absoluteFolder = $this->getInboxFolder() . '/' . $conversationFolderName;

        if ($this->isDir($absoluteFolder)) {
            return $absoluteFolder;
        }

        throw new \InvalidArgumentException("Conversation folder does not exist.");
    }

    public function getConversationFiles(string $conversationFolder): array
    {
        if ($this->isDir($conversationFolder)) {
            return array_diff(scandir($conversationFolder), ['.', '..']);
        }

        throw new \InvalidArgumentException("Conversation folder does not exist.");
    }

    private function isDir(string $folder): bool
    {
        return is_dir($folder);
    }

}
