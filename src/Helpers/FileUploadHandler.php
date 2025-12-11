<?php

class FileUploadHandler {
    
    private $uploadDir;
    private $allowedExtensions;
    private $allowedMimeTypes;
    private $maxSize;
    
    /**
     * Constructor
     * @param string $uploadDir Upload directory path
     * @param array $allowedExtensions Array of allowed file extensions
     * @param array $allowedMimeTypes Array of allowed MIME types
     * @param int $maxSize Maximum file size in bytes (default 10MB)
     */
    public function __construct($uploadDir, $allowedExtensions, $allowedMimeTypes, $maxSize = 10485760) {
        $this->uploadDir = $uploadDir;
        $this->allowedExtensions = $allowedExtensions;
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->maxSize = $maxSize;
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
    }
    
    /**
     * Upload file with validation
     * @param string $fileInputName Name of the file input field
     * @param bool $isOptional Whether the file is optional
     * @return array Result array with 'success', 'path', and optional 'message'
     */
    public function upload($fileInputName, $isOptional = false) {
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
            if ($isOptional) {
                return ['success' => true, 'path' => null];
            }
            return ['success' => false, 'message' => "File '$fileInputName' wajib diupload."];
        }
        
        $file = $_FILES[$fileInputName];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => $this->getUploadErrorMessage($file['error'])];
        }
        
        // Validate file size
        if ($file['size'] > $this->maxSize) {
            $maxMB = $this->maxSize / 1024 / 1024;
            return ['success' => false, 'message' => "File '$fileInputName' terlalu besar. Maksimal {$maxMB}MB."];
        }
        
        // Validate file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            return ['success' => false, 'message' => "Format file '$fileInputName' tidak valid. Diizinkan: " . implode(', ', $this->allowedExtensions)];
        }
        
        // Validate MIME type (content-based validation)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            return ['success' => false, 'message' => "Tipe file '$fileInputName' tidak valid."];
        }
        
        // Generate unique filename
        $newFileName = time() . '_' . uniqid() . '.' . $ext;
        $targetPath = $this->uploadDir . $newFileName;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Return path relative to web root
            $dbPath = str_replace('../', '', $targetPath);
            if (strpos($dbPath, 'files/') !== 0 && strpos($dbPath, 'audio/') !== 0) {
                $dbPath = './' . $dbPath;
            }
            return ['success' => true, 'path' => $dbPath];
        } else {
            return ['success' => false, 'message' => "Gagal mengupload file '$fileInputName'."];
        }
    }
    
    /**
     * Delete file
     * @param string $filePath Path to file to delete
     * @return bool Success status
     */
    public static function deleteFile($filePath) {
        if (!empty($filePath) && file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
    
    /**
     * Get upload error message
     * @param int $errorCode PHP upload error code
     * @return string Error message
     */
    private function getUploadErrorMessage($errorCode) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File melebihi upload_max_filesize di php.ini',
            UPLOAD_ERR_FORM_SIZE => 'File melebihi MAX_FILE_SIZE di form HTML',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ada',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
        ];
        return $errors[$errorCode] ?? 'Error upload tidak diketahui';
    }
    
    /**
     * Get MIME types for common file extensions
     * @param string $type File type category (pdf, image, video, audio, glb)
     * @return array Array of MIME types
     */
    public static function getMimeTypes($type) {
        $mimeTypes = [
            'pdf' => ['application/pdf'],
            'image' => ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
            'video' => ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo'],
            'audio' => ['audio/mpeg', 'audio/wav', 'audio/x-wav', 'audio/wave'],
            'glb' => ['model/gltf-binary', 'application/octet-stream'] // GLB can be detected as octet-stream
        ];
        return $mimeTypes[$type] ?? [];
    }
}
