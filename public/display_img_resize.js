console.log($('.images'));
$(window).on('load', function(){
console.log($('.images'));
  $('.images').each( function(i, e){
    $num = $(this).children().length;
    if($num == 1){
      $(this).children().css('width', '100%');
    }else if($num == 2){
      $(this).children().css('width', '50%').css('height', parseFloat($(this).css('width').slice(0,-2)) / 2);
    }else if($num == 3){
      $(this).children().css('width', '50%').css('height', parseFloat($(this).css('width').slice(0,-2)) / 4);
    }else if($num == 4){
      $(this).children().css('width', '50%').css('height', parseFloat($(this).css('width').slice(0,-2)) / 4);
    } 

  });
});
