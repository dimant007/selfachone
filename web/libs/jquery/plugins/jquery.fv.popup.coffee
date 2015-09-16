(($) ->
  hideScroll = ->
    oldWidth = $('body').width()
    $('body').css overflow: 'hidden'
    $('body').css paddingRight:  $('body').width() - oldWidth

  showScroll = ->
    $('body').css
      overflow: ''
      paddingRight: ''

  window.Popup = class
    constructor: ( classes = "", $body = null ) ->
      @popup = $("<div class='popup'><div><div><div class='popup-page #{classes}'></div></div></div></div>")
      @popup.appendTo(window.document.body);
      @popup.find('.popup-page')
        .append('<a class="close close-popup">×</a>')

      if $body
        @popup.find('.popup-page').append($body)
        @show()

      @popup.on 'click', '.close-popup', => @close()
      @popup.click (e) =>
        return if $(e.target).parents('.popup-page').length
        return if $(e.target).hasClass('popup-page')

        @close()

    setBody: ( $body ) ->
      @popup.find('.popup-page').html('')
        .append('<a class="close close-popup">×</a>')
        .append($body)

    close: (callback) ->
      @popup.fadeOut =>
        showScroll()
        callback.call @ if typeof callback is "function"

    show: (callback) ->
      hideScroll()
      @popup.hide().fadeIn =>
        callback.call @ if typeof callback is "function"


)(jQuery);