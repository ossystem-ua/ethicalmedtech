$(document).ready(function(){
  $('input[type=text]').each(function(i,el){
     $(el).blur(function(){
         if ($(el).val().length == 0){
             $(el).addClass('placeholder-style');
         }
     });
     $(el).focus(function(){
         $(el).removeClass('placeholder-style');
         
     });
    if ($(el).val().length == 0){
        $(el).addClass('placeholder-style');
    }
  });
  
  $('.event-status select').change(function() {
      var status = $('.event-status select option:selected').text();
      if($(this).val() === '7' && status === 'Not assessed') {
          $('.event-sub-status').slideDown();
      } else {
          $('.event-sub-status').slideUp();
      }
  });
  
  $('.emt-select-terms-country').change(function() {
      
      var conferenceId = $('.question_field').attr('data-conferenceId');
      /*if($(this).val() === '223' || $(this).val() === '57' || $(this).val() === '14' || $(this).val() === '21' || $(this).val() === '122' || 
              $(this).val() === '123' || $(this).val() === '124' || $(this).val() === '150' || $(this).val() === '160' || $(this).val() === '171' || 
              $(this).val() === '172' || $(this).val() === '176' || $(this).val() === '177' || $(this).val() === '190' || $(this).val() === '191' || 
              $(this).val() === '196' || $(this).val() === '204' || $(this).val() === '205' || $(this).val() === '216' || $(this).val() === '105' || 
              $(this).val() === '103' || $(this).val() === '72' || $(this).val() === '67' || $(this).val() === '56' || $(this).val() === '55' || 
              $(this).val() === '53' || $(this).val() === '73' || $(this).val() === '80' || $(this).val() === '33' || $(this).val() === '83' || 
              $(this).val() === '97' || $(this).val() === '98' || $(this).val() === '117') {*/
      if(   $(this).val() === '3'   || $(this).val() === '14'  || $(this).val() === '17'  || $(this).val() === '21'  || $(this).val() === '33'  || $(this).val() === '53'  ||
            $(this).val() === '55'  || $(this).val() === '56'  || $(this).val() === '57'  || $(this).val() === '63'  || $(this).val() === '67'  || $(this).val() === '72'  ||
            $(this).val() === '73'  || $(this).val() === '80'  || $(this).val() === '83'  || $(this).val() === '97'  || $(this).val() === '98'  || $(this).val() === '101' ||
            $(this).val() === '102' || $(this).val() === '103' || $(this).val() === '105' || $(this).val() === '108' || $(this).val() === '114' || $(this).val() === '117' ||
            $(this).val() === '118' || $(this).val() === '120' || $(this).val() === '122' || $(this).val() === '123' || $(this).val() === '124' || $(this).val() === '132' ||
            $(this).val() === '135' || $(this).val() === '144' || $(this).val() === '150' || $(this).val() === '160' || $(this).val() === '161' || $(this).val() === '162' ||
            $(this).val() === '164' || $(this).val() === '171' || $(this).val() === '172' || $(this).val() === '174' || $(this).val() === '176' || $(this).val() === '177' ||
            $(this).val() === '185' || $(this).val() === '190' || $(this).val() === '191' || $(this).val() === '196' || $(this).val() === '200' || $(this).val() === '204' ||
            $(this).val() === '205' || $(this).val() === '206' || $(this).val() === '215' || $(this).val() === '216' || $(this).val() === '222' || $(this).val() === '235')
      {
          $('.emt-mecomed-terms').hide();
          $('.emt-other-terms').hide();
          $('.emt-eucomed-terms').slideDown();
          var submitPath = "/conferences/edit/"+conferenceId+"/"+$(this).val()+"/step1";
          $('a.emt-eucomed-terms-submit-path').attr('href', submitPath);
      /*} else if ($(this).val() === '17' || $(this).val() === '63' || $(this).val() === '222' || $(this).val() === '185' || $(this).val() === '174' ||$(this).val() === '108' || 
              $(this).val() === '114' || $(this).val() === '101' || $(this).val() === '118' || $(this).val() === '121' || $(this).val() === '144' || $(this).val() === '161' ||
              $(this).val() === '162' || $(this).val() === '164' || $(this).val() === '102' || $(this).val() === '235' || $(this).val() === '206') {
          $('.emt-other-terms').hide();
          $('.emt-eucomed-terms').hide();
          $('.emt-mecomed-terms').slideDown();
          var submitPath = "/conferences/edit/"+conferenceId+"/"+$(this).val()+"/step1";
          $('a.emt-mecomed-terms-submit-path').attr('href', submitPath);
          */
      } else {
          console.log($(this).val());
          $('.emt-mecomed-terms').hide();
          $('.emt-eucomed-terms').hide();
          $('.emt-other-terms').slideDown();
          var submitPath = "/conferences/edit/"+conferenceId+"/"+$(this).val()+"/step1";
          $('a.emt-other-terms-submit-path').attr('href', submitPath);
      }
  });
  

  $("#emt-understood").change(function() {
      if($(this).prop("checked")) {
          $(".emt-other-terms-checkbox-on").slideDown();
      } else {
          $(".emt-other-terms-checkbox-on").hide();
      }
  });
  
});

function PopUpShow(id){
    $("#delete-popup-"+id).show();
}
function PopUpHide(id){
    $("#delete-popup-"+id).hide();
}
function SubmitPopUpShow(){
    $("#submit-popup").show();
}
function SubmitPopUpHide(){
    $("#submit-popup").hide();
}

function appendFileInput(){
    $('.files_input_wrapper').append('<div class="clear"></div><input type="file" name="additional_files[]">&nbsp;<input type="text" name="additional_files_comment[]" class="filecomment" placeholder="comment" maxlength="255">');
}