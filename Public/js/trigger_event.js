$(document).ready(function(){
	var i = 0;
	$(".odd_tr").each(function(){
		if(i%2==0){
			$(this).addClass('even_tr');
		}
		i++;
	});
	
	$('#school_add_send_btn').click(function(){
		var school_name=$('input[name=school_name]');
		var select_location=$('select[name=location]');
		
		if(select_location.val()==null){
		alert('地区不能为空！');
		return;
		}
		
		if(school_name.val()==''){
		alert('学校名不能为空！');
		school_name.focus();
		return;
		}
		
		$('#form1').submit();
		
	})
	
	$('#reset_btn').click(function(){
		$('#form1')[0].reset();   
	})
	
	
	$('#school_list_delete').click(function(){
		$('#form1').submit();
		
	})
	
});      