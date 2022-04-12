$(function() {
	
	var delay = (function() {
		var timer = 0;
		return function(callback, ms) {
			clearTimeout(timer);
			timer = setTimeout(callback, ms);
		};
	})();
	$("a.page_link").each(function() {
		var page_num = $(this).text();
		if(page_num==1)
		{
		  var url = window.location.href;
		  if($('#page').val()=='index')
		  {
			/*var newurl = $('#paramlink_index').val();
			newurl = newurl+'1';*/
		  }
		  else
		  {
			var newurl = url.split('?')[0];
			newurl = newurl+'?page=1';
		  }
		  $(this).attr('href',newurl);
		}
		
		
	});
	if($('.prev_pagination').attr('href')!=undefined)
	{
		var prevlink =  $('.prev_pagination').attr('href');
		if($('#page').val()=='index')
		{
			/*var pagenum = $(".current a[class='page_link']").text();
			if(pagenum==2)
			{
				var newurl = $('#paramlink_index').val();
				newurl = newurl+'1';
			}*/
					
		}
		else
		{
			var index_val = prevlink.indexOf('page');
			if(index_val==-1)
			{
				var url = window.location.href;
				var newurl = url.split('?')[0];
				newurl = newurl+'?page=1';
			}
		}
		$('.prev_pagination').attr('href',newurl);
		
	}

	$('.close-button').click(function(){
		var urlid = $('#urlid_live').val();
		if(urlid=='' || urlid==undefined)
		{
			$('.reveal-overlay').css('display','none');
			$('#html_id').attr('class','no-js');
		}
	});


	/** admin dashboard page ***/
	$("#click_approval").click(function(){
		$.ajax({
			method: "POST",
			url:"adminDashboard",
			data:{action:'click_approval'},
			headers:{

				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				var url = window.location.href;
				var regex = new RegExp('/[^/]*$');
				var newurl = url.replace(regex, '/');
				window.location.href = newurl+'admin_dashboard_live';
			}
		});
	});

	/*** admin dashboard staging page ***/
	$("#CheckAll").click(function(){
		$(".table-checkboxes").prop('checked', $(this).prop('checked'));
	});

	
	
		
	/******************** user dashboard page **************************/
	$('#contentsubmit').click(function(){
		
		var url = $('#contenturl').val();
		$('#error').html('');
		$.ajax({
			method: "POST",
			url:"userDashboard",
			data:{processtype:'urlsubmit',url:url},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {	
				if(result.includes('Url')==true)
				{
					$('#contenturl').val('');
					$('#error').html(result);
				}
				else
				{	
					$('#contentdiv').attr('class','hide');
					$('.thank-you').css('display','block');
				}
			}
		});
	});

	$('#displaycontent').click(function(){
		$('#contentdiv').attr('class','');
		$('#contenturl').val('');
		$('.thank-you').css('display','none');
	});

	

	$('#savedshow_records').change(function()
	{
		remove_paginate();
		var search = $("#savedsearch").val();
		var limit = $('#savedshow_records').find(":selected").val();	
		$.ajax({
			method: "POST",
			url:"userDashboardSaved",
			data:{action:'common',processtype:'limit',search:search,limit:limit},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$('#savedrender').html(result).foundation();
			}
		});

	});
	
	/** search page  ****/
	$('.btntag').click(function() {
		var tag = $(this).text();
		var prev_tag = $('#tag_val').val();
		if(prev_tag!='')
		{
			tag = prev_tag+','+tag;
		}
		$('#tag_val').val(tag);
		
	}); 

	/*** admin_dashboard_live page   ***/
	$('#checkAll_live').click(function () {
		if($(this).is(":checked")){
		$('.table-checkboxes').prop('checked', true);
		}else{
		$('.table-checkboxes').prop('checked', false);
		}
	});
	$('#select_domain').change(function(){
		var domain = $('#select_domain').find(":selected").val();
		$('#domain_input').val(domain);
		var limit = $('#show_records').val();
		$('#live_publish_filter').val('all');
		var filter = $('#live_publish_filter').val();
		//$('#domain_input').val('');
		remove_paginate();
		$.ajax({
			method: "POST",
			url:"admin-dashboard-live",
			data:{action:'domain_change',domain:domain,limit:limit,filter:filter},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$('#domain_response').html(result);
			}
		});
	});
	$('#domain_score').change(function(){
		
		$('#popup3').css('display','block');
		$('#change_dscore').css('display','block');
		$('#change_dscore').attr('aria-hidden','false');

	});
	$('#confirm_dscore').click(function(){
		var domain_score = $('#domain_score').find(":selected").val();
		var domain = $('#domain').val();
		$.ajax({
			method: "POST",
			url:"adminDashboardLive",
			data:{action:'Dscore_change',domain_score:domain_score,domain:domain},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$("div.float-right select").val(domain_score);
				$('.reveal-overlay').css('display','none');
				$('#change_dscore').css('display','none');
				$('#change_dscore').attr('aria-hidden','true');
				$('#html_id').attr('class','no-js');
			}
		});
	});
	$('#btnadd_tag').click(function(){
		var actionname = $('#actionname').val();
		var tag = $('#addtag_txt').val();		
		if(tag!='')
		{
			var article_id = $('#artid_live').val();
			var url_id = $('#urlid_live').val();
			$.ajax({
				method: "POST",
				url:"adminDashboardLive",
				data:{action:'edit_changes',process:'add_tag',url_id:url_id,article_id:article_id,data:tag,actionname:actionname},
				headers:{
					'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
				},
				success: function(result) {
					if(result!='')
					{
						var tagdata='<a class="label tiny tag" data-closable>';
							tagdata+=tag;
							tagdata+='<button class="close-button" aria-label="Close alert" onclick=remove_tag('+result+') type="button" data-close>';
							tagdata+='<span aria-hidden="true">&times;</span>';
							tagdata+='</button></a>';
						$('#tag_div').append(tagdata);
						$('#addtag_txt').val('');
						$('.tag_datalist').attr('id','');
					}
				}
			});
		}
	});
	$('#btnadd_tag_staging').click(function(){
		var actionname = $('#actionname').val();
		var tag = $('#addtag_txt').val();		
		if(tag!='')
		{
			var article_id = $('#artid_live').val();
			if(article_id=='')
				save_article_tag();
			else
			{			
				var url_id = $('#urlid_live').val();
				$.ajax({
					method: "POST",
					url:"adminDashboardStaging",
					data:{action:'edit_changes',process:'add_tag',url_id:url_id,article_id:article_id,data:tag,actionname:actionname},
					headers:{
						'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
					},
					success: function(result) {
						if(result!='')
						{
							var tagdata='<a class="label tiny tag" data-closable>';
								tagdata+=tag;
								tagdata+='<button class="close-button" aria-label="Close alert" onclick=remove_tag_staging('+result+') type="button" data-close>';
								tagdata+='<span aria-hidden="true">&times;</span>';
								tagdata+='</button></a>';
							$('#tag_div').append(tagdata);
							$('#addtag_txt').val('');
							$('.tag_datalist').attr('id','');
						}
					}
				});
			}
		}
		else
		{
			$('#addtag_txt').focus();
			return false;
		}
	});
	
	
	$('#live_publish_filter').change(function(){
		remove_paginate();
		var domain = $('#domain').val();
		var limit = $('#show_records').val();
		var filter = $('#live_publish_filter').val();
		$.ajax({
			method: "POST",
			url:"adminDashboardLive",
			data:{action:'filter_changed',domain:domain,limit:limit,filter:filter},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$('#domain_response').html(result);
			}
		});
	});


	/*****    index page */
	$('#index_reload').click(function(){
		
		$('#index_search').val('');
		index_artclick();
		/*var newurl = $('#actual_link').val();
		window.location.href = newurl;*/
		
	});
	
	$('#search_header').keypress(function(e) {
		var key = e.which;
		if (key == 13) // the enter key code
		{
		  $('#tag_check_val').val('');
		  index_artclick();
		  return false;
		}
		
	});
		
	$("#index_search").on('input', function () 
	{
		
		var val = this.value;
		//var search_val = $('#index_search').val();
		if(val.length>1)
			$('.tag_datalist').attr('id','tag_datalist');
		else
			$('.tag_datalist').attr('id','');
		
		var options = $('datalist')[0].options;
		
		for (var i=0;i<options.length;i++)
		{
			if (options[i].value == $(this).val()) 
			{
				var tagval = $(this).val();
				$('#tag_check_val').val(tagval);
				break;
			}
		}
		check_tag_datalist();
		
		
	});

	
});	
 
