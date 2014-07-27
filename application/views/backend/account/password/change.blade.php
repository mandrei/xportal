@layout('backend/inc/template')

@section('container')

    @if( count($errors->all()) != 0)

    <div class="alert alert-error">

         <button type="button" class="close" data-dismiss="alert">&times;</button>

        @foreach( $errors->all() as $e )

                {{ $e }}<br/>

        @endforeach

    </div>

    @endif

    @if( Session::has( 'updated' ) )

            

             <div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>{{ __('common.success_message') }}</div>

    @endif

        @if ( Session::has( 'current_password_fail' ) )

                

                 <div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>{{ __('common.error_password_message') }}</div>

        @endif

<br />


<?php echo Form::open('safe/change_password', 'POST', array('name' => 'edit_account_password')) ?>


{{ Form::token() }}



<table class="pull-left">


    <tr>

        <td><label><?php echo __('common.current_password') ?></label></td>

        <td><input type="password" name="current_password"  maxlength="40"  /></td>

    </tr>

    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>

    <tr>

        <td><label><?php echo __('common.new_password') ?></label></td>

        <td><input type="password" name="new_password" maxlength="40"  /></td>

    </tr>


    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>

    <tr>

        <td><label><?php echo __('common.confirm_password') ?>&nbsp;&nbsp;</label></td>

        <td><input type="password" name="confirm_password" maxlength="40"  /></td>

    </tr>

    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>


</table>



<div class="clearfix"></div>


<button type="submit" class="btn btn-primary" onclick="needToConfirm = false; ">{{ __('common.edit') }}</button>

{{ Form::close() }}


@endsection
