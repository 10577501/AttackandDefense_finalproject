//焦点图切换功能
$(function(){
	var height = 261; //每张图片的高度
	var speed = 800;  //动画时间
	var delay = 5000; //自动切换的间隔时间
	var now = 0;      //当前显示的图片索引
	var $picsUl = $('.hot-pics ul');  //获取对象
	//复制列表中的第一个图片，追加到列表最后
	$picsUl.find('li:first').clone().appendTo($picsUl);
	var $picsLi = $picsUl.find('li'); //获取对象
	var $barLi = $('.hot-bar li');    //获取对象
	var max = $picsLi.length-2;  //图片的最大索引
	var timer = null;            //计时器
	//设置周期计时器，实现图片自动切换
	timer = setInterval(change_auto,delay);
	//鼠标滑过时暂停移动，移出时恢复移动
	$('.hot').on({
		mouseenter:function(){
			clearInterval(timer);
		},
		mouseleave:function(){
			clearInterval(timer);
			timer = setInterval(change_auto,delay);
		}
	});
	//单击小圆点切换图片
	$barLi.click(function(){
		now = $(this).index();
		change_next();
		change_bar();
	});
	//图片自动切换
	function change_auto(){
		if(!$picsUl.is(':animated')){
			//判断是否达到图片列表末尾
			if(now < max){
				now += 1;
				change_next();
			}else{
				now = 0;
				change_reset();
			}
			change_bar();
		}
	}
	//切换到下一张图片
	function change_next(){
		$picsUl.animate({top:-height*now},speed);
	}
	//切换到第一张图片
	function change_reset(){
		$picsUl.animate({top:-height*(max+1)},speed,function(){
			$(this).css("top",0);
		});
	}
	//小圆点切换
	function change_bar(){
		$barLi.eq(now).addClass("current").siblings().removeClass("current");
	}
});
