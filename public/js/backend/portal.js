	//making right section elements selectable 
	$('#files-list').selectable();

	//making left section resizable
	$('#left-section').resizable();


	var Portal = {

	    'elements': "id",

	    /**
	    * Initializing portal
	    * Getting all parent directories
	    */
	    initPortal :{

	    		 initTree : function(){



						

				}//initTree

	    },//initPortal



	    /*
	    * Open parents opens left tree, shows right side files
	    */
	    openFolders: function(folder_route, elem){

	    	// console.log(folder_route)

	    	$.ajax({
				url: xPortal.base_url + '/safe/portal/get_folders_and_files',
                cache: false,
                type: "GET",
                data: { folder_route: folder_route, csrf_token: $('#csrf_token').val() },
                dataType: 'json'
				}).success(function(data){

					elem.next('ul').remove();

					//check if there is data in subfolders
					if(data.subfolders.length>0 ){

					//creating element to append
					var to_append = '';

					//add a ul to element to append
					to_append+='<ul>';

						//creating a loop
						for ( var i=0; i < data.subfolders.length; i++ ){



							if(data.subfolders[i].name == 'My Profile') {


								// //adding data to element to append
								to_append += '<li> <span onmouseleave="unhovering($(this))" onclick="window.open(\''+data.subfolders[i].route+'\')" go_to="'+data.subfolders[i].route+'" class="folders"> <span class="folder-type"></span> '+data.subfolders[i].name+'</span></li>';


							}//of
							else {

								//adding data to element to append
								to_append += '<li> <span folder_route="'+data.subfolders[i].route+'" onmouseenter="hovering($(this))" onmouseleave="unhovering($(this))" onclick="treeClick($(this))" class="folders"> <span class="folder-type"></span> '+data.subfolders[i].name+'</span></li>';

							}//else



						}//loop

					//closing ul
					to_append += '</ul>';

					//showing the created element
					$(elem).parent().append(to_append).find('>ul').show();

					//adding open folder icon
					// $(elem).find('span.folders').addClass('selected_folder').find('span.folder-type').addClass('selected-folder-white');


					};//if there are any subfolders

					show_actions();

					console.log(data)

					if(data.files.length>0){

						 	//show files in folder
							Portal.openFiles(data);

							Portal.rightSideData(data);

							$('#action-bar').show();

							$('#last-files-added').show();

						 }else{

						 	$('#action-bar').hide();
						 	
						 	//empty list
						 	$('#files-list').html('');

				
					
						 	Portal.rightSideData(data);

						 	$('#last-files-added').hide();

					}

				});

			// add_classes();

	    	// console.log('opening branch');

	    },//opneTreeBranch

	    //show files in folder
	    openFiles: function(data){

	    	var to_append = '';

	    	$('#files-list').html('');

	    	//creating a loop
			for ( var i=0; i < data.files.length; i++ ){

				var ext_name = data.files[i].name.split('.');

				//adding data to element to append
				to_append += '<li file_route="'+data.files[i].route+'"><span> '+ext_name[ext_name.length-1]+'</span> <br />'+ext_name[0]+'</li>';

			}//loop

			$('#files-list').html(to_append);

			// $('#sidebar').removeClass('hidden');

	    },

	    /*
	    * Close parents
	    */
	    closeTreeFolders: function(elem){

	    	$(elem).find('>ul').remove();

	    },//closeTreeBranch

	    rightSideData: function(data){

	    	var base = data.folder_information;

            console.log(base)


	    	//folder information
	    	$('#total-files span').html(base.total_files);


            if( base.folder_size != undefined  ) {
                $('#file-size span').html(base.folder_size);
            }else {
                $('#file-size span').html('-');
            }


	    	$('#file-size span').html(base.folder_size);


	    	$('#created-on span').html(base.folder_creation);

	    	$('#last-edited span').html(base.folder_modification);

	    	$('#folder_create_and_delete_actions').removeClass('hidden');

	    	$('#folder-information').removeClass('hidden'); 



	    	//last files added - to do
	    	Portal.lastFilesAdded(base.last_files_added);


	    },

	    addFiles: function(){

	    		$('#upload_button').hide();

	    		$('#upload_errors').hide();

				$('#upload_success').hide();	


				var folder_route = $('.selected_folder').attr('folder_route');


				if( folder_route == undefined ) {

					bootbox.alert( $('#folder_first').val(), function() { this.close; $('#upload_button').show(); });
					
				} //if
				else{

							$('input[name="folder_route"]').val(folder_route);


							var files = $('input[name="uploaded_files[]"]')[0].files;

								if(files.length == 0) {

									bootbox.alert($('#no_files_selected').val(), function() { this.close; $('#upload_button').show(); });


								}//if no file was selected
								else {

									formdata = false;
										
									if (window.FormData) {
										formdata = new FormData();
										
									}

							        
							        for ( i = 0; i < files.length; i++ ) {  

							            file = files[i];  

							            formdata.append("uploaded_files[]", file);

							        }//endfor  


							         	if (formdata) {


							         		 	formdata.append('folder_route',folder_route);

							         		 	

							                     $.ajax({  
												        url: xPortal.base_url + "/safe/portal/upload_files",  
												        type: "POST", 
												        data: formdata,
												        processData: false,  
					        							contentType: false,  
					        						
												        success: function (res) {

												        	if(res == 'error') {

												        		$('#upload_errors').find('span').html($('#upload_files_error').val());

												        		$('#upload_errors').show();	
												        	}//
												        	else if( res == 'file_size') {

												        		$('#upload_errors').find('span').html($('#upload_files_error_size').val());

												        		$('#upload_errors').show();	
												        	}

												        	else{

												 				
												 				/*
												 				*
												 				*  Clear inputs
												 				*
												 				*/
												 				var oldInput = document.getElementById("file_upload_input");
					     
															    var newInput = document.createElement("input"); 
															     
															    newInput.type 			= "file"; 
															    newInput.id 			= oldInput.id; 
															    newInput.name 			= oldInput.name; 
															    newInput.multiple 		= oldInput.multiple; 
															    newInput.className 		= oldInput.className; 
															    newInput.style.cssText 	= oldInput.style.cssText;
															    newInput.onchange		= function(){

																	var files = $("#file_upload_input")[0].files;


															    } 
															    // copy any other relevant attributes 
															     
															    oldInput.parentNode.replaceChild(newInput, oldInput);



												        		$('#files-name').html('');
															    /*
															    *
															    *  Show uploaded files
															    *
															    */
												        		$('#files-list').append(res);


												        		$('#upload_success').find('span').html($('#upload_files_success').val());

												        		$('#upload_success').show();

												        		$('#action-bar').show();


												        		// update_folder_informations(folder_route);

												        		// $('#file_upload_input').remove();

												        		$('#file_upload_input').change(function(){

												        			filesName();

												        		});



												        		/*
							                            		*
							                            		*  Remove deleted files and update right side
							                            		*
							                            		*/
							                            		// remove_files(data.deleted_files, folder_route);

							                            		treeClick($('.selected_folder'));

												        	}//else

												      		
												          $('#upload_button').show();

												        }//success
												     

												    }); //ajax 

					                      	}//formdata



									}//if at least 1 file was selected

				}//endif

				

	    },//add files

	    deleteFiles: function(){

	    	/*
			*
			*  Get selected files
			*
			*/
			var selected_files = $('li.ui-selected');

			var routes = [];


			/*
			*
			*  add files ids to ids
			*
			*/
			selected_files.each(function(){

				var route = $(this).attr('file_route');

				routes.push(route);

			});




			if(routes.length == 0) {

				bootbox.alert($('#no_files_selected').val(), function() { this.close });

			}//if no file was selected
			else {

				bootbox.confirm($('#sure_delete_files').val(),




					function(result) { 

						if (!result) {

							this.close;

						}//if not confirmeds
						else{


							this.close;

							//selected folder's route
							selected_folder = $('.selected_folder').attr('folder_route');

				                   $.ajax({
		                            url: xPortal.base_url + '/safe/portal/delete_files',
		                            cache: false,
		                            type: "POST",
		                            data: { routes: routes , folder_route:selected_folder, csrf_token: $('#csrf_token').val() },
		                            dataType: 'json',
		                            success: function(data)
		                            {

		                            	/*
		                            	*
		                            	*  Get folder id if all the files are from a single folder
		                            	*
		                            	*/
		                            	var folder_route = $('.selected_folder').parent().attr('folder_route');

		                            	

		                            	/*
		                            	*
		                            	*  Remove files from list
		                            	*
		                            	*/
		                            	if(data.error_code == 1) {

		                            		bootbox.alert($('#no_files_selected').val(), function() { this.close });	

		                            	}//if no ids
		                            	else if(data.error_code == 2) {

		                            		bootbox.alert($('#no_files_deleted').val(), function() { this.close });	

		                            	}//if no files were not deleted
		                            	else {

		                            		/*
		                            		*
		                            		*  Remove deleted files and update right side
		                            		*
		                            		*/
		                            		// remove_files(data.deleted_files, folder_route);

		                            		treeClick($('.selected_folder'));

		                            	}//if the files were deleted

		                            	

		                            }//on success,

		                    });//ajax


						}//if confirmed 

				 	});

			}//if at least a file was selected


	    },//delete files


	    downloadFiles: function(){
	    	/*
			*
			*  Get selected files
			*
			*/
			var selected_files = $('li.ui-selected');

			var routes = '';

			/*
			*
			*  add files ids to ids
			*
			*/
			selected_files.each(function(){

				var route = $(this).attr('file_route');

				if(route != undefined) {
					routes += route+':|:';
				}

			});

			if(routes.length == 0) {

				bootbox.alert($('#no_files_selected').val(), function() { this.close });

			}//if no file was selected
			else{

				console.log(routes)

				window.open(xPortal.base_url + '/safe/portal/download_files?routes=' + routes);

			}//if at least a file was selected

	    },//downloadFiles

	    downloadFolder: function(){

    		var folder_route = $('.selected_folder').attr('folder_route');


			if( folder_route == undefined ) {

				bootbox.alert($('#folder_first').val(), function() { this.close });
				
			} //if
			else{

				window.open(xPortal.base_url + '/safe/portal/download_folder?folder_route=' + folder_route);

			}//if a folder was selected

	    },


	    /*
	    * Cand se adauga un fisier - nu apare in lista din stanga numai daca dai click pe folderul parinte
	    *
	    */
	    createFolder: function(){

	    	var folder_route = $('.selected_folder').attr('folder_route');

			if( folder_route == undefined ) {

				bootbox.alert($('#folder_first').val() , function() { this.close });
				
			} //if
			else{

				bootbox.prompt($('#folder_name').val(), function(result) {       


				 if (result === null ) {

				 	this.close;

				 }//if cancel button was pressed
				 else if (result === '') {    

				    bootbox.alert($('#folder_name_error').val(), function() {}); 

				  }//if ok button was hit but no name was completed
				  else {

				                   $.ajax({
		                            url:  xPortal.base_url + '/safe/portal/add_folder',
		                            cache: false,
		                            type: "POST",
		                            data: {  csrf_token: $('#csrf_token').val(), folder_name: result,folder_route:folder_route },
		                            dataType: 'json',
		                            success: function(data)
		                            {

		                            	/*
		                            	*
		                            	*  returnez folder route
		                            	*
		                            	*/	

		                            	/*
		                            	*
		                            	*  Remove files from list
		                            	*
		                            	*/
		                            	if(data.error == 1) {

		                            		bootbox.alert(data.error_message, function() { this.close });	

		                            	}//if no routes
		                            	else if(data.error == 0 ){

		                            		/*
		                            		* Check if the parent folder alredy has subfolders
		                            		*
		                            		*/



		                            		var to_append = '<li> <span folder_route="'+data.folder_route+'"  onmouseenter="hovering($(this))" onmouseleave="unhovering($(this))" onclick="treeClick($(this))" class="folders"> <span class="folder-type"></span> '+result+'</span></li>';


		                            		// $('.selected_folder').after(to_append);console.log



		                            		/*
		                            		* Not working ok 
		                            		*
		                            		*/
		                            		if( $('span.selected_folder').parent().find('ul:first').html() != undefined ) {

		                                        $('span.selected_folder').parent().find('ul:first').append(to_append);

		                                    }else {

		                                        $('span.selected_folder').parent().append('<ul>'+to_append+'</ul>');
		                                    }

		                                    //showing added folder
		                                    $('span.selected_folder').parent().find('>ul').show();
		                            		

		                            	}//if the files were deleted

		                            	
		                            }//on success
		                           
		                    });//ajax

				  }//if folder name was completed

				});

			}//if a folder was selected

	    },//createFolder


	    /*
	    * Cand se sterge un fisier - nu dispare din lista din stanga numai daca dai click pe folderul parinte
	    *
	    * Cred ca ar tb si un mesaj ca folderul a fost sters ! 
	    */
	    deleteFolder: function(){

	    	var folder_route = $('.selected_folder').attr('folder_route');

			if( folder_route == undefined ) {

				bootbox.alert( $('#folder_first').val(), function() { this.close });
				
			} //if
			else{


					bootbox.confirm($('#sure_delete').val(), 

					function(result) { 

						if (!result) {

							this.close;

						}//if not confirmeds
						else{

							this.close;

				                   $.ajax({
		                            url: xPortal.base_url + '/safe/portal/delete_folder',
		                            cache: false,
		                            type: "POST",
		                            data: { folder_route: folder_route ,  csrf_token: $('#csrf_token').val() },
		                            dataType: 'json',
		                            success: function(data)
		                            {

		                            	/*
		                            	*
		                            	*  Remove files from list
		                            	*
		                            	*/
		                            	if(data.error == 1) {

		                            		bootbox.alert(data.error_message, function() { this.close });	

		                            	}//if no routes
		                            	else {

		                            		bootbox.alert($('#folder_deleted').val(), function() { this.close });

		                            		$('.selected_folder').parent().remove();	

		                            		hide_actions();

		                            		$('#files-list li').html('');

		                            		$('#files-list li').css('display','none');

		                            		$('#action-bar').hide();

		                            		$('.alert').hide();

		                            	}//if the files were deleted

		                            	
		                            }//on success
		                           
		                    });//ajax



						}//if confirmed 

				 	});

			}//if a folder was selected

	    },//delete folder

	    lastFilesAdded: function(data){

	    	$('#last-files-added ul').html('');

	    	var to_append = '';

	    	// $('#first-last-file span').html()
	    	for( i=0; i<data.length; i++ ){

	    		to_append += '<li><span>'+data[i]+'</span></li>';

	    	}

	    	$('#last-files-added ul').append(to_append);

	    	$('#last-files-added').show();
	    }

	};//end Portal object



    function refresh_list() {

        $.ajax({
            url: xPortal.base_url + '/safe/portal/refresh_list',
            cache: false,
            type: "POST",
            dataType: 'json'
        })//ajax
           .error(function( error ) {
                alert(error)
            });//ajax



    }//refresh_list


