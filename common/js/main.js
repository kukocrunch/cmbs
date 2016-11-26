$(document).ready(function(){
	$("#accountForm").validator();

	$(".page-item").on("click", function(){
		var offset = $(this).data("offset");

		$(".page-item").not($(this)).parent().removeClass("active");
		$(this).parent().addClass("active");
		
		$.post('/home/getTransactionLogs',
		{
			offset: offset,
			limit: 30,
			nonce: $("#nonce").val()
		}, function(data){
			var object = JSON.parse(data);
			$("#logContents").html('');
			for(var i = 0; i < object.length; i++){
				$("#logContents").append('<tr><td>'+object[i].id+'</td><td>'+object[i].account_number+
					'</td><td>'+object[i].terminal_id+'</td><td>'+object[i].amount+'</td><td>'+object[i].transaction_type+
					'</td><td>'+object[i].timestamp+'</td></tr>');
			}
		});
	});
});
