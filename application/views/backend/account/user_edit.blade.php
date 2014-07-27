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

    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ __('common.success_message') }}</div>

@endif

<br />


<?php echo Form::open('safe/account_user/edit', 'POST', array('name' => 'edit_account')) ?>


{{ Form::token() }}


<table class="pull-left">

    <tr>

        <td><label><?php echo __('common.first_name') ?></label></td>

        <td><input type="text" name="first_name" maxlength="50" value="{{ $user->first_name }}" /></td>

    </tr>


    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>

    <tr>

        <td><label><?php echo __('common.last_name')?></label></td>

        <td><input type="text" name="last_name" maxlength="50"  value="{{ $user->last_name }}" /></td>

    </tr>

    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>

    <tr>

        <td><label><?php echo __('common.email') ?></label></td>

        <td><input type="text" name="email" maxlength="100" value="{{ $user->email }}" /></td>

    </tr>

    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>

    <tr>

        <td><label><?php echo __('common.username') ?></label></td>

        <td><input type="text" name="username" maxlength="50" value="{{ $user->username }}" /></td>

    </tr>


    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>


    <tr>

        <td><label><?php echo __('common.phone') ?>&nbsp;&nbsp;</label></td>

        <td><input type="text" name="phone" maxlength="20" value="{{ $user->phone }}"  /></td>

    </tr>

    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>

    <tr>

        <td><label><?php echo __('common.address') ?></label></td>

        <td><textarea name="address" maxlength="250">{{ $user->address }}</textarea></td>

    </tr>


    <tr>

        <td colspan="2">&nbsp;</td>

    </tr>



</table>



<div class="clearfix"></div>

<br />


<button type="submit" class="btn btn-primary">{{ __('common.edit') }}</button>

{{ Form::close() }}

@endsection