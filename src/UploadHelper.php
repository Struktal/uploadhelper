<?php

namespace jensostertag\UploadHelper;

class UploadHelper {
    private string $inputName = "file";
    private bool $multiple = false;
    private array $allowedMimeTypes = [];
    private ?int $maxSize = null;
    private array $files = [];
    private bool $uploadSuccessful = false;
    private array $uploadErrors = [];

    /**
     * Set the Input Name of the File Input
     * @param string $inputName Name of the HTML File Input Element
     * @return $this Self
     */
    public function setInputName(string $inputName): UploadHelper {
        $this->inputName = $inputName;
        return $this;
    }

    /**
     * Set whether multiple Files should be allowed
     * @param bool $multiple Upload of multiple Files allowed
     * @return $this Self
     */
    public function setMultiple(bool $multiple): UploadHelper {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * Set the allowed MIME Types for the uploaded Files
     * @param array $allowedMimeTypes Allowed MIME Types
     * @return $this Self
     */
    public function setAllowedMimeTypes(array $allowedMimeTypes): UploadHelper {
        $this->allowedMimeTypes = $allowedMimeTypes;
        return $this;
    }

    /**
     * Set the maximum allowed File Size in MiB for each uploaded File
     * @param int|null $maxSize Maximum allowed File Size (MiB)
     * @return $this Self
     */
    public function setMaxSize(?int $maxSize): UploadHelper {
        if($maxSize !== null) {
            $this->maxSize = $maxSize * 1024 * 1024;
        } else {
            $this->maxSize = null;
        }

        return $this;
    }

    /**
     * Check whether a File is allowed to be uploaded
     * @param array $file File that should be checked
     * @return bool File Upload allowed
     */
    private function checkFile(array $file): bool {
        if(!(is_uploaded_file($file["tmp_name"]) && $file["error"] === UPLOAD_ERR_OK)) {
            $this->uploadErrors[] = UploadErrors::UPLOAD_ERR_NOT_UPLOADED;
            return false;
        }

        if(!(in_array($file["type"], $this->allowedMimeTypes))) {
            $this->uploadErrors[] = UploadErrors::UPLOAD_ERR_TYPE;
            return false;
        }

        if($this->maxSize !== null && $file["size"] > $this->maxSize) {
            $this->uploadErrors[] = UploadErrors::UPLOAD_ERR_SIZE;
            return false;
        }

        return true;
    }

    /**
     * Handle the uploaded Files
     * @return $this
     */
    public function handleUploadedFiles(): UploadHelper {
        if(isset($_FILES[$this->inputName])) {
            $this->uploadSuccessful = true;

            $files = [];
            if($this->multiple) {
                if(is_array($_FILES[$this->inputName]["name"])) {
                    for($i = 0; $i < sizeof($_FILES[$this->inputName]["name"]); $i++) {
                        $file = [
                            "name" => $_FILES[$this->inputName]["name"][$i],
                            "type" => $_FILES[$this->inputName]["type"][$i],
                            "tmp_name" => $_FILES[$this->inputName]["tmp_name"][$i],
                            "error" => $_FILES[$this->inputName]["error"][$i],
                            "size" => $_FILES[$this->inputName]["size"][$i]
                        ];

                        if(!($this->checkFile($file))) {
                            $this->uploadSuccessful = false;
                        } else {
                            $files[] = $file;
                        }
                    }
                } else {
                    $file = $_FILES[$this->inputName];
                    if(!($this->checkFile($file))) {
                        $this->uploadSuccessful = false;
                    } else {
                        $files[] = $file;
                    }
                }
            } else {
                if(is_array($_FILES[$this->inputName]["name"])) {
                    $this->uploadErrors[] = UploadErrors::UPLOAD_ERR_NO_MULTIPLE;
                    $this->uploadSuccessful = false;
                } else {
                    $file = $_FILES[$this->inputName];
                    if(!($this->checkFile($file))) {
                        $this->uploadSuccessful = false;
                    } else {
                        $files[] = $file;
                    }
                }
            }

            $this->files = $files;
        }

        return $this;
    }

    /**
     * Get the uploaded Files
     * @return array Uploaded Files
     */
    public function getFiles(): array {
        return $this->files;
    }

    /**
     * Check whether the Upload was successful
     * @return bool All Files uploaded successfully
     */
    public function successful(): bool {
        return $this->uploadSuccessful;
    }

    /**
     * Get the Upload Errors
     * @return array Upload Errors
     */
    public function getErrors(): array {
        return $this->uploadErrors;
    }
}
