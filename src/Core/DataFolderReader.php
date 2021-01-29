<?php

namespace App\Core;

class DataFolderReader
{
    public function getInboxFolder(): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'data', 'messages', 'inbox']);
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

        if (is_dir($absoluteFolder)) {
            return $absoluteFolder;
        }

        throw new \InvalidArgumentException("Conversation folder does not exist.");
    }

    public function getConversationFiles(string $conversationFolder): array
    {
        if (is_dir($conversationFolder)) {
            $files = array_diff(scandir($conversationFolder), ['.', '..']);
            return array_filter($files, fn($file) => strpos($file, '.json') !== false);
        }

        throw new \InvalidArgumentException("Conversation folder does not exist.");
    }
}
