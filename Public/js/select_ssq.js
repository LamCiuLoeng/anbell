$(document).ready(function(){

	$.get(school_handle_URL, function(data){
		
		var htm = ''
		for(var i=0;i<data.length;i++)	{
			if(data[i].parent_id == null)
			{
				htm+= ('<option value='+data[i].id+'>'+data[i].name+'</option>')
			}
		}
		$('#select_province').html(htm)
		
		var province_id = $('#select_province').val()
		var htm = ''
		for(var i=0;i<data.length;i++)	{
			if(data[i].parent_id == province_id)
			{
				htm+= ('<option value='+data[i].id+'>'+data[i].name+'</option>')
			}
		}
		$('#select_city').html(htm)
		
		var city_id = $('#select_city').val()
		var htm = ''
		for(var i=0;i<data.length;i++)	{
			if(data[i].parent_id == city_id)
			{
				htm+= ('<option value='+data[i].id+'>'+data[i].name+'</option>')
			}
		}
		$('#select_area').html(htm);
		
		//alert(data);
		$('#select_province').change(function(){
			var province_id = $('#select_province').val()
			var htm = ''
			for(var i=0;i<data.length;i++)	{
				if(data[i].parent_id == province_id)
				{
					htm+= ('<option value='+data[i].id+'>'+data[i].name+'</option>')
				}
			}
			$('#select_city').html(htm)
			
			var city_id = $('#select_city').val()
			var htm = ''
			for(var i=0;i<data.length;i++)	{
				if(data[i].parent_id == city_id)
				{
					htm+= ('<option value='+data[i].id+'>'+data[i].name+'</option>')
				}
			}
			$('#select_area').html(htm)
		})
		
		$('#select_city').change(function(){
			var city_id = $('#select_city').val()
			var htm = ''
			for(var i=0;i<data.length;i++)	{
				if(data[i].parent_id == city_id)
				{
					htm+= ('<option value='+data[i].id+'>'+data[i].name+'</option>')
				}
			}
			$('#select_area').html(htm)
		})
		
	}); 
	
});      