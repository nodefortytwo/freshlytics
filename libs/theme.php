<?php

function theme_table($header, $rows, $attrs = array()){
    $attr_array = array();
    foreach($attrs as $key=>$value){
        $attr_array[] = $key . ' = "' . $value . '"';
    }
    $str_attr = implode(' ', $attr_array);
    
    $html = '<table ' . $str_attr . '>';
    $html .= '<tr>';
    foreach($header as $col){
        $html .= '<th>' . $col . '</th>';
    }
    $html .= '</tr>';
    $i = 0;
    $classes = array();
    foreach($rows as $row){
        $classes[] = (fmod($i,2) ? 'odd' : 'even');
        if ($i == 0){$classes[] = 'first';}
        if ($i == (count($rows)-1)){$classes[] = 'last';}
        $classes = implode(' ', $classes);
        $html .= '<tr class="' . $classes . '">';
        foreach($row as $field){
          if (is_array($field)){$field = theme_list($field);}  
          $html .= '<td>' . $field . '</td>';  
        }
        $html .= '</tr>';
        $i++;
        $classes = array();
    }
    
    $html .= '</table>';
    return $html;
}


function theme_list($items = array()){
    $i = 0;
    $classes = array();
    $html = '<ul>';
    foreach($items as $item){
        $classes[] = (fmod($i,2) ? 'odd' : 'even');
        if ($i == 0){$classes[] = 'first';}
        if ($i == (count($items)-1)){$classes[] = 'last';}
        $classes = implode(' ', $classes);
        $html .= '<li class="' . $classes . '">';
        if (is_array($item)){$html .= theme_list($item);}
        $html .= $item;
        $html .= '</li>';
        $i++;
        $classes = array();
    }
    $html .= '</ul>';
    return $html;
}