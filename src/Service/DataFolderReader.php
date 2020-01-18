<?php

namespace App\Service;

class DataFolderReader
{

    /**
     * @return string
     */
    public function getInboxFolder(): string
    {
        return __DIR__ . '/../../data/messages/inbox';
    }

    /**
     * @return bool
     */
    public function checkMessengerFolderExist(): bool
    {
        return is_dir($this->getInboxFolder());
    }

    /**
     * @return array
     */
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

    /**
     * @param string $conversationFolderName
     * @return string
     */
    public function getConversationFolder(string $conversationFolderName): string
    {
        $absoluteFolder = $this->getInboxFolder() . '/' . $conversationFolderName;

        if ($this->isDir($absoluteFolder)) {
            return $absoluteFolder;
        }

        throw new \InvalidArgumentException("Conversation folder does not exist.");
    }

    /**
     * @param string $conversationFolder
     * @return array
     */
    public function getConversationFiles(string $conversationFolder): array
    {
        if ($this->isDir($conversationFolder)) {
            return array_diff(scandir($conversationFolder), ['.', '..']);
        }

        throw new \InvalidArgumentException("Conversation folder does not exist.");
    }

    /**
     * @param string $folder
     * @return bool
     */
    private function isDir(string $folder): bool
    {
        return is_dir($folder);
    }

}
