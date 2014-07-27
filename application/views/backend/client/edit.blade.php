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


<?php echo Form::open('safe/client/' .$client->user_id.   '/edit', 'POST', array('name' => 'edit_client')) ?>


{{ Form::token() }}



<table class="pull-left form-table client-add">


    
     <tr>

        <td><label><?php echo __('common.client_type') ?>&nbsp;</label></td>



        <td>
            
             <select name="client_type">

                         <option value="0" ><?php echo __('common.select' ,array('what' => __('common.client_type')))?></option>

                @foreach($client_types as $ct)

                         <option value="<?php echo $ct->id; ?>" <?php if ($ct->id == Input::old('client_type',$client->client_type_id) ) echo 'selected="selected"'; ?> ><?php echo $ct->name?></option>

                @endforeach

            </select>

        </td>

    </tr> 

    <tr>
        <td colspan="2">&nbsp;</td>

    </tr>


     
             <tr>
                   <td colspan="2"> <strong>Identification</strong> </td>
                </tr>

 
                <tr class='individuals'>
                    
                    <td><label><?php echo __('common.calendar_year_start'); ?>&nbsp;</label></td>

                    <td>
                           <select name='calendar_year_start'>
                               
                    
                                @for($i = 1993; $i <= date('Y'); $i++ )

                                    <option @if(Input::old('calendar_year_start',$client->calendar_year_start)  == $i ) selected=selected @endif value="{{$i}}">{{$i}}</option>

                                @endfor

                           </select> 

                    </td>

            </tr>

             <tr class="individuals">

                <td><label><?php echo __('common.title') ?>&nbsp;</label></td>

                 <td>

                     <select name="title">

                         <option value="0" selected="selected"><?php echo __('common.select' ,array('what' => __('common.title')))?></option>

                         @foreach($titles as $t)

                            <option value="<?php echo $t->id?>" <?php if ($t->id == Input::old('title', $client->title )) echo 'selected="selected"'?> ><?php echo $t->title?></option>

                         @endforeach

                     </select>

                 </td>

            </tr>

            <tr class="individuals">

                <td><label><?php echo __('common.first_name') ?>&nbsp;</label></td>

                <td><input type="text" name="first_name" class='to-uppercase'  maxlength="50"  value="{{ Input::old('first_name', $client->first_name) }}"/> </td>

            </tr>


            <tr class="individuals">

                <td><label><?php echo __('common.initials') ?>&nbsp;</label></td>

                <td><input type="text" name="initials" class='to-uppercase'  maxlength="3" value="{{ Input::old('initials',$client->initials) }}" /> </td>

            </tr>

            <tr class="individuals">

                <td><label><?php echo __('common.last_name') ?>&nbsp;</label></td>

                <td><input type="text" name="last_name" class='to-uppercase' maxlength="50" value="{{ Input::old('last_name',$client->last_name) }}" /> </td>

            </tr>


            <tr class="individuals">

                <td><label><?php echo __('common.default_client_name')?>&nbsp;</label></td>

                <td><input type="text" name="default_client_name" class="to-uppercase" maxlength="100" value="{{ Input::old('default_client_name',$client->default_client_name) }}"/></td>

            </tr>


            <tr class="individuals">

                <td><label><?php echo __('common.birth_date') ?>&nbsp;</label></td>

                <td> <div class='left'><input type="text" name="birth_date"  maxlength="10" readonly='readonly' value="@if($client->birth_date != '0000-00-00'){{$client->birth_date}}@endif" /></div> <div class='clear'></div> </td>

            </tr>


            <tr class="individuals">

                <td><label><?php echo __('common.marital_status') ?>&nbsp;</label></td>

                <td>

                     <select name="marital_status">

                                 <option value="0" selected="selected"><?php echo __('common.select' ,array('what' => __('common.marital_status')))?></option>

                        @foreach($marital_statuses as $m)

                                 <option value="<?php echo $m->id?>" <?php if ($m->id == Input::old('marital_status',$client->marital_status) ) echo 'selected="selected"'?> ><?php echo $m->name?></option>

                        @endforeach

                    </select>

                 </td>

            </tr>

           
    

            <tr class="corporations">

                <td><label><?php echo __('common.name') ?>&nbsp;</label></td>

                <td><input type="text" name="name" class='to-uppercase' maxlength="150" value="{{  Input::old('name',$client->name) }}" /> </td>

            </tr>

            <tr class='corporations'>
            
              <td><label><?php echo __('common.calendar_year_start') ?>&nbsp;</label></td>

                <td> <div class='left'><input type="text" name="corporation_calendar_year_start"  maxlength="10" readonly="readonly" value="{{ Input::old('corporation_calendar_year_start',$client->corporation_calendar_year_start) }}" /></div> <div class='clear'></div> </td>

            </tr>



            <tr class="corporations">

                <td><label><?php echo __('common.contact_person_first_name') ?>&nbsp;</label></td>

                <td><input type="text" name="contact_person_first_name" class='to-uppercase' maxlength="150"  value="{{ Input::old('contact_person_first_name',$client->contact_person_first_name) }}"/> </td>

            </tr>

            <tr class="corporations">

                <td><label><?php echo __('common.contact_person_last_name') ?>&nbsp;</label></td>

                <td><input type="text" name="contact_person_last_name" class='to-uppercase' maxlength="150"  value="{{Input::old('contact_person_last_name', $client->contact_person_last_name )}}"/> </td>

            </tr>

             <tr class="corporations">

                <td><label><?php echo __('common.position') ?>&nbsp;</label></td>

                <td>


                     <select name="position">

                                 <option value="0" ><?php echo __('common.select' ,array('what' => __('common.position')))?></option>

                            @foreach($positions as $p)

                                     <option value="<?php echo $p->id; ?>" <?php if ($p->id == Input::old('position',$client->position) ) echo 'selected="selected"'; ?> ><?php echo strtoupper($p->name);?></option>

                            @endforeach

                    </select>
                </td>

            </tr>

             

    <tr>

        <td colspan='2'><strong>{{ __('common.mailing_address') }}</strong></td>

    </tr>

 

    <tr>

        <td><label><?php echo __('common.street_address') ?>&nbsp;</label></td>

        <td><input type="text" name="street_address" class='to-uppercase' maxlength="1024" value="{{Input::old('street_address',$client->street_address) }}" /> </td>

    </tr>

   

    <tr>

        <td><label><?php echo __('common.city') ?>&nbsp;</label></td>

        <td><input type="text" name="city" class='to-uppercase' maxlength="256"  value="{{ Input::old('city',$client->city) }}"/> </td>

    </tr>

  
    <tr>

        <td><label><?php echo __('common.country') ?>&nbsp;</label></td>

        <td>

            <select name="country" id="country">

                <option value="0" selected="selected"><?php echo __('common.select' ,array('what' => __('common.country')))?></option>

                @foreach($countries as $c)

                   <option value="<?php echo $c->id?>" <?php if ($c->id == Input::old('country',$client->country ) ) echo 'selected="selected"'?> ><?php echo $c->name?></option>

                @endforeach

            </select>

        </td>

    </tr>



    <tr class="us">

        <td><label><?php echo __('common.state') ?>&nbsp;</label></td>

        <td>

            <select name="state">

                <option value="0" selected="selected"><?php echo __('common.select' ,array('what' => __('common.state')))?></option>

                @foreach($states as $s)

                    <option value="<?php echo $s->id?>" <?php if ($s->id == Input::old('state',$client->state) ) echo 'selected="selected"'?> ><?php echo $s->name?></option>

                @endforeach

            </select>

        </td>

    </tr>

    <tr class="us">

        <td><label><?php echo __('common.zip_code') ?>&nbsp;</label></td>

        <td><input type="text" name="zip_code"  maxlength="10" value="{{Input::old('zip_code', $client->zip_code) }}" /> </td>

    </tr>



    <tr class="canada">

        <td><label><?php echo __('common.province') ?>&nbsp;</label></td>

        <td>

            <select name="province">

                <option value="0" selected="selected"><?php echo __('common.select', array('what' => __('common.province')))?></option>

                @foreach($provinces as $p)

                     <option value="{{ $p->id }}" <?php if($p->id == Input::old('province',$client->province) ) echo 'selected="selected"'?>>{{ $p->name }}</option>

                @endforeach

            </select>

        </td>

   </tr>

    <tr class="canada">

        <td><label><?php echo __('common.postal_code') ?>&nbsp;</label></td>

        <td><input type="text" name="postal_code"  maxlength="7" id='split-postal'  class='to-uppercase'  value="{{ Input::old('postal_code',$client->postal_code) }}"/> </td>

    </tr>



     <tr>

        <td colspan='2'><strong>{{ __('common.telephone') }}</strong></td>

    </tr>


        <tr>

                <td><label><?php echo __('common.phone') ?>&nbsp;</label></td>

                <td class='phone-holder'>

                    (<input type="text" name="phone1"   value="{{ Input::old('phone1',substr($client->phone,0,-7)) }}"/>)

                     <input type="text" name="phone2"   class="second-phone-set" value="{{ Input::old('phone2',substr($client->phone,3,-4)) }}"/>

                   - <input type="text" name="phone3"   class='last-phone-set last_input' mlenght="4" value="{{ Input::old('phone3',substr($client->phone,6)) }}"/>

                </td>

        </tr>

     <tr>

        <td><label><?php echo __('common.fax') ?>&nbsp;</label></td>

        <td class='phone-holder'>

            (<input type="text" name="fax1"  maxlength="3" value="{{  Input::old('fax1',substr($client->fax,0,-7) ) }}" />)

             <input type="text" name="fax2"  maxlength="3" class="second-phone-set" value="{{  Input::old('fax2',substr($client->fax,3,-4)) }}" />

           - <input type="text" name="fax3"  maxlength="4" class='last-phone-set last_input' mlenght="4" value="{{  Input::old('fax3',substr($client->fax,6) )}}" />

        </td>

     </tr>

     
    <tr>

        <td colspan='2'><strong>Client Account</strong></td>

    </tr>


    <tr>

        <td><label>{{ __('common.email') }}</label></td>

        <td><input type="text" name="email" maxlength="100" value="{{  Input::old('email',$client->email) }}"/></td>

    </tr>

    <tr>

        <td><label><?php echo __('common.username') ?></label></td>

        <td><input type="text" name="username" maxlength="50" value="{{ Input::old('username',$client->username) }}" /></td>

    </tr>

    <tr>

        <td><label><?php echo __('common.max_size_folder') ?>&nbsp;</label></td>

        <td>

            <select name="folder_size" id="folder_size">
                @foreach($size as $s)

                  <option value="<?php echo $s->id?>" <?php if ($s->id == Input::old('folder_size',$client->client_size_type_id )) echo 'selected="selected"'?> ><?php echo $s->sizetype.' mb'?></option>

                @endforeach
                </select>
        </td>

    </tr>

            </table>

        </td>

    </tr>

    <tr>

        <td>&nbsp;</td>

        <td>&nbsp;</td>

    </tr>


     </table>



    <div class="clear"></div>
    