/*
*
*	function used for showing files name to be uploaded
*
*/
function filesName(){

	var files = $('input[name="uploaded_files[]"]')[0].files;

	var render_info='';

	for (var i = 0; i < files.length; i++){

		var split_val = files[i].name;


		split_val2 = split_val.split('\\');

		for ( var j=0; j < split_val2.length; j++ ){

		 	render_info += split_val2[j]+', ';

		}

		
		
		take_lenght = split_val2.length-1;

	}

	//render data
	$('#files-name').html(render_info);

}

// /*
// *
// *  Click event on tree folders
// *
// */
function treeClick(elem){

	var folder_route = elem.attr('folder_route');

	// $('.selected_folder').parents('li.parent').find('span.folders .folder-type').addClass('opened-folder');

	$('.folders').removeClass('selected_folder').find('.folder-type').removeClass('selected-folder-white');

	elem.addClass('selected_folder').find('.folder-type').addClass('selected-folder-white');

	Portal.openFolders(folder_route, elem);
	
}

$('#tree li span').bind('click', function(){

	$('.alert').hide();

});

// /*
// *
// *  Remove deleted files from 
// *
// */
function remove_files(deleted_files,folder_route) {

	for (var i=0; i<deleted_files.length; i++ ) {

		console.log(deleted_files[i])

		$('li[file_route="'+deleted_files[i]+'"]').remove();

		// console.log($('li[file_route="'+deleted_files[i]+'"]').length);	

	}//end for



	bootbox.alert($('#files_deleted').val(), function() { this.close });	

	if (folder_route != undefined ) { update_folder_informations(folder_route) };

	//hide action bar if no files are displayed
	if($('#files-list li').length<1){

	$('#action-bar').hide();

	}

}//remove files

