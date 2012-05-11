
jQuery(document).ready(function($){

  // @todo: use $.on in future versions (jQuery 1.7+)
  $(document).delegate('.ratings:not(.rated) a', 'click', function(event){

    event.preventDefault();

    var control = $(this).parents('.ratings'),
        rating = $(this).parents('li').index();

    $.ajax({
      url: (typeof atom_config !== 'undefined') ? atom_config.blog_url :  post_ratings.blog_url,
      type: 'GET',
      data: ({
        post_id: $('ul', control).data('post'),
        rate: rating,
      }),
      dataType: 'json',
      beforeSend: function(){
        control.removeClass('error').addClass('loading');
      },
      error: function(){
        control.addClass('error');
      },
      success: function(response){

        if(response.error){
          control.addClass('error').find('.meta').html(response.error);

        }else{
          control.removeClass('loading').addClass('rated');
          $('.rating', control).width(response.rating * 16).parents('ul').attr('title', response.text);
          $('.meta', control).html(response.status);

          // other plugin can hook into this event...
          control.trigger('rated_post', response);
        }

        $('a', control).remove();
      }
    });

    return true;

  });

});


