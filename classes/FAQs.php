<?php
// FAQs CRUD endpoints
if(!class_exists('DBConnection')){
    $dir = dirname(__FILE__);
    require_once($dir.'/../initialize.php');
    require_once($dir.'/DBConnection.php');
}
class FAQs extends DBConnection{
    private $settings;
    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
        $this->ensure_table();
    }
    public function __destruct(){
        parent::__destruct();
    }
    private function ensure_table(){
        // Create FAQs table if it does not exist
        $tbl = $this->conn->query("SHOW TABLES LIKE 'faqs'");
        if(!$tbl || $tbl->num_rows == 0){
            $sql = "CREATE TABLE `faqs` (
              `id` int(30) NOT NULL AUTO_INCREMENT,
              `question` text NOT NULL,
              `answer` mediumtext NOT NULL,
              `tags` text NULL,
              `status` tinyint(1) NOT NULL DEFAULT 1,
              `sort_order` int(11) NULL,
              `date_created` datetime NOT NULL DEFAULT current_timestamp(),
              `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->conn->query($sql);
        }
        // Ensure columns exist if table was created earlier without them
        $cols = ['tags','status','sort_order','date_created','date_updated'];
        foreach($cols as $c){
            $chk = $this->conn->query("SHOW COLUMNS FROM `faqs` LIKE '{$c}'");
            if(!$chk || $chk->num_rows == 0){
                if($c === 'tags') $this->conn->query("ALTER TABLE `faqs` ADD COLUMN `tags` text NULL AFTER `answer`");
                if($c === 'status') $this->conn->query("ALTER TABLE `faqs` ADD COLUMN `status` tinyint(1) NOT NULL DEFAULT 1 AFTER `tags`");
                if($c === 'sort_order') $this->conn->query("ALTER TABLE `faqs` ADD COLUMN `sort_order` int(11) NULL AFTER `status`");
                if($c === 'date_created') $this->conn->query("ALTER TABLE `faqs` ADD COLUMN `date_created` datetime NOT NULL DEFAULT current_timestamp() AFTER `sort_order`");
                if($c === 'date_updated') $this->conn->query("ALTER TABLE `faqs` ADD COLUMN `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp() AFTER `date_created`");
            }
        }
    }

    private function capture_err(){
        if(!$this->conn->error) return false;
        return json_encode(['status'=>'failed','error'=>$this->conn->error]);
    }

    public function list_all(){
        $res = $this->conn->query("SELECT * FROM `faqs` ORDER BY COALESCE(sort_order, 999999), id ASC");
        $data = [];
        if($res){
            while($row = $res->fetch_assoc()){
                $data[] = $row;
            }
        }
        return json_encode(['status'=>'success','data'=>$data]);
    }

    public function list_public(){
        $res = $this->conn->query("SELECT id, question, answer, tags FROM `faqs` WHERE `status` = 1 ORDER BY COALESCE(sort_order, 999999), id ASC");
        $data = [];
        if($res){
            while($row = $res->fetch_assoc()){
                $data[] = $row;
            }
        }
        return json_encode(['status'=>'success','data'=>$data]);
    }

    public function save(){
        // id optional for update
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $question = isset($_POST['question']) ? $this->conn->real_escape_string($_POST['question']) : '';
        $answer = isset($_POST['answer']) ? $this->conn->real_escape_string($_POST['answer']) : '';
        $tags = isset($_POST['tags']) ? $this->conn->real_escape_string($_POST['tags']) : '';
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        $sort = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : null;
        if(!$question || !$answer){
            return json_encode(['status'=>'failed','msg'=>'Question and Answer are required.']);
        }
        if($id > 0){
            $sql = "UPDATE `faqs` SET `question`='{$question}', `answer`='{$answer}', `tags`='{$tags}', `status`={$status}, `sort_order`=".($sort===null?"NULL":$sort)." WHERE id='{$id}'";
            $ok = $this->conn->query($sql);
        }else{
            // set default sort_order to next max
            if($sort === null){
                $max = $this->conn->query("SELECT MAX(COALESCE(sort_order,0)) m FROM `faqs`");
                $m = 0; if($max){ $r = $max->fetch_assoc(); $m = (int)$r['m']; }
                $sort = $m + 1;
            }
            $sql = "INSERT INTO `faqs` SET `question`='{$question}', `answer`='{$answer}', `tags`='{$tags}', `status`={$status}, `sort_order`=".($sort===null?"NULL":$sort);
            $ok = $this->conn->query($sql);
            if($ok) $id = $this->conn->insert_id;
        }
        if(!$ok){
            $err = $this->capture_err();
            return $err ? $err : json_encode(['status'=>'failed','msg'=>'Database error saving FAQ']);
        }
        return json_encode(['status'=>'success','id'=>$id]);
    }

    public function delete(){
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if($id <= 0) return json_encode(['status'=>'failed','msg'=>'Invalid FAQ id']);
        $ok = $this->conn->query("DELETE FROM `faqs` WHERE id='{$id}'");
        if(!$ok){
            $err = $this->capture_err();
            return $err ? $err : json_encode(['status'=>'failed','msg'=>'Database error deleting FAQ']);
        }
        return json_encode(['status'=>'success']);
    }

    public function reorder(){
        // expects order[]=id1&id[]=id2...
        if(!isset($_POST['order']) || !is_array($_POST['order'])){
            return json_encode(['status'=>'failed','msg'=>'Order array required']);
        }
        $i = 1;
        foreach($_POST['order'] as $id){
            $id = (int)$id; if($id <= 0) continue;
            $this->conn->query("UPDATE `faqs` SET sort_order={$i} WHERE id='{$id}'");
            $i++;
        }
        return json_encode(['status'=>'success']);
    }
}

$faqs = new FAQs();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
header('Content-Type: application/json');
switch($action){
    case 'list':
        echo $faqs->list_all();
        break;
    case 'list_public':
        echo $faqs->list_public();
        break;
    case 'save':
        echo $faqs->save();
        break;
    case 'delete':
        echo $faqs->delete();
        break;
    case 'reorder':
        echo $faqs->reorder();
        break;
    default:
        echo json_encode(['status'=>'failed','msg'=>'No action']);
}
?>

