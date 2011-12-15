<?php


class RestQuery{
    private $url, $since;
    
    public $users, $tags, $tracks, $query_strings, $tids;
    
     public function __construct($users = array(), $tags = array(), $tracks = array(), $since) {
        $this->url = "http://search.twitter.com/search.json";
        
        
        $this->users = $users;
        $this->tags = $tags;
        $this->tracks = $tracks;
        $q = array_merge($users, $tags, $tracks);
        $this->prepare_querystrings($q);
        
        
     }
     
     public function execute(){

         $data = get_data($this->url  . '?' . $this->query_strings[0]);
         $data = json_decode($data);
         $this->process_data($data);    
     }     
     
     private function process_data($data){
        foreach($data->results as $tweet){
            $tweet = new Tweet($tweet->id_str);
            $tweet->save();
            $this->tids[] = $tweet->tid;
        }
     }

     
     private function prepare_querystrings($q){
        foreach($q as $query){
            $this->query_strings[] = 'q=' . urlencode($query) . '&include_entities=true&rpp=100&result_type=recent';
        }
        
     }
     
   
}