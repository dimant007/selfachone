// Generated by CoffeeScript 1.7.1
(function() {
  (function($) {
    var propertyFunc, propertyName, styleSupport, transformPropName, _i, _len, _ref, _results;
    styleSupport = function(prop) {
      var capProp, div, prefix, prefixes, vendorProp, _i, _len;
      capProp = prop.charAt(0).toUpperCase() + prop.slice(1);
      prefixes = ["Moz", "Webkit", "O", "ms"];
      div = document.createElement("div");
      if (div.style[prop] != null) {
        return prop;
      }
      for (_i = 0, _len = prefixes.length; _i < _len; _i++) {
        prefix = prefixes[_i];
        vendorProp = prefix + capProp;
        if (div.style[vendorProp] != null) {
          return vendorProp;
        }
      }
    };
    transformPropName = styleSupport("transform");
    $.cssNumber.scale = true;
    $.cssNumber.rotate = true;
    propertyFunc = function(propertyName) {
      return {
        get: function(elem, computed, extra) {
          elem._transform = elem._transform || {};
          return elem._transform[propertyName];
        },
        set: function(elem, value) {
          var prop, result, _ref;
          elem._transform = elem._transform || {};
          elem._transform[propertyName] = value;
          result = "";
          _ref = elem._transform;
          for (prop in _ref) {
            value = _ref[prop];
            result += " " + prop + "(" + value + ")";
          }
          return elem.style[transformPropName] = result;
        }
      };
    };
    _ref = ["scale", "scaleX", "scaleY", "translateX", "translateY", "rotate"];
    _results = [];
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      propertyName = _ref[_i];
      _results.push($.cssHooks[propertyName] = propertyFunc(propertyName));
    }
    return _results;
  })(jQuery);

}).call(this);