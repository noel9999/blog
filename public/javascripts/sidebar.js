$('.sidebar').hover(
    function(){
      $(this).stop().animate(
        {
          right: '0',
          backgroundColor: 'rgb(243,244,246)',
          opacity: '1'
        },
        500
      );
    },
    function(){
      $(this).stop().animate(
        {
          right: '-271px',
          backgroundColor: 'rgb(75,79,87)',
          opacity: '0.5'
        },
        500
      );
    }
  );


$(function(){
  // 先取得 #cart 及其 top 值
  var $sidebar = $('.sidebar'),
    _top = $sidebar.offset().top;

  // 當網頁捲軸捲動時
  var $win = $(window).scroll(function(){
    // 如果現在的 scrollTop 大於原本 #sidebar 的 top 時
    if($win.scrollTop() >= _top){
      // 如果 $sidebar 的座標系統不是 fixed 的話
      if($sidebar.css('position')!='fixed'){
        // 設定 #sidebar 的座標系統為 fixed
        $sidebar.css({
          position: 'fixed',
          top: 0
        });
      }
    }else{
      // 還原 #sidebar 的座標系統為 absolute
      $sidebar.css({
        position: 'absolute'
      });
    }
  });
});
