<?php
class Query {
    public $qid, $name, $tags, $users, $tracks, $type, $updated, $heartbeat, $created, $deleted;
    
    public function __construct($qid=0) {
        
        //load some defaults
        $this->qid = 0;
        $this->tags = array();
        $this->users = array();
        $this->tracks = array();
        $this->type = 1;
        $this->deleted = 0;
        $this->updated = 0;
        $this->heartbeat = 0;
        $this->created = time();
        
        if ($qid!=0){
            $this->qid = $qid;
            $this->load();
        }       
	}
    public function load(){
       global $db;
       $db->query('SELECT * FROM query WHERE qid = ' . $this->qid . ' LIMIT 1');
       $res = $db->fetch_all();
       
       if (is_array($res[0])){
            $row = $res[0];
            $this->name = $row['name'];
            if (!empty($row['tags']) && $row['tags']){
                $this->tags = json_decode($row['tags']);
            }

            if (!empty($row['users'])&& $row['users']){
                
                $this->users = json_decode($row['users']);
            }
            if (!empty($row['tracks'])&& $row['tracks']){
                $this->tracks = json_decode($row['tracks']);
            }
       }
    }
    public function save(){
        global $db;
        $this->updated = time();
        if ($this->qid==0){
            $db->query('INSERT INTO query (`name`, tags, users, tracks, type, updated, heartbeat, created, deleted) 
                        VALUES ("'.$this->name.'",
                         "'.mysql_real_escape_string(json_encode($this->tags)).'", 
                         "'.mysql_real_escape_string(json_encode($this->users)).'", 
                         "'.mysql_real_escape_string(json_encode($this->tracks)).'", 
                         '.$this->type.', 
                         '.$this->updated.', 
                         '.$this->heartbeat.', 
                         '.$this->created.', 
                         '.$this->deleted.')');
            $this->qid = $db->last_id();
        }else{
            $db->query('UPDATE query SET 
                `name` = "'.$this->name.'",
                `tags` = "'.mysql_real_escape_string(json_encode($this->tags)).'",
                `users` = "'.mysql_real_escape_string(json_encode($this->users)).'",
                `tracks` = "'.mysql_real_escape_string(json_encode($this->tracks)).'",
                `type` = '.$this->type.',
                `updated` = '.$this->updated.',
                `heartbeat` = '.$this->heartbeat.',
                `created` = '.$this->created.',
                `deleted` = '.$this->deleted.'
            ');
        }
    }
    
    public function execute(){
        global $db;
        $run_time = time();
        //get the tids
        $rq = new RestQuery($this->users, $this->tags, $this->tracks, $this->last_tid());
        $rq->execute();
        foreach($rq->tids as $tid){
            $db->query('INSERT INTO join_query_tweet (qid,tid,time) VALUES ('.$this->qid.','.$tid.','.$run_time.');');
        }
        $this->heartbeat = $run_time;
        $this->save();

    }
    
    public function last_tid(){
        global $db;
        $db->query('SELECT tweet.tid FROM join_query_tweet AS jgt 
                    INNER JOIN tweet ON tweet.tid = jgt.tid
                    ORDER BY jid DESC
                    LIMIT 1;');
        $res = $db->fetch_all();
        if (!empty($res)){
            return $res[0]['tid'];    
        }else{
            return 0;
        }
    }
    
}
function query_js(){
    $path = module_get_path('query');
    $js['footer']['query'][] = $path . '/js/core.js';
    return $js;
}

function query_menu(){
    
    $menu = array();
    
    $menu['queries'] = array(
        'callback' => 'view_queries'
    );
    
    $menu['queries/view'] = array(
        'callback' => 'view_query'
    );
    
    $menu['ajax/query/edit'] = array(
        'callback' => 'ajax_edit_query'
    );
    $menu['ajax/query/edit_form'] = array(
        'callback' => 'ajax_query_edit_form'
    );
    $menu['ajax/query/view_query_table'] = array(
        'callback' => 'ajax_view_query_list'
    );
    $menu['ajax/query/run'] = array(
        'callback' => 'ajax_run_query'
    );
    
    return $menu;
}

function view_query($qid){
    $query = new Query($qid);
    
    $query->execute();
}

function ajax_run_query($qid){
    $query = new Query($qid);
    
    
        
}


function ajax_edit_query(){
    if ($_POST['qid']){
        $query = new Query($_POST['qid']);
    }else{
        $query = new Query();
    }
    $query->name = $_POST['name'];
    if(!empty($_POST['tags'])){$query->tags = $_POST['tags'];}
    if(!empty($_POST['users'])){$query->users = $_POST['users'];}
    if(!empty($_POST['tracks'])){$query->tracks = $_POST['tracks'];}
    $query->save();
    print json_encode($query);
}

function ajax_query_edit_form(){
    
   if (!empty($_GET['qid'])){
        $query = new Query($_GET['qid']);   
   }else{
        $query = new Query();
   } 

   $form = '';
   $form .= '<input type="hidden" id="qid" name="qid" value="'.$query->qid.'"/>';
   $form .= '<div class="form-item">';
   $form .= '<label for="name">Query Title</label><input type="text" id="name" name="name" value="'.$query->name.'"/>';
   $form .= '</div>';
   
   //tags
   $form .= '<div class="form-item">';
   $form .= '<label for="tags">Tags</label>';
   $form .= '<select id="tags" name="tags[]" class="chosen" multiple="multiple">';
   foreach($query->tags as $tag){
        $form .= '<option selected=selected>' . $tag . '</option>'; 
   }
   $form .= '</select>';
   $form .= '<a class="select_add"><span class="ui-icon ui-icon-plusthick"></span></a>';
   $form .= '</div>';
   
   //users
   $form .= '<div class="form-item">';
   $form .= '<label for="users">Users</label>';
   $form .= '<select id="users" name="users[]" class="chosen" multiple="multiple">';
   foreach($query->users as $user){
        $form .= '<option selected=selected>' . $user . '</option>'; 
   }
   $form .= '</select>';
   $form .= '<a class="select_add"><span class="ui-icon ui-icon-plusthick"></span></a>';
   $form .= '</div>';
   
   //tracks
   $form .= '<div class="form-item">';
   $form .= '<label for="tracks">Tracks</label>';
   $form .= '<select id="tracks" name="tracks[]" class="chosen" multiple="multiple">';
   foreach($query->tracks as $track){
        $form .= '<option selected=selected>' . $track . '</option>'; 
   }
   $form .= '</select>';
   $form .= '<a class="select_add"><span class="ui-icon ui-icon-plusthick"></span></a>';
   $form .= '</div>';
   
   
   $form = '<form>' . $form . '</form>'; 
   $return = array();
   $return['title'] = 'Add a Query';
   $return['body'] = $form;
   print(json_encode($return)); 
}

function ajax_view_query_list(){
    global $db;
        
    $header = array('name', 'tags', 'users', 'tracks', 'last_run','');
    $db->query('SELECT * FROM query ORDER BY created desc');
    $res = $db->fetch_all();
    $rows = array();
    foreach($res as $result){
        $rows[] = array(
            $result['name'],
            json_decode($result['tags']),
            json_decode($result['users']),
            json_decode($result['tracks']),
            $result['heartbeat'],
            '<a href="#" class="edit-link edit-query" data="'.$result['qid'].'">edit query</a>'
            .'<a href="queries/view/~/'.$result['qid'].'" class="view-link">view query</a>'           
        );
    }
    
    print(json_encode(theme_table($header, $rows, array('id' => 'queries_list', 'width' => '50%'))));
}

function view_queries(){
    global $db;
    
    print('<a href="#" id="query_add_form">Add Query</a>');
    
    print('<div id="query_list_table"></div>');
}