//スクロールされたときに動く
addContent();
$(window).on("scroll", function() {
 
  // なんか$(window).height()がうまく取れない
  // document_h -> html表示の高さ
  var document_h = $(document).height();
  // window_h -> 今見えている画面の最下部がhtml表示の上からどれくらいの高さであるか
  var window_h = $(window).height() + $(window).scrollTop();
  var scroll_pos = (document_h - window_h);

  // 画面最下部の時
  if (scroll_pos <= 1){
    addContent();
  }
});


function addContent() {
console.log("addContent");
  var add_content = "";
  var content_count = $('#count').val();

  $.post({
    type: "post",
    datatype: "json",
    url: "selectContent.php",
    data: { count : content_count }
  }).done( function( data ){
    
    $.each(data, function( key, val){
      add_content += "<div class='post'>";
      add_content += "<a href='" + val.user_profile_url + "'>"
      if( val.user_icon != "" ){
        add_content += "<img src='/image/" + val.user_icon + "' class='user_icon'>";
      }else{
        add_content += "<span class='dummy_icon'></span>";
      }
      add_content += "<span class='user_name'>" + val.user_name + "</span></a>"
                   + "<span class='updated_at'>" + val.updated_at + "</span>"
                   + "<span class='body'>" + nl2br(val.body) + "</span>";
      if( val.image_files != [] ){
        add_content += "<div class='images'>";
        for (var image_file of val.image_files){
          console.log(image_file);
          add_content += "<img src='/image/" + image_file.image_filename + "' class='posted_image'>";
        }
        add_content += "</div>";
      }
    });
    $("#posts").append(add_content);
    $('#count').val($('#count').val() + data.length);

  }).fail(function(e){
    console.log(e);
  });
}

function nl2br(str) {
    str = str.replace(/\r\n/g, "<br />");
    str = str.replace(/(\n|\r)/g, "<br />");
    return str;
}

