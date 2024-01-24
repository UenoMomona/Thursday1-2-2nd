
$('#imageInput').change(function() {
  $('.error').html('');
  $('.submit').attr('disabled', false);
  for(var i = 0; i < 4; i++){
    if($('#imageBase64Input_' + i)){
      $('#imageBase64Input_'+i).remove();
    }else{
      break;
    }
  }
  $('canvas').remove();


  if (this.files.length < 1) {
    // 未選択の場合
    return;
  }

  if( this.files.length > 4){
    $('.error').html('画像の投稿は4枚までです！');
    $('.submit').attr('disabled', true);
  }

  console.log(this.files);
  for(var i = 0; i < this.files.length; i++){
    var file = this.files[i];
    if (!file.type.startsWith('image/')){ // 画像でなければスキップ
      return;
    }
   
    //$('.canvases').append('<canvas class="canvas_' + i + '" ></canvas>')
    $('button').before('<input id="imageBase64Input_' + i + '" name="image_base64_' + i + '" value="" type="hidden">');
    const imageInput = $('#imageBase64Input_' + i);
    //const context = ($('canvas')[i].getContext('2d'));
    //const canvas = $('canvas_' + i);
    //const canvas_data = $('canvas')[i]

    const reader = new FileReader();
    const image = new Image();
  
    reader.onload = () => { // ファイルの読み込み完了したら動く処理を指定
      image.onload = () => { // 画像として読み込み完了したら動く処理を指定
        let canvas = document.createElement("canvas") //キャンバスを作成
        let context = canvas.getContext('2d')

        // 元の縦横比を保ったまま縮小するサイズを決めてcanvasの縦横に指定する
        const originalWidth = image.naturalWidth; // 元画像の横幅
        const originalHeight = image.naturalHeight; // 元画像の高さ
        const maxLength = 500; // 横幅も高さも1000以下に縮小するものとする
       
        var width = 0;
        var height = 0;
        if (originalWidth <= maxLength && originalHeight <= maxLength) {
          width = originalWidth;
          height = originalHeight;
        } else if (originalWidth > originalHeight) { // 横長画像の場合
          width = maxLength;
          height = maxLength * originalHeight / originalWidth;
        } else { // 縦長画像の場合
          width = maxLength * originalWidth / originalHeight;
          height = maxLength;
        }
        canvas.width = width;
        canvas.height = height;
    console.log(width,height);
        // canvasに実際に画像を描画 (canvasはdisplay:noneで隠れているためわかりにくいが...)
        context.drawImage(image, 0, 0, originalWidth, originalHeight, 0, 0, width, height);
        // canvasの内容をbase64に変換しinputのvalueに設定
        imageInput.val( canvas.toDataURL());
        
        $('.canvases').append(canvas);
      };
      image.src = reader.result;
    };
    reader.readAsDataURL(file);


  }

});