function popup_close(idval)
{
	$('.reveal-overlay').css('display','none');
	$('#'+idval).css('display','none');
	$('#'+idval).attr('aria-hidden','true');
	$('#html_id').attr('class','no-js');
}

function articleclick(id)
{
	var artid = $('#artid'+id).val();
	var arturl = $('#arturl'+id).text();
	$.ajax({
		method: "POST",
		url:"search",
		data:{action:'articleclick',artid:artid,arturl:arturl},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {
			alert(result);
		}
	});
}
/**** admin dashboard staging page *****/
function stagingurlsubmit()
{
	remove_paginate();
	var url = new Array();
	var search = $('#domain').val();
	var status = $('#selectstatus').find(":selected").val();
	var limit = $('#show_records').find(":selected").val();
	url = $('#stagingaddurl').val();
	$.ajax({
		method: "POST",
		url:"adminDashboardStaging",
		data:{action:'addurl',url:url,status:status,search:search,limit:limit},
		headers:{

			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {
			$('#stagingaddurl').val('');
			$('#stagingrender').html(result).foundation();
			$('.reveal-overlay').css('display','none');
			$('#popup6').css('display','none');
			$('#html_id').attr('class','no-js');
		}
	});
}

function filterlimit_staging()
{
	$('#selectstatus').val('all');
	filterstaging();
}
function error_view(url_id)
{
	if(url_id!='')
	{
		$.ajax({
			method: "POST",
			url:"adminDashboardStaging",
			data:{action:'error_view',url_id:url_id},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$("#error_view").html(result);				
			}
		});
	}
}
function stagingprocess(processtype)
{
	var status = $('#selectstatus').find(":selected").val();
	var limit = $('#show_records').find(":selected").val();
	var search = $('#domain').val();
	var selected = new Array();
	$('#tblsort input[class="table-checkboxes"]:checked').each(function() {
		selected.push($(this).val());
	});
	if(selected.length==0)
	{
		$('#popup5').css('display','block');
		$('#stagingerror').css('display','block');
		$('#stagingerror').attr('aria-hidden','false');
	}
	else
	{
		remove_paginate();
		$.ajax({
			method: "POST",
			url:"adminDashboardStaging",
			data:{action:'common',processtype:processtype,stagingarr:selected,status:status,search:search,limit:limit},
			headers:{

				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {			
				$('.reveal-overlay').css('display','none');
				$('#'+processtype+'con').css('display','none');				
				$('#stagingrender').html(result).foundation();
				$('#approvalcount').html($('#staging').html());
				$('#html_id').attr('class','no-js');
			}
		}); 					
	}
}
function crawl(id)
{
	remove_paginate();
	var status = $('#selectstatus').find(":selected").val();
	var limit = $('#show_records').find(":selected").val();
	var search = $('#domain').val();
	$.ajax({
		method: "POST",
		url:"adminDashboardStaging",
		data:{action:'do_crawl',id:id,status:status,search:search,limit:limit},
		headers:{

			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {			
			$('#stagingrender').html(result).foundation();;			
		}
	}); 
}

function filterstaging()
{
	remove_paginate();
	var status = $('#selectstatus').find(":selected").val();
	var limit = $('#show_records').find(":selected").val();
	var search = $('#domain').val();	
	$.ajax({
		method: "POST",
		url:"adminDashboardStaging",
		data:{action:'status',status:status,search:search,limit:limit},
		headers:{

			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {
			$('#stagingrender').html(result).foundation();
		}
	});
}

/**** admin dashboard live page *****/
function livepage_process(process)
{
	$('#btn_'+process).hide();
	var url_ids=[]; 
	$('#tblsort input[class="table-checkboxes"]:checked').each(function() {
		url_ids.push($(this).val());
	});
	if(url_ids.length==0)
	{
		$('#popup8').css('display','block');
		$('#popup_error').css('display','block');
		$('#popup_error').attr('aria-hidden','false');
		$('#btn_'+process).show();
	}
	else
	{
		remove_paginate();
		var domain = $('#domain').val();
		var limit = $('#show_records').val();
		var filter = $('#live_publish_filter').val();
		$.ajax({
			method: "POST",
			url:"adminDashboardLive",
			data:{action:'common_action',flow:process,url_ids:url_ids,domain:domain,limit:limit,filter:filter},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				
				$("#domain_response").html(result);
				$('#approvalcount').html($('#staging').html());
				$('.reveal-overlay').css('display','none');
				$('#html_id').attr('class','no-js');
				$('#'+process).css('display','none');
				$('#'+process).attr('aria-hidden','true');
				$('#btn_'+process).show();
				
			}
		});
	}
}
function change_publish(url_id,element)
{
	remove_paginate();
	var domain = $('#domain').val();
	var limit = $('#show_records').val();
	var filter = $('#live_publish_filter').val();
	if(element.checked)
		var do_action = 'published';
	else
		var do_action = 'prepublished';
	$.ajax({
		method: "POST",
		url:"adminDashboardLive",
		data:{action:'change_publish',process:do_action,url_id:url_id,domain:domain,limit:limit,filter:filter},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {
			
			$("#domain_response").html(result);
			$('#approvalcount').html($('#staging').html());
		}
	});
}
function edit_popup(action)
{
	remove_paginate();
	var status = $('#selectstatus').find(":selected").val();
	var limit = $('#show_records').find(":selected").val();
	var filter = $('#live_publish_filter').val();
	var search = $('#domain').val();
	var actionname = $('#actionname').val();
	if(action=='prepublished' || action=='published')
	{
		var url_id = $('#urlid_live').val();
		var article_id = $('#artid_live').val();
		var domain = $('#domain').val();
		$.ajax({
			method: "POST",
			url:"adminDashboardLive",
			data:{action:'edit_changes',process:action,url_id:url_id,article_id:article_id,data:action,domain:domain,actionname:actionname,status:status,search:search,limit:limit,filter:filter},
			headers:{

				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {	
				if(action=='prepublished')
				{
					$('#edit_publish').text('Publish');
					$('#edit_publish').attr('onclick','edit_popup("published")');
				}
				else
				{
					$('#edit_publish').text('Unpublish');
					$('#edit_publish').attr('onclick','edit_popup("prepublished")');
				}
				if(actionname=='')	
					$("#domain_response").html(result);
				else
					$("#stagingrender").html(result).foundation();
				$('#approvalcount').html($('#staging').html());	
			}
		});
	}
	else if(action=='save')
	{
		remove_paginate();
		var formdata = $('#frmedit').serialize();
		var domain = $('#domain').val();
		$.ajax({
			method: "POST",
			url:"adminDashboardLive",
			data:{action:'edit_changes',domain:domain,process:action,data:formdata,actionname:actionname,status:status,search:search,limit:limit,filter:filter},
			headers:{

				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				if(actionname=='')	
					$("#domain_response").html(result);
				else
					$("#stagingrender").html(result).foundation();
			}
		});
	}
	else if(action=='re_crawl' || action=='delete')
	{
		remove_paginate();
		var url_id = $('#urlid_live').val();
		var article_id = $('#artid_live').val();
		var domain = $('#domain').val();
		$.ajax({
			method: "POST",
			url:"adminDashboardLive",
			data:{action:'edit_changes',domain:domain,process:action,url_id:url_id,article_id:article_id,actionname:actionname,status:status,search:search,limit:limit,filter:filter},
			headers:{

				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {				
				if(actionname=='staging')
				{
					$('#popup7').css('display','none');
					$("#stagingrender").html(result).foundation();
				}
				else
				{	
					$('#popup9').css('display','none');
					$("#domain_response").html(result);
				}
				$('#editArticle').css('display','none');
				$('#editArticle').attr('aria-hidden','true');
				$('#html_id').attr('class','no-js');
			}
		});
	}
}
function edit_popup_staging(action)
{
	remove_paginate();
	var status = $('#selectstatus').find(":selected").val();
	var limit = $('#show_records').find(":selected").val();
	var search = $('#domain').val();
	var actionname = $('#actionname').val();
	if(action=='prepublished' || action=='published')
	{
		var article_id = $('#artid_live').val();
		if(article_id!='')
		{
			if($('[class="label tiny tag"]').text()!='')
			{
				var url_id = $('#urlid_live').val();
				var domain = $('#domain').val();
				$.ajax({
					method: "POST",
					url:"adminDashboardStaging",
					data:{action:'edit_changes',process:action,url_id:url_id,article_id:article_id,data:action,domain:domain,actionname:actionname,status:status,search:search,limit:limit},
					headers:{

						'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
					},
					success: function(result) {	
						if(action=='prepublished')
						{
							$('#edit_publish').text('Publish');
							$('#edit_publish').attr('onclick','edit_popup("published")');
						}
						else
						{
							$('#edit_publish').text('Unpublish');
							$('#edit_publish').attr('onclick','edit_popup("prepublished")');
						}
						$("#stagingrender").html(result).foundation();	
						$('#approvalcount').html($('#staging').html());	
					}
				});
			}
			else
			{
				$('#addtag_txt').focus();
				return false;
			}
		}
		else
		{
			$('#edit_arttitle').focus();
			return false;
		}
	}
	if(action=='complete')
	{
		var url_id = $('#urlid_live').val();
		var article_id = $('#artid_live').val();
		var formdata = $('#frmedit').serialize();
		var domain = $('#domain').val();
		if(article_id!='')
		{
			if($('[class="label tiny tag"]').text()!='')
			{
				$.ajax({
					method: "POST",
					url:"adminDashboardStaging",
					data:{action:'edit_changes',process:action,url_id:url_id,article_id:article_id,data:formdata,domain:domain,actionname:actionname,status:status,search:search,limit:limit},
					headers:{

						'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
					},
					success: function(result) {	
						$("#stagingrender").html(result).foundation();
						$('#comp_btn_staging').text('Completed');
						$('#approvalcount').html($('#staging').html());	
					}
				});
			}
			else
			{
				$('#addtag_txt').focus();
				return false;
			}
			
		}
		else
		{
			$('#edit_arttitle').focus();
			return false;
		}
	}
	else if(action=='save')
	{
		remove_paginate();
		var formdata = $('#frmedit').serialize();
		var domain = $('#domain').val();
		$.ajax({
			method: "POST",
			url:"adminDashboardStaging",
			data:{action:'edit_changes',domain:domain,process:action,data:formdata,actionname:actionname,status:status,search:search,limit:limit},
			headers:{

				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$("#stagingrender").html(result).foundation();
			}
		});
	}
	else if(action=='re_crawl' || action=='delete')
	{
		remove_paginate();
		var url_id = $('#urlid_live').val();
		var article_id = $('#artid_live').val();
		var domain = $('#domain').val();
		$.ajax({
			method: "POST",
			url:"adminDashboardStaging",
			data:{action:'edit_changes',domain:domain,process:action,url_id:url_id,article_id:article_id,actionname:actionname,status:status,search:search,limit:limit},
			headers:{

				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {				
				$('#popup7').css('display','none');
				$("#stagingrender").html(result).foundation();
				
				$('#editArticle').css('display','none');
				$('#editArticle').attr('aria-hidden','true');
				$('#html_id').attr('class','no-js');
			}
		});
	}
}

function edit_article(url_id)
{
	var actionname = $('#actionname').val();
	if(url_id!='')
	{
		$.ajax({
			method: "POST",
			url:"adminDashboardLive",
			data:{action:'edit_view',url_id:url_id,actionname:actionname},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$("#editArticle").html(result);				
			}
		});
	}
}
function edit_article_staging(url_id)
{
	var actionname = $('#actionname').val();
	if(url_id!='')
	{
		$.ajax({
			method: "POST",
			url:"adminDashboardStaging",
			data:{action:'edit_view',url_id:url_id,actionname:actionname},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$("#editArticle").html(result);			
			}
		});
	}
}
function remove_tag(tag)
{
	var actionname = $('#actionname').val();
	if(tag!='')
	{
		var article_id = $('#artid_live').val();
		var url_id = $('#urlid_live').val();
		$.ajax({
			method: "POST",
			url:"adminDashboardLive",
			data:{action:'edit_changes',process:'remove_tag',article_id:article_id,data:tag,actionname:actionname},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
			
				
			}
		});
	}
}
function remove_tag_staging(tag)
{
	var actionname = $('#actionname').val();
	if(tag!='')
	{
		var article_id = $('#artid_live').val();
		var url_id = $('#urlid_live').val();
		$.ajax({
			method: "POST",
			url:"adminDashboardStaging",
			data:{action:'edit_changes',process:'remove_tag',article_id:article_id,data:tag,actionname:actionname},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
			
				
			}
		});
	}
}

/***** admin dashboard users page ***/
function admin_user_range()
{
	var limit = $('#admin_user_range').val();
	$.ajax({
			method: "POST",
			url:"adminDashboardUsers",
			data:{action:'limit_change',limit:limit},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$('#tbldata').html(result);
				
			}
		});
}
function user_process(action)
{
		$('#btnconfirm_'+action).hide();
		var limit = $('#admin_user_range').val();
		var user_ids=[]; 
		$('#tblsort input[class="table-checkboxes"]:checked').each(function() {
			user_ids.push($(this).val());
		});
		if(user_ids.length==0)
		{
			$('#popup4').css('display','block');
			$('#popup_error').css('display','block');
			$('#popup_error').attr('aria-hidden','false');
		}
		else
		{
			$.ajax({
				method: "POST",
				url:"adminDashboardUsers",
				data:{action:'user_process',process:action,user_ids:user_ids,limit:limit},
				headers:{
					'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
				},
				success: function(result) {
					$('#tbldata').html(result);
					$('#btnconfirm_'+action).show();
					$('.reveal-overlay').css('display','none');
					$('#do_'+action).css('display','none');
					$('#do_'+action).attr('aria-hidden',"true");
					$('#html_id').attr('class','no-js');
				}
			});
		}
}

