timer = require('./src/timer.coffee')
util  = require('./src/util.coffee')
func  = require('./src/func.coffee')

module.exports =
  Handlers: require('./src/Handlers.coffee')
  LocalStorage: require('./src/LocalStorage.coffee')

  delay: timer.delay
  interval: timer.interval
  isBlank: util.isBlank
  functionParameters: func.functionParameters
