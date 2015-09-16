window.GoogleAnalytics = class
  @trackEvent: ( o ) ->
    if window._gaq
      _gaq.push [ '_trackEvent', o.category, o.action ]

    if window.ga
      ga 'send',
        hitType: 'event'
        eventCategory: o.category
        eventAction: o.action

  @trackSocial: ( o ) ->
    o.href = window.location.href unless o.href

    if window._gaq
      _gaq.push [ '_trackSocial', o.network, o.action, o.target, o.href ]

    if window.ga
      ga 'send',
        hitType: 'social'
        socialNetwork: o.network
        socialAction: o.action
        socialTarget: o.target
        page: o.href