function edituser_process(action)
{
	if(action=='save')
		var formdata = $('#frmedit').serialize();
	else
		var formdata = '';
	var userid = $('#user_id').val();
	var limit = $('#admin_user_range').val();
	$.ajax({
		method: "POST",
		url:"adminDashboardUsers",
		data:{action:'edituser_process',process:action,user_id:userid,formdata:formdata,limit:limit},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {
			$('#tbldata').html(result);
			$('#popup5').css('display','none');
			$('#editUser').css('display','none');
			$('#html_id').attr('class','no-js');
		}
	});
	
}

function change_userstatus(userid,element)
{
	
	if(element.checked)
		var action = 'unmute';
	else
		var action = 'mute';
	var limit = $('#admin_user_range').val();
	$.ajax({
		method: "POST",
		url:"adminDashboardUsers",
		data:{action:'user_edit',process:action,user_id:userid,limit:limit},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {
			$('#tbldata').html(result);
			
		}
	});
}
function edituser_view(userid)
{
	
	$.ajax({
		method: "POST",
		url:"adminDashboardUsers",
		data:{action:'edituser_view',user_id:userid},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {
			$('#editUser').html(result);
			
		}
	});

}

/*********************************user dashboard saved**************************************/
function savedarticle(processtype)
{
	var search = $("#savedsearch").val();
	var limit = $('#savedshow_records').find(":selected").val();
	var selected = new Array();
	$('#tblsort input[class="table-checkboxes"]:checked').each(function() {
		selected.push($(this).val());
	});
	if(selected.length==0)
	{
		$('#popup2').css('display','block');
		$('#savederror').css('display','block');
		$('#savederror').attr('aria-hidden','false');
	}
	else
	{
		remove_paginate();
		$.ajax({
			method: "POST",
			url:"userDashboardSaved",
			data:{action:'common',processtype:processtype,savedarr:selected,search:search,limit:limit},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {			
				$('.reveal-overlay').css('display','none');
				$('#saveddelete').css('display','none');				
				$('#savedrender').html(result).foundation();				
				$('#html_id').attr('class','no-js');
			}
		}); 					
	}
}

