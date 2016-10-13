var changedFlag = false;

$(document).ready(function(){
    $('.datepicker').datepicker({ 
        autoSize: true, 
        firstDay: 1, 
        dateFormat: "dd.mm.yy",
        changeMonth: true,
        changeYear: true
    });
    $( ".timepicker" ).timePicker();

    
    
    $("#ossystem_emtbundle_conference_therapeuticArea").select2({
        allowClear: true,
        placeholder: '',
    });

    $(".filters #ossystem_emtbundle_conference_therapeuticArea").select2({
        allowClear: true,
        placeholder: '',
    });

    $("form").validationEngine();

    $("#ossystem_emtbundle_conference_delegatesCountries").selectMultiple();
    
    $('.tipsify').tipsify('');
    $('input, select').change(function(){
        changedFlag  = true;
    });

    $('.breadcrumbs a').click(function(ev){
        if (changedFlag){
            if (confirm("You made some changes. Do you wish to leave page without saving?")){
                changedFlag = false;
            }else{
                ev.preventDefault();
            }
        }

    });
    
});
