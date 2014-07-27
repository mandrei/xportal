@layout('backend/inc/template')

@section('container')

@if(count($errors->all()) != 0) 

    <div class="alert alert-error">

        <button type="button" class="close" data-dismiss="alert">&times;</button>

        @foreach( $errors->all() as $e )

          {{ $e }}<br/>

        @endforeach

    </div>

@endif


@if( Session::has('no_permissions'))
    
    <div class="alert alert-error">

        <button type="button" class="close" data-dismiss="alert">&times;</button>

        {{__('common.no_permissions')}}

    </div>

@endif


@if( Session::has( 'updated' ) )

        <div class="alert alert-success">

             <button type="button" class="close" data-dismiss="alert">&times;</button>

             {{ __('common.success_message') }}

         </div>

@endif


<?php echo Form::open('safe/user/' .$user->user_id.   '/edit', 'POST', array('name' => 'edit_user')) ?>


{{ Form::token() }}



<table class="pull-left form-table">

    <tr>

        <td><label><?php echo __('common.first_name') ?></label></td>

        <td><input type="text" name="first_name" maxlength="100" value="{{ $user->first_name }}" /></td>

    </tr>

  
    <tr>

        <td><label><?php echo __('common.last_name')?></label></td>

        <td><input type="text" name="last_name" maxlength="100"  value="{{ $user->last_name }}" /></td>

    </tr>



    <tr>

        <td><label>{{ __('common.email') }}</label></td>

        <td><input type="text" name="email" maxlength="100" value="{{ $user->email }}"/></td>

    </tr>



    <tr>

        <td><label><?php echo __('common.username') ?></label></td>

        <td><input type="text" name="username" maxlength="50" value="{{ $user->username }}" /></td>

    </tr>


    <tr>

        <td><label><?php echo __('common.address') ?></label></td>

        <td><textarea name="address">{{ $user->address }}</textarea></td>

    </tr>


    <tr>

        <td><label><?php echo __('common.phone') ?></label></td>

        <td><input type="text" name="phone"  value="{{ $user->phone }}"  /></td>

    </tr>




</table>


<div class='right-side-table-container'>

<table class="pull-left regular-table" style='margin-left:50px; margin-top:0px;'>

    <tr><td colspan='2'><h3>{{__('common.permissions')}}</h3></td></tr>

    @foreach($modules as $m)

        <tr>
            <td><label></label>{{$m['name']}}</td>
            <td>{{Form::checkbox('permissions[]',$m['id'] ,in_array($m['id'], $permissions))}}</td>
        </tr>

        @if(count($m['kids']) > 0 )

            @foreach($m['kids'] as $k)

                <tr>
                    <td> <span style="margin-left:20px;"> {{$k->name}} </span></td>
                    <td style='padding-left:10px;' >{{Form::checkbox('permissions[]',$k->id ,in_array($k->id, $permissions))}}</td>
                </tr>

            @endforeach

        @endif

    @endforeach

</table>

</div>


<div class="clearfix"></div>

<br />


<button type="submit" class="btn btn-primary">{{ __('common.edit') }}</button>

{{ Form::close() }}

@endsection

@section('footer_js')


@parent

<script type="text/javascript">


    $(document).ready(function() {


        $('#start_date').datepicker({
            dateFormat: "yy-mm-dd" ,
            showOn: "button",
            buttonImage: "{{URL::base()}}/img/calendar/calendar.png",
            buttonImageOnly: true

        });//datepicker

        //hover on datepicker calendar
        $('.ui-datepicker-trigger').hover(

            function(){

                $(this).prop('src','{{URL::base()}}/img/calendar/calendar_hover.png')

            },

            function(){

                $(this).prop('src', '{{URL::base()}}/img/calendar/calendar.png')

            }

        );

    });//document.ready




</script>


@endsection