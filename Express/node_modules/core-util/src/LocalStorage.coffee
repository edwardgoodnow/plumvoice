_ = require('lodash')

# If we're on an environment without localStore, emulate it with a singleton obj
unless localStorage?
  store = {}
  localStorage =
    removeItem: (key) ->
      delete store[key]
    setItem: (key, value) ->
      store[key] = value
    getItem: (key) ->
      store[key]

module.exports =
  ###
  Gets or sets the value for the given key.
  @param key:         The unique identifier of the value (this is prefixed with the namespace).
  @param value:       (optional). The value to set (pass null to remove).
  @param options:
            default:  (optional). The default value to return if the session does not contain the value (ie. undefined).
  ###
  prop: (key, value, options = {}) ->
    if value is null
      # REMOVE.
      localStorage.removeItem(key)

    else if value isnt undefined
      # WRITE.
      type =  if _.isString(value)
                'string'
              else if _.isBoolean(value)
                'bool'
              else if _.isNumber(value)
                'number'
              else if _.isDate(value)
                'date'
              else
                'object'

      writeValue = { value:value, type:type }
      localStorage.setItem(key, JSON.stringify(writeValue))

    else
      # READ ONLY.
      if json = localStorage.getItem(key)
        json = JSON.parse(json)
        value = switch json.type
                  when 'null', 'bool', 'string' then json.value
                  when 'number' then json.value.toNumber()
                  when 'date' then new Date(json.value)
                  when 'object' then json.value

      else
        value = undefined
      value = options.default if value is undefined

    # Finish up.
    value
