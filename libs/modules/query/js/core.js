
$(document).ready(function(){
    $('#query_add_form').bind('click', function(elem){
        query_edit_form();
        return false;
    });
    
    if ($('#query_list_table')){
       get_query_list_table();
    }
    

    
    
});


function query_edit_form(qid){
    console.log(qid);
    //get the html for the form
    $.ajax({
      url: "/freshlytics/ajax/query/edit_form",
      context: document.body,
      data:'qid='+qid,
      success: function(data){
        var result = eval('(' + data + ')');
        
        
        $('#jquery_dialog').html(result.body);
        $(".chosen").chosen({no_results_text: "No results matched"});
        
        
        $(".chzn-choices").prepend('<span class="data" style="dislay:none; color:white;"></span>');
        
        
        $(".chzn-choices input").keydown(function (e){
            console.log(e.which);
            if (e.which == 13 || e.which == 9){
                console.log(this);
                add_select_element($(this).parents('.form-item').children());
                if (e.which == 13){return false;}   
            }
        })
        $(".chzn-choices input").keyup(function (e){
            console.log(e.which);
            $(this).parents('.chzn-choices').children('.data').html($(this).val());
            });
        $(".select_add").bind('click', function(elem){
            add_select_element(this);
        });
        $(".chzn-choices input").bind('focusout', function(elem){
            add_select_element(this);
        });
        $('#jquery_dialog').children('form').bind('submit', function(){query_add_form_submit(this);return false;});
        $('#jquery_dialog').dialog({
			height: 400, 
            width: 500,
			modal: true,
            resizable: false,
            title: result.title,
            buttons: [{
                text: "Add",
                click: function() { query_edit_form_submit($(this).children('form')); }
            }]
		});
      }
    });
}

function add_select_element(elem){
    var value = $(elem).siblings('.chzn-container').children('.chzn-choices').children('.data').html();
    if(!value){return false;}
    var type = $(elem).siblings('select').attr('id');
    switch (type)
    {
      case 'tags': if (value.charAt(0) != '#'){alert('not a # tag'); return false;}
                        break;
      case 'users': if (value.charAt(0) != '@'){alert('not a valid user (must start with @)'); return false;}
                        break;
    }
    
    $(elem).siblings('select').prepend('<option selected="selected">'+value+'</option>')
    $(elem).siblings('select').trigger("liszt:updated");
    $(elem).siblings('.chzn-container').children('.chzn-choices').children('.data').html('')
    return false;
}

function query_edit_form_submit(form){

    $(form).children("input#name").removeClass('error');
    var name = $('form').children("input#name").val();
    if (name == ''){
        $('form').children("input#name").addClass('error'); 
        return false;   
    }
    
    //form looks good lets submit it to the db
    var dataString = $(form).serialize();
    console.log(dataString);  
    $.ajax({
      type: 'POST',
      url: "/freshlytics/ajax/query/edit",
      context: document.body,
      data: dataString,
      success: function(data){
        console.log(data);
        var result = eval('(' + data + ')');
      
        if (result.error){
            $(form).html(result.error);
            return false;
        }else{
            get_query_list_table();
            $('#jquery_dialog').dialog("close");
        }
      }
    });
}

function get_query_list_table(){
    $.ajax({
      url: "ajax/query/view_query_table",
      context: document.body,
      success: function(data){
        var result = eval('(' + data + ')');
        $('#query_list_table').html(result);
        $('.edit-query').bind('click', function(elem){
            query_edit_form($(this).attr('data'));
            return false;
        });          
      }
    }); 
}