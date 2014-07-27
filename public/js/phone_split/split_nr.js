$('.phone-holder input').keyup(function(e){

     var last_input_lenght = $(this).parent().find('.last_input').attr('mlenght');


	input_val = $(this).val();

	//if the number was introduced and delete jump from input to input backwards.

	if (e.keyCode == 8 && input_val.length == 0 || e.keyCode == 46 && input_val.length == 0 ) {

		$(this).prev().focus();

	}

	//if this is not last child max length will be 3 && jump to next input
	if( !$(this).is(':last-child') && input_val.length > 2 ){

		$(this).prop('maxlength','3').next().focus();

	}else{

		$(this).prop('maxlength', last_input_lenght);

	}

	if( $(this).is(':first-child') && input_val.length>2 ){

		take_whole_nr = $(this).val();

		

		var first_split = take_whole_nr.slice(0,3);

		var second_split = take_whole_nr.slice(3,6);

		var last_split = take_whole_nr.slice(6);

		$(this).parent().find('input:nth-child(1)').val(first_split);
		$(this).parent().find('input:nth-child(2)').val(second_split);
		$(this).parent().find('input:nth-child(3)').val(last_split);


	}

	var first_input = $(this).parent().find('input:first-child').val();

	if(first_input.length == 0){

		$(this).parent().find('input').prop('maxlength','10');

	}

});




