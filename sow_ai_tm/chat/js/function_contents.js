//生徒番号のリストにすでにあるかどうか　あればtrue なければfalse
function group_check(no) {
	//生徒番号の要素数を取得
	var groupText = document.getElementById('groupText');
	//groupText.options:セレクトボックス内の全てのオプション要素
	//Array.from(groupText.options) により、このオプションの HTML コレクションを通常のJavaScript配列に変換します。
	//some():指定された生徒番号（no）がセレクトボックス内のいずれかのオプションの value と一致する場合に true を返し、そうでない場合に false を返します。
    return Array.from(groupText.options).some(option => option.value === no);
}
//グループ追加
function setgroupno() {
	//追加する生徒番号
	var addst = document.getElementById('groupaddno').value;
    if (!addst) return;
    
    if (group_check(addst)) {
        alert("生徒番号はすでに登録されています");
        return;
    }
    //グループ番号取得
    var gno = document.getElementById('groupSelect').selectedIndex;
    var groupArrays = [groupArray1, groupArray2, groupArray3];
    var selectedGroup = groupArrays[gno];
	//セレクトボックスに追加（jQuery）
    $("select[name='textarea_group']").append(new Option(addst, addst));
	//配列に追加
    selectedGroup.push(addst);
    document.getElementById('groupaddno').value = "";    
}
function delete_group() {
	//グループ番号取得
    var gno = document.getElementById('groupSelect').selectedIndex;
    var groupArrays = [groupArray1, groupArray2, groupArray3];
    var selectedGroup = groupArrays[gno];

	//グループが選択され、生徒番号が選択されていれば
    if (selectedGroup && document.getElementById('groupText').selectedIndex >= 0) {
		//配列から要素を削除
        selectedGroup.splice(document.getElementById('groupText').selectedIndex, 1);
		//セレクトボックスから削除
        document.getElementById('groupText').remove(document.getElementById('groupText').selectedIndex);
    }
}
function change_group(){
	//グループ番号取得
    var gno = document.getElementById('groupSelect').selectedIndex;
    var groupArrays = [groupArray1, groupArray2, groupArray3];
    var selectedGroup = groupArrays[gno];

	//リストの一括削除
    var sl = document.getElementById('groupText');
    while(sl.lastChild) {
        sl.removeChild(sl.lastChild);
    }

	//配列の要素をリストに追加
    selectedGroup.forEach(studentNo => {
        $("select[name='textarea_group']").append(new Option(studentNo, studentNo));
    });

}