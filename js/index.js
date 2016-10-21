	//http://test.shabby-wjt.cn:8000/BBT_duiduipen/interface.php
	var swiper = new Swiper(".swiper-container", {
		direction: 'horizontal',
		initialSlide: 0
	});
	var answer = new Array();
	var question = new Array();
	var title = new Array();
	var maxIndex = -1;
	var data = new Array();


	function setDaTa() {
		for (var i = 0; i < 8; i++) {
			title[i] = "hello world-" + (i + 1);
			var option = new Array();
			for (var j = 0; j < 4; j++) {
				option[j] = "this is option " + (i + 1) + "-" + (j + 1);
			}
			question[i] = option;
		}
		for (var i = 0; i <= 9; i++)
			answer[i] = 0;
	}


	function cutSpace(str) {
		for (var i = 0;
			(str.charAt(i) == ' ') && i < str.length; i++);
		if (i == str.length) return ''; //whole string is space
		var newstr = str.substr(i);
		for (var i = newstr.length - 1; newstr.charAt(i) == ' ' && i >= 0; i--);
		newstr = newstr.substr(0, i + 1);
		return newstr;
	}

	window.onload = function() {

		var width = document.body.clientWidth;
		var rem = width / 7.5;
		$('html').css('font-size', rem);
		//swiper.lockSwipes();

		setDaTa();
	}


	$('#sign_up').bind('click', function() {
		swiper.unlockSwipes();
		swiper.slideNext();
		swiper.lockSwipes();
	})



	$('#test_begin').bind('click', function() {
		var pattern = /^1[3|4|5|6|7|8][0-9]\d{4,8}$/;
		var name = $('#name').attr('value');
		var sex = $('#sex option').not(function() {
			return !this.selected;
		}).attr('value');
		var academy = $('#academy option').not(function() {
			return !this.selected;
		}).attr('value');
		var grade = $('#grade option').not(function() {
			return !this.selected;
		}).attr('value');
		var phone = $('#phone').attr('value');
		var wechat = $('#wechat_id').attr('value');
		name = cutSpace(name);
		if (name.length == 0) {
			alert("未填写姓名！");
			$('#name').focus();
			return;
		}
		if (sex == "default") {
			alert("未选择性别！");
			return;
		}
		if (academy == "default") {
			alert("未选择学院！");
			return;
		}
		if (grade == "default") {
			alert("未选择年级！");
			return;
		}
		if (phone == "") {
			alert("未填写手机！");
			$('#phone').focus();
			return;
		}
		if (!pattern.exec(phone)) {
			alert("手机号码格式不对，请重新填写~");
			$('#phone').focus();
		}
		//0:name 1:sex 2:academy 3:grade 4:phone 5:wid
		data[0] = name;
		data[1] = sex;
		data[2] = academy;
		data[3] = grade;
		data[4] = phone;
		data[5] = wechat;
		swiper.unlockSwipes();
		swiper.slideNext();
		swiper.lockSwipes();
	});

	//点击流程：
	//获取点击的选项
	//获取之前点击的选项，对比两个选项
	//如果和之前点击的一样，不进行操作
	//如果不一样，之前的加一个褪色的css，点击的加一个显色的css，计数器加1
	//上一步和下一步根据index来判断

	$('#chose_1').bind('click', function() {
		chose(1, 0);
	})
	$('#chose_2').bind('click', function() {
		chose(2, 0);
	})
	$('#chose_3').bind('click', function() {
		chose(3, 0);
	})
	$('#chose_4').bind('click', function() {
		chose(4, 0);
	})
	$('#chose_img_1').bind('click', function() {
		chose(1, 1);
	})
	$('#chose_img_2').bind('click', function() {
		chose(2, 1);
	})
	$('#chose_img_3').bind('click', function() {
		chose(3, 1);
	})
	$('#chose_img_4').bind('click', function() {
		chose(4, 1);
	})



	function clearClass(selector) {
		if ($(selector).hasClass("animated"))
			$(selector).removeClass("animated");
		if ($(selector).hasClass("show"))
			$(selector).removeClass("show");
		if ($(selector).hasClass("hide"))
			$(selector).removeClass("hide");
		if ($(selector).hasClass("animated_fast"))
			$(selector).removeClass("animated_fast");

	}

	function chose(x, type) {
		var index = answer[9];
		var last = answer[index];
		if (last == x) return;
		var pre = "#chose_";
		if (type == 1)
			pre = "#chose_img_";
		var selector = pre + x;
		var lastSelector = pre + last;
		clearClass(selector);
		$(selector).addClass("animated");
		$(selector).addClass("show");
		$(lastSelector).addClass("animated");
		$(lastSelector).addClass("hide");
		answer[index] = x;
		if (answer[9] > maxIndex)
			maxIndex = answer[9];
	}

	function setColor(last, next, type) {

		if (last == next) return;
		var pre = "#chose_";
		if (type == 1)
			pre = "#chose_img_";
		if (next == 0) {
			var selector = pre + last;
			clearClass(selector);
			$(selector).addClass("animated_fast");
			$(selector).addClass("hide");

			return;
		}
		if (last == 0) {
			var selector = pre + next;
			clearClass(selector);
			$(selector).addClass("animated_fast");
			$(selector).addClass("show");

			return;
		}
		var selector = pre + last;
		var nextSelector = pre + next;
		clearClass(selector);
		clearClass(nextSelector);
		$(selector).addClass("animated_fast");
		$(selector).addClass("hide");
		$(nextSelector).addClass("animated_fast");
		$(nextSelector).addClass("show");
	}

	$("#last").bind('click', function() {

		var index = answer[9];
		if (index == 0) return;
		if (index != 1) {
			$("#last").css("background-color", "#6444ab");
			$("#next").css("background-color", "#6444ab");
		} else {
			$("#last").css("background-color", "#95919f");
			$("#next").css("background-color", "#6444ab");
		}
		index--;
		$("#title").text(title[index]);
		for (var i = 1; i <= 4; i++) {
			$("#option_" + i).text(question[index][i - 1]);
		}
		setColor(answer[index + 1], answer[index], 0);
		answer[9] = index;
	})

	$("#next").bind('click', function() {
		if (maxIndex < answer[9]) {
			alert("请选择一个项目！");
			return;
		}
		var index = answer[9];
		if (index == 7) {
			answer[9] = index + 1;
			swiper.unlockSwipes();
			swiper.slideNext(function() {}, 0);
			swiper.lockSwipes();
			setColor(answer[7], answer[8], 1);
			return;
		}
		$("#last").css("background-color", "#6444ab");
		$("#next").css("background-color", "#6444ab");
		index++;
		$("#title").text(title[index]);
		for (var i = 1; i <= 4; i++) {
			$("#option_" + i).text(question[index][i - 1]);
		}
		var type = 0;
		if (index == 8)
			type = 1;
		setColor(answer[index - 1], answer[index], type);
		answer[9] = index;
	})


	$("#last_img").bind('click', function() {
		var index = answer[9];
		index--;
		answer[9] = index;
		swiper.unlockSwipes();
		swiper.slidePrev(function() {}, 0);
		swiper.lockSwipes();
		setColor(answer[8], answer[7], 0)
	})

	$("#next_img").bind('click', function() {
		if (maxIndex < answer[9]) {
			alert("请选择一个项目！");
			return;
		}
		packAndSentData();
		swiper.unlockSwipes();
		swiper.slideNext();
		swiper.lockSwipes();
	})

	function packAndSentData() {
		var a = 0;
		var b = 0;
		var c = 0;
		var d = 0;
		for (var i = 0; i < 9; i++)
			switch (answer[i]) {
				case 1:
					a++;
					break;
				case 2:
					b++;
					break;
				case 3:
					c++;
					break;
				case 4:
					d++;
					break;
			}
		alert("name: " + data[0] + "\nsex: " + data[1] + "\nacademy: " + data[2] + "\ngrade " + data[3] + "\nphone: " + data[4]);
		alert("文艺爱情: " + a + "\n科幻动作: " + b + "\n恐怖悬疑: " + c + "\n动画喜剧: " + d);
		// $.ajax({
		// 	type: 'POST',
		// 	url: 'http://test.shabby-wjt.cn:8000/BBT_duiduipen/interface.php',
		// 	data: {
		// 		//0:name 1:sex 2:academy 3:grade 4:phone 5:wid
		// 		name: data[0],
		// 		sex: data[1],
		// 		academy: data[2],
		// 		grade: data[3],
		// 		phone: data[4],
		// 		wechat: data[5],
		// 		insert: 1,
		// 		mark: {
		// 			"文艺爱情": a,
		// 			"科幻动作": b,
		// 			"恐怖悬疑": c,
		// 			"动画喜剧": d
		// 		}
		// 	}

		// 	,
		// 	dataType: 'json',
		// 	success: function() {
		// 		alert(status);
		// 	}
		// })
	}