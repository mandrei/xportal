<!Doctype html>

<html>

@section('head')

<meta charset="utf-8">

<title>xPortal</title>

<link rel="shortcut icon" type="image/x-icon" href="{{ URL::base()}}/img/favicon.ico" />

<link href='http://fonts.googleapis.com/css?family=Michroma' rel='stylesheet' type='text/css'>

<!--[if lt IE 9]>
    {{HTML::script('js/html5shiv.js')}}
    <![endif]-->


<script type="text/javascript">
    var $buoop = {vs:{i:8,i:9,f:15,o:10.6,s:4,n:9}}
    $buoop.ol = window.onload;
    window.onload=function(){
        try {if ($buoop.ol) $buoop.ol();}catch (e) {}
        var e = document.createElement("script");
        e.setAttribute("type", "text/javascript");
        e.setAttribute("src", "http://browser-update.org/update.js");
        document.body.appendChild(e);
    }
</script>

<!--Jquery-->
{{ HTML::script('js/common/jquery-1.8.3.min.js') }}

{{HTML::script('assets/jq-ui/js/jquery-ui-1.10.3.custom.min.js')}}


<!--Bootstrap-->

{{ HTML::script('assets/bootstrap/js/bootstrap.js') }}

{{ HTML::style('assets/bootstrap/css/bootstrap.css') }}

{{ HTML::style('assets/bootstrap/css/bootstrap-responsive.min.css') }}

<!--Chosen script-->
{{ HTML::script('assets/chosen/chosen/chosen.jquery.min.js') }}


<!--Bootbox-->
{{ HTML::script('assets/bootstrap/bootbox.js')}}


<!--Our css-->
{{ HTML::style('css/style.css') }}

<!--Chosen CSS-->
{{ HTML::style('assets/chosen/chosen/chosen.css') }}


<script type="text/javascript">


    /*
     *
     * Global Namespace
     *
     * base_url: some .js files we need the base url
     *
     *
     *
     */
    var xPortal = {

        'base_url'   : '{{ URL::base() }}'

    }//portal base url



</script>


<!--Common functions-->



@yield_section


</head>

<body>


<div class="navbar" style="margin-bottom: 0px;">
    <div class="navbar-inner">

        <a class="brand" href="{{ Url::base() }}/safe/portal"><img src='{{URL::base()}}/img/backend/logo_login.png' width='115' alt='xPortal' title='xPortal'/></a>

        <ul class="nav">

            <!--dropdown menu-->

      
            @if( Users_Auth::has_access( 2 ) )

                <li <?php if( $selected_page == 'portal' ) echo 'class="active"'; ?>  ><a href="{{URL::to('safe/portal')}}" > <i class="icon-folder-open"></i> {{ __('common.portal') }} </a>
                </li>

            @endif



          <!--Users-->

           @if( Users_Auth::has_access( 6 ) )

                <li class="dropdown <?php if( $selected_page == 'users' ) echo 'active'; ?>" >
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class='icon-user'></i> <?php echo __('common.users') ?></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?php echo URL::to('safe/users') ?>"><i class='icon-th-list'></i> <?php echo __('common.all') ?></a></li>
                        <li><a href="<?php echo URL::to('safe/user/add') ?>"><i class='icon-plus'></i> <?php echo __('common.add') ?></a></li>
                    </ul>
                </li>

            @endif

             @if( Users_Auth::has_access( 7) )

                <li class="dropdown <?php if( $selected_page == 'clients' ) echo 'active'; ?>" >
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class='icon-briefcase'></i> <?php echo __('common.clients') ?></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?php echo URL::to('safe/clients') ?>"><i class='icon-th-list'></i> <?php echo __('common.all') ?></a></li>
                        <li><a href="<?php echo URL::to('safe/client/add') ?>"><i class='icon-plus'></i> <?php echo __('common.add') ?></a></li>
                    </ul>

                </li>

            @endif

             @if( Users_Auth::has_access( 8 ) )

              <!--END Users-->
                <li <?php if( $selected_page == 'settings' ) echo 'class="active"'; ?> >
                    <a href="<?php echo URL::to('safe/settings') ?>"> <i class="icon-warning-sign"></i> {{ __('common.settings') }}</a>
                </li>

            @endif

         </ul>

        <ul class="nav pull-right">

            <li>



            </li>

            <li>

                <div class="btn-group" style="min-width:120px;">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">

                         <i class="icon-user"></i> {{ Session::get('user.name') }}

                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu logout">

                      @if(Session::get('user.type') == 1 || Session::get('user.type') == 0 )

                        <li><a href="<?php echo URL::to('safe/account_user/edit') ?>"><i class="icon-user"></i> <?php echo  __('common.account_settings') ?></a></li>

                      @else

                        <li><a href="<?php echo URL::to('safe/account_client/edit') ?>"><i class="icon-user"></i> <?php echo  __('common.account_settings') ?></a></li>

                     @endif

                        <li><a href="<?php echo URL::to('safe/change_password') ?>"><i class="icon-lock"></i> <?php echo  __('common.change_pass') ?></a></li>

                        <li class="divider"></li>

                       

                    </ul>

                </div>

            </li>

             <li class='btn btn-custom' style='margin-left: 10px;' ><a href="<?php echo URL::to('logout') ?>" style='padding:0;'> <!--<i class="icon-off"></i>--> <?php echo __('common.logout') ?> </a></li>

        </ul>

    </div>

</div>

    <ul class="breadcrumb">

        @foreach( $breadcrumbs as $link => $value )

        @if( $link == 'last' )

                <li class="active">{{ $value }}</li>

        @else

                <li><a href="{{ URL::base() . '/' . $link }}">{{ $value }}</a> <span class="divider">/</span></li>

        @endif


        @endforeach

    </ul>



    <div id="container">

        @section('container')


      @yield_section

    </div>




    <!--    CONFIRMATION POPUP-->
    <div class="modal hide fade" id="confirm_modal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 id="modal_title"></h3>
        </div>
        <div class="modal-body">
            <p id="modal_text"></p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">{{ __('common.cancel') }}</a>
            <a href="#" id="modal_link" class="btn btn-success">{{ __('common.continue') }}</a>
        </div>
    </div>


    <!--AJAX LOADING MODAL-->
    <div class="modal_ajax_loading"></div>

    <div class='selector'></div>

    <div id='tutorial'>

            <div id='info-container'></div>

            <div id='window'>

              <div id='close-tutorial' style='position:absolute; top:-10px;right:-10px; color:black; font-weight:bold; cursor:pointer;' onclick="$(this).parents('#tutorial').hide();" >X</div>

              <ul>

                @section('tutorials')

                @yield_section

              </ul>


              <!-- <button class='btn btn-primary left back' onclick='tutorials( $(".forward") , $(this));'>&raquo; Prev</button> -->

              <button class='btn btn-primary right forward tut-1' onclick='tutorials($(this));'>Next &raquo;</button>

              <img src='{{URL::base()}}/img/sman.jpg' id='img-sman'/>

            </div>



          </div><!--end tutorial -->


    @section('footer_js')

    {{ HTML::script('js/common/backend/common.js') }}

    <!--jquery ui custom -->

    {{ HTML::style('assets/jq-ui/css/custom-theme/jquery-ui-1.10.3.custom.min.css') }}

    {{ HTML::script('assets/jq-ui/js/jquery-ui-1.10.3.custom.min.js') }}

    @yield_section


</body>


</html>