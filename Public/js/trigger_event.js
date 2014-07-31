$(document).ready(function(){
	$('.current').parent().addClass('div_page_content')
	
	$.get(request_category_URL, function(data){
		var htm=''
		for(var i=0;i<data.length;i++)
		{
			htm+= ('<option value='+data[i].id+'>'+data[i].name+'</option>')
		}
		$('#select_category').append(htm)

	})
	
	var i = 0;
	$(".odd_tr").each(function(){
		if(i%2==0){
			$(this).addClass('even_tr');
		}
		i++;
	});
	
	$('#account_send_btn').click(function(){
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
	
	$('#school_send_btn').click(function(){

		
		$('#form1').submit();
		
	})
	
	$('#class_send_btn').click(function(){
		var class_name=$('input[name=class_name]');
		var select_location=$('select[name=location]');
		var select_school=$('select[name=school_name]');
		
		if(select_location.val()==null){
		alert('地区不能为空！');
		return;
		}
		
		if(select_school.val()==null){
		alert('学校名不能为空！');
		return;
		}
		
		if(class_name.val()==''){
		alert('班级名不能为空！');
		class_name.focus();
		return;
		}
		
		$('#form1').submit();
		
	})
	
	$('#question_send_btn').click(function(){
		var question_name=$('input[name=question_name]');
		var correct_answer=$('input[name=correct_answer]');
		var answer01=$('input[name=answer01]');
		var answer_content01=$('input[name=answer_content01]');
		var answer02=$('input[name=answer02]');
		var answer_content02=$('input[name=answer_content02]');
		
		if(question_name.val()==''){
		alert('题目不能为空！');
		question_name.focus();
		return;
		}
		
		if(correct_answer.val()==''){
		alert('正确答案不能为空！');
		correct_answer.focus();
		return;
		}
		
		if(answer01.val()==''){
		alert('答案不能为空！');
		answer01.focus();
		return;
		}
		
		if(answer_content01.val()==''){
		alert('答案内容不能为空！');
		answer_content01.focus();
		return;
		}
		
		if(answer02.val()==''){
		alert('答案不能为空！');
		answer02.focus();
		return;
		}
		
		if(answer_content02.val()==''){
		alert('答案内容不能为空！');
		answer_content02.focus();
		return;
		}
		
		$('#form1').submit();
		
	})
	
	$('#reset_btn').click(function(){
		$('#form1')[0].reset();   
	})
	
	$('#account_list_edit').click(function(){
		$('#form1').attr('action',account_edit_URL);
		$('#form1').submit();
		
	})
	
	$('#account_list_delete').click(function(){
		$('#form1').attr('action',account_list_delete_URL);
		$('#form1').submit();
		
	})
	
	$('#school_list_edit').click(function(){
		$('#form1').attr('action',school_edit_URL);
		$('#form1').submit();
		
	})
	
	$('#school_list_delete').click(function(){
		$('#form1').attr('action',school_list_delete_URL);
		$('#form1').submit();
		
	})
	
	$('#class_list_edit').click(function(){
		$('#form1').attr('action',class_edit_URL);
		$('#form1').submit();
		
	})
	
	$('#class_list_delete').click(function(){
		$('#form1').attr('action',class_list_delete_URL);
		$('#form1').submit();
		
	})
	
	$('#question_list_edit').click(function(){
		$('#form1').attr('action',question_edit_URL);
		$('#form1').submit();
		
	})
	
	$('#question_list_delete').click(function(){
		$('#form1').attr('action',question_list_delete_URL);
		$('#form1').submit();
		
	})
	
});      