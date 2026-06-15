//アップロード用のファイルが選択されたら
function disp_picture_body(img_url){
	//$('body').append('<img src="../'+img_url+'">')
	document.getElementById('uppicturearea').innerHTML = '<img src="'+img_url+'">';
}
var upload_url = 'https://www.hisatomi-kk.com/app';
var upload_path = upload_url+'/upload.php';
var up_filepath = "";
var up_filename = "";
//写真をアップロード
function picture_upload(){
		var imgs = $('#img_file').get(0);
		if (imgs.files.length == 0) {
			alert('ファイルを選択してください');
			return;
		}
		//ファイル名取得
	    up_filepath = imgs.value;

	    // フォームデータを取得
	    var formdata = new FormData($('#img_form').get(0));
		//アップロードしたファイル名をIPアドレス、生徒番号、時刻で指定
		up_filename = roomid + document.getElementById('myno').value + get_time_f();
		formdata.append('fname',up_filename);

	    // POSTでアップロード
	    $.ajax({
	        url  : upload_path,
	        type : "POST",
	        data : formdata,
	        cache       : false,
	        contentType : false,
	        processData : false,
	        dataType    : "json"
	    })
	    .done(function(data, textStatus, jqXHR){
	    	if (data.error === 0) {
	    		//$('#img_file').val(null);
	    		disp_picture_body( upload_url + data.data['img_url']);
	    	}else{
	    		alert('エラーが発生しました');
	    	}
	    })
	    .fail(function(jqXHR, textStatus, errorThrown){
	    	//console.log(jqXHR);
	    	alert('エラーが発生しました');
	    });
}
//ファイル名から拡張子を取得
function get_extension(path) {
    var basename = path.split(/[\\/]/).pop(),  // extract file name from full path ...
                                               // (supports `\\` and `/` separators)
        pos = basename.lastIndexOf('.');       // get last position of `.`

    if (basename === '' || pos < 1)            // if file name is empty or ...
        return "";                             //  `.` not found (-1) or comes first (0)

    return basename.slice(pos + 1);            // extract extension ignoring `.`
}