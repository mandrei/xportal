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


{{ Form::open('safe/portal/add_folder/'.$folder_id, 'POST', array('name' => 'add_folder')) }}

{{ Form::token() }}

<table class="pull-left form-table">

    <tr>

        <td><label><?php echo __('common.folder_name') ?>&nbsp;&nbsp;</label></td>

        <td><input type="text" name="folder_name" maxlength="100" value="{{ Input::old('folder_name') }}" /></td>

    </tr>

    </table>


<div class="clearfix"></div>


<button type="submit" class="btn btn-primary">{{ __('common.add') }}</button>

{{ Form::close() }}

@endsection