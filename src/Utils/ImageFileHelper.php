<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 18.02.2018
 * Time: 21:49
 */

namespace Utils;


class ImageFileHelper {
    public function uploadFiles($files, $storeFolder, $filePrefix, $quality)
    {
        if (!empty($files)) {
            $tempFile = $files['file']['tmp_name'];
            $destFolder = $storeFolder . "/";
            $targetFile = $destFolder . uniqid($filePrefix) . ".jpg";

            if (!file_exists($destFolder))
                mkdir($destFolder, 0x0777, true);

            if ($this->changeImageQuality($tempFile, $targetFile, $quality)) {
                $action = new \stdClass();
                $action->action = "add";
                $action->data = $targetFile;

                return $action;
            } else {
                echo "Error occurred\n";
                return null;
            }
        }
    }

    private function changeImageQuality($tempFile, $targetFile, $restrainedQuality)
    {
        //open a stream for the uploaded image
        $streamHandle = @fopen($tempFile, 'r');
        //create a image resource from the contents of the uploaded image
        $resource = imagecreatefromstring(stream_get_contents($streamHandle));

        $isDone = false;

        if (!$resource)
            return $isDone;

        //close our file stream
        @fclose($streamHandle);

        //move the uploaded file with a lesser quality
        $isDone = imagejpeg($resource, $targetFile, $restrainedQuality);
        //delete the temporary upload
        @unlink($tempFile['tmp_name']);
        return $isDone;
    }

    /**
     * @param array $actions
     * @param string $storeFolder
     * @param string $filePrefix
     * @return \stdClass;
     */
    public function checkAction($actions, $storeFolder, $filePrefix)
    {
        $uploadedFiles = [];
        $removedFiles = [];
        $destFolder = $storeFolder . "/";

        foreach ($actions as $action) {
            if ($action->action == "add") {
                $targetFile = $destFolder . uniqid($filePrefix) . ".jpg";

                if (rename($action->data, $targetFile)) {
                    $uploadedFiles[] = $targetFile;
                } else {
                    echo "Error occurred\n";
                }
            } else if ($action->action == "remove") {
                $removedFiles[] = $action->data;

                // TODO: remove image file from disk
            }
        }

        $filteredFiles = new \stdClass();
        $filteredFiles->uploaded = $uploadedFiles;
        $filteredFiles->removed = $removedFiles;
        return $filteredFiles;
    }

    /**
     * @param string $filePath
     */
    public function removeFile($filePath)
    {
        unlink($filePath);
    }

    /**
     * @param string[] $filePaths
     */
    public function removeFiles($filePaths)
    {
        foreach ($filePaths as $filePath) {
            $this->removeFile($filePath);
        }
    }

    public function prepareAction($filePath, $actionType)
    {
        $action = new \stdClass();
        $action->action = $actionType;
        $action->data = $filePath;
        return $action;
    }

}