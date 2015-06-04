$(function() {
	$(".date").datepicker({
		format: "yyyy-mm-dd",
		todayBtn: "linked",
		language: "ru",
		autoclose: true,
		todayHighlight: true
	}).on("changeDate", function(e){
		check();
		console.log("дата изменена!");
	});
	
	$(".player-select").chosen({
		no_results_text: "Игрок не найден!",
		single_backstroke_delete: false,
		search_contains: true,
		width: "250px"
	});
	
	var name = $(".name");
	var rounds = $(".rounds");
	var protocol = $(".protocol");
	var date = $(".date");
	
	// событие нажалия клавиши в поле "Название турнира"
	name.keyup(function(){
		check();
		console.log("название турнира изменено");
	});
	// событие изменения поля "Количество партий"
	rounds.change(function(){
		check();
		console.log("количество партий изменено");
	});
	// событие изменения поля "Тип протокола"
	protocol.change(function(){
		check();
		console.log("тип протокола изменен");
	});
	// событие изменения поля "Игроки"
	$(".player-select").on("change", function(evt, params) {
		check();
	});
	// событие нажатия кнопки "Добавить"
	$(".add-tournament").submit(function() {
		if(check()) {
			return true;
		} else {
			alert("Неправильно заполнена форма. Проверьте введенные данные и попробуйте еще раз.");
			return false;
		}
	});
	check();
});
function check() {
	// переменные, содержащие результат проверки
	var v_name = false; // Название турнира
	var v_date = false; // Дата
	var v_rounds = false; // Количество партий
	var v_protocol = false; // Протокол
	var v_players = false; // Игроки (в зависимости от протокола)
	
	// проверка названия турнира
	var regexp_name = /^[0-9а-яА-ЯёЁa-zA-Z-\s.,!]+$/;
	var name = $(".name").val();
	if (regexp_name.test(name)) {
		v_name = true;
	} else {
		v_name = false;
	}
	//проверка даты
	var regexp_date = /[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])/;
	var date = $(".date").val();
	if (regexp_date.test(date)) {
		v_date = true;
	} else {
		v_date = false;
	}
	// проверка количества партий
	var rounds = $(".rounds").val();
	switch(rounds) {
		case "3":
			v_rounds = true;
			break;
		case "5":
			v_rounds = true;
			break;
		case "7":
			v_rounds = true;
			break;
		default:
			v_rounds = false;
	}
	// проверка протокола и количества игроков
	var protocol = $(".protocol :selected").val();
	var selected = 0;
	$(".player-select option:selected").each(function(){
            selected++;
        });
    $(".selected-players").text(selected);
	switch(protocol) {
		case "krug":
			v_protocol = true;
			if ((selected>2)&&(selected<13)) {
				v_players = true;
			} else {
				v_players = false;
			}
			break;
		case "vib8":
			v_protocol = true;
			if(selected==8) {
				v_players = true;
			} else {
				v_players = false;
			}
			break;
		case "vib16":
			v_protocol = true;
			if(selected==16) {
				v_players = true;
			} else {
				v_players = false;
			}
			break;
		default:
			v_protocol = false;
			v_players = false;
		}
		// окончание проверки
		if(v_name) {
			$(".name").css("border", "1px solid black");			
		} else {
			$(".name").css("border", "1px solid red");
		}
		if(v_date) {
			$(".date").css("border", "1px solid black");			
		} else {
			$(".date").css("border", "1px solid red");
		}
		if(v_rounds) {
			$(".rounds").css("border", "1px solid black");			
		} else {
			$(".rounds").css("border", "1px solid red");
		}
		if(v_protocol) {
			$(".protocol").css("border", "1px solid black");
		} else {
			$(".protocol").css("border", "1px solid red");			
		}
		if(v_players) {
			$(".selected-players").css("color", "black");
		} else {
			$(".selected-players").css("color", "red");
		}
		if((v_name)&(v_date)&(v_rounds)&(v_protocol)&(v_players)) {
			$(".submit").css("color", "black");
			return true;
		} else {
			$(".submit").css("color", "red");
			return false;
		}
}