<div class="clearfix"></div>

<br />


<button type="submit" class="btn btn-primary" style="width:150px;">{{ __('common.edit') }}</button>

{{ Form::close() }}

@endsection

@section('footer_js')

{{ HTML::script('js/phone_split/split_nr.js') }}

@parent



<script type="text/javascript">


  <?php if( Input::old('client_type',$client->client_type_id) == 1  ) { ?>

            $('.individuals').show();
            $('.corporations').hide();

            $('.hide_associated_individuals').show();
            $('.hide_associated_corporation').hide();
           
         <?php } else if(Input::old('client_type',$client->client_type_id) == 2 || Input::old('client_type',$client->client_type_id) == 3 || Input::old('client_type',$client->client_type_id) == 4 ) {  ?> 

            
            $('.individuals').hide();
            $('.corporations').show();

            $('.hide_associated_individuals').hide();
            $('.hide_associated_corporation').show();
        

         <?php }else { ?> 

            $('.individuals').hide();
            $('.corporations').hide();

            $('.hide_associated_individuals').hide();
            $('.hide_associated_corporation').hide();

          <?php } ?>



    //Country (USA OR CANADA)
    var country = $('#country').val();

    //For zip , postal code , state and province
    $('.us').hide();

    $('.canada').hide();

    if(country == 1)
    {
        $('.us').show();
    }//show state and zip code
    else if(country == 2)
    {
        $('.canada').show();

    }//show zip , postal code , state and province

    $('select[name="country"]').change(function(){

        var option = $(this).val();

        if(option != 0 ) {

            if ( option == 1 ){
                $('.us').show();
                $('.canada').hide();

            }//if us
            else{
                $('.us').hide();
                $('.canada').show();

            }//if canada

        }//if not select

    });//on change

    

    $(document).ready(function() {


         /*
         * Complete default name
         *
         */
         $('input[name="first_name"],input[name="initials"],input[name="last_name"]').keyup(function(){

            var first_name = $('input[name="first_name"]').val(),
                last_name  = $('input[name="last_name"]').val(),
                initials   = $('input[name="initials"]').val();

            $('input[name="default_client_name"]').val(last_name+', '+first_name+' '+initials);

         });



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

         $('select[name="client_type"]').change(function(){

            var option = $(this).val();

            if(option != 0 ) {

                if ( option == 1 ){
                    $('.individuals').show();
                    $('.hide_associated_individuals').show();
                    $('.hide_associated_corporation').hide();
                    $('.corporations').hide();
                    $('#federal_number').hide();
                    $('#jurisdiction_number').hide();

                    $('.chzn-container chzn-container-multi').addClass('full-width');


                    $('#corporate-associates').hide();
                }//if individuals
                else if( option == 2 || option == 3 || option==4 ){
                    $('.individuals').hide();
                    $('.corporations').show();
                    $('.hide_associated_corporation').show();
                    $('.hide_associated_individuals').hide();

                    $('#individual-associates').hide();


                }//if corporations
             

            }//if not select

        });//on change


    });//document.ready

    $('input[name="birth_date"]').datepicker({

        dateFormat: "yy-mm-dd",
        showOn: "button",
        buttonImage: "{{URL::base()}}/img/calendar/calendar.png",
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        yearRange: "1950:"

    });



    $('input[name="corporation_calendar_year_start"]').datepicker({

            dateFormat: "yy-mm-dd",
            showOn: "button",
            buttonImage: "{{URL::base()}}/img/calendar/calendar.png",
            buttonImageOnly: true,
            changeMonth: true,
            changeYear: true,
             yearRange: "1950:"

        });



</script>


@endsection