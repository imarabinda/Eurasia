@include('template.header')
@yield('content')
@include('template.script')

@stack('scripts')

<script type="text/javascript">

    $(document).ready(function() {
        @production
        
        $.fn.dataTable.ext.errMode = 'none';
        
        @endproduction
        
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }  
    });

$(document).ajaxError(function(event, jqxhr, settings, thrownError ){
 $.LoadingOverlay("hide");
 if('xhrFields' in settings){
     if(settings.xhrFields.responseType == 'blob'){
         swal(
             thrownError,
             "Can't download file.",
             'error'
             );
             return;   
            }
        }
        
        var response = jqxhr.responseJSON;
        if(response.message =='CSRF token mismatch.'){
            
                            swal({
                                             title: thrownError,
                                             text: response.message,
                                             type: 'error',
                                             timer: 2000
                                          }).then(
                                          function(){

                                          }, 
                                          function (dismiss) {
                                                if (dismiss === 'timer') {
                                                location.reload();
                               }
                                    })

                                   }else{
                                    if(jqxhr.status == 422){
                                        return;
                                    }
                swal(
                              thrownError,
                              response.message,
                              'error'
                           );

                                   }
})


    //   $(document).ajaxStart(function(){
    // $.LoadingOverlay("show");
    //     });

$(document).ajaxStop(function(){
    $.LoadingOverlay("hide");
});



       var $container = $('#sidebar-main'),
    $scrollTo = $('li.active');

if($scrollTo.length > 0){
// $container.scrollTop(
//     $scrollTo.offset().top - $container.offset().top + $container.scrollTop()
// );

// Or you can animate the scrolling:
$container.animate({
    scrollTop: $scrollTo.offset().top - $container.offset().top + $container.scrollTop() - 200
});
}



function download(url,data={},loading = false){
    if(!loading){
         $.LoadingOverlay("show",{progress:true,text:'Starting...',progressResizeFactor:'0.10',textResizeFactor:'0.20',progressFixedPosition:'bottom',progressColor:"#2a3a4a"});      
    }
            counter=setInterval(timer, 1200); 
            $.ajax({
        url: url,
        method: 'POST',
        data:data,

        xhrFields: {
            responseType: 'blob'
        },
        success: function (message,text,xhr) {    
        clearInterval(counter);
        count=100;
        $.LoadingOverlay("progress",100); 
        $.LoadingOverlay("text","Downloading..."); 
   
           var filename = "";
         var disposition = xhr.getResponseHeader('Content-Disposition');
           if (disposition && disposition.indexOf('attachment') !== -1) {
            var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
            var matches = filenameRegex.exec(disposition);
            if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
         }

             if (typeof window.chrome !== 'undefined') {
                // Chrome version
                var a = document.createElement('a');
            var url = window.URL.createObjectURL(message);
            a.href = url;
            a.download = filename;
            document.body.append(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            
            } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
                // IE version
                var blob = new Blob([message], { type: 'application/force-download' });
                window.navigator.msSaveBlob(blob, filename);
            } else {
                // Firefox version
                var file = new File([message], filename, { type: 'application/force-download' });
                window.open(URL.createObjectURL(file));
            }
            
        $.LoadingOverlay("hide");
        }
        });        
    }


var counter = null;
var count = 100;

function timer()
{
    count=count-1;

    if (count <= 0)
    {
        $.LoadingOverlay("text","Files are to big!");
        setTimeout(function(){
        $.LoadingOverlay("text","Just calm down, download will start soon...");
        },1500);
        setTimeout(function(){
        $.LoadingOverlay("text","Mmmmmm.... boored ? Just a little bit.");
        },11500);

        setTimeout(function(){
        $.LoadingOverlay("text","Ohhh right there!");
        },21500);
        
        clearInterval(counter);
        count=100;
        return;
    }
var now  = (100-count);
  $.LoadingOverlay("progress",now); 
  $.LoadingOverlay("text",now+"% completed"); 
}



function toFixed(x) {
  if (Math.abs(x) < 1.0) {
    var e = parseInt(x.toString().split('e-')[1]);
    if (e) {
        x *= Math.pow(10,e-1);
        x = '0.' + (new Array(e)).join('0') + x.toString().substring(2);
    }
  } else {
    var e = parseInt(x.toString().split('+')[1]);
    if (e > 20) {
        e -= 20;
        x /= Math.pow(10,e);
        x += (new Array(e+1)).join('0');
    }
  }
  return x;
}


@yield('script')


    });
</script>


@include('template.footer')