function user_delete(id,type)
{
	remove_paginate();
	var search = $("#savedsearch").val();
	var limit = $('#savedshow_records').find(":selected").val();
	$.ajax({
		method: "POST",
		url:"userDashboardSaved",
		data:{action:'common',processtype:'delete',id:id,type:type,search:search,limit:limit},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {								
			$('#savedrender').html(result).foundation();				
		}
	}); 
}

function sortTable(n,data_type=null) 
{
	var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
	table = document.getElementById("tblsort");
	switching = true;
	//Set the sorting direction to ascending:
	dir = "asc"; 
	/*Make a loop that will continue until
	no switching has been done:*/
	while (switching) {
	//start by saying: no switching is done:
	switching = false;
	rows = table.rows;
	/*Loop through all table rows (except the
	first, which contains table headers):*/
	for (i = 1; i < (rows.length - 1); i++)
	{
		//start by saying there should be no switching:
		shouldSwitch = false;
		/*Get the two elements you want to compare,
		one from current row and one from the next:*/
		x = rows[i].getElementsByTagName("TD")[n];
		y = rows[i + 1].getElementsByTagName("TD")[n];
		/*check if the two rows should switch place,
		based on the direction, asc or desc:*/

		value1 = x.innerHTML;
		value2 = y.innerHTML;

		if (dir == "asc")
		{
			if(data_type==null)
			{
				if(value1.toLowerCase() > value2.toLowerCase())
				{
					//if so, mark as a switch and break the loop:
					shouldSwitch= true;
					break;
				}
			}
			else if(data_type=='number')
			{
				if(Number(value1) > Number(value2))
				{
					shouldSwitch = true;
					break;
				}
			}
			else if(data_type=='date')
			{
				if(new Date(value1)>new Date(value2))
				{
					shouldSwitch= true;
					break;
				}
			}
			
		}
		else if (dir == "desc")
		{
			if(data_type==null)
			{
				if(value1.toLowerCase() < value2.toLowerCase())
				{
					//if so, mark as a switch and break the loop:
					shouldSwitch= true;
					break;
				}
			}
			else if(data_type=='number')
			{
				if(Number(value1) < Number(value2))
				{
					shouldSwitch = true;
					break;
				}
			}
			else if(data_type=='date')
			{
				if(new Date(value1)<new Date(value2))
				{
					shouldSwitch= true;
					break;
				}
			}
		}
	}
	if (shouldSwitch) {
		/*If a switch has been marked, make the switch
		and mark that a switch has been done:*/
		rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
		switching = true;
		//Each time a switch is done, increase this count by 1:
		switchcount ++;      
	} else {
		/*If no switching has been done AND the direction is "asc",
		set the direction to "desc" and run the while loop again.*/
		if (switchcount == 0 && dir == "asc") {
		dir = "desc";
		switching = true;
		}
	}
	}
}
function clear_txt(element)
{
	element.value='';
}

