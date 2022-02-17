<?php 

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

class FileControl {

    private $client;
    private $adapter;
    private $filesystem;

    public function __construct($options,  $bucketName) {
       $this->client = new Aws\S3\S3Client($options);

        $this->adapter = new League\Flysystem\AwsS3V3\AwsS3V3Adapter(
        $this->client, $bucketName);

        $this->filesystem = new League\Flysystem\Filesystem($this->adapter);     
    }

    public function listFiles ($path, $recursive){
        
        return $listing = $this->filesystem->listContents($path, $recursive);
        
            /** @var \League\Flysystem\StorageAttributes $item */
    }


}