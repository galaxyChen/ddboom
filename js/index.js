	//http://test.shabby-wjt.cn:8000/BBT_duiduipen/interface.php
	var swiper = new Swiper(".swiper-container", {
		direction: 'vertical',
		initialSlide: 0
	});
	var answer = [];
	var question = [];
	var title = [];
	var maxIndex = -1;
	var data = [];
	var pro = [];


	function setData() {
		answer[9] = 0;
		var option = [];
		title[0] = "1.如果你生活在乱世，你会选择下列哪一位作为搭档一起谋求生路？";
		option[0] = "帅气鲜肉";
		option[1] = "科学怪人";
		option[2] = "老练侦探";
		option[3] = "漩涡鸣人";
		question[0] = option;
		pro[0] = "题目9-1";

		option = [];
		title[1] = "2.在你生日的时候你更愿意收到什么礼物？";
		option[0] = "一个漂亮的水晶球";
		option[1] = "一套炫酷的运动装备";
		option[2] = "一个整蛊人的玩具";
		option[3] = "一个可爱的大白";
		question[1] = option;
		pro[1] = "题目9-2";

		option = [];
		title[2] = "3.你更喜欢下列哪个游乐园项目？";
		option[0] = "旋转木马";
		option[1] = "过山车";
		option[2] = "鬼屋";
		option[3] = "夹娃娃机";
		question[2] = option;
		pro[2] = "题目9-3";

		option = [];
		title[3] = "4.如果有一天，你参加一个角色扮演晚会，你会选择怎么样的着装？";
		option[0] = "穿着带有青春气息的校服";
		option[1] = "造型炫酷的盔甲";
		option[2] = "惊悚的鬼怪或者吸血鬼造型";
		option[3] = "小丑服装";
		question[3] = option;
		pro[3] = "题目9-4";

		option = [];
		title[4] = "5.电影的终幕有一行字是hold my hand to the end of our life，你认为电影的前一幕会是？";
		option[0] = "一对恋人刚刚决定携手向前行";
		option[1] = "高智商科学家为妻子研制长寿药品";
		option[2] = "一个怨灵依旧纠缠着主角不放";
		option[3] = "多啦A梦乘着时光机离大雄而去";
		question[4] = option;
		pro[4] = "题目9-5";

		option = [];
		title[5] = "7.如果你能拥有以下四个能力之一，你希望是哪一个？";
		option[0] = "满分情书";
		option[1] = "飞檐走壁";
		option[2] = "神探技能";
		option[3] = "手办制作";
		question[5] = option;
		pro[5] = "题目9-6";


		option = [];
		title[6] = "8.你更希望在以下哪个地方看电影？";
		option[0] = "露天的汽车影院";
		option[1] = "拥有5D观影效果的影院";
		option[2] = "黑漆漆的地下室";
		option[3] = "迪士尼里面的影院";
		question[6] = option;
		pro[6] = "题目9-7";

		option = [];
		title[7] = "9.如果你有机会体验电影里的故事，你希望成为以下哪一部电影的主角？";
		option[0] = "我的少女时代";
		option[1] = "饥饿游戏";
		option[2] = "釜山行";
		option[3] = "飞屋环游记";
		question[7] = option;
		pro[7] = "题目9-8";
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

	function isWeiXin() {
		var ua = window.navigator.userAgent.toLowerCase();
		if (ua.match(/MicroMessenger/i) == 'micromessenger') {
			return true;
		} else {
			return false;
		}
	}

	window.onload = function() {

		if (!isWeiXin()) {
			alert("为了更好的体验，请在微信打开该链接！");
			$("body").hide();
		}

		var height = document.body.clientHeight;
		var width = document.body.clientWidth;
		$(".swiper-slide").css("-moz-background-size", width + "px " + height + "px");
		$(".swiper-slide").css("background-size", width + "px " + height + "px");
		var rem = width / 7.5;
		$('html').css('font-size', rem);
		swiper.lockSwipes();
		setData();
		$('#loading-text').removeClass('animated-infinite');
		$('#loading-text').removeClass('flash');
		$('#loading-text').addClass('animated');
		$('#loading-text').addClass('fadeOut');
		$('#cover').addClass('fadeOut');
		setTimeout("$('#cover').remove();", 1100);

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
			return;
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

	$('#chose_option_1').bind('click', function() {
		chose(1, 0);
	})
	$('#chose_option_2').bind('click', function() {
		chose(2, 0);
	})
	$('#chose_option_3').bind('click', function() {
		chose(3, 0);
	})
	$('#chose_option_4').bind('click', function() {
		chose(4, 0);
	})
	$('#chose_option_img_1').bind('click', function() {
		chose(1, 1);
	})
	$('#chose_option_img_2').bind('click', function() {
		chose(2, 1);
	})
	$('#chose_option_img_3').bind('click', function() {
		chose(3, 1);
	})
	$('#chose_option_img_4').bind('click', function() {
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


	function goLast() {
		var index = answer[9];
		$("#title").text(title[index]);
		$("#process").text(pro[index]);
		for (var i = 1; i <= 4; i++) {
			$("#option_" + i).text(question[index][i - 1]);
		}
		setColor(answer[index + 1], answer[index], 0);
		answer[9] = index;
	}

	$("#last").bind('click', function() {

		var index = answer[9];
		if (index == 0) return;
		if (index != 1) {

		} else {
			$("#last").css("background-color", "#929292");
			$("#next").css("background-color", "#262626");
			$("#last").css("border-color", "#929292");
			$("#next").css("border-color", "#262626");

		}

		index--;
		answer[9] = index;
		goLast();
	})

	function goNext() {
		var index = answer[9];
		$("#last").css("background-color", "#262626");
		$("#next").css("background-color", "#262626");
		$("#last").css("border-color", "#262626");
		$("#next").css("border-color", "#262626");

		index++;
		$("#title").text(title[index]);
		$("#process").text(pro[index]);

		for (var i = 1; i <= 4; i++) {
			$("#option_" + i).text(question[index][i - 1]);
		}
		var type = 0;
		if (index == 8)
			type = 1;
		setColor(answer[index - 1], answer[index], type);
		answer[9] = index;
	}

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
		goNext();
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
			// alert("name: " + data[0] + "\nsex: " + data[1] + "\nacademy: " + data[2] + "\ngrade " + data[3] + "\nphone: " + data[4]);
			// alert("文艺爱情: " + a + "\n科幻动作: " + b + "\n恐怖悬疑: " + c + "\n动画喜剧: " + d);
		$.ajax({
			type: 'POST',
			url: './php/interface.php',
			//"./php/test.php",
			data: {
				//0:name 1:sex 2:academy 3:grade 4:phone 5:wid
				name: data[0],
				gender: data[1],
				college: data[2],
				grade: data[3],
				phone: data[4],
				wechat: data[5],
				insert: 1,
				mark: JSON.stringify({
					"A": a,
					"B": b,
					"C": c,
					"D": d
				})
			},
			success: function(status) {
				var statu = JSON.parse(status);
				if (statu["status"] > 0) {
					alert("恭喜你报名成功!\n" +
						"你可以从11月2日起在【华工百步梯】公众号菜单栏\n[电影对对碰]查询配对结果");
					$("#lottery_code").text("抽奖码" + statu["info"]);
					$("#type").text("你的类型是：" + statu["type"]);
					swiper.unlockSwipes();
					swiper.slideNext();
					swiper.lockSwipes();
				} else {
					alert("服务器小伙伴炸了~不好意思报名失败了 \nstatu: " + statu["status"] + "\ninfo: " + statu["info"]);
				}
			}
		})
	}


	$(document).ready(function() {
		$(document).bind('keydown', function(evt) {
			if (evt.keyCode == 9) {
				if (evt.preventDefault) {
					evt.preventDefault();
				} else {
					evt.returnValue = false;
				}
			}
		});
	});