/*** index page */
function tagclick(tagval)
{
	var prev_tag = $('#tag_val').val();
	
	tagval=tagval.toLowerCase();
	tagval=tagval.trim(); 
	tagval=tagval.replace(/[^a-zA-Z0-9\.]+/g,"-");
	tagval=tagval.replace(/\.+/g, "-");
	tagval=tagval.replace(/-{2,}/g, '-');
	var last_tagval = tagval.charAt(tagval.length-1);
	if(last_tagval=='-')
	{
		tagval = tagval.slice(0, tagval.length - 1);
	}
	/*if(prev_tag!='')
	{
		var tag = prev_tag+','+tagval;
	}
	else*/
	var tag = tagval;
	$('#tag_val').val(tag);
	tag_filter(tagval);
}
function tag_filter(tagval='')
{
	/*$('#popup1').css('display','block');	
	$('#loading_div_index').css('display','block');*/
	remove_paginate_index(tagval);
	var tag = $('#tag_val').val();
	var index_search = $('#index_search').val();
	var index_search_txt = $('#index_search_txt').val(); 
	$.ajax({
		method: "POST",
		url:"",
		data:{action:'tag_filter',tag:tag,filter:index_search_txt,index_search:index_search},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {	
			$("#index_response").html(result).foundation();
			if($('#tag_search_index_head').val()=='')
			{
				$('#index_search').val('');
				$('.tag_datalist').attr('id','');
			}
			//if(typeof addthis !== 'undefined') { addthis.layers.refresh(); }
			/*$('#popup1').css('display','none');	
			$('#loading_div_index').css('display','none');*/
									
		}
	}); 
}
function remove_paginate()
{
	var url = window.location.href;
	var newurl = url.split('?')[0];
	window.history.pushState({ path: newurl }, '', newurl);
}
function remove_paginate_index(tagval='')
{
	var url = window.location.href;
	var regex = new RegExp('/[^/]*$');
	var newurl = $('#actual_link').val();
	if(tagval!='')
	{
		/*tagval=tagval.toLowerCase();
		tagval=tagval.trim(); 
		tagval=tagval.replace(/[^a-zA-Z0-9\.]+/g,"-");
		tagval=tagval.replace(/\.+/g, "-");
		tagval=tagval.replace(/-{2,}/g, '-');
		var last_tagval = tagval.charAt(tagval.length-1);
		if(last_tagval=='-')
		{
			tagval = tagval.slice(0, tagval.length - 1);
		}*/
		var title_tag = tagval.charAt(0).toUpperCase() + tagval.slice(1);
		title_tag=title_tag.replace(/-/g," ");
		var title = title_tag+' - Seeking Retirement';
		var meta_desc = 'Search the web for retirement information about '+tagval.replace(/-/g," ");
		var canonical_id = newurl+tagval;
		//tagval = tagval+'?page=1';
		
	}
	else
	{
		//newurl = newurl+'?page=1';
		var title = $('#conf_meta_tit').val();
		var meta_desc = $('#conf_meta_desc').val();
		var canonical_id = newurl+tagval;
	}
	
	$('#title_index').html(title);
	$('#meta_description').attr('content',meta_desc);
	$('#canonical_id').attr('href',canonical_id);
	$('#paramlink_index').val(canonical_id);
	window.history.pushState({ path: newurl }, '', newurl+tagval);
}
function remove_tag_index(tag)
{
	var prev_tag = $('#tag_val').val();
	//tag = tag.slice(0, -1);
	var arr = $('#tag_val').val().split(",");
	arr = arr.filter(val => val !== tag);
	var last_tag = arr.slice(-1)[0];
	newtag = arr.join();
	$('#tag_val').val(newtag);
	tag_filter(last_tag);
}
function save_article_index(artid,id)
{
	$.ajax({
		method: "POST",
		url:"",
		data:{action:'save_article',artid:artid},
		headers:{

			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {
			if(result=='signin')
			{
				var newurl = $('#actual_link').val();
				window.location.href = newurl+'users/'+result;
			}
			else 
			{
				$('#'+id).text('Saved');
			}
		}
	})
}
function cookieurl(url)
{
	$.ajax({
		method: "POST",
		url:"",
		data:{action:'cookie',url:url},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {			
		}
	});
}
function index_artclick()
{
	/*$('#popup1').css('display','block');	
	$('#loading_div_index').css('display','block');*/
	var index_search =  $('#index_search').val();
	var page = $('#page').val();
	if(page=='index')
		var url = "";
	else
		var url = page;
	//if(index_search!='')
	//{
		if(page=='index')
			remove_paginate_index();
		
		$.ajax({
			method: "POST",
			url:url,
			data:{action:'article_search',index_search:index_search},
			async:false,
			headers:{

				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				if(page=='index')
				{
					//$('#filter_container').attr('class','filter');
					$("#index_response").html(result).foundation();
					
				}
				else
				{
					var url = window.location.href;
					var regex = new RegExp('/[^/]*$');
					var newurl = window.location.href;
					newurl = newurl.substring(0, newurl.lastIndexOf('/'));
					window.location.href = newurl;
				}	
				
			}
		})
		return false;
	/*}
	else
	{
		var url = window.location.href;
		var regex = new RegExp('/[^/]*$');
		var newurl = url.replace(regex, '/');
		window.location.href = newurl;
	}*/
			
}

function staging_search()
{
	var status = $('#selectstatus').find(":selected").val();
	var limit = $('#show_records').find(":selected").val();
	var search = $('#domain').val();
	remove_paginate();
	if(search!='')
	{
		$.ajax({
			method: "POST",
			url:"adminDashboardStaging",
			data:{action:'search',status:status,search:search,limit:limit},
			headers:{

				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$('#stagingrender').html(result).foundation();
			}
		});
	}
	else
	{
		var url = window.location.href;
		var newurl = url.split('?')[0];
		window.location.href = newurl;
	}
}
function domain_btnsearch()
{
	var domain = $('#domain_input').val();
	var limit = $('#show_records').val();
	var filter = $('#live_publish_filter').val();
	if(domain=='')
	{
		$('#popup2').css('display','block');
		$('#domain_error').css('display','block');
		
	}
	else
	{
		remove_paginate();
		$.ajax({
			method: "POST",
			url:"adminDashboardLive",
			data:{action:'domain_change',domain:domain,limit:limit,filter:filter},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$("div.top-bar-left select").val(domain);
				$('#domain_response').html(result);
			}
		});
	}

}
function btn_tag_search()
{
	var tagval = $('#txt_tag_search').val();
	if(tagval!='')
	{
		var prev_tag = $('#tag_val').val();
		if(prev_tag!='')
		{
			var tag = prev_tag+','+tagval;
		}
		else
			var tag = tagval;
		$('#tag_val').val(tag);
		tag_filter(tagval);
	}
}

