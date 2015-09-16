(($)->
  $.fn.fadeUp = (speed, callback) ->
    this.each ->
      if typeof speed is "function"
        callback = speed
        speed = "normal"
      $(this).slideUp(speed, callback).animate {opacity: 0}, {queue: false, duration: speed, complete: -> $(this).css('opacity', '')}

  $.fn.fadeDown = (speed, callback) ->
    this.each ->
      if typeof speed is "function"
        callback = speed
        speed = "normal"
      opacity = $(this).css('opacity') || 1;
      $(this).slideDown(speed, callback).css({opacity: 0}).animate {opacity: opacity}, {queue: false, duration: speed, complete: -> $(this).css('opacity', '')}
)(jQuery)