<?php

class Tweet{
    public $tid;
    
    public function __construct($tid = 0) {
        
        $this->tid = $tid;
        
        
    }
    
    public function indb(){
        global $db;
        $db->query('SELECT tid FROM tweet WHERE tid = ' . $this->tid);
        $res = $db->fetch_all();
        
        return (empty($res) ? false : true);
    }
    
    public function save(){
       global $db;
       if ($this->indb()){
         
       }else{
            $db->query('INSERT INTO tweet (tid) VALUES (' . $this->tid . ');');  
       }
    }
}