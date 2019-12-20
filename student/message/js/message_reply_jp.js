//変数宣言処理

//受信者レベル(student/teacher/admin)判別変数
//※student->studentは不可能
var userLevel = 0;

//データ取得 : admin
var adminList = document.getElementsByClassName('input-admin');
if(adminList.length != 0) {
    var userLevel = 1;
}

//データ取得 : teacher
var teacherList = document.getElementsByClassName('input-teacher');
if(teacherList.length != 0) {
    var userLevel = 2;
}

//データ取得 : student
//※student->studentは不可能  別レベルページ(admin/teacher)用ロジック
//var student = [];
//var studentList = document.getElementsByClassName('input-student');

//データ取得 : grade
//※studentはgrade選択不可能  別レベルページ(admin/teacher)用ロジック
//var grade = [];
//var gradeList = document.getElementsByClassName('input-grade');

//データ取得 : course
//※studentはcourse選択不可能  別レベルページ(admin/teacher)用ロジック
//var course = [];
//var courseList = document.getElementsByClassName('input-course');

//データ取得 : classroom
//※studentはclassroom選択不可能  別レベルページ(admin/teacher)用ロジック
//var classroom = [];
//var classroomList = document.getElementsByClassName('input-classroom');

//チェックボックス押下処理 : admin
$('.input-admin').on('change', function () {
    userLevel = 1;
    checkedRadioCheckboxDisable(['teacher','student','grade','course','classroom']);
});

//チェックボックス押下処理 : teacher
$('.input-teacher').on('change', function () {
    userLevel = 2;
    checkedRadioCheckboxDisable(['admin','student','grade','course','classroom']);
});

//チェックボックス押下処理 : student
$('.input-student').on('change', function () {
    userLevel = 3;
    checkedRadioCheckboxDisable(['admin','teacher','grade','course','classroom']);
});

//チェックボックス押下処理 : grade
$('.input-grade').on('change', function () {
    userLevel = 0;
    checkedRadioCheckboxDisable(['admin','teacher','student']);
});

//チェックボックス押下処理 : course
$('.input-course').on('change', function () {
    userLevel = 0;
    checkedRadioCheckboxDisable(['admin','teacher','student']);
});

//チェックボックス押下処理 : classroom
$('.input-classroom').on('change', function () {
    userLevel = 0;
    checkedRadioCheckboxDisable(['admin','teacher','student']);
});

checkedRadioCheckboxDisable = function( index ){
    //console.log(index);
    for(i = 0 ; i < index.length ; i++) {
        //studentから送信不可能な個所はコメントアウト
        if(index[i] == 'admin') {
            for( i = 0 ; i < adminList.length ; i++ ){
                adminList[i].checked = false;
            }
        } else if(index[i] == 'teacher') {
            for( i = 0 ; i < teacherList.length ; i++ ){
                teacherList[i].checked = false;
            }
        //} else if(index[i] == 'student') {
        //    for( i = 0 ; i < studentList.length ; i++ ){
        //        studentList[i].checked = false;
        //    }
        //} else if(index[i] == 'grade') {
        //    for( i = 0 ; i < gradeList.length ; i++ ){
        //        gradeList[i].checked = false;
        //    }
        //} else if(index[i] == 'course') {
        //    for( i = 0 ; i < courseList.length ; i++ ){
        //        courseList[i].checked = false;
        //    }
        //} else if(index[i] == 'classroom') {
        //    for( i = 0 ; i < classroomList.length ; i++ ){
        //        classroomList[i].checked = false;
        }
    }
};

