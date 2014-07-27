@layout('backend/inc/template')

@section('container')


@if($count == 0 )

    <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo __('common.no_clients') ?>
    </div>

@else

      @if($total_clients == 0)

        @if($search != '!' )

            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo __('common.no_client_found') . $search ?>
            </div>

    @endif

@endif


<div class='table-container'>

    <div class='table-header'>

        <div class='search-table left' >

            <input type='text' placeholder="<?php echo __('common.search') ?>" link_to_search='safe/clients' />

        </div><!--end search table -->


        <div class='filter-toggle right' ></div>
        <!--end filter -->

        <div class='clear'></div>

    </div><!--end table header -->

    <div class='table-filter' @if($client_type == 0 ) style="display: none;" @else style="display:block;" @endif>

        <div class='left ' style="margin-top:15px;">

          <label> <strong>Per page</strong> </label>

          <select name='per_page'>
            <option value='999999999999'>All</option>
            <option value='5' @if($per_page == 5) selected='selected' @endif> 5 </option>
            <option value='10' @if($per_page == 10) selected='selected' @endif> 10 </option>
            <option value='20' @if($per_page == 20) selected='selected' @endif> 20 </option>
            <option value='30' @if($per_page == 30) selected='selected' @endif> 30 </option>
            <option value='40' @if($per_page == 40) selected='selected' @endif> 40 </option>
            <option value='50' @if($per_page == 50) selected='selected' @endif> 50 </option>
            <option value='100' @if($per_page == 100) selected='selected' @endif> 100 </option>
          </select>

        <label> <strong>Client type</strong></label>

          <select name="client_type" >
            
            <option value="0">All</option>

            @foreach($client_types as $type)

              <option value="{{$type->id}}" @if($client_type == $type->id) selected="selected" @endif >{{$type->name}}</option>

            @endforeach

          </select>



           <button onclick="filter();" class="btn btn-primary " style="margin:10px;">Filter</button>
        


        </div><!--end left container -->

        <div class='filter-button-container right'>




        </div><!--end filter button container -->

        <div class='clear'></div>

    </div><!--end filter -->

    @endif

    @if( $total_clients != 0 )

    <table class="table table-bordered filter-serach-option">

        <tr>

            {{ Tools::sortable_table_head($table_head, $order_by, $order_direction, 'safe/clients', array('search' => $search, 'client_type' => $client_type, 'per_page' => $per_page) ) }}

        </tr>

        @foreach( $clients as $u )


        <tr class="listuser">
            <td style='width:50px;text-align:center;'>
              <input type='checkbox' value='{{$u->id}}' name='clients[]'>
              </td>
            <td>{{ $u->type }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->username }}</td>
      
            <td style='width:200px!important;'>
                @if($u->update_client == 1)

                    <a href="#" class="btn btn-warning"  id_of_user="{{ $u->id }}">
                        {{ __('common.updated_details') }}
                    </a>

                @endif

                @if($u->new_files == 1)

                 <a href="#" class="btn btn-success"  onclick="send_alert({{$u->id}});" id="new_files_{{$u->id}}">
                    <!-- <i class="icon-exclamation-sign"></i> -->
                    {{ __('common.new_files_alert') }}
                </a>

                @endif

                <a href="#" class="btn user_details"  id_of_user="{{ $u->id }}">
                    <!-- <i class="icon-arrow-up"></i> -->
                    {{ __('common.details') }}
                </a>

                <a href="{{ URL::base() }}/safe/client/{{ $u->id }}/edit" class="btn btn-info">
                    <!-- <i class="icon-edit"></i> -->
                    {{ __('common.edit') }}
                </a>


                <a href="#" onclick="confirm( '{{ __('common.please_confirm') }}' ,'{{ __('common.are_you_sure_delete') }}', '/safe/client/{{ $u->id }}/delete')" class="btn btn-danger">
                    <!-- <i class="icon-remove"></i> -->
                    {{ __('common.delete') }}
                </a>



            </td>
        </tr>



        @endforeach


    </table>

    <div class='tfoot-check-all'> 

      <label><input type='checkbox' onclick='check_uncheck_all($(this));' name='all'><span>Check all</span></label>

      <div class='clear'></div>

    </div>

    <div id='pagination'>

        <div class='count_tr'>Showing {{$found_clients}} out of {{$total_clients}} clients</div>

        {{ $links }}

         <div class='clear'></div>

    </div><!--end pagination -->

    <br />

     <div style="margin-left:10px">

          <input type='radio' value='1' name='separate_files' style="margin-bottom:5px;"> <span> Separate files for individuals and corporate </span>

          <button onclick='export_to_csv();' class='btn btn-success'>Export to csv</button>  
          

        </div>

    @endif

