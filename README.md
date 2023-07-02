# PHP-UploadHelper
This is a helper library for PHP that helps to process uploaded files.

PHP stores uploaded files in the `$_FILES` array. However, this array is not very trivial to use for multiple file uploads. This library provides an easy way to check whether a file upload should be allowed and to get the files in a more convenient way.

## Installation
To install this library, include it in your project using composer:
```json
{
    "require": {
        "jensostertag/php-uploadhelper": "dev-main"
    }
}
```

## Usage
<details>
<summary><b>Upload a single file</b></summary>

The following example shows how to allow the upload of a single file.

Let's assume you have a form with a file input named `fileInputName`:
```html
<form method="post" enctype="multipart/form-data">
    <input type="file" name="fileInputName" id="file">
    <input type="submit" value="Upload">
</form>
```

In your PHP script that is called when the form is submitted, use the `UploadHelper` class to check whether the file upload should be allowed and to get the uploaded files:
```php
$uploadHelper = new UploadHelper();

// File Upload Options
$uploadHelper->setInputName("fileInputName") // Set the Name of the File Input
             ->setMultiple(false) // Only allow a single File
             ->setAllowedMimeTypes(["image/jpeg", "image/png"]) // Only allow JPEG and PNG Files
             ->setMaxSize(2) // Only allow Files up to 2 MiB
             ->handleUploadedFiles();

// Check if there were Errors during the Upload
if(!($uploadHelper->successful())) {
    $errors = $uploadHelper->getErrors();
    return;
}

// Get the uploaded File
$uploadedFile = $uploadHelper->getUploadedFiles();
```

If the file upload was successful, the `$uploadedFile` will be an array with the following structure:
```php
[
    [0] => [
        "name" => "file.jpeg",
        "type" => "image/jpeg",
        "tmp_name" => "/tmp/php/php1h4j1o",
        "error" => 0,
        "size" => 1024
    ]
]
```
</details>

<details>
<summary><b>Upload multiple files</b></summary>

The following example shows how to allow the upload of multiple files.

Let's assume you have a form with a file input named `fileInputName`, with the `multiple` attribute set:
```html
<form method="post" enctype="multipart/form-data">
    <input type="file" name="fileInputName" id="file" multiple>
    <input type="submit" value="Upload">
</form>
```

In your PHP script that is called when the form is submitted, use the `UploadHelper` class to check whether the file upload should be allowed and to get the uploaded files:
```php
$uploadHelper = new UploadHelper();

// File Upload Options
$uploadHelper->setInputName("fileInputName") // Set the Name of the File Input
             ->setMultiple(true) // Allow multiple Files
             ->setAllowedMimeTypes(["image/jpeg", "image/png"]) // Only allow JPEG and PNG Files
             ->setMaxSize(2) // Only allow Files up to 2 MiB
             ->handleUploadedFiles();

// Check if there were Errors during the Upload
if(!($uploadHelper->successful())) {
    $errors = $uploadHelper->getErrors();
    return;
}

// Get the uploaded Files
$uploadedFiles = $uploadHelper->getUploadedFiles();
```

If the file upload was successful, the `$uploadedFiles` will be an array with the following structure:
```php
[
    [0] => [
        "name" => "file1.jpeg",
        "type" => "image/jpeg",
        "tmp_name" => "/tmp/php/php1h4j1o",
        "error" => 0,
        "size" => 1024
    ],
    [1] => [
        "name" => "file2.jpeg",
        "type" => "image/jpeg",
        "tmp_name" => "/tmp/php/php1h4j1o",
        "error" => 0,
        "size" => 1024
    ],
    // ...
]
```
</details>

<details>
<summary><b>File upload options</b></summary>

The following options can be set for the file upload:

| Option                                         | Description                                    | Default                             |
|------------------------------------------------|------------------------------------------------|-------------------------------------|
| `setInputName(string $inputName)`              | Sets the name of the file input.               | -                                   |
| `setMultiple(bool $multiple)`                  | Sets whether multiple files should be allowed. | `false`                             |
| `setAllowedMimeTypes(array $allowedMimeTypes)` | Sets the allowed mime types.                   | `[]` (All uploads will be rejected) |
| `setMaxSize(int $maxSize)`                     | Sets the maximum size of the file in MiB.      | `∞` (No maximum size)               |
</details>

<details>
<summary><b>Upload errors</b></summary>

You can check whether there were errors during the file upload with the `successful()` method. It returns `true` if there were no errors and `false` if there was at least one error.

Errors that occur during the file upload can be retrieved with the `getErrors()` method. It returns them as Enum values of the `UploadError` class. There are the following errors:

| Error                     | Code | Description                                                                            |
|---------------------------|------|----------------------------------------------------------------------------------------|
| `UPLOAD_ERR_NOT_UPLOADED` | `0` | The file was not uploaded via HTTP POST or the PHP upload error is not `UPLOAD_ERR_OK`. |
| `UPLOAD_ERR_TYPE`         | `1` | The file type is not allowed.                                                           |
| `UPLOAD_ERR_SIZE`         | `2` | The file size is too large.                                                             |
| `UPLOAD_ERR_MULTIPLE`     | `3` | Multiple files were uploaded, but only a single file is allowed.                        |
</details>