function live_show_filter()
{
	remove_paginate();
	var domain = $('#domain').val();
	var limit = $('#show_records').val();
	var filter = $('#live_publish_filter').val();
	$.ajax({
		method: "POST",
		url:"adminDashboardLive",
		data:{action:'range_changed',domain:domain,limit:limit,filter:filter},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {
			$('#domain_response').html(result);
		}
	});
	
}

function article_search()
{
	remove_paginate();
	var search = $("#savedsearch").val();
	var limit = $('#savedshow_records').find(":selected").val();
	if(search!='')		
	{
		$.ajax({
			method: "POST",
			url:"userDashboardSaved",
			data:{action:'common',processtype:'search',search:search,limit:limit},
			headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {			
				$('#savedrender').html(result).foundation();			
			}
		}); 
	}
	else
	{
		var url = window.location.href;
		var newurl = url.split('?')[0];
		window.location.href = newurl;
	}
}

function cancel_setting()
{
	$('.reveal-overlay').css('display','none');
	$('#confirmation').css('display','none');
}
function confirm_setting()
{
	$.ajax({
		method: "POST",
		url:"adminSetting",
		data:{action:'recrawl_setting'},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {	
			/**here***/
			var url = window.location.href;
			var regex = new RegExp('/[^/]*$');
			var newurl = url.replace(regex, '/');
			window.location.href = newurl+'admin_dashboard_staging';
		}
	}); 
}

