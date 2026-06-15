//パスワード自動生成
function get_password_teacher(){
    var length = 5; // パスワードの長さ
    var characters = '0123456789'; // 使用可能な文字
    var password = '';
    for (var i = 0; i < length; i++) {
        password += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    document.getElementById('teacherpass').value = password;
}

function get_password_student(){
    var length = 5; // パスワードの長さ
    var characters = '0123456789'; // 使用可能な文字
    var password = '';
    for (var i = 0; i < length; i++) {
        password += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    document.getElementById('studentpass').value = password;
}

function get_id_teacher(){
    var length = 5; // パスワードの長さ
    var characters = '0123456789'; // 使用可能な文字
    var password = '';
    for (var i = 0; i < length; i++) {
        password += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    document.getElementById('teacherid').value = "t" + password;
}

function get_id_student(){
    var length = 5; // パスワードの長さ
    var characters = '0123456789'; // 使用可能な文字
    var password = '';
    for (var i = 0; i < length; i++) {
        password += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    document.getElementById('studentid').value = "s" + password;
}