<?php
class AwsS3Client {
    private $accessKey;
    private $secretKey;
    private $region;
    private $bucket;
    private $basePrefix;

    public function __construct($accessKey, $secretKey, $region, $bucket, $basePrefix = 'Files/'){
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->region = $region;
        $this->bucket = $bucket;
        $this->basePrefix = trim($basePrefix, '/').'/';
    }

    public function buildPublicUrl($key){
        $key = ltrim($key, '/');
        return 'https://'.$this->bucket.'.s3.'.$this->region.'.amazonaws.com/'.$key;
    }

    public function putObject($key, $body, $contentType = 'application/octet-stream', $aclPublic = true){
        $key = ltrim($key, '/');
        // Prepend base prefix
        $fullKey = $this->basePrefix.$key;

        $service = 's3';
        $host = $this->bucket.'.s3.'.$this->region.'.amazonaws.com';
        $uri = '/'.str_replace('%2F','/',rawurlencode($fullKey));
        $now = gmdate('Ymd\THis\Z');
        $date = gmdate('Ymd');

        $payloadHash = hash('sha256', $body);

        $canonicalHeaders = [
            'content-type' => $contentType,
            'host' => $host,
            'x-amz-content-sha256' => $payloadHash,
            'x-amz-date' => $now,
        ];
        if($aclPublic){
            $canonicalHeaders['x-amz-acl'] = 'public-read';
        }

        // Build canonical headers string
        $canonicalHeadersStr = '';
        $signedHeaders = array_keys($canonicalHeaders);
        sort($signedHeaders);
        foreach($signedHeaders as $h){
            $canonicalHeadersStr .= strtolower($h).':'.trim($canonicalHeaders[$h])."\n";
        }
        $signedHeadersStr = implode(';', $signedHeaders);

        $canonicalRequest = "PUT\n".
            $uri."\n".
            "\n". // no query string
            $canonicalHeadersStr.
            "\n".
            $signedHeadersStr."\n".
            $payloadHash;

        $algorithm = 'AWS4-HMAC-SHA256';
        $credentialScope = $date.'/'.$this->region.'/'.$service.'/aws4_request';
        $stringToSign = $algorithm."\n".$now."\n".$credentialScope."\n".hash('sha256', $canonicalRequest);

        $signature = $this->sign($stringToSign, $date, $this->region, $service);

        $authorization = $algorithm.' Credential='.$this->accessKey.'/'.$credentialScope.', SignedHeaders='.$signedHeadersStr.', Signature='.$signature;

        // Build headers
        $headers = [
            'Authorization: '.$authorization,
            'Content-Type: '.$contentType,
            'Host: '.$host,
            'x-amz-content-sha256: '.$payloadHash,
            'x-amz-date: '.$now,
        ];
        if($aclPublic){
            $headers[] = 'x-amz-acl: public-read';
        }

        // Execute request
        $url = 'https://'.$host.$uri;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $rawHeaders = substr($response, 0, $headerSize);
        $respBody = substr($response, $headerSize);
        curl_close($ch);

        if($err){
            return [ 'ok' => false, 'error' => $err, 'status' => 0 ];
        }
        // Handle wrong region redirect: retry once with x-amz-bucket-region
        if(($status == 301 || $status == 400) && stripos($rawHeaders, 'x-amz-bucket-region') !== false){
            if(preg_match('/x-amz-bucket-region:\s*([^\r\n]+)/i', $rawHeaders, $m)){
                $newRegion = trim($m[1]);
                if(!empty($newRegion) && strtolower($newRegion) !== strtolower($this->region)){
                    $this->region = $newRegion;
                    return $this->putObject($key, $body, $contentType, $aclPublic);
                }
            }
        }
        if($status >= 200 && $status < 300){
            return [ 'ok' => true, 'key' => $fullKey, 'status' => $status, 'url' => $this->buildPublicUrl($fullKey) ];
        }
        return [ 'ok' => false, 'status' => $status, 'response' => $respBody ];
    }

    private function sign($stringToSign, $date, $region, $service){
        $kSecret = 'AWS4'.$this->secretKey;
        $kDate = hash_hmac('sha256', $date, $kSecret, true);
        $kRegion = hash_hmac('sha256', $region, $kDate, true);
        $kService = hash_hmac('sha256', $service, $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
        return hash_hmac('sha256', $stringToSign, $kSigning);
    }
}
?>