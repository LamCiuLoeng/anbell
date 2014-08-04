$(document).ready(function(){

	$.get(ssq_handle_URL, function(data){
		
		var htm = ''
		for(var i=0;i<data.length;i++)	{
			if(data[i].parent_code == null)
			{
				htm+= ('<option value='+data[i].code+'>'+data[i].name+'</option>')
			}
		}
		$('#select_province').append(htm)
		
		var province_code = $('#select_province').val()
		var htm = ''
		for(var i=0;i<data.length;i++)	{
			if(data[i].parent_code == province_code)
			{
				htm+= ('<option value='+data[i].code+'>'+data[i].name+'</option>')
			}
		}
		$('#select_city').append(htm)
		
		var city_code = $('#select_city').val()
		var htm = ''
		for(var i=0;i<data.length;i++)	{
			if(data[i].parent_code == city_code)
			{
				htm+= ('<option value='+data[i].code+'>'+data[i].name+'</option>')
			}
		}
		$('#select_area').append(htm);
		
		//alert(data);
		$('#select_province').change(function(){
			var province_code = $('#select_province').val()
			var htm = ''
			for(var i=0;i<data.length;i++)	{
				if(data[i].parent_code == province_code)
				{
					htm+= ('<option value='+data[i].code+'>'+data[i].name+'</option>')
				}
			}
			$('#select_city').html(htm)
			
			var city_code = $('#select_city').val()
			var htm = ''
			for(var i=0;i<data.length;i++)	{
				if(data[i].parent_code == city_code)
				{
					htm+= ('<option value='+data[i].code+'>'+data[i].name+'</option>')
				}
			}
			$('#select_area').html(htm)
		})
		
		$('#select_city').change(function(){
			var city_code = $('#select_city').val()
			var htm = ''
			for(var i=0;i<data.length;i++)	{
				if(data[i].parent_code == city_code)
				{
					htm+= ('<option value='+data[i].code+'>'+data[i].name+'</option>')
				}
			}
			$('#select_area').html(htm)
		})
		
	}); 
	
	
	
});      