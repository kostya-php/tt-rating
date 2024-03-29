﻿$(function() {
	$(".date").datepicker({
		format: "yyyy-mm-dd",
		todayBtn: "linked",
		language: "ru",
		autoclose: true,
		todayHighlight: true
	}).on("changeDate", function(e){
		check_atform();
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
		check_atform();
		console.log("название турнира изменено");
	});
	// событие изменения поля "Количество партий"
	rounds.change(function(){
		check_atform();
		console.log("количество партий изменено");
	});
	// событие изменения поля "Тип протокола"
	protocol.change(function(){
		check_atform();
		console.log("тип протокола изменен");
	});
	// событие изменения поля "Игроки"
	$(".player-select").on("change", function(evt, params) {
		check_atform();
	});
	// событие нажатия кнопки "Добавить"
	$(".add-tournament").submit(function() {
		if(check_atform()) {
			return true;
		} else {
			alert("Неправильно заполнена форма. Проверьте введенные данные и попробуйте еще раз.");
			return false;
		}
	});
	// событие нажатия кнопки "Изменить"
	$("#edit_match").submit(function() {
		if(check_rounds()) {
			return true;
		} else {
			alert("Неправильно заполнена форма. Проверьте введенные данные и попробуйте еще раз.");
			return false;
		}
	});
});
function check_atform() {
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
		if((v_name)&&(v_date)&&(v_rounds)&&(v_protocol)&&(v_players)) {
			$(".submit").css("color", "black");
			return true;
		} else {
			$(".submit").css("color", "red");
			return false;
		}
}

// проверка соответствия выбранной опции и состояния элементов. вызывается каждый раз при изменении опции.
function set_option(s) {
	var rounds = $("#rounds").val();
	switch(s) {
		case "none":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).val("");
				$("#xx_"+i).prop('disabled', false);
				$("#yy_"+i).val("");
				$("#yy_"+i).prop('disabled', false);
			}
			$("#y").val("0");
			$("#y").prop("readonly", true);
			$("#x").val("0");
			$("#x").prop("readonly", true);
			break;
		case "neyav":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).val("0");
				$("#xx_"+i).prop('disabled', true);
				$("#yy_"+i).val("0");
				$("#yy_"+i).prop('disabled', true);
			}
			$("#y").val("0");
			$("#y").prop("readonly", true);
			$("#x").val("0");
			$("#x").prop("readonly", true);
			break;
		case "tech_x":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).val("0");
				$("#xx_"+i).prop("disabled", true);
				$("#yy_"+i).val("0");
				$("#yy_"+i).prop("disabled", true);
			}
			$("#x").val("0");
			$("#x").prop("readonly", true);
			$("#y").val(Math.ceil(rounds / 2));
			$("#y").prop("readonly", true);
			break;
		case "tech_y":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).val("0");
				$("#xx_"+i).prop("disabled", true);
				$("#yy_"+i).val("0");
				$("#yy_"+i).prop("disabled", true);
			}
			$("#y").val("0");
			$("#y").prop("readonly", true);
			$("#x").val(Math.ceil(rounds / 2));
			$("#x").prop("readonly", true);
			break;
		case "pred":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).val("0");
				$("#xx_"+i).prop('disabled', true);
				$("#yy_"+i).val("0");
				$("#yy_"+i).prop('disabled', true);
			}
			$("#y").val("0");
			$("#y").prop("readonly", false);
			$("#x").val("0");
			$("#x").prop("readonly", false);
			break;
		case "reset":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).val("0");
				$("#xx_"+i).prop('disabled', true);
				$("#yy_"+i).val("0");
				$("#yy_"+i).prop('disabled', true);
			}
			$("#y").val("0");
			$("#y").prop("readonly", true);
			$("#x").val("0");
			$("#x").prop("readonly", true);
			break;
	}
}

// проверка ввода счета партий. вызывается один раз при загрузке страницы и каждый раз при вводе счета.
function check_rounds() {
	if($("input[name=option]:checked", "#edit_match").val() === "none") {
		var rounds = $("#rounds").val();
		var max_round = Math.ceil(rounds / 2);
		var x = 0;
		var y = 0;
		var error = false;
		for(i=1;i<=rounds;i++) {
			var xx = parseInt($("#xx_"+i).val(),10);
			var yy = parseInt($("#yy_"+i).val(),10);
			if((xx>yy)&&(xx >= 10)&&(yy >= 10)&&(xx-yy==2)) {
				x++;
			} else 
				if((xx > yy)&&(xx==11)&&(yy<10)) {
					x++;
				} else 
					if((yy>xx)&&(yy >= 10)&&(xx >= 10)&&(yy-xx==2)) {
						y++;
					} else
						if((yy > xx)&&(yy==11)&&(xx<10)) {
							y++;
						}
			if((x < max_round)&&(y < max_round)) {
				if(xx == yy) error = true;
				if((xx > yy)&&(xx<11)) {
					error = true;
				}
				if((xx < yy)&&(yy<11)) {
					error = true;
				}
			}
		}
		$("#x").val(x);
		$("#y").val(y);
		var match_rounds = parseInt(x+y,10);
		$("#match_rounds").val(match_rounds);
		if((!error)&&((x > y)||(x < y))&&((x == max_round)||(y == max_round))) {
			for(i=1;i<=rounds;i++) {
				xx = $("#xx_"+i).val();
				yy = $("#yy_"+i).val();
				if ((xx === "")&&(yy === "")) {		
					$("#xx_"+i).prop('disabled', true);
					$("#xx_"+i).val("0");
					$("#yy_"+i).prop('disabled', true);
					$("#yy_"+i).val("0");
				}
			}
		} else {
			for(i=1;i<=rounds;i++) {
				xx = $("#xx_"+i).val();
				yy = $("#yy_"+i).val();
				if ((xx == "0")&&(yy == "0")) {	
					$("#xx_"+i).prop('disabled', false);
					$("#xx_"+i).val("");
					$("#yy_"+i).prop('disabled', false);
					$("#yy_"+i).val("");
				}
			}
		}
	}
	if(error) return false; else
		if(!error) return true;
}

// проверка соответсткия выбранной опции и состояния элементов. вызывается 1 раз после загрузки страницы.
function check_option() {
	var rounds = $("#rounds").val();
	var option = $("input[name=option]:checked", "#edit_match").val();
	switch(option) {
		case "none":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).prop('disabled', false);
				$("#yy_"+i).prop('disabled', false);
			}
			$("#y").prop("readonly", true);
			$("#x").prop("readonly", true);
			break;
		case "neyav":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).prop('disabled', true);
				$("#yy_"+i).prop('disabled', true);
			}
			$("#y").prop("readonly", true);
			$("#x").prop("readonly", true);
			break;
		case "tech_x":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).prop("disabled", true);
				$("#yy_"+i).prop("disabled", true);
			}
			$("#x").prop("readonly", true);
			$("#y").prop("readonly", true);
			break;
		case "tech_y":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).prop("disabled", true);
				$("#yy_"+i).prop("disabled", true);
			}
			$("#y").prop("readonly", true);
			$("#x").prop("readonly", true);
			break;
		case "pred":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).prop('disabled', true);
				$("#yy_"+i).prop('disabled', true);
			}
			$("#y").prop("readonly", false);
			$("#x").prop("readonly", false);
			break;
		case "reset":
			for(i=1;i<=rounds;i++) {
				$("#xx_"+i).prop('disabled', true);
				$("#yy_"+i).prop('disabled', true);
			}
			$("#y").prop("readonly", true);
			$("#x").prop("readonly", true);
			break;
	}
}