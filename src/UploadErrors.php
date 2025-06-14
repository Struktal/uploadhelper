<?php

namespace struktal\FileUpload;

enum UploadErrors {
    case UPLOAD_ERR_NOT_UPLOADED;
    case UPLOAD_ERR_TYPE;
    case UPLOAD_ERR_SIZE;
    case UPLOAD_ERR_MULTIPLE;

    /**
     * Get the Upload Error Code
     * @return int
     */
    function getCode(): int {
        return match($this) {
            self::UPLOAD_ERR_NOT_UPLOADED => 0,
            self::UPLOAD_ERR_TYPE => 1,
            self::UPLOAD_ERR_SIZE => 2,
            self::UPLOAD_ERR_MULTIPLE => 3
        };
    }
}
