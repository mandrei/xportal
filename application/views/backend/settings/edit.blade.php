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

@if( Session::has( 'updated' ) )

    <div class="alert alert-success">

        <button type="button" class="close" data-dismiss="alert">&times;</button>

    {{ __('common.success_message') }}

   </div>

@endif

<br />

<?php echo Form::open('safe/settings', 'POST', array('name' => 'edit_settings')) ?>


{{ Form::token() }}


<table class="pull-left">

    @foreach($settings as $s)

    <tr>

        <td><label><?php echo ucfirst(str_replace('_', ' ',($s->name))) ?>&nbsp;</label></td>

        <td><input type="text" name="{{ $s->name }}" value="{{ $s->value }}"</td>

    </tr>


    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>

    @endforeach

</table>

<div class="clearfix"></div>


<button type="submit" class="btn btn-primary">{{ __('common.edit') }}</button>

{{ Form::close() }}

@endsection