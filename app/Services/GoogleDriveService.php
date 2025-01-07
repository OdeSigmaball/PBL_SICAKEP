<?php

namespace App\Services;


use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Cache;

class GoogleDriveService
{
    private $driveService;

    public function __construct()
    {
        $client = new Client();
        $client->setClientId('40125133392-ob420dh39u6vrre3aqegj220bs5lu919.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setAccessType('offline');
        $client->addScope(Drive::DRIVE);

        // Langsung tambahkan refresh token di sini
        $refreshToken = '1//04WYnfUdECXKYCgYIARAAGAQSNwF-L9IrJSL7lqxZhuWLeayARD6TXVB6aM966pPM9vaEA3jjPsYb1v94BEmvsSlgbl-TF6r8bZ4';
        $accessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);

        // Set access token yang dihasilkan
        $client->setAccessToken($accessToken);

        // Perbarui token jika sudah kedaluwarsa
        if ($client->isAccessTokenExpired()) {
            $newAccessToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $client->setAccessToken($newAccessToken);
        }

        $this->driveService = new Drive($client);
    }

    public function createFolder($name, $parentId = null)
{
    $folderMetadata = [
        'name' => $name,
        'mimeType' => 'application/vnd.google-apps.folder',
    ];

    // Tambahkan parent ID jika ada
    if ($parentId) {
        $folderMetadata['parents'] = [$parentId];
    }

    try {
        $folder = $this->driveService->files->create(new Drive\DriveFile($folderMetadata), [
            'fields' => 'id',
        ]);
        return $folder;
    } catch (\Exception $e) {
        throw new \Exception("Gagal membuat folder: " . $e->getMessage());
    }
}


    public function uploadFile($filePath, $fileName, $parentId)
    {
        $fileMetadata = [
            'name' => $fileName,
            'parents' => [$parentId],
        ];

        $content = file_get_contents($filePath);

        return $this->driveService->files->create(
            new Drive\DriveFile($fileMetadata),
            [
                'data' => $content,
                'mimeType' => mime_content_type($filePath),
                'uploadType' => 'multipart',
            ]
        );
    }

    public function deleteFile($fileId)
    {
        return $this->driveService->files->delete($fileId);
    }

}
