<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

class GCSConnector {
    protected $storage;

    function __construct($projectId, $keyFilePath) {
        $this->storage = $this->initializeStorage($projectId, $keyFilePath);
    }

    private function initializeStorage($projectId, $keyFilePath) {
        $storage = new StorageClient([
            'projectId' => $projectId,
            'keyFilePath' => $keyFilePath,
        ]);

        return $storage;
    }

    public function getStorageClient() {
        return $this->storage;
    }
}

?>