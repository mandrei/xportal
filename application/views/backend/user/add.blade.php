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


@if( Session::has('no_email_no_password'))

    <div class="alert alert-error">

        <button type="button" class="close" data-dismiss="alert">&times;</button>

        {{__('common.no_email_no_password')}}

    </div>

@endif

@if( Session::has('no_permissions'))

    <div class="alert alert-error">

        <button type="button" class="close" data-dismiss="alert">&times;</button>

        {{__('common.no_permissions')}}

    </div>

@endif




{{ Form::open('safe/user/add', 'POST', array('name' => 'add_user')) }}


{{ Form::token() }}



<table class="pull-left form-table">

    <tr>

        <td><label><?php echo __('common.first_name') ?></label></td>

        <td><input type="text" name="first_name" maxlength="100" value="{{ Input::old('first_name') }}" /></td>

    </tr>

    <tr>

        <td><label><?php echo __('common.last_name')?></label></td>

        <td><input type="text" name="last_name" maxlength="100"  value="{{ Input::old('last_name') }}" /></td>

    </tr>


    <tr>

        <td><label>{{ __('common.email') }}</label></td>

        <td><input type="text" name="email" maxlength="100" value="{{ Input::old('email') }}"/></td>

    </tr>

    <tr>

        <td><label><?php echo __('common.username') ?></label></td>

        <td><input type="text" name="username" maxlength="50" value="{{ Input::old('username') }}" /></td>

    </tr>

    <tr>

        <td><label><?php echo __('common.password') ?></label></td>

        <td>

            <input type="password" name="password" id="password" maxlength="40"  class="pull-left" />

            <div class="pull-left" id="password_pop" style="margin-top: -15px;margin-left: 225px; position:relative;"></div>

        </td>

    </tr>


    <tr>

        <td><label><?php echo __('common.confirm_password') ?>&nbsp;</label></td>

        <td><input type="password" name="password_confirmation"  maxlength="40"  /> </td>

    </tr>

    <tr>

        <td><label><?php echo __('common.phone') ?></label></td>

        <td><input type="text" name="phone" maxlength="20" value="{{ Input::old('phone') }}" /></td>

    </tr>


    <tr>
        <td><label><?php echo __('common.address') ?></label></td>

        <td><textarea name="address" >{{ Input::old('address') }}</textarea></td>
    </tr>


</table>

<div class='right-side-table-container'>

    

<table class="pull-left regular-table" style='margin-left:50px; margin-top:0px;'>

    <tr><td colspan='2'><h3>{{__('common.permissions')}}</h3></td></tr>

    @foreach($modules as $m)

        <tr>
            <td><label></label>{{$m['name']}}</td>
            <td>{{Form::checkbox('permissions[]',$m['id'] ,in_array($m['id'], Input::old('permissions',array())))}}</td>
        </tr>

        @if(count($m['kids']) > 0 )

            @foreach($m['kids'] as $k)

                <tr>
                    <td> <span style="margin-left:20px;"> {{$k->name}} </span></td>
                    <td style='padding-left:10px;' >{{Form::checkbox('permissions[]',$k->id ,in_array($k->id, Input::old('permissions',array())))}}</td>
                </tr>

            @endforeach

        @endif

    @endforeach

</table>

</div><!--end right-side-table-continer -->


<div class="clearfix"></div>


<button type="submit" class="btn btn-primary">{{ __('common.add') }}</button>

<input type='hidden' value='add_user' id='for-tutorial'>

{{ Form::close() }}

@section('tutorials')

@parent

    {{__('tutorials.users_add', array('user' => Session::get('user.name') ))}}


@endsection


@endsection


@section('footer_js')


@parent

{{HTML::script('js/common/backend/tutorial.js')}}

<script type="text/javascript">


    $(document).ready(function() {

        $('#password_pop').popover({
            html        : true,
            title       : '<i class="icon-warning-sign"></i> <span style="color:black;">{{ __("common.important") }}</span><i class="icon-remove right" id="remove_popover"></i>',
            content     : '{{ __("common.password_not_mandatory") }}'

        });



        $('#password_pop').popover('show');

        $('#remove_popover').click(function(){

            $('#password_pop').popover('hide');
        });


        $('#start_date').datepicker({

            dateFormat: "yy-mm-dd",
            showOn: "button",
            buttonImage: "{{URL::base()}}/img/calendar/calendar.png",
            buttonImageOnly: true

        });

        //hover on datepicker calendar
        $('.ui-datepicker-trigger').hover(

            function(){

                $(this).prop('src','{{URL::base()}}/img/calendar/calendar_hover.png')

            },

            function(){

                $(this).prop('src', '{{URL::base()}}/img/calendar/calendar.png')

            }

        );

        $('.popover').css('margin-top','-10.5px').css('top','292.5px').css('left','375px');

    });//document.ready




</script>


@endsection