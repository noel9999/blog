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