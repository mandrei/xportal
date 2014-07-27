@layout('backend/inc/template')

@section('container')


@if($count == 0 )

    <div class="alert alert-error">
         <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo __('common.no_users') ?>
    </div>

@else

      @if($total_users == 0)

        @if($search != '!' )

            <div class="alert alert-error">
                 <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo __('common.no_user_found')  .$search ?>
            </div>

    @endif

@endif

@if(Session::has('deleted'))

            <div class="alert alert-success">
                 <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{__('common.user_deleted')}}
            </div>

@endif


<div class='table-container'>

    <div class='table-header'>

        <div class='search-table left' >

            <input type='text' placeholder="<?php echo __('common.search') ?>" link_to_search='safe/users' />

        </div><!--end search table -->

        <!--        <div class='filter-toggle right' ></div><!--end filter -->

        <div class='clear'></div>

    </div><!--end table header -->

    <div class='table-filter'>

        <div class='left'>


        </div><!--end left container -->

        <div class='filter-button-container right'>


        </div><!--end filter button container -->

        <div class='clear'></div>

    </div><!--end filter -->

    @endif

    @if( $total_users != 0 )

    <table class="table table-bordered filter-serach-option">

        <tr>

            {{ Tools::sortable_table_head($table_head, $order_by, $order_direction, 'safe/users', array('search' => $search) ) }}

        </tr>

        @foreach( $users as $u )


        <tr class="listuser">
            <td>{{ $u->first_name }}</td>
            <td>{{ $u->last_name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->username }}</td>
            <td>{{ $u->phone }}</td>
            <td style='width:200px!important;'>
                <a href="#" class="btn user_details"  id_of_user="{{ $u->id }}">
                    <!-- <i class="icon-arrow-up"></i> -->
                    {{ __('common.details') }}
                </a>

                <a href="{{ URL::base() }}/safe/user/{{ $u->id }}/edit" class="btn btn-info">
                    <!-- <i class="icon-edit"></i> -->
                    {{ __('common.edit') }}
                </a>


                <a href="#" onclick="confirm( '{{ __('common.please_confirm') }}' ,'{{ __('common.are_you_sure_delete') }}', '/safe/user/{{ $u->id }}/delete')" class="btn btn-danger">
                    <!-- <i class="icon-remove"></i> -->
                    {{ __('common.delete') }}
                </a>

            </td>
        </tr>


        @endforeach


    </table>

    <div id='pagination'>

        {{ $links }}

        <div class='clear'></div>

    </div><!--end pagination -->


    @endif

</div><!--end table container -->



<!--Users DETAILS MODAL-->
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

<input type='hidden' value='users' id='for-tutorial'>

@section('tutorials')

@parent

    {{__('tutorials.users_all', array('user' => Session::get('user.name') ))}}

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
            url: xPortal.base_url + '/safe/user/' + user_id,
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

                    console.log(data)


                    /*
                     * Set the title
                     */
                    $('#modal_view_user_title').html(data.first_name + ' ' + data.last_name);


                    /*
                     * Set the body
                     */
                    var event_body = '<table>';

                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.first_name') }}: </td><td>" + data.first_name + "</td>";

                    event_body += '</tr>';


                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.last_name') }}: </td><td>" + data.last_name + "</td>";

                    event_body += '</tr>';


                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.email') }}: </td><td>" + data.email + "</td>";

                    event_body += '</tr>';


                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.username') }}: &nbsp;&nbsp;</td><td>" + data.username + "</td>";

                    event_body += '</tr>';


                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.address') }}: &nbsp;&nbsp;</td><td>" + data.address + "</td>";

                    event_body += '</tr>';


                    event_body += '<tr>';

                    event_body += "<td> {{ __('common.phone') }}: &nbsp;&nbsp;</td><td>" + data.phone + "</td>";

                    event_body += '</tr>';



                    event_body += '<tr>';

                    event_body += "<td> &nbsp; </td>";

                    event_body += '</tr>';



                    event_body += '<tr>';

                    event_body += "<td style=\"vertical-align:text-top;\"> {{ __('common.permissions') }}: &nbsp;&nbsp;</td><td><ul>" ;


                        for (i in data.permissions) {
                            if (data.permissions[i].permission == 1) 
                            event_body += '<li>'+data.permissions[i].name+'</li>';
                        }

                    event_body +=  "</ul></td>";

                    event_body += '</tr>';






                    $('#modal_view_user_body').html(event_body);



                    /*
                     * Open modal
                     */
                    $('#modal_view_user').modal('show');


                }//if we don't have errors

            });//AJAX done



    });//user details on click

</script>

@endsection
