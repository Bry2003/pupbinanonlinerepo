<?php
require_once __DIR__.'/../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class AwsSdkS3 {
    private $client;
    private $bucket;
    private $basePrefix;
    private $region;

    public function __construct($accessKey, $secretKey, $region, $bucket, $basePrefix = 'Files/') {
        $this->bucket = $bucket;
        $this->basePrefix = trim($basePrefix, '/').'/';
        $this->region = strtolower($region);
        $this->client = new S3Client([
            'version' => 'latest',
            'region' => $this->region,
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey,
            ],
            'http' => ['verify' => true],
            'suppress_php_deprecation_warning' => true
        ]);

        // Auto-detect and correct bucket region to avoid PermanentRedirect
        try {
            $probe = new S3Client([
                'version' => 'latest',
                'region' => 'us-east-1',
                'credentials' => [
                    'key' => $accessKey,
                    'secret' => $secretKey,
                ],
                'http' => ['verify' => true],
                'suppress_php_deprecation_warning' => true
            ]);
            $loc = $probe->getBucketLocation(['Bucket' => $this->bucket]);
            $detected = $loc->get('LocationConstraint');
            if(!$detected) { $detected = 'us-east-1'; }
            if($detected === 'EU') { $detected = 'eu-west-1'; }
            $detected = strtolower($detected);
            if($detected !== $this->region){
                $this->region = $detected;
                $this->client = new S3Client([
                    'version' => 'latest',
                    'region' => $this->region,
                    'credentials' => [
                        'key' => $accessKey,
                        'secret' => $secretKey,
                    ],
                    'http' => ['verify' => true],
                    'suppress_php_deprecation_warning' => true
                ]);
            }
        } catch (\Exception $e) {
            // Swallow detection errors; will rely on provided region.
        }
    }

    public function putObject($key, $body, $contentType = null, $makePublic = true) {
        $fullKey = $this->basePrefix.ltrim($key, '/');
        $params = [
            'Bucket' => $this->bucket,
            'Key' => $fullKey,
            'Body' => $body,
        ];
        if ($contentType) $params['ContentType'] = $contentType;
        if ($makePublic) $params['ACL'] = 'public-read';
        try {
            $result = $this->client->putObject($params);
            return [
                'ok' => true,
                'status' => 200,
                'url' => $this->buildPublicUrl($fullKey)
            ];
        } catch (AwsException $e) {
            // If region mismatch is suspected, try one more time after detection
            $msg = $e->getAwsErrorMessage() ?: $e->getMessage();
            // If bucket blocks public ACLs, retry without ACL
            if($makePublic && (
                stripos($msg, 'BlockPublicAcls') !== false ||
                stripos($msg, 'public ACLs are prevented') !== false ||
                stripos($msg, 'does not allow ACLs') !== false
            )){
                try {
                    unset($params['ACL']);
                    $result = $this->client->putObject($params);
                    return [
                        'ok' => true,
                        'status' => 200,
                        'url' => $this->buildPublicUrl($fullKey)
                    ];
                } catch (AwsException $e3) {
                    $msg = $e3->getAwsErrorMessage() ?: $e3->getMessage();
                }
            }
            if(stripos($msg, 'Permanent redirect') !== false || stripos($msg, 'wrong region') !== false){
                try {
                    $loc = $this->client->getBucketLocation(['Bucket' => $this->bucket]);
                    $detected = $loc->get('LocationConstraint');
                    if(!$detected) { $detected = 'us-east-1'; }
                    if($detected === 'EU') { $detected = 'eu-west-1'; }
                    $detected = strtolower($detected);
                    if($detected !== $this->region){
                        $this->region = $detected;
                        $this->client = new S3Client([
                            'version' => 'latest',
                            'region' => $this->region,
                            'credentials' => $this->client->getCredentials(),
                            'http' => ['verify' => true],
                            'suppress_php_deprecation_warning' => true
                        ]);
                        $result = $this->client->putObject($params);
                        return [
                            'ok' => true,
                            'status' => 200,
                            'url' => $this->buildPublicUrl($fullKey)
                        ];
                    }
                } catch (\Exception $e2) {
                    // ignore and fall through
                }
            }
            return [
                'ok' => false,
                'status' => 0,
                'error' => $msg
            ];
        }
    }

    public function buildPublicUrl($key){
        $key = ltrim($key, '/');
        return 'https://'.$this->bucket.'.s3.'.strtolower($this->region).'.amazonaws.com/'.$key;
    }

    // Generate a presigned GET URL for an object key. $expires can be
    // a string like "+15 minutes" or an integer number of seconds.
    public function getPresignedUrl($key, $expires = '+15 minutes'){
        $fullKey = $this->basePrefix.ltrim($key, '/');
        try {
            $cmd = $this->client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key' => $fullKey
            ]);
            // Allow both string durations and integer seconds
            $expiry = $expires;
            if(is_int($expires)){
                $expiry = (new \DateTimeImmutable('@'.(time()+$expires)))->setTimezone(new \DateTimeZone('UTC'));
            }
            $request = $this->client->createPresignedRequest($cmd, $expiry);
            return (string)$request->getUri();
        } catch (\Exception $e){
            return $this->buildPublicUrl($fullKey);
        }
    }
}
?>