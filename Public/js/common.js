$(document).ready(function(){
	if($('.show_content_b').height()<500){
		$('.show_content_b').height(500)
	}
});  




function getlocationchildren(obj,subid,school_id){
    var t = $(obj);
    var v = $(":selected",t).val();
    
    if(v){
        var url = '/index.php/Admin/Util/get_location_children';
        var params = {
            _c : $(":selected",t).val(),
            t : $.now()
        };
        $.getJSON(url,params,function(r){
            if(r.flag!=0){
                
            }else{
                if(r.children){
                    var html = '<option value="">请选择</option>';
                    for(var i=0;i<r.children.length;i++){
                        html += '<option value="'+r.children[i]['code']+'">'+r.children[i]['name']+'</option>';
                    }
                    $(subid).html(html);
                }
                    
                
                if(r.school){
                    var html2 = '<option value="">请选择</option>';
                    for(var j=0;j<r.school.length;j++){
                        html2 += '<option value="'+r.school[j]['id']+'">'+r.school[j]['name']+'</option>';
                    }
                    $(school_id).html(html2);
                }
                    
            }
        });
        
    }else{
        $("subid").html('<option value=""></option>');
    }
    
}


function get_class_by_school(obj,c) {
    var t = $(obj);
    if(!t.val()){
        $(c).html("");
    }else{
        var url = '/index.php/Admin/Util/get_class_by_school';
        var params = {
            _c : t.val(),
            t : $.now()
        };
        $.getJSON(url,params,function(r){
            if(r.flag != 0 ){
                alert(r.msg);
            }else{
                var html = '<option value="">请选择</option>';
                for(var i=0;i<r.data.length;i++){
                    html += '<option value="'+r.data[i]['id']+'">'+r.data[i]['name']+'</option>';
                }
                $(c).html(html);
            }
        });
    }
}




    