function check_tag_datalist()
{
	
	var tag_checkval = $('#tag_check_val').val();
	var page = $('#page').val();
	if(page=='index')
		var url = "";
	else
		var url = page;
	if(tag_checkval!='')
	{
		setTimeout(function() {
			var search_val = $('#index_search').val();
			if($('#tag_check_val').val()==search_val)
			{
				if(page=='index')
					remove_paginate_index();
				$.ajax({
					method: "POST",
					url:url,
					data:{action:'article_search',tag_select:search_val},
					headers:{

						'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
					},
					success: function(result) {
						if(page=='index')
						{
							//$('#filter_container').attr('class','filter');
							$("#index_response").html(result).foundation();
						}
						else
						{
							var url = window.location.href;
							var regex = new RegExp('/[^/]*$');
							var newurl = window.location.href;
							newurl = newurl.substring(0, newurl.lastIndexOf('/'));
							window.location.href = newurl;
						}
					
					}
				})
			}
		}, 1500);
	}
	
}
function sorting_index(sortmode)
{
	var arr = $('#tag_val').val().split(",");
	var last_tag = arr.slice(-1)[0];
	remove_paginate_index(last_tag);
	$.ajax({
		method: "POST",
		url:"",
		data:{action:'sorting_index',sortmode:sortmode},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {	
			$("#index_response").html(result).foundation();
		}
	});
	
}

function tag_datalist_admin(val)
{
	if(val.length>1)
		$('.tag_datalist').attr('id','tag_datalist');
	else
		$('.tag_datalist').attr('id','');

}

