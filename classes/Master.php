<?php
// Use absolute paths to avoid CLI include path issues
require_once(dirname(__DIR__).'/config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function export_archives_excel(){
		$program_id = isset($_GET['program_id']) ? $_GET['program_id'] : '';
		$year = isset($_GET['year']) ? $_GET['year'] : '';
		$where = [];
		$join = " LEFT JOIN student_list s ON a.student_id = s.id ";
		if(!empty($program_id)){
			$pid = $this->conn->real_escape_string($program_id);
			$where[] = " COALESCE(NULLIF(a.curriculum_id,0), s.department_id) = '{$pid}' ";
		}
		if(!empty($year)){
			$yr = $this->conn->real_escape_string($year);
			$where[] = " YEAR(a.date_created) = '{$yr}' ";
		}
		if($this->settings->userdata('login_type') == 2){
			$aid = $this->conn->real_escape_string($this->settings->userdata('id'));
			$where[] = " s.adviser_id = '{$aid}' ";
		}
		$wsql = '';
		if(!empty($where)) $wsql = ' WHERE '.implode(' AND ', $where);
		$sql = "SELECT a.*, d.name AS program_name, d.id AS program_id,
				sd.name AS student_program, sd.id AS student_program_id,
				CONCAT(u.firstname, ' ', u.lastname) AS adviser_name
				FROM archive_list a
				LEFT JOIN department_list d ON a.curriculum_id = d.id
				{$join}
				LEFT JOIN department_list sd ON s.department_id = sd.id
				LEFT JOIN users u ON s.adviser_id = u.id
				{$wsql}
				ORDER BY a.date_created DESC, a.title DESC";
		$q = $this->conn->query($sql);
		$fnParts = [];
		if(!empty($program_id)){
			$pname = '';
			$dp = $this->conn->query("SELECT name FROM department_list WHERE id = '{$program_id}'");
			if($dp && $dp->num_rows) { $pname = $dp->fetch_assoc()['name']; }
			$fnParts[] = preg_replace('/[^a-z0-9]+/i','_', $pname ?: 'program_'.$program_id);
		}
		if(!empty($year)) $fnParts[] = $year;
		$fname = 'archives'.(!empty($fnParts) ? '-'.implode('-', $fnParts) : '').'-'.date('Ymd-His').'.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="'.$fname.'"');
		$out = fopen('php://output', 'w');
		fputcsv($out, ['Date Created','Year','Archive Code','Project Title','Program','Adviser','Status','Document Path']);
		if($q){
			while($r = $q->fetch_assoc()){
				$prog = !empty($r['program_name']) ? $r['program_name'] : (!empty($r['student_program']) ? $r['student_program'] : '');
				$adviser = !empty($r['adviser_name']) ? $r['adviser_name'] : '';
				$st = $r['status'];
				if($st === '1') $stLabel = 'Published';
				elseif($st === '2') $stLabel = 'Adviser Verified';
				else $stLabel = 'Not Published';
				$doc = isset($r['document_path']) ? explode('?',$r['document_path'])[0] : '';
				fputcsv($out, [
					isset($r['date_created']) ? date('Y-m-d H:i', strtotime($r['date_created'])) : '',
					isset($r['date_created']) ? date('Y', strtotime($r['date_created'])) : ($r['year'] ?? ''),
					$r['archive_code'] ?? '',
					isset($r['title']) ? ucwords($r['title']) : '',
					$prog ? ucwords($prog) : '',
					$adviser ? ucwords($adviser) : '',
					$stLabel,
					$doc
				]);
			}
		}
		fclose($out);
		exit;
	}
	function export_archives_xlsx(){
		$program_id = isset($_GET['program_id']) ? $_GET['program_id'] : '';
		$year = isset($_GET['year']) ? $_GET['year'] : '';
		$where = [];
		$join = " LEFT JOIN student_list s ON a.student_id = s.id ";
		if(!empty($program_id)){
			$pid = $this->conn->real_escape_string($program_id);
			$where[] = " COALESCE(NULLIF(a.curriculum_id,0), s.department_id) = '{$pid}' ";
		}
		if(!empty($year)){
			$yr = $this->conn->real_escape_string($year);
			$where[] = " YEAR(a.date_created) = '{$yr}' ";
		}
		if($this->settings->userdata('login_type') == 2){
			$aid = $this->conn->real_escape_string($this->settings->userdata('id'));
			$where[] = " s.adviser_id = '{$aid}' ";
		}
		$wsql = '';
		if(!empty($where)) $wsql = ' WHERE '.implode(' AND ', $where);
		$sql = "SELECT a.*, d.name AS program_name, d.id AS program_id,
				sd.name AS student_program, sd.id AS student_program_id,
				CONCAT(u.firstname, ' ', u.lastname) AS adviser_name
				FROM archive_list a
				LEFT JOIN department_list d ON a.curriculum_id = d.id
				{$join}
				LEFT JOIN department_list sd ON s.department_id = sd.id
				LEFT JOIN users u ON s.adviser_id = u.id
				{$wsql}
				ORDER BY a.date_created DESC, a.title DESC";
		$q = $this->conn->query($sql);
		$rows = [];
		$headers = ['Date Created','Year','Archive Code','Project Title','Program','Adviser','Status','Document Path'];
		$rows[] = $headers;
		if($q){
			while($r = $q->fetch_assoc()){
				$prog = !empty($r['program_name']) ? $r['program_name'] : (!empty($r['student_program']) ? $r['student_program'] : '');
				$adviser = !empty($r['adviser_name']) ? $r['adviser_name'] : '';
				$st = $r['status'];
				if($st === '1') $stLabel = 'Published';
				elseif($st === '2') $stLabel = 'Adviser Verified';
				else $stLabel = 'Not Published';
				$doc = isset($r['document_path']) ? explode('?',$r['document_path'])[0] : '';
				$rows[] = [
					isset($r['date_created']) ? date('Y-m-d H:i', strtotime($r['date_created'])) : '',
					isset($r['date_created']) ? date('Y', strtotime($r['date_created'])) : ($r['year'] ?? ''),
					$r['archive_code'] ?? '',
					isset($r['title']) ? ucwords($r['title']) : '',
					$prog ? ucwords($prog) : '',
					$adviser ? ucwords($adviser) : '',
					$stLabel,
					$doc
				];
			}
		}
		$fnParts = [];
		if(!empty($program_id)){
			$pname = '';
			$dp = $this->conn->query("SELECT name FROM department_list WHERE id = '{$program_id}'");
			if($dp && $dp->num_rows) { $pname = $dp->fetch_assoc()['name']; }
			$fnParts[] = preg_replace('/[^a-z0-9]+/i','_', $pname ?: 'program_'.$program_id);
		}
		if(!empty($year)) $fnParts[] = $year;
		$fname = 'archives'.(!empty($fnParts) ? '-'.implode('-', $fnParts) : '').'-'.date('Ymd-His').'.xlsx';
		if(!class_exists('ZipArchive')){
			// Fallback to CSV if ZipArchive not available
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="'.str_replace('.xlsx','.csv',$fname).'"');
			$out = fopen('php://output', 'w');
			foreach($rows as $r) fputcsv($out, $r);
			fclose($out);
			exit;
		}
		$zip = new ZipArchive();
		$tmp = tempnam(sys_get_temp_dir(), 'xlsx');
		$zip->open($tmp, ZipArchive::OVERWRITE);
		// Build shared strings
		$strings = [];
		$index = [];
		$addString = function($s) use (&$strings, &$index){
			$key = (string)$s;
			if(!array_key_exists($key, $index)){
				$index[$key] = count($strings);
				$strings[] = $key;
			}
			return $index[$key];
		};
		foreach($rows as $r){
			foreach($r as $c){ $addString($c); }
		}
		$sst = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($strings).'" uniqueCount="'.count($strings).'">';
		foreach($strings as $s){
			$sst .= '<si><t>'.htmlspecialchars($s, ENT_XML1|ENT_COMPAT, 'UTF-8').'</t></si>';
		}
		$sst .= '</sst>';
		$zip->addFromString('xl/sharedStrings.xml', $sst);
		// Build sheet
		$sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
			.'<sheetData>';
		$colLetter = function($i){
			$letters = '';
			$i = (int)$i;
			while($i >= 0){
				$letters = chr(65 + ($i % 26)) . $letters;
				$i = intdiv($i, 26) - 1;
			}
			return $letters;
		};
		for($r = 0; $r < count($rows); $r++){
			$sheet .= '<row r="'.($r+1).'">';
			for($c = 0; $c < count($rows[$r]); $c++){
				$val = (string)$rows[$r][$c];
				$idx = $index[$val];
				$sheet .= '<c r="'.$colLetter($c).($r+1).'" t="s"><v>'.$idx.'</v></c>';
			}
			$sheet .= '</row>';
		}
		$sheet .= '</sheetData></worksheet>';
		$zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
		// Workbook and relationships
		$wbrels = '<?xml version="1.0" encoding="UTF-8"?>'
			.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
			.'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
			.'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
			.'</Relationships>';
		$zip->addFromString('xl/_rels/workbook.xml.rels', $wbrels);
		$wb = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
			.'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
			.'<sheets><sheet name="Archives" sheetId="1" r:id="rId1"/></sheets>'
			.'</workbook>';
		$zip->addFromString('xl/workbook.xml', $wb);
		// Root rels
		$rels = '<?xml version="1.0" encoding="UTF-8"?>'
			.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
			.'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
			.'</Relationships>';
		$zip->addFromString('_rels/.rels', $rels);
		// Content types
		$ct = '<?xml version="1.0" encoding="UTF-8"?>'
			.'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
			.'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
			.'<Default Extension="xml" ContentType="application/xml"/>'
			.'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
			.'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
			.'<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
			.'</Types>';
		$zip->addFromString('[Content_Types].xml', $ct);
		$zip->close();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="'.$fname.'"');
		readfile($tmp);
		@unlink($tmp);
		exit;
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_department(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `department_list` set {$data} ";
		}else{
			$sql = "UPDATE `department_list` set {$data} where id = '{$id}' ";
		}
		$check = $this->conn->query("SELECT * FROM `department_list` where `name`='{$name}' ".($id > 0 ? " and id != '{$id}'" : ""))->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Department Name Already Exists.";
		}else{
			$save = $this->conn->query($sql);
			if($save){
				$rid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = "Department details successfully added.";
				else
					$resp['msg'] = "Department details has been updated successfully.";
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occured.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		if($resp['status'] =='success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_department(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `department_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Department has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_curriculum(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `curriculum_list` set {$data} ";
		}else{
			$sql = "UPDATE `curriculum_list` set {$data} where id = '{$id}' ";
		}
		$check = $this->conn->query("SELECT * FROM `curriculum_list` where `name`='{$name}' and `department_id` = '{department_id}' ".($id > 0 ? " and id != '{$id}'" : ""))->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Curriculum Name Already Exists.";
		}else{
			$save = $this->conn->query($sql);
			if($save){
				$rid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = "Curriculum details successfully added.";
				else
					$resp['msg'] = "Curriculum details has been updated successfully.";
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occured.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		if($resp['status'] =='success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_curriculum(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `curriculum_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Curriculum has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
    function save_id_registry(){
        extract($_POST);
        if(!isset($name) || trim($name) === ''){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Name is required.';
            return json_encode($resp);
        }
        $tbl = $this->conn->query("SHOW TABLES LIKE 'id_registry'");
        if(!$tbl || $tbl->num_rows == 0){
            $this->conn->query("CREATE TABLE `id_registry` (
                `id` int(30) NOT NULL AUTO_INCREMENT,
                `id_number` varchar(50) NOT NULL,
                `name` text NULL,
                `status` tinyint(1) NOT NULL DEFAULT 1,
                `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_id_number` (`id_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
        $cols = [];
        $resCols = $this->conn->query("SHOW COLUMNS FROM `id_registry`");
        if($resCols){
            while($r = $resCols->fetch_assoc()){ $cols[$r['Field']] = true; }
        }
        $data = "";
        foreach($_POST as $k=>$v){
            if($k !== 'id' && isset($cols[$k])){
                if(!is_numeric($v)) $v = $this->conn->real_escape_string($v);
                if(!empty($data)) $data .= ",";
                $data .= "`{$k}`='{$v}'";
            }
        }
        if(strpos($data,'`status`') === false){
            if(!empty($data)) $data .= ",";
            $data .= "`status`='1'";
        }
        if(empty($id)){
            $check = $this->conn->query("SELECT * FROM `id_registry` WHERE `id_number`='{$id_number}'")->num_rows;
            if($check > 0){
                $resp['status'] = 'failed';
                $resp['msg'] = 'ID Number already exists in registry.';
                return json_encode($resp);
            }
            $sql = "INSERT INTO `id_registry` SET {$data}";
        }else{
            $check = $this->conn->query("SELECT * FROM `id_registry` WHERE `id_number`='{$id_number}' AND id != '{$id}'")->num_rows;
            if($check > 0){
                $resp['status'] = 'failed';
                $resp['msg'] = 'ID Number already exists in registry.';
                return json_encode($resp);
            }
            $sql = "UPDATE `id_registry` SET {$data} WHERE id = '{$id}'";
        }
        $save = $this->conn->query($sql);
        if($save){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success', empty($id) ? 'ID added to registry.' : 'ID registry entry updated.');
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'An error occured.';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }
    function delete_id_registry(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `id_registry` WHERE id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success','ID removed from registry.');
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }
	function import_id_registry(){
        $resp = [ 'status' => 'failed' ];
        // Ensure table exists (without department_id column)
        $tbl = $this->conn->query("SHOW TABLES LIKE 'id_registry'");
        if(!$tbl || $tbl->num_rows == 0){
            $this->conn->query("CREATE TABLE `id_registry` (
                `id` int(30) NOT NULL AUTO_INCREMENT,
                `id_number` varchar(50) NOT NULL,
                `name` text NULL,
                `status` tinyint(1) NOT NULL DEFAULT 1,
                `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_id_number` (`id_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
        if(!isset($_FILES['import_file']) || empty($_FILES['import_file']['tmp_name'])){
            $resp['msg'] = 'No file uploaded.';
            $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
            @file_put_contents($logDir.'/import.log', date('c')." ID_IMPORT_FAIL name=".($_FILES['import_file']['name'] ?? 'unknown')." reason=no_file\n", FILE_APPEND);
            return json_encode($resp);
        }
        $tmp = $_FILES['import_file']['tmp_name'];
        $name = $_FILES['import_file']['name'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $type = function_exists('mime_content_type') ? @mime_content_type($tmp) : '';
        $hasHeader = false;

        // Upload to S3 if enabled
        $uploadedUrl = '';
        if(defined('AWS_S3_ENABLE') && AWS_S3_ENABLE){
            $keyPrefix = 'id_registry/imports/';
            $ct = 'application/octet-stream';
            if($ext === 'csv') $ct = 'text/csv';
            if($ext === 'xlsx') $ct = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            if($ext === 'docx') $ct = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            $content = file_get_contents($tmp);
            try{
                $useSdk = (defined('AWS_S3_USE_SDK') && AWS_S3_USE_SDK);
                if($useSdk){
                    require_once dirname(__DIR__).'/libs/AwsSdkS3.php';
                    $client = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                    $key = $keyPrefix.date('Ymd-His').'-'.preg_replace('/[^a-zA-Z0-9_.-]/','_', $name);
                    $put = $client->putObject($key, $content, $ct, false);
                    if($put && isset($put['ok']) && $put['ok']) $uploadedUrl = $put['url'];
                }else{
                    require_once dirname(__DIR__).'/libs/AwsS3Client.php';
                    $client = new AwsS3Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                    $key = $keyPrefix.date('Ymd-His').'-'.preg_replace('/[^a-zA-Z0-9_.-]/','_', $name);
                    $put = $client->putObject($key, $content, $ct, false);
                    if($put && isset($put['ok']) && $put['ok']) $uploadedUrl = $put['url'];
                }
            }catch(Exception $e){
                // ignore upload failure, continue import
                $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
                @file_put_contents($logDir.'/s3_upload.log', date('c')." IMPORT FAIL name={$name} err=".$e->getMessage()."\n", FILE_APPEND);
            }
        }

        // Parse rows
        $rows = [];
        if($ext === 'csv'){
            $fh = fopen($tmp, 'r');
            if(!$fh){ $resp['msg'] = 'Unable to read CSV.'; $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true); @file_put_contents($logDir.'/import.log', date('c')." ID_IMPORT_FAIL name={$name} ext={$ext} reason=csv_open\n", FILE_APPEND); return json_encode($resp); }
            $header = [];
            while(($data = fgetcsv($fh)) !== false){
                if(empty($data)) continue;
                if(empty($header)){
                    if($hasHeader){
                        $header = array_map(function($h){ return strtolower(trim(preg_replace('/[^a-z0-9_]+/i','_', $h))); }, $data);
                        continue;
                    }else{
                        $header = [];
                    }
                }
                $rows[] = $data;
            }
            fclose($fh);
            // Map rows to associative if header available
            if(!empty($header)){
                $rows = array_map(function($r) use($header){
                    $assoc = [];
                    foreach($header as $i=>$k){ $assoc[$k] = isset($r[$i]) ? $r[$i] : ''; }
                    return $assoc;
                }, $rows);
            }
        }elseif($ext === 'docx'){
            if(!class_exists('ZipArchive')){ $resp['msg'] = 'DOCX import requires ZipArchive PHP extension.'; $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true); @file_put_contents($logDir.'/import.log', date('c')." ID_IMPORT_FAIL name={$name} ext={$ext} reason=docx_zip_missing\n", FILE_APPEND); return json_encode($resp); }
            $zip = new ZipArchive();
            if($zip->open($tmp) === true){
                $xml = $zip->getFromName('word/document.xml');
                $zip->close();
                if($xml){
                    if(!function_exists('simplexml_load_string')){ $resp['msg'] = 'DOCX import requires SimpleXML PHP extension.'; $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true); @file_put_contents($logDir.'/import.log', date('c')." ID_IMPORT_FAIL name={$name} ext={$ext} reason=docx_simplexml_missing\n", FILE_APPEND); return json_encode($resp); }
                    $text = preg_replace('/<w:.*?>/','', $xml);
                    $text = strip_tags($text);
                    $lines = preg_split('/\r?\n+/',$text);
                    foreach($lines as $ln){
                        $ln = trim($ln);
                        if(!$ln) continue;
                        $parts = array_map('trim', explode(',', $ln));
                        $rows[] = $parts; // expect [id_number, name, status(optional)]
                    }
                }
            }else{
                $resp['msg'] = 'Unable to open DOCX.'; $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true); @file_put_contents($logDir.'/import.log', date('c')." ID_IMPORT_FAIL name={$name} ext={$ext} reason=docx_open\n", FILE_APPEND); return json_encode($resp);
            }
        }elseif($ext === 'xlsx'){
            if(!class_exists('ZipArchive')){ $resp['msg'] = 'XLSX import requires ZipArchive PHP extension.'; $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true); @file_put_contents($logDir.'/import.log', date('c')." ID_IMPORT_FAIL name={$name} ext={$ext} reason=xlsx_zip_missing\n", FILE_APPEND); return json_encode($resp); }
            $zip = new ZipArchive();
            if($zip->open($tmp) === true){
                $shared = [];
                $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
                if($sharedXml){
                    if(!function_exists('simplexml_load_string')){ $resp['msg'] = 'XLSX import requires SimpleXML PHP extension.'; $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true); @file_put_contents($logDir.'/import.log', date('c')." ID_IMPORT_FAIL name={$name} ext={$ext} reason=xlsx_simplexml_missing\n", FILE_APPEND); return json_encode($resp); }
                    $sx = simplexml_load_string($sharedXml);
                    foreach($sx->si as $si){
                        $t = '';
                        if(isset($si->t)) $t = (string)$si->t;
                        else{ foreach($si->xpath('.//t') as $tt){ $t .= (string)$tt; } }
                        $shared[] = $t;
                    }
                }
                $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
                if(!$sheet){ $sheet = $zip->getFromName('xl/worksheets/sheet2.xml'); }
                if(!$sheet){ $sheet = $zip->getFromName('xl/worksheets/sheet.xml'); }
                if($sheet){
                    if(!function_exists('simplexml_load_string')){ $resp['msg'] = 'XLSX import requires SimpleXML PHP extension.'; $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true); @file_put_contents($logDir.'/import.log', date('c')." ID_IMPORT_FAIL name={$name} ext={$ext} reason=xlsx_simplexml_missing\n", FILE_APPEND); return json_encode($resp); }
                    $sx = simplexml_load_string($sheet);
                    foreach($sx->sheetData->row as $r){
                        $rowVals = [];
                        foreach($r->c as $c){
                            $tattr = (string)$c['t'];
                            $v = isset($c->v) ? (string)$c->v : '';
                            if($tattr === 's' && $v !== ''){ $v = isset($shared[(int)$v]) ? $shared[(int)$v] : $v; }
                            $rowVals[] = $v;
                        }
                        $rows[] = $rowVals;
                    }
                }
                $zip->close();
                if($hasHeader && !empty($rows)){
                    $header = array_map(function($h){ return strtolower(trim(preg_replace('/[^a-z0-9_]+/i','_', $h))); }, $rows[0]);
                    $rows = array_slice($rows,1);
                    $rows = array_map(function($r) use($header){
                        $assoc = [];
                        foreach($header as $i=>$k){ $assoc[$k] = isset($r[$i]) ? $r[$i] : ''; }
                        return $assoc;
                    }, $rows);
                }
            }else{
                $resp['msg'] = 'Unable to open XLSX.'; $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true); @file_put_contents($logDir.'/import.log', date('c')." ID_IMPORT_FAIL name={$name} ext={$ext} reason=xlsx_open\n", FILE_APPEND); return json_encode($resp);
            }
        }else{
            $resp['msg'] = 'Unsupported file type.';
            $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
            @file_put_contents($logDir.'/import.log', date('c')." ID_IMPORT_FAIL name={$name} ext={$ext} reason=unsupported_type\n", FILE_APPEND);
            return json_encode($resp);
        }

        // Insert rows
        $imported = 0; $duplicates = 0; $skipped = 0;
        foreach($rows as $row){
            $idn = ''; $nm = ''; $st = 1;
            if(is_array($row) && array_keys($row) !== range(0, count($row)-1)){
                $idn = $row['id_number'] ?? ($row['id'] ?? ($row['idno'] ?? ''));
                $nm = $row['name'] ?? ($row['fullname'] ?? '');
                $st = isset($row['status']) ? (int)$row['status'] : 1;
            }else{
                $idn = isset($row[0]) ? trim($row[0]) : '';
                $nm = isset($row[1]) ? trim($row[1]) : '';
                $st = isset($row[2]) ? (int)$row[2] : 1;
            }
            if(!$idn){ $skipped++; continue; }
            $idn = $this->conn->real_escape_string($idn);
            $nm = $this->conn->real_escape_string($nm);
            $chk = $this->conn->query("SELECT id FROM `id_registry` WHERE `id_number`='{$idn}'");
            if($chk && $chk->num_rows > 0){ $duplicates++; continue; }
            $fields = "`id_number`='{$idn}', `name`='{$nm}', `status`='{$st}'";
            $ins = $this->conn->query("INSERT INTO `id_registry` SET {$fields}");
            if($ins){ $imported++; } else { $skipped++; }
        }
        $resp['status'] = 'success';
        $resp['imported'] = $imported;
        $resp['duplicates'] = $duplicates;
        $resp['skipped'] = $skipped;
        if(!empty($uploadedUrl)) $resp['uploaded_url'] = $uploadedUrl;
        // Log summary to assist debugging
        $logDir = base_app.'uploads/logs'; if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
        @file_put_contents($logDir.'/import.log', date('c')." ID_IMPORT name={$name} ext={$ext} imported={$imported} duplicates={$duplicates} skipped={$skipped} url=".($uploadedUrl ?: 'none')."\n", FILE_APPEND);
        $this->settings->set_flashdata('success',"Imported {$imported} IDs. Duplicates: {$duplicates}. Skipped: {$skipped}.");
        return json_encode($resp);
    }
	function save_archive(){
		if(empty($_POST['id'])){
			$pref= date("Ym");
			$code = sprintf("%'.04d",1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `archive_list` where archive_code = '{$pref}{$code}'")->num_rows;
				if($check > 0){
					$code = sprintf("%'.04d",abs($code)+1);
				}else{
					break;
				}
			}
			$_POST['archive_code'] = $pref.$code;
			$_POST['student_id'] = $this->settings->userdata('id');
			$_POST['curriculum_id'] = $this->settings->userdata('department_id');
		}
		if(isset($_POST['program_id']) && !empty($_POST['program_id'])){
			$pid = (int)$_POST['program_id'];
			$chk = $this->conn->query("SELECT id FROM department_list WHERE id = '{$pid}' AND status = 1");
			if($chk && $chk->num_rows > 0){
				$_POST['curriculum_id'] = $pid;
			}
		}
		if((!isset($_POST['curriculum_id']) || empty($_POST['curriculum_id'])) && isset($_POST['student_id']) && !empty($_POST['student_id'])){
			$sc = $this->conn->query("SELECT department_id FROM student_list WHERE id = '{$_POST['student_id']}'");
			if($sc && $sc->num_rows > 0){
				$sr = $sc->fetch_assoc();
				$_POST['curriculum_id'] = $sr['department_id'];
			}
		}
		if(isset($_POST['abstract']))
		$_POST['abstract'] = htmlentities($_POST['abstract']);
		if(isset($_POST['members']))
		$_POST['members'] = htmlentities($_POST['members']);
		extract($_POST);
		$title_chk = isset($_POST['title']) ? trim($_POST['title']) : '';
		if(empty($title_chk)){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Project Title is required.';
			return json_encode($resp);
		}
		$_ttl = $this->conn->real_escape_string($title_chk);
		$dup_q = "SELECT id FROM `archive_list` WHERE LOWER(TRIM(`title`)) = LOWER(TRIM('{$_ttl}')) ".(!empty($id) ? " AND id != '{$id}'" : "");
		$dup_res = $this->conn->query($dup_q);
		if($dup_res && $dup_res->num_rows > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Project title already exists. Please use a unique title.';
			return json_encode($resp);
		}
		$data = "";
        if(isset($_FILES['pdf']) && !empty($_FILES['pdf']['tmp_name'])){
            $type = @mime_content_type($_FILES['pdf']['tmp_name']);
            $ext = strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION));
            // Allow common cases: accurate PDF MIME or generic octet-stream with .pdf extension
            $isPdf = ($type === 'application/pdf') || ($type === 'application/octet-stream' && $ext === 'pdf') || ($ext === 'pdf');
            if(!$isPdf){
                // Log the mismatch for easier debugging
                $logDir = base_app.'uploads/logs';
                if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
                @file_put_contents($logDir.'/s3_upload.log', date('c')." PDF TYPE INVALID name=".$_FILES['pdf']['name']." type=".($type?:'unknown')."\n", FILE_APPEND);
                $resp['status'] = "failed";
                $resp['msg'] = "Invalid Document File Type. Please upload a .pdf file.";
                return json_encode($resp);
            }
        }
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','program_id')) && !is_array($_POST[$k])){
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		// Build and execute save query defensively to avoid empty UPDATE errors
		if(empty($id)){
			$sql = "INSERT INTO `archive_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			if(!empty($data)){
				$sql = "UPDATE `archive_list` set {$data} where id = '{$id}' ";
				$save = $this->conn->query($sql);
			}else{
				// No field changes; treat as success and proceed with potential file uploads
				$save = true;
			}
		}
		if($save){
			$aid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			$resp['id'] = $aid;
			if(empty($id))
				$resp['msg'] = "Archive was successfully submitted";
			else
				$resp['msg'] = "Archive details was updated successfully.";

                if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
                    $fname = 'uploads/banners/archive-'.$aid.'.png';
                    $dir_path = base_app.$fname;
                    $upload = $_FILES['img']['tmp_name'];
                    $type = mime_content_type($upload);
                    $allowed = array('image/png','image/jpeg');
                    if(!in_array($type,$allowed)){
                        $resp['msg'].=" But Image failed to upload due to invalid file type.";
                       }else{
                        $bannerUrl = '';
                        $uploaded_img = null;
                        $localBannerPath = $fname;
                        $hasGd = function_exists('imagecreatetruecolor') && function_exists('imagepng') && function_exists('imagecopyresampled');
                        if($hasGd){
                            $new_height = 720; 
                            $new_width = 1280;  
                            list($width, $height) = getimagesize($upload);
                            $t_image = imagecreatetruecolor($new_width, $new_height);
                            imagealphablending($t_image, false);
                            imagesavealpha($t_image, true);
                            $gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
                            imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                            if($gdImg){
                                // Try AWS S3 first if enabled
                                if(defined('AWS_S3_ENABLE') && AWS_S3_ENABLE && defined('AWS_S3_BUCKET') && AWS_S3_BUCKET && defined('AWS_ACCESS_KEY_ID') && AWS_ACCESS_KEY_ID && defined('AWS_SECRET_ACCESS_KEY') && AWS_SECRET_ACCESS_KEY && defined('AWS_S3_REGION') && AWS_S3_REGION){
                                    if(defined('AWS_S3_USE_SDK') && AWS_S3_USE_SDK){
                                        require_once dirname(__DIR__).'/libs/AwsSdkS3.php';
                                    } else {
                                        require_once dirname(__DIR__).'/libs/AwsS3Client.php';
                                    }
                                    // Capture PNG binary in memory
                                    ob_start();
                                    imagepng($t_image);
                                    $pngData = ob_get_clean();
                                    $key = 'banners/archive-'.$aid.'.png';
                                    if(defined('AWS_S3_USE_SDK') && AWS_S3_USE_SDK){
                                        $client = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                                        // Upload without public ACL; viewing uses presigned URLs
                                        $put = $client->putObject($key, $pngData, 'image/png', false);
                                    } else {
                                        $client = new AwsS3Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                                        $put = $client->putObject($key, $pngData, 'image/png', false);
                                    }
                                    if($put && isset($put['ok']) && $put['ok']){
                                        $bannerUrl = $put['url'];
                                    } else {
                                        // Log error and optionally fail fast
                                        $logDir = base_app.'uploads/logs';
                                        if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
                                        @file_put_contents($logDir.'/s3_upload.log', date('c')." BANNER FAIL aid=$aid status=".($put['status'] ?? 'n/a')." error=".($put['error'] ?? '')."\n", FILE_APPEND);
                                        if(defined('AWS_S3_REQUIRE') && AWS_S3_REQUIRE){
                                            $resp['status'] = 'failed';
                                            $resp['msg'] = 'Failed to upload banner to S3. Please check region/ACL and bucket policy.';
                                        }
                                    }
                                } else if(defined('AWS_S3_ENABLE') && AWS_S3_ENABLE) {
                                    // S3 enabled but missing config vars
                                    $logDir = base_app.'uploads/logs';
                                    if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
                                    $missing = [];
                                    if(!defined('AWS_S3_BUCKET') || !AWS_S3_BUCKET) $missing[] = 'AWS_S3_BUCKET';
                                    if(!defined('AWS_ACCESS_KEY_ID') || !AWS_ACCESS_KEY_ID) $missing[] = 'AWS_ACCESS_KEY_ID';
                                    if(!defined('AWS_SECRET_ACCESS_KEY') || !AWS_SECRET_ACCESS_KEY) $missing[] = 'AWS_SECRET_ACCESS_KEY';
                                    if(!defined('AWS_S3_REGION') || !AWS_S3_REGION) $missing[] = 'AWS_S3_REGION';
                                    @file_put_contents($logDir.'/s3_upload.log', date('c')." BANNER SKIP aid=$aid missing=".implode(',', $missing)."\n", FILE_APPEND);
                                }
                                // Fallback to local storage if S3 disabled or not required
                                if(empty($bannerUrl) && (!defined('AWS_S3_REQUIRE') || !AWS_S3_REQUIRE)){
                                    if(is_file($dir_path)) unlink($dir_path);
                                    $uploaded_img = imagepng($t_image, $dir_path);
                                    $localBannerPath = $fname;
                                }
                                imagedestroy($gdImg);
                                imagedestroy($t_image);
                            }else{
                                $resp['msg'].=" But Image failed to process due to unknown reason.";
                            }
                        } else {
                            // No GD available: upload original image bytes
                            $ext = ($type == 'image/png') ? '.png' : '.jpg';
                            $key = 'banners/archive-'.$aid.$ext;
                            $localBannerPath = 'uploads/banners/archive-'.$aid.$ext;
                            if(defined('AWS_S3_ENABLE') && AWS_S3_ENABLE && defined('AWS_S3_BUCKET') && AWS_S3_BUCKET && defined('AWS_ACCESS_KEY_ID') && AWS_ACCESS_KEY_ID && defined('AWS_SECRET_ACCESS_KEY') && AWS_SECRET_ACCESS_KEY && defined('AWS_S3_REGION') && AWS_S3_REGION){
                                if(defined('AWS_S3_USE_SDK') && AWS_S3_USE_SDK){
                                    require_once dirname(__DIR__).'/libs/AwsSdkS3.php';
                                    $client = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                                    $put = $client->putObject($key, file_get_contents($upload), $type, false);
                                } else {
                                    require_once dirname(__DIR__).'/libs/AwsS3Client.php';
                                    $client = new AwsS3Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                                    $put = $client->putObject($key, file_get_contents($upload), $type, false);
                                }
                                if($put && isset($put['ok']) && $put['ok']){
                                    $bannerUrl = $put['url'];
                                } else {
                                    $logDir = base_app.'uploads/logs';
                                    if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
                                    @file_put_contents($logDir.'/s3_upload.log', date('c')." BANNER FAIL aid=$aid status=".($put['status'] ?? 'n/a')." error=".($put['error'] ?? '')."\n", FILE_APPEND);
                                    if(defined('AWS_S3_REQUIRE') && AWS_S3_REQUIRE){
                                        $resp['status'] = 'failed';
                                        $resp['msg'] = 'Failed to upload banner to S3. Please check region/ACL and bucket policy.';
                                    }
                                }
                            } else if(defined('AWS_S3_ENABLE') && AWS_S3_ENABLE) {
                                $logDir = base_app.'uploads/logs';
                                if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
                                $missing = [];
                                if(!defined('AWS_S3_BUCKET') || !AWS_S3_BUCKET) $missing[] = 'AWS_S3_BUCKET';
                                if(!defined('AWS_ACCESS_KEY_ID') || !AWS_ACCESS_KEY_ID) $missing[] = 'AWS_ACCESS_KEY_ID';
                                if(!defined('AWS_SECRET_ACCESS_KEY') || !AWS_SECRET_ACCESS_KEY) $missing[] = 'AWS_SECRET_ACCESS_KEY';
                                if(!defined('AWS_S3_REGION') || !AWS_S3_REGION) $missing[] = 'AWS_S3_REGION';
                                @file_put_contents($logDir.'/s3_upload.log', date('c')." BANNER SKIP aid=$aid missing=".implode(',', $missing)."\n", FILE_APPEND);
                            }
                            if(empty($bannerUrl) && (!defined('AWS_S3_REQUIRE') || !AWS_S3_REQUIRE)){
                                $dirLocal = base_app.$localBannerPath;
                                @mkdir(dirname($dirLocal), 0777, true);
                                @move_uploaded_file($upload, $dirLocal);
                                $uploaded_img = is_file($dirLocal);
                            }
                        }
                    }
                    if(!empty($bannerUrl)){
                        $this->conn->query("UPDATE archive_list set `banner_path` = CONCAT('".$this->conn->real_escape_string($bannerUrl)."','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$aid}' ");
                    }elseif(!empty($uploaded_img)){
                        $this->conn->query("UPDATE archive_list set `banner_path` = CONCAT('{$localBannerPath}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$aid}' ");
                    }
                }
                else if(isset($_POST['generated_banner']) && !empty($_POST['generated_banner'])){
                    $gb = $_POST['generated_banner'];
                    $gbClean = explode('?', $gb)[0];
                    $finalPath = $gbClean;
                    // If S3 is enabled, push generated PNG to S3 and use remote URL
                    if(defined('AWS_S3_ENABLE') && AWS_S3_ENABLE && defined('AWS_S3_BUCKET') && AWS_S3_BUCKET && defined('AWS_ACCESS_KEY_ID') && AWS_ACCESS_KEY_ID && defined('AWS_SECRET_ACCESS_KEY') && AWS_SECRET_ACCESS_KEY && defined('AWS_S3_REGION') && AWS_S3_REGION){
                        $fsPath = base_app.$gbClean;
                        if(is_file($fsPath)){
                            $pngData = @file_get_contents($fsPath);
                            if($pngData){
                                $key = 'banners/archive-'.$aid.'.png';
                                if(defined('AWS_S3_USE_SDK') && AWS_S3_USE_SDK){
                                    require_once dirname(__DIR__).'/libs/AwsSdkS3.php';
                                    $client = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                                    $put = $client->putObject($key, $pngData, 'image/png', false);
                                } else {
                                    require_once dirname(__DIR__).'/libs/AwsS3Client.php';
                                    $client = new AwsS3Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                                    $put = $client->putObject($key, $pngData, 'image/png', false);
                                }
                                if($put && isset($put['ok']) && $put['ok']){
                                    $finalPath = $put['url'];
                                }
                            }
                        }
                    }
                    $this->conn->query("UPDATE archive_list set `banner_path` = CONCAT('{$finalPath}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$aid}' ");
                }
                if(isset($_FILES['pdf']) && $_FILES['pdf']['tmp_name'] != ''){
                    $fname = 'uploads/pdf/archive-'.$aid.'.pdf';
                    $dir_path = base_app.$fname;
                    $upload = $_FILES['pdf']['tmp_name'];
                    $type = mime_content_type($upload);
                    $allowed = array('application/pdf');
                    if(!in_array($type,$allowed)){
                        $resp['msg'].=" But Document File has failed to upload due to invalid file type.";
                    }else{
                        $docUrl = '';
                        if(defined('AWS_S3_ENABLE') && AWS_S3_ENABLE && defined('AWS_S3_BUCKET') && AWS_S3_BUCKET && defined('AWS_ACCESS_KEY_ID') && AWS_ACCESS_KEY_ID && defined('AWS_SECRET_ACCESS_KEY') && AWS_SECRET_ACCESS_KEY && defined('AWS_S3_REGION') && AWS_S3_REGION){
                            $pdfData = file_get_contents($upload);
                            if(defined('AWS_S3_USE_SDK') && AWS_S3_USE_SDK){
                                require_once dirname(__DIR__).'/libs/AwsSdkS3.php';
                                $client = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                                // Store PDFs under Files/pdf/ to match local path and console expectations
                                $key = 'pdf/archive-'.$aid.'.pdf';
                                $put = $client->putObject($key, $pdfData, 'application/pdf', false);
                            } else {
                                require_once dirname(__DIR__).'/libs/AwsS3Client.php';
                                $client = new AwsS3Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                                // Store PDFs under Files/pdf/
                                $key = 'pdf/archive-'.$aid.'.pdf';
                                $put = $client->putObject($key, $pdfData, 'application/pdf', false);
                            }
                            if($put && isset($put['ok']) && $put['ok']){
                                $docUrl = $put['url'];
                            } else {
                                $logDir = base_app.'uploads/logs';
                                if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
                                @file_put_contents($logDir.'/s3_upload.log', date('c')." PDF FAIL aid=$aid status=".($put['status'] ?? 'n/a')." error=".($put['error'] ?? '')."\n", FILE_APPEND);
                                if(defined('AWS_S3_REQUIRE') && AWS_S3_REQUIRE){
                                    $resp['status'] = 'failed';
                                    $resp['msg'] = 'Failed to upload document to S3. Please check region/ACL and bucket policy.';
                                }
                            }
                        } else if(defined('AWS_S3_ENABLE') && AWS_S3_ENABLE) {
                            $logDir = base_app.'uploads/logs';
                            if(!is_dir($logDir)) @mkdir($logDir, 0777, true);
                            $missing = [];
                            if(!defined('AWS_S3_BUCKET') || !AWS_S3_BUCKET) $missing[] = 'AWS_S3_BUCKET';
                            if(!defined('AWS_ACCESS_KEY_ID') || !AWS_ACCESS_KEY_ID) $missing[] = 'AWS_ACCESS_KEY_ID';
                            if(!defined('AWS_SECRET_ACCESS_KEY') || !AWS_SECRET_ACCESS_KEY) $missing[] = 'AWS_SECRET_ACCESS_KEY';
                            if(!defined('AWS_S3_REGION') || !AWS_S3_REGION) $missing[] = 'AWS_S3_REGION';
                            @file_put_contents($logDir.'/s3_upload.log', date('c')." PDF SKIP aid=$aid missing=".implode(',', $missing)."\n", FILE_APPEND);
                        }
                        if(empty($docUrl) && (!defined('AWS_S3_REQUIRE') || !AWS_S3_REQUIRE)){
                            $uploaded = move_uploaded_file($_FILES['pdf']['tmp_name'], $dir_path);
                        }
                    }
                    if(!empty($docUrl)){
                        $this->conn->query("UPDATE archive_list set `document_path` = CONCAT('".$this->conn->real_escape_string($docUrl)."','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$aid}' ");
                    }elseif(isset($uploaded)){
                        $this->conn->query("UPDATE archive_list set `document_path` = CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$aid}' ");
                    }
                }

                // Handle Supporting Documents (Multiple)
                if(isset($_FILES['support_files']) && count($_FILES['support_files']['tmp_name']) > 0){
                    $s3_enabled = defined('AWS_S3_ENABLE') && AWS_S3_ENABLE && defined('AWS_S3_BUCKET') && AWS_S3_BUCKET;
                    $s3_client = null;
                    if($s3_enabled){
                         if(defined('AWS_S3_USE_SDK') && AWS_S3_USE_SDK){
                            require_once dirname(__DIR__).'/libs/AwsSdkS3.php';
                            $s3_client = new AwsSdkS3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                        } else {
                            require_once dirname(__DIR__).'/libs/AwsS3Client.php';
                            $s3_client = new AwsS3Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_S3_REGION, AWS_S3_BUCKET, AWS_S3_BASE_PREFIX);
                        }
                    }

                    foreach($_FILES['support_files']['tmp_name'] as $k => $tmp_name){
                        if(empty($tmp_name)) continue;
                        $fname = $_FILES['support_files']['name'][$k];
                        $clean_fname = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fname);
                        $final_url = '';
                        
                        if($s3_enabled){
                            $key = 'support/archive-'.$aid.'-'.$k.'-'.$clean_fname;
                            $fileData = file_get_contents($tmp_name);
                            $mime = mime_content_type($tmp_name);
                            $put = $s3_client->putObject($key, $fileData, $mime, false);
                            if($put && isset($put['ok']) && $put['ok']){
                                $final_url = $put['url'];
                            }
                        }

                        if(empty($final_url) && (!defined('AWS_S3_REQUIRE') || !AWS_S3_REQUIRE)){
                            $localDir = base_app.'uploads/support/';
                            if(!is_dir($localDir)) @mkdir($localDir, 0777, true);
                            $localPath = 'uploads/support/archive-'.$aid.'-'.$k.'-'.$clean_fname;
                            move_uploaded_file($tmp_name, base_app.$localPath);
                            $final_url = $localPath;
                        }

                        if(!empty($final_url)){
                            $this->conn->query("INSERT INTO `archive_files` set archive_id = '{$aid}', file_path = '".$this->conn->real_escape_string($final_url)."', original_name = '".$this->conn->real_escape_string($fname)."'");
                        }
                    }
                }
			
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occured.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function id_registry_template(){
		$type = strtolower(isset($_GET['type']) ? $_GET['type'] : 'csv');
		if($type === 'csv'){
			$fn = 'id_registry_template.csv';
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="'.$fn.'"');
			echo "ID Number,Name,Status\r\n";
			echo "123456,,\r\n";
			echo "987654,Juan Dela Cruz,1\r\n";
			exit;
		}
		if($type === 'docx'){
			$zip = new ZipArchive();
			$tmp = tempnam(sys_get_temp_dir(), 'tpl');
			$zip->open($tmp, ZipArchive::OVERWRITE);
			$zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>\n<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>');
			$zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>\n<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>');
			$docxml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p><w:r><w:t>ID Number, Name, Status</w:t></w:r></w:p><w:p><w:r><w:t>123456</w:t></w:r></w:p><w:p><w:r><w:t>987654, Juan Dela Cruz, 1</w:t></w:r></w:p><w:sectPr/></w:body></w:document>';
			$zip->addFromString('word/document.xml', $docxml);
			$zip->close();
			header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
			header('Content-Disposition: attachment; filename="id_registry_template.docx"');
			readfile($tmp);
			@unlink($tmp);
			exit;
		}
		if($type === 'xlsx'){
			$zip = new ZipArchive();
			$tmp = tempnam(sys_get_temp_dir(), 'tpl');
			$zip->open($tmp, ZipArchive::OVERWRITE);
			$zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>\n<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/></Types>');
			$zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>\n<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
			$zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?>\n<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets></workbook>');
			$zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?>\n<Relationships xmlns="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/></Relationships>');
			$shared = '<?xml version="1.0" encoding="UTF-8"?>\n<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="5" uniqueCount="5"><si><t>ID Number</t></si><si><t>Name</t></si><si><t>Status</t></si><si><t>987654</t></si><si><t>Juan Dela Cruz</t></si></sst>';
			$zip->addFromString('xl/sharedStrings.xml', $shared);
			$sheet = '<?xml version="1.0" encoding="UTF-8"?>\n<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData><row r="1"><c r="A1" t="s"><v>0</v></c><c r="B1" t="s"><v>1</v></c><c r="C1" t="s"><v>2</v></c></row><row r="2"><c r="A2"><v>123456</v></c><c r="B2" t="s"><v></v></c><c r="C2"><v>1</v></c></row><row r="3"><c r="A3" t="s"><v>3</v></c><c r="B3" t="s"><v>4</v></c><c r="C3"><v>1</v></c></row></sheetData></worksheet>';
			$zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
			$zip->close();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="id_registry_template.xlsx"');
			readfile($tmp);
			@unlink($tmp);
			exit;
		}
		header('Content-Type: text/plain');
		echo 'Invalid template type';
		exit;
	}
	function delete_archive(){
		extract($_POST);
		$get = $this->conn->query("SELECT * FROM `archive_list` where id = '{$id}'");
		$del = $this->conn->query("DELETE FROM `archive_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"archive Records has deleted successfully.");
			if($get->num_rows > 0){
				$res = $get->fetch_array();
				$banner_path = explode("?",$res['banner_path'])[0];
				$document_path = explode("?",$res['document_path'])[0];
				if(is_file(base_app.$banner_path))
					unlink(base_app.$banner_path);
				if(is_file(base_app.$document_path))
					unlink(base_app.$document_path);
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function update_status(){
		// Validate and sanitize inputs
		$raw_status = isset($_POST['status']) ? $_POST['status'] : null;
		$raw_id = isset($_POST['id']) ? $_POST['id'] : null;
		$status = is_numeric($raw_status) ? (int)$raw_status : null;
		$id = is_numeric($raw_id) ? (int)$raw_id : null;

		if($status === null || $id === null){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Invalid request: missing or malformed status/id.';
			return json_encode($resp);
		}

        // Permission Check
        $login_type = $this->settings->userdata('login_type');
        $user_id = $this->settings->userdata('id');

        if($login_type == 2){ // Adviser
             // Check if this adviser is assigned to the student of this archive
             $chk_adviser = $this->conn->query("SELECT s.adviser_id FROM archive_list a INNER JOIN student_list s ON a.student_id = s.id WHERE a.id = '{$id}'");
             if($chk_adviser->num_rows > 0){
                 $res = $chk_adviser->fetch_assoc();
                 if($res['adviser_id'] != $user_id){
                     $resp['status'] = 'failed';
                     $resp['msg'] = 'You are not authorized to update this archive.';
                     return json_encode($resp);
                 }
             } else {
                 $resp['status'] = 'failed';
                 $resp['msg'] = 'Student data not found for this archive.';
                 return json_encode($resp);
             }

             if(!in_array($status, [0, 2])){
                 $resp['status'] = 'failed';
                 $resp['msg'] = 'Advisers can only set status to Pending or Verified.';
                 return json_encode($resp);
             }
        } elseif($login_type != 1) { // Not Admin and Not Adviser
             $resp['status'] = 'failed';
             $resp['msg'] = 'Permission denied.';
             return json_encode($resp);
        }

		// Ensure the archive exists
		$exists = $this->conn->prepare("SELECT 1 FROM `archive_list` WHERE `id` = ? LIMIT 1");
		$exists->bind_param('i', $id);
		if(!$exists->execute()){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Database error while checking record: '.$this->conn->error;
			return json_encode($resp);
		}
		$exists_res = $exists->get_result();
		$exists->close();
		if(!$exists_res || $exists_res->num_rows === 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Archive not found.';
			return json_encode($resp);
		}

		// Update status via prepared statement
		$upd = $this->conn->prepare("UPDATE `archive_list` SET `status` = ? WHERE `id` = ?");
		$upd->bind_param('ii', $status, $id);
		if($upd->execute()){
			$resp['status'] = 'success';
			$resp['msg'] = 'Archive status has successfully updated.';
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occurred. Error: '.$this->conn->error;
		}
		$upd->close();

		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
    private function ensure_chat_tables(){
        $this->conn->query("CREATE TABLE IF NOT EXISTS chat_threads (id int(30) NOT NULL AUTO_INCREMENT, user_id int(30) NOT NULL, admin_id int(30) NOT NULL, date_created datetime NOT NULL DEFAULT current_timestamp(), PRIMARY KEY(id), KEY ua_idx(user_id,admin_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        $this->conn->query("CREATE TABLE IF NOT EXISTS chat_messages (id int(30) NOT NULL AUTO_INCREMENT, thread_id int(30) NOT NULL, sender_type tinyint(1) NOT NULL, sender_id int(30) NOT NULL, recipient_type tinyint(1) NOT NULL, recipient_id int(30) NOT NULL, algorithm varchar(50) NOT NULL, iv varchar(255) DEFAULT NULL, enc_key longtext, enc_key_sender longtext NULL, ciphertext longtext NOT NULL, date_created datetime NOT NULL DEFAULT current_timestamp(), deleted_by_user tinyint(1) NOT NULL DEFAULT 0, deleted_by_admin tinyint(1) NOT NULL DEFAULT 0, PRIMARY KEY(id), KEY t_idx(thread_id), CONSTRAINT chat_messages_thread_fk FOREIGN KEY (thread_id) REFERENCES chat_threads(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        // Ensure enc_key_sender column exists if table created earlier without it
        $cols = [];
        $res = $this->conn->query("SHOW COLUMNS FROM chat_messages");
        if($res){ while($r = $res->fetch_assoc()){ $cols[$r['Field']] = true; } }
        if(!isset($cols['enc_key_sender'])){
            $this->conn->query("ALTER TABLE chat_messages ADD COLUMN enc_key_sender longtext NULL AFTER enc_key");
        }
        $this->conn->query("CREATE TABLE IF NOT EXISTS chat_keys (id int(30) NOT NULL AUTO_INCREMENT, owner_type tinyint(1) NOT NULL, owner_id int(30) NOT NULL, public_key longtext NOT NULL, date_created datetime NOT NULL DEFAULT current_timestamp(), PRIMARY KEY(id), UNIQUE KEY owner_unique (owner_type, owner_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
    function chat_save_public_key(){
        $this->ensure_chat_tables();
        $owner_type = $this->settings->userdata('login_type');
        $owner_id = $this->settings->userdata('id');
        $pub = isset($_POST['public_key']) ? $_POST['public_key'] : '';
        if(!$owner_type || !$owner_id || !$pub){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid key data.';
            return json_encode($resp);
        }
        $pub = $this->conn->real_escape_string($pub);
        $chk = $this->conn->query("SELECT id FROM chat_keys WHERE owner_type='{$owner_type}' AND owner_id='{$owner_id}'");
        if($chk && $chk->num_rows > 0){
            $row = $chk->fetch_assoc();
            $save = $this->conn->query("UPDATE chat_keys SET public_key='{$pub}' WHERE id='{$row['id']}'");
        }else{
            $save = $this->conn->query("INSERT INTO chat_keys SET owner_type='{$owner_type}', owner_id='{$owner_id}', public_key='{$pub}'");
        }
        if($save){
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'DB error.';
        }
        return json_encode($resp);
    }
    function chat_get_public_key(){
        $this->ensure_chat_tables();
        $owner_type = isset($_GET['owner_type']) ? (int)$_GET['owner_type'] : 0;
        $owner_id = isset($_GET['owner_id']) ? (int)$_GET['owner_id'] : 0;
        $resp = ['status'=>'failed'];
        if(!$owner_type || !$owner_id){
            $resp['msg'] = 'Invalid owner.';
            return json_encode($resp);
        }
        $q = $this->conn->query("SELECT public_key FROM chat_keys WHERE owner_type='{$owner_type}' AND owner_id='{$owner_id}'");
        if($q && $q->num_rows > 0){
            $r = $q->fetch_assoc();
            $resp['status'] = 'success';
            $resp['public_key'] = $r['public_key'];
        }else{
            $resp['msg'] = 'No key.';
        }
        return json_encode($resp);
    }
    function chat_create_thread(){
        $this->ensure_chat_tables();
        $me_type = $this->settings->userdata('login_type');
        $me_id = $this->settings->userdata('id');
        $other_type = isset($_POST['other_type']) ? (int)$_POST['other_type'] : 0;
        $other_id = isset($_POST['other_id']) ? (int)$_POST['other_id'] : 0;
        if(!$me_type || !$me_id || !$other_type || !$other_id){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid participants.';
            return json_encode($resp);
        }
        $user_id = $me_type == 2 ? $me_id : $other_id;
        $admin_id = $me_type == 1 ? $me_id : $other_id;
        $chk = $this->conn->query("SELECT id FROM chat_threads WHERE user_id='{$user_id}' AND admin_id='{$admin_id}'");
        if($chk && $chk->num_rows > 0){
            $row = $chk->fetch_assoc();
            $resp['status'] = 'success';
            $resp['thread_id'] = (int)$row['id'];
            return json_encode($resp);
        }
        $save = $this->conn->query("INSERT INTO chat_threads SET user_id='{$user_id}', admin_id='{$admin_id}'");
        if($save){
            $resp['status'] = 'success';
            $resp['thread_id'] = (int)$this->conn->insert_id;
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'DB error.';
        }
        return json_encode($resp);
    }
    function chat_send_message(){
        $this->ensure_chat_tables();
        $me_type = $this->settings->userdata('login_type');
        $me_id = $this->settings->userdata('id');
        $thread_id = isset($_POST['thread_id']) ? (int)$_POST['thread_id'] : 0;
        $recipient_type = isset($_POST['recipient_type']) ? (int)$_POST['recipient_type'] : 0;
        $recipient_id = isset($_POST['recipient_id']) ? (int)$_POST['recipient_id'] : 0;
        $algorithm = isset($_POST['algorithm']) ? $this->conn->real_escape_string($_POST['algorithm']) : '';
        $iv = isset($_POST['iv']) ? $this->conn->real_escape_string($_POST['iv']) : '';
        $enc_key = isset($_POST['enc_key']) ? $this->conn->real_escape_string($_POST['enc_key']) : '';
        $enc_key_sender = isset($_POST['sender_enc_key']) ? $this->conn->real_escape_string($_POST['sender_enc_key']) : '';
        $ciphertext = isset($_POST['ciphertext']) ? $this->conn->real_escape_string($_POST['ciphertext']) : '';
        if(!$me_type || !$me_id || !$thread_id || !$recipient_type || !$recipient_id || !$algorithm || !$ciphertext){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid payload.';
            return json_encode($resp);
        }
        $tchk = $this->conn->query("SELECT id FROM chat_threads WHERE id='{$thread_id}'");
        if(!$tchk || $tchk->num_rows == 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Thread missing.';
            return json_encode($resp);
        }
        $sql = "INSERT INTO chat_messages SET thread_id='{$thread_id}', sender_type='{$me_type}', sender_id='{$me_id}', recipient_type='{$recipient_type}', recipient_id='{$recipient_id}', algorithm='{$algorithm}', iv=".(strlen($iv)?"'{$iv}'":"NULL").", enc_key=".(strlen($enc_key)?"'{$enc_key}'":"NULL").", enc_key_sender=".(strlen($enc_key_sender)?"'{$enc_key_sender}'":"NULL").", ciphertext='{$ciphertext}'";
        $save = $this->conn->query($sql);
        if($save){
            $resp['status'] = 'success';
            $resp['id'] = (int)$this->conn->insert_id;
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'DB error.';
        }
        return json_encode($resp);
    }
    function chat_fetch_messages(){
        $this->ensure_chat_tables();
        $me_type = $this->settings->userdata('login_type');
        $thread_id = isset($_GET['thread_id']) ? (int)$_GET['thread_id'] : 0;
        $resp = ['status'=>'failed'];
        if(!$me_type || !$thread_id){
            $resp['msg'] = 'Invalid request.';
            return json_encode($resp);
        }
        $delField = $me_type == 2 ? 'deleted_by_user' : 'deleted_by_admin';
        $rows = [];
        $q = $this->conn->query("SELECT id, sender_type, sender_id, recipient_type, recipient_id, algorithm, iv, enc_key, enc_key_sender, ciphertext, date_created FROM chat_messages WHERE thread_id='{$thread_id}' AND {$delField}=0 ORDER BY id ASC");
        if($q){
            while($r = $q->fetch_assoc()) $rows[] = $r;
            $resp['status'] = 'success';
            $resp['messages'] = $rows;
        }else{
            $resp['msg'] = 'DB error.';
        }
        return json_encode($resp);
    }
    function chat_delete_message(){
        $this->ensure_chat_tables();
        $me_type = $this->settings->userdata('login_type');
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if(!$me_type || !$id){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid request.';
            return json_encode($resp);
        }
        $field = $me_type == 2 ? 'deleted_by_user' : 'deleted_by_admin';
        $upd = $this->conn->query("UPDATE chat_messages SET {$field}=1 WHERE id='{$id}'");
        if($upd){
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'DB error.';
        }
        return json_encode($resp);
    }
    function chat_list_threads(){
        $this->ensure_chat_tables();
        $me_type = $this->settings->userdata('login_type');
        $me_id = $this->settings->userdata('id');
        $rows = [];
        if($me_type == 2){
            $q = $this->conn->query("SELECT t.id, t.admin_id, u.firstname, u.lastname FROM chat_threads t LEFT JOIN users u ON t.admin_id = u.id WHERE t.user_id='{$me_id}' ORDER BY t.id DESC");
        }else{
            $q = $this->conn->query("SELECT t.id, t.user_id, s.firstname, s.lastname FROM chat_threads t LEFT JOIN student_list s ON t.user_id = s.id WHERE t.admin_id='{$me_id}' ORDER BY t.id DESC");
        }
        if($q){
            while($r = $q->fetch_assoc()) $rows[] = $r;
            $resp['status'] = 'success';
            $resp['threads'] = $rows;
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'DB error.';
        }
        return json_encode($resp);
    }
    function chat_list_admins(){
        $rows = [];
        $q = $this->conn->query("SELECT id, firstname, lastname FROM users WHERE status = 1 ORDER BY lastname, firstname");
        if($q){
            while($r = $q->fetch_assoc()) $rows[] = $r;
            $resp['status'] = 'success';
            $resp['admins'] = $rows;
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'DB error.';
        }
        return json_encode($resp);
    }
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
if(!in_array($action, ['id_registry_template','export_archives_excel','export_archives_xlsx','none'])){
    header('Content-Type: application/json');
}
    switch ($action) {
	case 'save_department':
		echo $Master->save_department();
	break;
	case 'delete_department':
		echo $Master->delete_department();
	break;
	case 'save_curriculum':
		echo $Master->save_curriculum();
	break;
	case 'delete_curriculum':
		echo $Master->delete_curriculum();
	break;
	case 'save_archive':
		echo $Master->save_archive();
	break;
	case 'delete_archive':
		echo $Master->delete_archive();
	break;
		case 'update_status':
			echo $Master->update_status();
		break;
	case 'save_id_registry':
		echo $Master->save_id_registry();
	break;
		case 'delete_id_registry':
			echo $Master->delete_id_registry();
		break;
		case 'import_id_registry':
			echo $Master->import_id_registry();
		break;
		case 'id_registry_template':
			$Master->id_registry_template();
		break;
		case 'export_archives_excel':
			$Master->export_archives_excel();
		break;
		case 'export_archives_xlsx':
			$Master->export_archives_xlsx();
		break;
		
		
		default:
			// echo $sysset->index();
			break;
	}
