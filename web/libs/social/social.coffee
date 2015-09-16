$ ->
  locale = fvSite.config.get "social.locale"

  if fbAppId = fvSite.config.get "social.facebook.applicationId"
    $.getScript "//connect.facebook.net/#{locale}/all.js", ->
      FB.init
        appId: fbAppId
        xfbml: true

      $("body").trigger('fbinited')

  if vkAppId = fvSite.config.get("social.vkontakte.applicationId")
    $.getScript "//vkontakte.ru/js/api/openapi.js", ->
      VK.init
        apiId: vkAppId

      $("body").trigger('vkinited')

  window.social = {} unless window.social?
  window.social.auth = {}

  class Facebook
    signIn: ( callback, fallback )->
      FB.getLoginStatus (response)->
        if response.status is "not_authorized" or response.status is "unknown"
          FB.login (authResponse) ->
            if authResponse.status is "connected"
              return callback authResponse
            else
              return fallback authResponse

        else if response.status is "connected"
          return callback response
        else
          return fallback response

    signOut: (callback, fallback) ->

  class Vkontakte
    signIn: ( callback, fallback ) ->
      VK.Auth.getLoginStatus (response) ->
        if response.session
          callback response
        else
          VK.Auth.login (authResponse) ->
            if authResponse.session
              callback authResponse
            else
              fallback authResponse
          , 1 + 8192

          channel = window.location.protocol + '//' + window.location.hostname + "/channel"
          channel = 'close.html'
          url = VK._domain.main + VK._path.login + '?client_id=' + VK._apiId + '&display=popup&redirect_uri=' + channel + '&response_type=token&scope=' + (1 + 8192)

          VK.UI.popup
            width: 620,
            height: 370,
            url: url

    signOut: (callback, fallback) ->

  class SocialShare
    Vkontakte: (data) ->
      data.share.url = window.location.href unless data.share.url?

      shareUrl = 'http://vkontakte.ru/share.php?'
      shareUrl += 'url=' + encodeURIComponent data.share.url
      shareUrl += '&title=' + encodeURIComponent data.share.title
      shareUrl += '&description=' + encodeURIComponent data.share.description
      shareUrl += '&image=' + encodeURIComponent data.share.image
      shareUrl += '&noparse=true';

      window.open shareUrl, 'Share', 'toolbar=0,status=0,width=626,height=536'

    Facebook: (data) ->
      data.share.url = window.location.href unless data.share.url?

      FB.ui
        method: 'feed'
        link: data.share.url
        name: data.share.title
        description: data.share.description
        picture: data.share.image

    Pinterest: (data) ->
      data.share.url = window.location.href unless data.share.url?

      shareUrl = 'http://www.pinterest.com/pin/create/button/?'
      shareUrl += 'url=' + encodeURIComponent data.share.url
      shareUrl += '&media=' + encodeURIComponent data.share.image
      shareUrl += '&description=' + encodeURIComponent data.share.title + "\n" + data.share.description

      window.open shareUrl, 'Share', 'toolbar=0,status=0,width=626,height=536'

    Twitter: (data) ->
      data.share.url = window.location.href unless data.share.url?

      shareUrl  = 'http://twitter.com/share?';
      shareUrl += 'text='      + encodeURIComponent data.share.title + "\n" + data.share.description
      shareUrl += '&url='      + encodeURIComponent data.share.url
      shareUrl += '&counturl=' + encodeURIComponent data.share.url

      window.open shareUrl, 'Share', 'toolbar=0,status=0,width=626,height=536'

  class Invite
    Do: (callback) ->
      if fvSite.config.get("user.subclass") is "User_Vk"
        return @Vkontakte ( id ) ->
          $.ajax
            url: "/invite/vkontakte"
            data:
              id: id
            dataType: "json"
            success: (data) ->
              callback() if callback?

      if fvSite.config.get("user.subclass") is "User_FB"
        return @Facebook (data) ->
          $.ajax
            url: "/invite/facebook"
            data: data
            dataType: "json"
            success: (data) ->
              callback() if callback?

      throw "Not social user"

    Vkontakte: (callback) ->
      $.ajax
        url: "/invite/render"
        dataType: "json"
        success: (data) ->
          if data.success
            $popup = $ data.html
            $popup.appendTo("body").hide().fadeIn()

            $popup.find(".invite-clear-search").click ->
              $popup.find(".invite-search-haystack>*").show()
              $popup.find(".invite-search-needle").val ''
              no

            $popup.find(".invite-search-needle").keydown ->
              filter = (needle) ->
                $popup.find(".invite-search-haystack>*").each ->
                  regEx = if needle then new RegExp ".*#{needle}.*", "ig" else /.*/
                  $link = $ @

                  return $link.show() if regEx.test $link.data "name"
                  $link.hide()

                $popup.trigger('change')

              setTimeout (=> filter $(this).val()), 1

            $popup.find(".invite-user").on "click", ->
              $this = $ @
              VK.api "wall.post",
                owner_id: $this.data "id"
                message: fvSite.dictionary.get("inviteTitle") + "\n" + fvSite.dictionary.get("inviteText")
                attachments: "http://#{document.location.host}?req=" + fvSite.config.get('user.id')
                ->
                  callback $this.data "id"
              no

            close = -> $popup.fadeOut -> $popup.remove()

            $popup.find(".invite-close").on "click", close

    Facebook: (callback) ->
      FB.ui(
        method: "apprequests"
        title: fvSite.dictionary.get "inviteTitle"
        message: fvSite.dictionary.get "inviteText"
        (response) -> callback response
      )

  window.social.auth.facebook = new Facebook
  window.social.auth.vkontakte = new Vkontakte
  window.social.share = new SocialShare

  window.social.invite = new Invite
