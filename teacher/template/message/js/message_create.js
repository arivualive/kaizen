//変数宣言処理

//受信者レベル(student/teacher/admin)判別変数
//※student->studentは不可能
var userLevel = 0;

//データ取得 : student
var studentList = document.getElementsByClassName('input-student');

//データ取得 : teacher
var teacherList = document.getElementsByClassName('input-teacher');

//データ取得 : admin
//var adminList = document.getElementsByClassName('input-admin');

//データ取得 : grade
var gradeList = document.getElementsByClassName('input-grade');

//データ取得 : course
var courseList = document.getElementsByClassName('input-course');

//データ取得 : classroom
var classroomList = document.getElementsByClassName('input-classroom');

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
        //adminから送信不可能な個所はコメントアウト
        if(index[i] == 'student') {
            for( j = 0 ; j < studentList.length ; j++ ){
                studentList[j].checked = false;
            }
        } else if(index[i] == 'teacher') {
            for( j = 0 ; j < teacherList.length ; j++ ){
                teacherList[j].checked = false;
            }
        //} else if(index[i] == 'admin') {
        //    for( j = 0 ; j < admjnList.length ; j++ ){
        //        admjnList[j].checked = false;
        //    }
        } else if(index[i] == 'grade') {
            for( j = 0 ; j < gradeList.length ; j++ ){
                gradeList[j].checked = false;
            }
        } else if(index[i] == 'course') {
            for( j = 0 ; j < courseList.length ; j++ ){
                courseList[j].checked = false;
            }
        } else if(index[i] == 'classroom') {
            for( j = 0 ; j < classroomList.length ; j++ ){
                classroomList[j].checked = false;
            }
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
    if(userLevel == 3) {
        receiveLevelText = "生徒";
        for( i = 0 ; i < studentList.length ; i++ ){
            if(studentList[i].checked == true){
                getList.push(studentList[i].value.split("|"));
            }
        }
    } else if(userLevel == 2) {
        receiveLevelText = "教員";
        for( i = 0 ; i < teacherList.length ; i++ ){
            if(teacherList[i].checked == true){
                getList.push(teacherList[i].value.split("|"));
            }
        }
    //} else if(userLevel == 1) {
    //    receiveLevelText = "管理者";
    //    for( i = 0 ; i < adminList.length ; i++ ){
    //        if(adminList[i].checked == true){
    //            getList.push(adminList[i].value.split("|"));
    //        }
    //    }
    } else if(userLevel == 0) {
        var j = 0;
        receiveLevelText = "範囲";
        for( i = 0 ; i < gradeList.length ; i++ ){
            if(gradeList[i].checked == true){
                getList.push(gradeList[i].value.split("|"));
                receiveRangeList[0] = getList[j++][0];
            }
        }
        for( i = 0 ; i < courseList.length ; i++ ){
            if(courseList[i].checked == true){
                getList.push(courseList[i].value.split("|"));
                receiveRangeList[1] = getList[j++][0];
            }
        }
        for( i = 0 ; i < classroomList.length ; i++ ){
            if(classroomList[i].checked == true){
                getList.push(classroomList[i].value.split("|"));
                receiveRangeList[2] = getList[j++][0];
            }
        }
    }

    //タイトル関連
    if(document.getElementById('input-title').value.length != 0){
        modal.find('.modal-title').text(document.getElementById('input-title').value);
        document.send_form.elements['title'].value = document.getElementById('input-title').value;
    } else {
        modal.find('.modal-title').text("タイトルを入力してください");
        document.getElementsByClassName('submit')[0].classList.add('disabled');
        document.getElementById('submit').disabled = true;
        document.send_form.elements['sendFlag'].value = false;
    }

    //メッセージ関連
    if(document.getElementById('input-message').value.length != 0){
        modal.find('.modal-message').text(document.getElementById('input-message').value);
        document.send_form.elements['message'].value = document.getElementById('input-message').value;
    } else {
        modal.find('.modal-message').text("メッセージを入力してください");
        document.getElementsByClassName('submit')[0].classList.add('disabled');
        document.getElementById('submit').disabled = true;
        document.send_form.elements['sendFlag'].value = false;
    }

    //メッセージ関連
    if(document.getElementsByClassName('input-type')[0].checked == true) {
        document.send_form.elements['type'].value = 0
        modal.find('.modal-type').text("お知らせ");
    } else if(document.getElementsByClassName('input-type')[1].checked == true) {
        document.send_form.elements['type'].value = 1
        modal.find('.modal-type').text("公開メッセージ");
    } else if(document.getElementsByClassName('input-type')[2].checked == true) {
        document.send_form.elements['type'].value = 2
        modal.find('.modal-type').text("非公開メッセージ");
    } else {};

    //チェックボックス関連その１(個人宛メッセージ)
    if(getList.length != 0 && userLevel != 0){
        for( i = 0 ; i < getList.length ; i++ ){
            receiveIdList.push(Number(getList[i][0]))
            receiveNameList.push(getList[i][1])
        }

        modal.find('.modal-receive-level').text(receiveLevelText);
        modal.find('.modal-receive').text(receiveNameList);
        document.send_form.elements['receive_user_level_id'].value = userLevel;
        document.send_form.elements['receive_user_id'].value = receiveIdList;
    } else if(getList.length != 0 && userLevel == 0){
        for( i = 0 ; i < getList.length ; i++ ){
            //Idの方は、既にチェックボックスの確認処理で代入済
            receiveNameList.push(getList[i][1])
        }

        modal.find('.modal-receive-level').text(receiveLevelText);
        modal.find('.modal-receive').text(receiveNameList);
        document.send_form.elements['receive_user_level_id'].value = userLevel;
        document.send_form.elements['receive_user_id'].value = receiveIdList;
        document.send_form.elements['grade_id'].value = receiveRangeList[0];
        document.send_form.elements['course_id'].value = receiveRangeList[1];
        document.send_form.elements['classroom_id'].value = receiveRangeList[2];
    } else {
        modal.find('.modal-receive').text("受信者を設定してください");
        document.getElementsByClassName('submit')[0].classList.add('disabled');
        document.getElementById('submit').disabled = true;
        document.send_form.elements['sendFlag'].value = false;
    }

    //チェックボックス関連その１(範囲宛メッセージ)

    //console.log(receiveLevelText);
    //console.log(receiveIdList);
    //console.log(receiveNameList);

});

