$(document).ready(function(){
	function setheight(){
		if($('.sub_content').height()<500){
			$('.sub_content').height(500)
		}
	}
	setTimeout('setheight()',1000);
	
	
	var folder_path = '/anbels/Home/Public/images/';
	function switch_image(current_img,replace_img) 
	{
		$("img[src='"+folder_path+current_img+"']").hover(
		  function () {
			$(this).attr("src",folder_path+replace_img); 
		  },
		  function () {
			$(this).attr("src",folder_path+current_img); 
		  }
		); 
	}
	
	function switch_background(current_img,replace_img) 
	{
		$("img[src='"+folder_path+current_img+"']").hover(
		  function () {
			$(this).attr("src",folder_path+replace_img); 
		  },
		  function () {
			$(this).attr("src",folder_path+current_img); 
		  }
		); 
	}
	
	switch_image('home_06.jpg','home_06_h.jpg');
	switch_image('home_07.jpg','home_07_h.jpg');
	switch_image('home_08.jpg','home_08_h.jpg');
	switch_image('home_09.jpg','home_09_h.jpg');
	switch_image('home_10.jpg','home_10_h.jpg');
	switch_image('home_11.jpg','home_11_h.jpg');
	
	switch_image('safe_resource_13.jpg','safe_resource_13_h.jpg');

	
	$("img[src='/anbels/Home/Public/images/guanyuanbeier_06.jpg']").hover(
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/guanyuanbeier_06_h.jpg"); 
	  },
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/guanyuanbeier_06.jpg"); 
	  }
	); 
	
	$("img[src='/anbels/Home/Public/images/guanyuanbeier_08.jpg']").hover(
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/guanyuanbeier_08_h.jpg"); 
	  },
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/guanyuanbeier_08.jpg"); 
	  }
	); 
	
	$("img[src='/anbels/Home/Public/images/guanyuanbeier_10.jpg']").hover(
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/guanyuanbeier_10_h.jpg"); 
	  },
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/guanyuanbeier_10.jpg"); 
	  }
	); 
	
	$("img[src='/anbels/Home/Public/images/news_06.jpg']").hover(
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/news_06_h.jpg"); 
	  },
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/news_06.jpg"); 
	  }
	); 
	
	$("img[src='/anbels/Home/Public/images/news_08.jpg']").hover(
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/news_08_h.jpg"); 
	  },
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/news_08.jpg"); 
	  }
	); 
	
	$("img[src='/anbels/Home/Public/images/news_10.jpg']").hover(
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/news_10_h.jpg"); 
	  },
	  function () {
		$(this).attr("src","/anbels/Home/Public/images/news_10.jpg"); 
	  }
	); 
});
