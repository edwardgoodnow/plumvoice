_ = require('lodash')


STRIP_COMMENTS = /((\/\/.*$)|(\/\*[\s\S]*?\*\/))/mg
ARGUMENT_NAMES = /([^\s,]+)/g



module.exports =
  ###
  Determines the parameter names of a function

    See: http://stackoverflow.com/questions/1007981/how-to-get-function-parameter-names-values-dynamically-from-javascript

  @param func: The function to examine.
  @returns an array of strings.
  ###
  functionParameters: (func) ->
    return [] unless _.isFunction(func)
    fnStr = func.toString().replace(STRIP_COMMENTS, '')
    result = fnStr.slice(fnStr.indexOf('(')+1, fnStr.indexOf(')')).match(ARGUMENT_NAMES)
    result = [] if result is null
    result