//データ取得・生成処理（主にモーダルダイアログ表示データ）
$('#Modal-messagecheck').on('show.bs.modal', function (event) {
    var getList = [];
    var receiveIdList = [];
    var receiveNameList = [];
    var receiveRangeList = [0,0,0];
    var receiveLevelText = "";
    document.getElementsByClassName('submit')[0].classList.remove('disabled');

    //送信ボタンの有効化・無効化
    document.getElementById('submit').disabled = false;

    //送信フラグ
    document.send_form.elements['sendFlag'].value = true;

    //モーダル用変数
    var modal = $(this);

    //チェックボックスの確認
    if(userLevel == 1) {
        receiveLevelText = "管理者";
        for( i = 0 ; i < adminList.length ; i++ ){
            if(adminList[i].checked == true){
                getList.push(adminList[i].value.split("|"));
            }
        }
    } else if(userLevel == 2) {
        receiveLevelText = "教員";
        for( i = 0 ; i < teacherList.length ; i++ ){
            if(teacherList[i].checked == true){
                getList.push(teacherList[i].value.split("|"));
            }
        }
    //} else if(userLevel == 3) {
    //    receiveLevelText = "生徒";
    //    for( i = 0 ; i < studentList.length ; i++ ){
    //        if(studentList[i].checked == true){
    //            receiveList.push(studentList[i].value.split("|"));
    //        }
    //    }
    //} else if(userLevel == 0) {
    //    var j = 0;
    //    receiveLevelText = "範囲";
    //    for( i = 0 ; i < gradeList.length ; i++ ){
    //        if(gradeList[i].checked == true){
    //            getList.push(gradeList[i].value.split("|"));
    //            receiveRangeList[0] = getList[j++][0];
    //        }
    //    }
    //    for( i = 0 ; i < courseList.length ; i++ ){
    //        if(courseList[i].checked == true){
    //            getList.push(courseList[i].value.split("|"));
    //            receiveRangeList[1] = getList[j++][0];
    //        }
    //    }
    //    for( i = 0 ; i < classroomList.length ; i++ ){
    //        if(classroomList[i].checked == true){
    //            getList.push(classroomList[i].value.split("|"));
    //            receiveRangeList[2] = getList[j++][0];
    //        }
    //    }
    }

    //タイトル関連
    //if(document.getElementById('input-title').value.length != 0){
    //    modal.find('.modal-title').removeClass("attention");
    //    modal.find('.modal-title').text(document.getElementById('input-title').value);
    //    document.send_form.elements['title'].value = document.getElementById('input-title').value;
    //} else {
    //    modal.find('.modal-title').addClass("attention");
    //    modal.find('.modal-title').text("タイトルを入力してください");
    //    document.getElementsByClassName('submit')[0].classList.add('disabled');
    //    document.getElementById('submit').disabled = true;
    //    document.send_form.elements['sendFlag'].value = false;
    //}

    //メッセージ関連
    if(document.getElementById('input-message').value.length != 0){
        modal.find('.modal-message').removeClass("attention");
        modal.find('.modal-message').text(document.getElementById('input-message').value);
        document.send_form.elements['message'].value = document.getElementById('input-message').value;
    } else {
        modal.find('.modal-message').removeClass("attention");
        modal.find('.modal-message').text("メッセージを入力してください");
        document.getElementsByClassName('submit')[0].classList.add('disabled');
        document.getElementById('submit').disabled = true;
        document.send_form.elements['sendFlag'].value = false;
    }

    //チェックボックス関連その１(個人宛メッセージ)
    if(getList.length != 0 && userLevel != 0){
        for( i = 0 ; i < getList.length ; i++ ){
            receiveIdList.push(Number(getList[i][0]))
            getList[i][1] = getList[i][1].replace(/　/g, '').replace(/ /g, ''); //表示の体裁のため全角・半角スペースを削除
            receiveNameList.push(getList[i][1])
        }

        if(receiveNameList.length > 3) {
            receiveNameList = getList[0][1] + ',' + getList[1][1] + ',' + getList[2][1] + '... 他' + (receiveNameList.length - 3) + '名';
        }

        modal.find('.modal-receive-level').text(receiveLevelText);
        modal.find('.modal-receive').text(receiveNameList);
        modal.find('.modal-receive').removeClass("attention");
        document.send_form.elements['receive_user_level_id'].value = userLevel;
        document.send_form.elements['receive_user_id'].value = receiveIdList;
    //} else if(getList.length != 0 && userLevel == 0){
    //    for( i = 0 ; i < getList.length ; i++ ){
    //        //Idの方は、既にチェックボックスの確認処理で代入済
    //        receiveNameList.push(getList[i][1])
    //    }

        modal.find('.modal-receive-level').text(receiveLevelText);
        modal.find('.modal-receive').text(receiveNameList);
        modal.find('.modal-receive').removeClass("attention");
        document.send_form.elements['receive_user_level_id'].value = userLevel;
        document.send_form.elements['receive_user_id'].value = receiveIdList;
        document.send_form.elements['grade_id'].value = receiveRangeList[0];
        document.send_form.elements['course_id'].value = receiveRangeList[1];
        document.send_form.elements['classroom_id'].value = receiveRangeList[2];
    } else {
        modal.find('.modal-receive').text("受信者を設定してください");
        modal.find('.modal-receive').addClass("attention");
        document.getElementsByClassName('submit')[0].classList.add('disabled');
        document.getElementById('submit').disabled = true;
        document.send_form.elements['sendFlag'].value = false;
    }

    //チェックボックス関連その１(範囲宛メッセージ)

    //console.log(receiveLevelText);
    //console.log(receiveIdList);
    //console.log(receiveNameList);

});

