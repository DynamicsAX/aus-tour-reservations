/**
 * Created by eric on 6/25/16.
 */
var today = new Date();
var plusDate = new Date();
plusDate.setDate(plusDate.getDate() + 3);
var dd = today.getDate();
var mm = today.getMonth()+1; //January is 0!
var yyyy = today.getFullYear();


var ddPlus = plusDate.getDate();
var mmPlus = plusDate.getMonth()+1; //January is 0!
var yyyyPlus = plusDate.getFullYear();
var startDate= "-"+yyyy+'/'+mm+'/'+dd;
var endDate= '+'+yyyyPlus+'-'+mmPlus+'-'+ddPlus;
var date = new Date();
console.log("StartDate",startDate);
console.log("EndDate",endDate);
console.log("plusOne",date.setDate(date.getDate() + 1));
jQuery('.datePicker').datetimepicker(
    {
        timepicker:false,
        format:'Y-m-d',

        minDate:'-1970/01/00',//yesterday is minimum date(for today use 0 or -1970/01/01)
        maxDate:'+1970/01/03'//tomorrow is maximum date calendar
    }
);
jQuery('.datePickerMain').datetimepicker(
    {
        timepicker:false,
        format:'Y-m-d',}
);jQuery('.timePicker').datetimepicker(
    {
        datepicker:false,
        format:'H:i'
    }
);


(function($){
    function TimeDiff(a,b)
    {

        var first = a.split(":");
        var second = b.split(":");

        var xx;
        var yy;

        if(parseInt(first[0]) < parseInt(second[0])){

            if(parseInt(first[1]) < parseInt(second[1])){

                yy = parseInt(first[1]) + 60 - parseInt(second[1]);
                xx = parseInt(first[0]) + 24 - 1 - parseInt(second[0])

            }else{
                yy = parseInt(first[1]) - parseInt(second[1]);
                xx = parseInt(first[0]) + 24 - parseInt(second[0])
            }



        }else if(parseInt(first[0]) == parseInt(second[0])){

            if(parseInt(first[1]) < parseInt(second[1])){

                yy = parseInt(first[1]) + 60 - parseInt(second[1]);
                xx = parseInt(first[0]) + 24 - 1 - parseInt(second[0])

            }else{
                yy = parseInt(first[1]) - parseInt(second[1]);
                xx = parseInt(first[0]) - parseInt(second[0])
            }

        }else{


            if(parseInt(first[1]) < parseInt(second[1])){

                yy = parseInt(first[1]) + 60 - parseInt(second[1]);
                xx = parseInt(first[0]) - 1 - parseInt(second[0])

            }else{
                yy = parseInt(first[1]) - parseInt(second[1]);
                xx = parseInt(first[0]) - parseInt(second[0])
            }


        }



        if(xx < 10)
            xx = "0" + xx;


        if(yy < 10)
            yy = "0" + yy;

        return(xx + "." + yy)
    }
    function calculateTime(){
        var start_time = $('#start_time').val();
        var end_time = $('#end_time').val();
        if(!end_time || !start_time){
            return;
        }
        var diff = TimeDiff(end_time,start_time);
        $('#hours').val(diff);
    }
    $('#start_time').on('change',function () {
        calculateTime();
    });
    $('#end_time').on('change',function () {
        calculateTime();
    });
})(jQuery);