</div><!--end table container -->



<!--clients DETAILS MODAL-->
<div id="modal_view_user" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
        <h3 id="modal_view_user_title"></h3>
    </div>
    <div class="modal-body" id="modal_view_user_body">


    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">{{ __('common.close') }}</button>
    </div>
</div>


<input type='hidden' value='clients' id='for-tutorial'>

@section('tutorials')

@parent

    {{__('tutorials.clients_all', array('user' => Session::get('user.name') ))}}

@endsection


@endsection

@section('footer_js')

@parent

{{HTML::script('js/common/backend/tutorial.js')}}


<script type="text/javascript">



    $('.user_details').on('click', function(){

        /*
         * Get the id of the event
         */
        var user_id = $(this).attr('id_of_user');


        /*
         * Show ajax animation
         */
        loading_start();


        $.ajax({
            url: xPortal.base_url + '/safe/client/' + user_id,
            type: 'get',
            dataType: 'json',
            cache:false

        })//ajax
            .always(function() {

                loading_stop();

            })//always
            .fail(function() {

                alert("{{ __('common.ajax_fail') }}")

            })//on Fail
            .done(function(data){

                if( data.error == 1)
                {

                    alert("{{ __('common.ajax_fail') }}");


                }//if we have errors
                else
                {

                    if (data.client_type_id == 1) {

                        $('#modal_view_user_title').html(data.first_name + ' ' + data.last_name);

                    }//if individual
                    else {

                        $('#modal_view_user_title').html(data.name);

                    }//if company
                    

                    /*
                     * Set the body
                     */
                    var event_body = '<table>';

                    /*
                    *
                    *  Common
                    *
                    */
                   event_body += '<tr>';

                    event_body += "<td colspan='2'> <strong>Identification:</strong> </td>" ;

                    event_body += '</tr>';



                   event_body += '<tr>';

                    event_body += "<td colspan='2'> &nbsp;</td>" ;

                    event_body += '</tr>';





                      if (data.client_type_id == 1) {

                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.calendar_year_start') }}: </td><td>" + data.calendar_year_start + "</td>";

                    event_body += '</tr>';


                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.title') }}: </td><td>" + data.title + "</td>";

                    event_body += '</tr>';


                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.first_name') }}: </td><td>" + data.first_name + "</td>";

                    event_body += '</tr>';


                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.last_name') }}: </td><td>" + data.last_name + "</td>";

                    event_body += '</tr>';


                      event_body += '<tr>';

                    event_body += "<td> {{ __('common.default_client_name') }}: </td><td>" + data.default_client_name + "</td>";

                    event_body += '</tr>';



                   if(data.birth_date == '0000-00-00')
                   {

                        event_body += '<tr>';

                        event_body += "<td> {{ __('common.birth_date') }}: &nbsp;&nbsp;</td><td>" + "</td>";

                        event_body += '</tr>';

                   }//if there is no date
                   else
                   {
                       event_body += '<tr>';

                       event_body += "<td> {{ __('common.birth_date') }}: &nbsp;&nbsp;</td><td>" + data.birth_date + "</td>";

                       event_body += '</tr>';

                   }//else show birth date

                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.marital_status') }}: &nbsp;&nbsp;</td><td>" + data.marital_status + "</td>";

                    event_body += '</tr>';

                  }//if individual

                 else if(data.client_type_id == 2)
                 {

                   event_body += '<tr>';

                   event_body += "<td> {{ __('common.name') }}: &nbsp;&nbsp;</td><td>" + data.name + "</td>";

                   event_body += '</tr>';

                    event_body += '<tr>';

                   event_body += "<td> {{ __('common.calendar_year_start') }}: &nbsp;&nbsp;</td><td>" + data.corporation_calendar_year_start + "</td>";

                   event_body += '</tr>';

                 
                  event_body += '<tr>';

                  event_body += "<td> {{ __('common.contact_person_last_name') }}: &nbsp;&nbsp;</td><td>" + data.contact_person_last_name + "</td>";

                  event_body += '</tr>';


                  event_body += '<tr>';

                  event_body += "<td> {{ __('common.contact_person_first_name') }}: &nbsp;&nbsp;</td><td>" + data.contact_person_first_name + "</td>";

                  event_body += '</tr>';


                  event_body += '<tr>';

                  if(data.position != 0) {

                      event_body += "<td> {{ __('common.position') }}: &nbsp;&nbsp;</td><td>" + data.position + "</td>";

                  }else {

                    event_body += "<td> {{ __('common.position') }}: &nbsp;&nbsp;</td><td> - </td>";

                  }

                  
              
                }//if corporation




                    event_body += '<tr>';

                    event_body += "<td colspan='2'> &nbsp;</td>" ;

                    event_body += '</tr>';



                   event_body += '<tr>';

                    event_body += "<td colspan='2'> <strong> {{ __('common.mailing_address') }} </strong> </td>" ;

                    event_body += '</tr>';



                   event_body += '<tr>';

                    event_body += "<td colspan='2'> &nbsp;</td>" ;

                    event_body += '</tr>';



                    event_body += "<td> {{ __('common.street_address') }}: </td><td>" + data.street_address + "</td>";

                    event_body += '</tr>';


                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.city') }}: </td><td>" + data.city + "</td>";

                    event_body += '</tr>';



                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.country') }}: </td><td>" + data.country_name + "</td>";

                    event_body += '</tr>';

                   if(data.country == 1)
                   {

                       event_body += '<tr>';

                       if(data.state) {
                         event_body += "<td> {{ __('common.state') }}: </td><td>" + data.state + "</td>";
                       }else {
                         event_body += "<td> {{ __('common.state') }}: </td><td> - </td>";
                       }

                      

                       event_body += '</tr>';


                       event_body += '<tr>';

                       event_body += "<td> {{ __('common.zip_code') }}: </td><td>" + data.zip_code + "</td>";

                       event_body += '</tr>';

                   }//if its usa
                   else
                   {

                        event_body += '<tr>';

                        if(data.province) {
                        event_body += "<td> {{ __('common.province') }}: </td><td>" + data.province + "</td>";
                       }else {
                         event_body += "<td> {{ __('common.province') }}: </td><td> - </td>";
                       }

                        

                        event_body += '</tr>';


                        event_body += '<tr>';

                        event_body += "<td> {{ __('common.postal_code') }}: </td><td>" + data.postal_code + "</td>";

                        event_body += '</tr>';

                   }//else its canada



                    event_body += '<tr>';

                    event_body += "<td colspan='2'> &nbsp;</td>" ;

                    event_body += '</tr>';



                    event_body += '<tr>';

                    event_body += "<td colspan='2'>  <strong>{{ __('common.telephone') }} </strong> </td>" ;

                    event_body += '</tr>';



                    event_body += '<tr>';

                    event_body += "<td colspan='2'> &nbsp;</td>" ;

                    event_body += '</tr>';



                    event_body += '</tr>';

                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.phone') }}: &nbsp;&nbsp;</td><td>" + data.phone + "</td>";

                    event_body += '</tr>';


                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.fax') }}: </td><td>" + data.fax + "</td>";

                    event_body += '</tr>';



                    event_body += '<tr>';

                    event_body += "<td colspan='2'> &nbsp;</td>" ;

                    event_body += '</tr>';



                   event_body += '<tr>';

                    event_body += "<td colspan='2'> <strong> Client Account </strong></td>" ;

                    event_body += '</tr>';



                   event_body += '<tr>';

                    event_body += "<td colspan='2'> &nbsp;</td>" ;

                    event_body += '</tr>';



                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.email') }}: </td><td>" + data.email + "</td>";

                    event_body += '</tr>';


                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.username') }}: </td><td>" + data.username + "</td>";

                    event_body += '</tr>';


                    $('#modal_view_user_body').html(event_body);



                    /*
                     * Open modal
                     */
                    $('#modal_view_user').modal('show');


                }//if we don't have errors

            });//AJAX done



    });//user details on click



    $('.btn-warning').on('click', function(){

        /*
         * Get the id of the event
         */
        var user_id = $(this).attr('id_of_user');


        /*
         * Show ajax animation
         */
        loading_start();


        $.ajax({
            url: xPortal.base_url + '/safe/client/' + user_id + '/view',
            type: 'get',
            dataType: 'json',
            cache:false

        })//ajax
            .always(function() {

                loading_stop();

            })//always
            .fail(function() {

                alert("{{ __('common.ajax_fail') }}")

            })//on Fail
            .done(function(data){

                if( data.error == 1)
                {

                    alert("{{ __('common.ajax_fail') }}");


                }//if we have errors
                else
                {

                    $("a.btn.btn-warning[id_of_user='"+user_id+"']").remove();


                }//if we don't have errors

            });//AJAX done



    });//user details on click



    function filter() {

       var client_type_id = $('select[name="client_type"]').val();

       var per_page = $('select[name="per_page"]').val();

       location.href = "{{URL::to('safe/clients?client_type="+client_type_id+"&per_page="+per_page+"')}}";

    }//filter



    function send_alert(user_id) {

       loading_start();


       $.ajax({
            url: xPortal.base_url + '/safe/portal/' + user_id + '/alert_new_files',
            type: 'get',
            dataType: 'json'

        })//ajax
            .always(function() {

                loading_stop();

            })//always
            .fail(function() {

                alert("{{ __('common.ajax_fail') }}")

            })//on Fail
            .done(function(data){

                if( data.error == 1)
                {

                    alert("{{ __('common.ajax_fail') }}");


                }//if we have errors
                else
                {


                  //remove button 
                  $('#new_files_'+user_id).remove();

                  bootbox.alert( 'The client has been notified!', function() {  });


                }//if we don't have errors

            });//AJAX done


    }//send alert



    function export_to_csv(){

      var clients = '';

       $('input[name="clients[]"]:checked').each(function(){

          clients += $(this).val()+','

       });

       clients = clients.slice(0,-1);

       var client_type =  $('select[name="client_type"]').val();

       var separate_files =  $('input[name="separate_files"]:radio:checked').val();

       if(separate_files == undefined) separate_files = 0;

       location.href = "{{URL::to('safe/clients/export?clients="+clients+"&client_type="+client_type+"&separate_files="+separate_files+"')}}";


    }//export


    function check_uncheck_all(elem) {

      var how = false;

      if(elem.is(':checked') ) {
          how = true;
      }

      $('input[name="clients[]"]').each(function(){

        $(this).prop('checked',how);

      });


      if(how) {

        $('input[name="all"]').parent().find('span').html('Uncheck all'); 

      }//if checked
      else {

        $('input[name="all"]').parent().find('span').html('Check all'); 

      }

    }//select_all

</script>

@endsection
