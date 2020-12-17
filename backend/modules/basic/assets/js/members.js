$(function (){
         
    $('#addPerson').click( function(){
        $(this).attr("src","images/common/plussign-pushed.png");
         $.get('index.php?r=geneology/sponsorship/add-person');
         //alert('Clicked AddPerson Button');
    });
    
    
 });


