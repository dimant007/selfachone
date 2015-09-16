window.fvSite = class

  class Config
    data = {}
    isReady = false

    constructor: ( @data ) ->

    get: (cPath) ->
      path = cPath.split "."
      response = @data

      deep = (level) ->
        return response = response[level] if response and response[level]?
        response = null

      deep it for it in path
      response

  class Dictionary
    constructor: ( @data ) ->

    get: ( key ) ->
      return @data[key] if @data.hasOwnProperty(key)

      if jQuery and fvSite.config.get('debug')
        jQuery.ajax
          url: '/tools/add-dictionary-key'
          data: key: key

        @data[key] = null

      key


  @config: new Config( $CONFIG$ )
  @dictionary: new Dictionary( $DICTIONARY$ )