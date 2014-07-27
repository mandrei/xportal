$(document).ready(function(){

    $('#left-side').height($('#right-side').outerHeight());

    /*************************************************
    *
    * Search for tables
    *
    *************************************************/
    //on enter
    $('.search-table>input').keypress(function(e){

        //take input value
        var search_val = $(this).val();

        //take input route
        var search_link = $(this).attr('link_to_search');

        //if enter is pressed
        if(e.which==13){

            //search for url
            location.href=xPortal.base_url+"/"+search_link+"?search="+search_val;

        }//if enter is pressed

    });//end search for tables

    //extend search input on focus
    $('.search-table>input').focusin(function(){

        //animate width
        $(this).stop().animate({'width':'256px'})

        //remove placeholder
        $(this).addClass('hide-placeholder');
    });

    $('.search-table>input').focusout(function(){

        //animate width back to normal
        $(this).stop().animate({'width':'200px'});

        //addplaceholder
        $(this).removeClass('hide-placeholder');

    });

    //toggle for table filter
    $('.filter-toggle').click(function(){

        $(this).toggleClass('filter-toggle-active');

        $('.table-filter').stop().slideToggle();
    });

    $('.row-fluid-holder').hover(

        function(){

            // $(this).find('.row-fluid').css('background-color', '#d5f9d2');

            $(this).css('background-color','#d5f9d2');

            $(this).find('[class*="span"]').css('background-color','#bddfb5');
        },

        function(){

            // $(this).find('.row-fluid').css('background-color', '#eee');

            $(this).css('background-color','#eee');

            $(this).find('[class*="span"]').css('background-color','#ddd');

        }

    );

        //select template
    $('.row-fluid-holder').click(function(){

        $('.row-fluid-holder').removeClass('selected-template');

        $('.row-fluid-holder [class*="span"]').removeClass('selected-template-col');

        $(this).addClass('selected-template');

        $(this).find('[class*="span"]').addClass('selected-template-col');

        var index_of = $('.selected-template').parents('li').index();

        $('#sel-temp').val(index_of+1);

        console.log($('#sel-temp').val());

    });



});





//***********************Function for checking before leaving*************************


//************************************************************************************
    //this function is called when input, textarea default values had been changed and not saved




formmodified=0;

   

    $('form *, textarea').change(function(){
        formmodified=1;
    });

    window.onbeforeunload = confirmExit;

    function confirmExit() {

        if (formmodified == 1) {

                             
            return  "You have information that was not saved.";
        }
    }
    //this is the input for save the changes... after click there is no need for alert.
    $("input[name='commit'], button[type='submit'], input[type='submit'], input[type='button']").click(function() {
        formmodified = 0;
    });

    $(".btn-custom").on('mousedown', function(){

      $(this).addClass('btn-custom-pressed');

    });

    $(".btn-custom").on('mouseup', function(){

      $(this).removeClass('btn-custom-pressed');

    })

    





/*
 * Functions to show and hide animation during an ajax request
 */
function loading_start()
{

    $('.modal_ajax_loading').show();

}//start loading

function loading_stop()
{

    $('.modal_ajax_loading').hide();

}//start loading


/*
 * Used to confirm a redirect
 *
 * The html of the popup is in inc/template before </body>
 */
function confirm(title, body, link)
{

    /*
     * Set the title
     */
    $('#modal_title').html(title);


    /*
     * Set the Body
     */
    $('#modal_text').html(body);


    /*
     * Set the Link
     */
    $('#modal_link').attr('href', xPortal.base_url  + link);


    /*
     * Show the popup
     */
    $('#confirm_modal').modal('show');

}//confirm


function customConfirm(message, true_func, false_func){

  var container = document.createElement('div');
    //template for modal window
    container.innerHTML += '<div class="modal custom-confirm">'+
                            '<div class="modal-body">' +
                                '<div>' + message + '</div><br/><br/>' +
                                '<div class="controls">'+ 
                                    '<button type="button" class="btn primary">OK</button>' +
                                    '<button type="button" class="btn">Cancel</button>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
    //modal window
    var modal = container.firstChild;
    container = document.createElement('div');
    container.innerHTML = '<div class="modal-backdrop  in"></div>';
    //dark background
    var background = container.firstChild;
    //get click OK button
    var ok = modal.getElementsByTagName('button')[0];
    ok.onclick = function() {
        modal.parentNode.removeChild(modal);
        document.body.removeChild(background);
        true_func();
    }
    //get click Cancel button
    var cancel = modal.getElementsByTagName('button')[1];
    cancel.onclick = function() {
        modal.parentNode.removeChild(modal);
        document.body.removeChild(background);
        false_func();
    }
    document.body.appendChild(background);
    document.body.appendChild(modal);

}//end custom confirm