function save_article_tag()
{
	if($('#edit_arttitle').val()=='')
	{
		$('#edit_arttitle').focus();
		return false;
	}
	else if($('#edit_artdomain').val()=='')
	{
		$('#edit_artdomain').focus();
		return false;
	}
	else
	{
		var formdata = $('#frmedit').serialize();
		$.ajax({
			method: "POST",
			url:"adminDashboardStaging",
			data:{action:'save_article_tag',data:formdata},
			headers:{

				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success: function(result) {
				$("#editArticle").html(result);					
			}
		});
	}
	
}
function art_live_search()
{
	remove_paginate();
	var domain = $('#domain').val();
	var limit = $('#show_records').val();
	var filter = $('#live_publish_filter').val();
	var search_val = $('#art_title_search').val();
	$.ajax({
		method: "POST",
		url:"adminDashboardLive",
		data:{action:'article_search',search:search_val,domain:domain,limit:limit,filter:filter},
		headers:{
			'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
		},
		success: function(result) {
			
			$("#domain_response").html(result);
			$('#approvalcount').html($('#staging').html());
			$('.reveal-overlay').css('display','none');
			$('#html_id').attr('class','no-js');
		}
	});
}

function kwgrp_match_click()
{
	remove_paginate();
	var kwgrp_match = $('textarea#kwgrp_match').val();
	var limit = $('#show_records').val();
	if(kwgrp_match!='')
	{
		if(kwgrp_match.indexOf(':')>-1)
		{
			$.ajax
			({
				method : 'POST',
				url	   : 'adminMatch',
				data   : {action:'match_request',limit:limit,kwgrp_match:encodeURIComponent(kwgrp_match)},
				headers:{
						'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
				},
				success:function(result)
				{
					$('.reveal-overlay').css('display','none');
					$('#popup1').css('display','none');
					$('#html_id').attr('class','no-js');
					$('#match_div').html(result);
					
				}
			});
		}
		else
		{
			$('#popup4').css('display','block');
			$('#warning_match').css('display','block');
		}
	}
}

function editmatch()
{
	var selected = new Array();
	var limit = $('#show_records').val();
	$('#tblsort input[class="checkbox_match"]:checked').each(function() {
		selected.push($(this).val());
	});
	if(selected.length>0)
	{
		remove_paginate();
		$('#btn_edit_match').css('display','none');
		$.ajax
		({
			method : 'POST',
			url	   : 'adminMatch',
			data   : {action:'edit_match',selected:selected,limit:limit},
			headers:{
					'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
					
			},
			success:function(result)
			{
				$('#btn_edit_match').css('display','block');
				$('#editmatch').html(result);
				$('#popup2').css('display','block');
				$('#editmatch').css('display','block');
				$('#html_id').attr('class','no-js');
				
			}
		}); 
	}
	else
	{
		$('#popup5').css('display','block');
		$('#warning_match_select').css('display','block');
	}
}

function match_show_filter()
{
	remove_paginate();
	var limit = $('#show_records').val();
	$.ajax
	({
		method : 'POST',
		url	   : 'adminMatch',
		data   : {action:'match_limit',limit:limit},
		headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
				
		},
		success:function(result)
		{
			$('#match_div').html(result);
		}
	});
}

function match_update_click()
{
	remove_paginate();
	var limit = $('#show_records').val();
	if($('#all_chk_match').is(':checked'))
	{
		if(limit=='all')
		{
			var update_meth = 'all';
			var formdata = $("#kwphr_edit").val();
		}
	}
	else
	{
		var update_meth = 'partial';
		var formdata = $("#frm_match_edit").serialize();
	}
	
	$.ajax
	({
		method : 'POST',
		url	   : 'adminMatch',
		data   : {action:'match_update',update_method:update_meth,formdata:formdata,limit:limit},
		headers:{
				'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
				
		},
		success:function(result)
		{
			$('.reveal-overlay').css('display','none');
			$('#popup2').css('display','none');
			$('#html_id').attr('class','no-js');
			$('#match_div').html(result);
		}
	});
}

function delete_match()
{
	var selected = new Array();
	var limit = $('#show_records').val();
	$('#tblsort input[class="checkbox_match"]:checked').each(function() {
		selected.push($(this).val());
	});
	if(selected.length==0)
	{
		$('.reveal-overlay').css('display','none');
		$('#popup3').css('display','none');
		$('#html_id').attr('class','no-js');
		
		$('#popup5').css('display','block');
		$('#warning_match_select').css('display','block');
	}
	else
	{
		if($('#all_chk_match').is(':checked'))
		{
			if(limit=='all')
			{
				var delete_meth = 'all';
			}
		}
		else
		{
			var delete_meth = 'partial';
		}
		remove_paginate();
		$.ajax
		({
			method : 'POST',
			url	   : 'adminMatch',
			data   : {action:'delete_match',selected:selected,limit:limit,delete_method:delete_meth},
			headers:{
					'X-CSRF-Token':$('meta[name="csrfToken"]').attr('content')
			},
			success:function(result)
			{
				$('.reveal-overlay').css('display','none');
				$('#popup3').css('display','none');
				$('#html_id').attr('class','no-js');
				$('#match_div').html(result);
			}
		}); 
	}
}

function allcheck_match(element)
{
	if($(element).is(":checked"))
	{
		$('.checkbox_match').prop('checked', true);
	}
	else
	{
		$('.checkbox_match').prop('checked', false);
	}
}
	
	