function show_actions() {

	  		/*
             * Show available actions
             *
             */
            $('#upload-files-holder').removeClass('hidden');
            $('#download_button').removeClass('hidden');
            $('#folder_create_and_delete_actions').removeClass('hidden');
            $('#accepted-formats').removeClass('hidden');
            $('#folder-information').removeClass('hidden');
            $('#last-files-added').removeClass('hidden');
           

}//showactions

function hide_actions() {

			$('#upload-files-holder').addClass('hidden');
            $('#download_button').addClass('hidden');
            $('#folder_create_and_delete_actions').addClass('hidden');
            $('#accepted-formats').addClass('hidden');
            $('#folder-information').addClass('hidden');
            $('#last-files-added').addClass('hidden');
    
}//hide actions



function hovering(elem){

	if(elem.find('.opened-folder')){

			elem.find('.opened-folder').addClass('opened-folder-hover');
			elem.css('color', 'white')

		}

			elem.find('.folder-type').addClass('closed-folder-hover');
			elem.css('color','white');

};

function unhovering(elem){

			if(elem.find('.opened-folder')){

				elem.find('.opened-folder').removeClass('opened-folder-hover');
				elem.css('color', 'inherit');

			}

				elem.find('.folder-type').removeClass('closed-folder-hover');
				elem.css('color','inherit');


}


	$(document).ready(function(){

		//if there are no files on files list hide action bar
		if($('#files-list li').length<1){

			$('#action-bar').hide();
			
		}

		//marking the parents
		$('#tree>li').addClass('parent');


		$(document).ajaxSend(function(){

			loading_start();

		});

		$(document).ajaxComplete(function(){

			loading_stop();

		});



	});//doc ready
