_ = require('lodash')


module.exports =
  ###
  Provides a more convenient way of setting a timeout.

  @param msecs:  The milliseconds to delay.
  @param func:   The function to invoke.

  @returns  The timer handle.
            Use the [stop] method to cancel the timer.
  ###
  delay: (msecs, func) ->
    # Check parameters.
    if _.isFunction(msecs)
      func = msecs
      msecs = 0 # Immediate "defer" when no milliseconds value specified.

    return unless _.isFunction(func)

    # Return an object with the running timer.
    result =
      msecs: msecs
      id:    setTimeout(func, msecs)
      stop: -> clearTimeout(@id)


  ###
  Provides a more convenient way of setting an interval.

  @param msecs:  The period of the function call in milliseconds.
  @param func:   The function to invoke.

  @returns  The interval handle.
            Use the [stop] method to cancel the interval.
  ###
  interval: (msecs, func) ->
    # both arguments required
    return unless _.isNumber(msecs)
    return unless _.isFunction(func)

    # Return an object with the running timer.
    result =
      msecs: msecs
      id:    setInterval(func, msecs)
      stop: -> clearInterval(@id)
