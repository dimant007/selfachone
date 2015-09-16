( ($) ->
  styleSupport = ( prop ) ->
    capProp = prop.charAt( 0 ).toUpperCase() + prop.slice( 1 )
    prefixes = [ "Moz", "Webkit", "O", "ms" ]
    div = document.createElement "div"

    return prop if div.style[prop]?

    for prefix in prefixes
      vendorProp = prefix + capProp
      return vendorProp if div.style[vendorProp]?


  transformPropName = styleSupport "transform"

  $.cssNumber.scale = true
  $.cssNumber.rotate = true

  propertyFunc = (propertyName) ->
    get: ( elem, computed, extra ) ->
      elem._transform = elem._transform || {}
      elem._transform[propertyName]

    set: ( elem, value ) ->
      elem._transform = elem._transform || {}
      elem._transform[propertyName] = value

      result = ""
      for prop, value of elem._transform
        result += " #{prop}(#{value})"

      elem.style[transformPropName] = result

  for propertyName in [ "scale", "scaleX", "scaleY", "translateX", "translateY", "rotate" ]
    $.cssHooks[propertyName] = propertyFunc(propertyName)

)( jQuery );