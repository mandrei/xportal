@layout('backend/inc/template')

@section('container')
			
			<style type="text/css">

				#container{
					padding-left:0;
					padding-right: 0;
				}

			</style>

            @if(Session::has('folder_empty'))

                 <div class="alert alert-error">

                            <button type="button" class="close" data-dismiss="alert">&times;</button>
     
                                <span>
                                    The folder you have tried to download was empty.
                                </span>

                 </div>

            @endif

			 <div id='left-section' class='left'>


                <div id='left-section-holder'>

                    <ul id="tree">

                        @foreach($folders as $f)

                            <li>  

                                @if( $f['name'] == 'My Profile' ) 

                                    <span onmouseleave="unhovering($(this))" onclick='window.open("{{$f["route"]}}")' class="folders"> <span class="folder-type"></span>  {{$f['name']}}</span>

                                @else

                                    <span folder_route='{{$f["route"]}}' class='folders' onmouseenter='hovering($(this))' onmouseleave='unhovering($(this))'  onclick='treeClick($(this))'> <span class='folder-type'></span>  {{$f['name']}} </span>

                                @endif

                             </li>

                        @endforeach


                    </ul>

                    @if(Session::get('user.type') == 1 || Session::get('user.type') == 0 )

                   <div style="margin-left:18px;margin-top: 15px;cursor: pointer;" onclick="refresh_list()">

                       <span class="icon-refresh"></span> Refresh List

                   </div>

                    @endif

                </div><!--end left holder -->

            </div><!--left section -->



			<div id='right-section'>

				<div id='right-section-holder' >


                     @if(Users_Auth::has_access( 5 ) )

					<div id='sidebar' class='right'>

						 	<div class="alert alert-error" id="upload_errors" 

                                                    style="display:none;">
                        
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
         
                                    <span>


                                    </span>

                             </div>

                         <div class="alert alert-success" id="upload_success"  style="display:none;" >

                            <button type="button" class="close" data-dismiss="alert">&times;</button>
     
                                <span>
                                    
                                </span>

                         </div>

                        <div id='upload-files-holder' class="hidden">

                            
                            <form name="add_form" enctype="multipart/form-data" method="POST" accept-charset="UTF-8">

                                    <input type="hidden" name="selected_folder" value="">

                                    <input type="hidden" name="folder_route" value="">

                                    <input type="hidden" name="csrf_token" value="pzr4timknoJ1YcBxhajGXF3SqsWYpY8dm0mcLFfi">

                                    <div id='input-holder' class='btn btn-primary'>

                                        Select files
                                        <input id="file_upload_input" type='file' onchange='filesName();' name='uploaded_files[]' multiple />

                                    </div><!--input holder -->

                                    <button class='btn btn-primary'>Select :what</button>

                                    <div id='files-name'></div>

                                    <div class='clear'></div>

                                   <input type='button' onclick="Portal.addFiles();return false;" id='upload_button' class='btn btn-primary right' value='upload' style='margin-top:15px; width:120px;'/>
                        
                                
                            </form><!--end uploading form -->

                        </div><!--end upload files older -->


                        <span id='accepted-formats' class="hidden">(Accepted formats: .pdf, .doc, .docx, .png, .jpg, .gif, .xls, Max:20Mb)</span>

                        <div id="download_button" class="hidden">


                        <br />

                        <button class='btn btn-primary download-button' onclick="Portal.downloadFolder();return false;">Download folder</button>

                        <br />


                        </div>

                        <br />

                        <!-- start folder information -->

						<div id='folder-information' class='sidebar-section hidden'>

							<div class='table-header'>

								{{__('common.folder_information')}}

							</div><!--table header -->

							<ul>

								<li id='total-files'>{{ __('common.total_files') }}:<span></span></li>

								<li id='file-size'>{{ __('common.size') }}:<span></span></li>

								<li id='created-on'>{{ __('common.created_on') }}:<span></span></li>

								<li id='last-edited'>{{ __('common.last_edited') }}:<span></span></li>

							</ul>

						</div><!--end folder information -->


						<div id='last-files-added' class='sidebar-section hidden'>

							<div class='table-header'>

								{{__('common.last_files_added')}}

							</div><!--end table header -->

							<ul style="overflow: hidden;">

                                

							</ul>

						</div><!--end last files added -->


                        <br />

                        <div id="folder_create_and_delete_actions" class="hidden">

                            @if(Users_Auth::has_access(3))

                                <input type='button' class='btn btn-primary right' value='delete folder' onclick="Portal.deleteFolder();return false;"/>

                                @if(Users_Auth::has_access(4))

                                    <input type='button' class='btn btn-primary left' value='create folder' onclick="Portal.createFolder();return false;"/>

                                @endif

                            @endif

                        </div>


					</div><!--end sidebar -->

                    @endif


					<div id='files-container'>

                        <ul id='action-bar'>

                            <li>{{ __('common.actions') }}</li>

                            @if(Users_Auth::has_access(3) )

                                <li onclick='Portal.deleteFiles();return false;' title='Delete file'><div id='action-delete'></div></li>

                            @endif

                            <li onclick='Portal.downloadFiles();return false;' title='Download file'><div id='action-download'></div></li>

                        </ul>

						<ul id='files-list'>


						</ul><!--files list -->

					</div><!--end files-section -->


				</div><!--end right holder -->

			</div><!--end right section -->



<!-- Messages -->
<input type="hidden" id="csrf_token" value='{{ Session::token() }}'>
<input type="hidden" value="{{__('common.no_files_found')}}" id="no_files_found">
<input type="hidden" value="{{__('common.no_files_selected')}}" id='no_files_selected'>
<input type="hidden" value="{{__('common.files_deleted')}}" id="files_deleted">
<input type="hidden" value="{{__('common.not_all_files_deleted')}}" id="not_all_files_deleted">
<input type="hidden" value="{{__('common.upload_files_success')}}" id='upload_files_success'>
<input type="hidden" value="{{__('common.upload_files_error')}}" id='upload_files_error'>
<input type="hidden" value="{{__('common.upload_files_error_size')}}" id='upload_files_error_size'>
<input type="hidden" value="{{__('common.folder_name')}}" id='folder_name'>
<input type="hidden" value="{{__('common.folder_first')}}" id="folder_first">
<input type="hidden" value="{{__('common.are_you_sure_to_delete',array('what' => 'folder? <br/>All files and subfolders will be lost !'))}}" id="sure_delete">
<input type="hidden" value="{{__('common.are_you_sure_to_delete',array('what' => 'files'))}}" id="sure_delete_files">
<input type="hidden" value="{{__('common.folder_not_deleted')}}" id="folder_not_deleted">
<input type="hidden" value="{{__('common.folder_deleted')}}" id="folder_deleted">
<input type="hidden" value="{{__('common.folder_name_error')}}" id="folder_name_error">
<input type="hidden" value="{{__('common.error_create_folder')}}" id="error_create_folder">
<input type="hidden" name='selected_folder_from_post' value="{{Input::get('folder_id',0)}}">


<!-- End messages -->
			

			<!--making divs same height -->

			<script type="text/javascript">

				$('#left-section').css('min-height',$('#right-section').height());

			</script>

			<!--making divs same height -->
          
<!-- <div class='selector'></div> -->

<input type='hidden' value='portal_page' id='for-tutorial'>

@section('tutorials')

@parent

	{{__('tutorials.portal', array('user' => Session::get('user.name') ))}}

@endsection


@endsection

@section('footer_js')

@parent


{{HTML::script('js/backend/portal.js')}}


@endsection