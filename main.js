/* United Charitable custom javascript */
(function($) {

  /*collapse mobile menu */
  function setup_collapsible_submenus() {
       var $menu = $('#mobile_menu'),
           top_level_link = '#mobile_menu .menu-item-has-children > a';

       $menu.find('a').each(function() {
           $(this).off('click');

           if ( $(this).is(top_level_link) ) {
               $(this).attr('href', '#');
           }

           if ( ! $(this).siblings('.sub-menu').length ) {
               $(this).on('click', function(event) {
                   $(this).parents('.mobile_nav').trigger('click');
               });
           } else {
               $(this).on('click', function(event) {
                   event.preventDefault();
                   $(this).parent().toggleClass('visible');
               });
           }
       });
   }

   $(window).load(function() {
       setTimeout(function() {
           setup_collapsible_submenus();
       }, 700);
   });

/* Dropdown Menu Click Handler */
/* click handler for non-mega-menu dropdown */
  $(function(){
      jQuery('.menu-item.menu-item-has-children:not(.mega-menu)').on('click', function(e){
          jQuery(this).find('.sub-menu').toggle();
        })
  })



 /* Library Redirect */
 $(function(){
   if($('#library-redirect').length > 0){
     setTimeout(function(){
       window.location.href = $('#library-redirect').data('url');
     },2000);
   }
 })

/* blog categories */
$(function(){
  jQuery('.et_pb_blog_grid_wrapper:not(.library-blog-grid) .post-meta a').each(function(){
   var cat = jQuery(this).html()

    if(cat == "Blog" || cat == "Case Study" || cat == "News" || cat == "Technical Blog"){
      jQuery(this).remove();
  	}
  })

  jQuery('.et_pb_blog_grid_wrapper:not(.library-blog-grid) .post-meta').each(function(){
  var content = jQuery(this).html();

  content = content.replace(/, ,/g, ",");
    content = content.replace(/\| ,/g, "|");
    content = content.replace(/^,/g, "");
  content = content.replace(/, $/g, "");
  jQuery(this).html(content);
  })

  jQuery('.single-post .et_pb_title_meta_container a').each(function(){
   var cat = jQuery(this).html()
//   console.log(cat)
    if(cat == "Blog" || cat == "Case Study" || cat == "News" || cat == "Technical Blog"){
      jQuery(this).remove();
  	}
  })

  jQuery('.single-post .et_pb_title_meta_container').each(function(){
  var content = jQuery(this).html();

  content = content.replace(/, ,/g, ",");
    content = content.replace(/\| ,/g, "|");
  content = content.replace(/, $/g, "");
  jQuery(this).html(content);
  })

})


/* animation  classes */

  $(function(){
    rsdAnimationInit();
  });

  $(window).on('scroll', function (e) {
      rsdAnimationInit();
  });


function rsdAnimationInit() {

     var animationEl = $('.fade-in, .slide-left, .slide-right, .slide-up');


      animationEl.each(function () {
          var currentEl = $(this);

          if (rsdOnScreen(currentEl)) {
              currentEl.addClass('animate-in');
          }
      });
  }

  // takes jQuery(element) a.k.a. $('element')
  function rsdOnScreen(element) {
      // window bottom edge
      var windowBottomEdge = $(window).scrollTop() + $(window).height();

      // element top edge
      var elementTopEdge = element.offset().top;
      var offset = 100;

      // if element is between window's top and bottom edges
      return elementTopEdge + offset <= windowBottomEdge;
  }





  /* Blog Resize */

  $(function(){

    if(  $(".true-grid").length > 0){
      setTimeout(function(){
        $(window).trigger('resize');
      }, 1000);
    }


    $( window ).resize(function() {
    $(".true-grid").each(function(){
        equalise_articles($(this));
    });
  });

  $(".true-grid").each(function(){
      var blog = $(this);
      equalise_articles($(this));

      var observer = new MutationObserver(function (mutations) {
          equalise_articles(blog);
      });

      var config = { subtree: true, childList: true };
      observer.observe(blog[0], config);
  });

  function equalise_articles(blog){
      var articles = blog.find("article");

      var heights = [];

      articles.each(function(){
          var height = 0;
          height += jQuery(this).height();
          heights.push(height);
      });

      var max_height = Math.max.apply(Math,heights);

      articles.each(function(){
          $(this).height(max_height);
      });
  }

$(document).ajaxComplete(function(){
    $(".true-grid").imagesLoaded().then(function(){
        console.log("images loaded");
        $(".true-grid").each(function(){
            equalise_articles($(this));
        });
    });
});


$.fn.imagesLoaded = function () {
    var $imgs = this.find('img[src!=""]');
    if (!$imgs.length) {return $.Deferred().resolve().promise();}
    var dfds = [];
    $imgs.each(function(){
        var dfd = $.Deferred();
        dfds.push(dfd);
        var img = new Image();
        img.onload = function(){dfd.resolve();}
        img.onerror = function(){dfd.resolve();}
        img.src = this.src;

    });
    return $.when.apply($,dfds);
}
})

function initSlider(){
    $('.testimonial-slider .et_pb_posts .et_pb_ajax_pagination_container').slick({
	  arrows: true,
	  infinite: true,
	  speed: 300,
	  slidesToShow: 1,
	  adaptiveHeight: true
	});
}

$(document).on('ready', function () {
    initSlider();
});
	
})(jQuery);

