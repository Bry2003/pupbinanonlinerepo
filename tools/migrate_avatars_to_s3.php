<?php
// Migrate existing local avatar file references in `users` and `student_list` tables to AWS S3.
// Usage (from project root):
//   php tools/migrate_avatars_to_s3.php            # run migration
//   php tools/migrate_avatars_to_s3.php dry=1      # dry-run preview

require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../libs/AwsSdkS3.php');

function log_line($msg){
    echo date('c') . ' ' . $msg . "\n";
}

function is_s3_url($url){
    if(!preg_match('/^https?:\/\//i', $url)) return false;
    if(!defined('AWS_S3_BUCKET') || !AWS_S3_BUCKET) return false;
    $bucket = preg_quote(AWS_S3_BUCKET, '/');
    $region = defined('AWS_S3_REGION') ? preg_quote(strtolower(AWS_S3_REGION), '/') : '([a-z0-9\-]+)';
    $patterns = [
        '/https?:\/'.$bucket.'\.s3\.'.$region.'\.amazonaws\.com\//i',
        '/https?:\/'.$bucket.'\.s3\.amazonaws\.com\//i',
        '/https?:\/s3\.'.$region.'\.amazonaws\.com\/'.$bucket.'\//i',
        '/https?:\/s3\.amazonaws\.com\/'.$bucket.'\//i',
        '/https?:\/'.$bucket.'\.s3\-'.$region.'\.amazonaws\.com\//i',
    ];
    foreach($patterns as $p){
        if(preg_match($p, $url)) return true;
    }
    return false;
}

function migrate_table_avatars(mysqli $conn, AwsSdkS3 $sdk, $table, $idColumn, $who){
    $migrated = 0; $skipped = 0; $errors = 0;
    $rs = $conn->query("SELECT `{$idColumn}` AS id, `avatar` FROM `{$table}`");
    if(!$rs){
        log_line("ERROR: query failed for {$table}: " . $conn->error);
        return [0,0,1];
    }
    while($row = $rs->fetch_assoc()){
        $id = intval($row['id']);
        $av = $row['avatar'];
        if(!$av || $av==='0' || $av==='1') { $skipped++; continue; }
        $base = explode('?', $av)[0];
        // Skip if already an S3 URL
        if(is_s3_url($base)) { $skipped++; continue; }
        // Expect local file under base_app
        $localPath = base_app . $base;
        if(!is_file($localPath)) { log_line("WARN: missing file for {$who} {$id}: {$base}"); $skipped++; continue; }
        $mime = @mime_content_type($localPath) ?: 'application/octet-stream';
        $ext = '.bin';
        if($mime === 'image/png') $ext = '.png';
        else if($mime === 'image/jpeg') $ext = '.jpg';
        $key = AWS_S3_AVATAR_SUBPATH . $who . '-' . $id . '-migrated-' . time() . '-' . bin2hex(random_bytes(3)) . $ext;
        try{
            $data = file_get_contents($localPath);
            $res = $sdk->putObject($key, $data, $mime, true);
            if(isset($res['ok']) && $res['ok'] && isset($res['url'])){
                $url = $res['url'];
                $val = $conn->real_escape_string($url.'?v='.(time()));
                $upd = $conn->query("UPDATE `{$table}` SET `avatar` = '{$val}' WHERE `{$idColumn}` = '{$id}'");
                if($upd){
                    $migrated++;
                    log_line("OK: {$who} {$id} => {$url}");
                }else{
                    $errors++;
                    log_line("ERROR: DB update failed for {$who} {$id}: " . $conn->error);
                }
            }else{
                $errors++;
                $err = is_array($res) && isset($res['error']) ? $res['error'] : 'unknown';
                log_line("ERROR: S3 upload failed for {$who} {$id}: {$err}");
            }
        }catch(Exception $e){
            $errors++;
            log_line("ERROR: {$who} {$id} exception: " . $e->getMessage());
        }
    }
    return [$migrated, $skipped, $errors];
}

$dry = false;
foreach($argv as $arg){
    if(strpos($arg, 'dry=') === 0){
        $dry = trim(substr($arg, 4)) === '1';
    }
}

if(!defined('AWS_S3_ENABLE') || !AWS_S3_ENABLE){
    log_line('ERROR: AWS_S3_ENABLE is false; enable S3 in initialize.php');
    exit(1);
}
if(!AWS_ACCESS_KEY_ID || !AWS_SECRET_ACCESS_KEY || !AWS_S3_BUCKET || !AWS_S3_REGION){
    log_line('ERROR: Missing AWS credentials or bucket/region');
    exit(1);
}

$sdk = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, defined('AWS_S3_BASE_PREFIX') ? AWS_S3_BASE_PREFIX : '');

if($dry){
    log_line('Dry-run: listing candidates that would be migrated...');
    foreach([['users','id','user'],['student_list','id','student']] as $spec){
        list($table,$idCol,$who) = $spec;
        $rs = $conn->query("SELECT `{$idCol}` AS id, `avatar` FROM `{$table}`");
        $count = 0; $already = 0; $missing = 0; $numeric = 0;
        while($row = $rs->fetch_assoc()){
            $count++;
            $av = $row['avatar'];
            if(!$av || $av==='0' || $av==='1'){ $numeric++; continue; }
            $base = explode('?', $av)[0];
            if(is_s3_url($base)) { $already++; continue; }
            if(!is_file(base_app.$base)) { $missing++; }
        }
        log_line("{$table}: total={$count} already_s3={$already} missing_local={$missing} numeric_or_empty={$numeric}");
    }
    exit(0);
}

log_line('Starting avatar migration to S3...');
list($uMig,$uSkip,$uErr) = migrate_table_avatars($conn, $sdk, 'users', 'id', 'user');
list($sMig,$sSkip,$sErr) = migrate_table_avatars($conn, $sdk, 'student_list', 'id', 'student');

log_line("Summary: users migrated={$uMig} skipped={$uSkip} errors={$uErr}");
log_line("Summary: students migrated={$sMig} skipped={$sSkip} errors={$sErr}");
log_line('Done.');

?>